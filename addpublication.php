<?php

include_once("/var/www/html/usewod-prov/helper.php");

if ( isset($_POST['pub-title']) && !empty($_POST['pub-title']) && // title and author are mandatory
    isset($_POST['pub-author']) && !empty($_POST['pub-author']) ) {
  add_paper_from_fields($_POST['pub-title'], $_POST['pub-author'], $_POST['pub-venue'], $_POST['pub-year'], $_POST['pub-url'], $_POST['pub-img']);
}

function add_paper_from_fields($title, $authors, $venue, $year, $url, $img) {
  global $store;
  global $usewod_url;
  $names = explode("\n",$authors);
  $gid = uniqid("graph/");
  $id = uniqid("publication/");

  $newpub = array("id" => $usewod_url.$id, "title" => $title);
  $triples = [' usewod:'.$id.' a schema:ScholarlyArticle ', 
              ' usewod:'.$id.' schema:name '.'"'.$title.'" ' ];
  if ( isset($year) && !empty($year) ) {
    $newpub["year"]=$year;
    $triples[] = ' usewod:'.$id.' schema:copyrightYear '.'"'.$year.'" ';
  }
  if ( isset($url) && !empty($url) ) {
    $newpub["url"] = $url;
    $triples[] = ' usewod:'.$id.' schema:url <'.$url.'> ';
  }
  if ( isset($img) && !empty($img) ) {
    $newpub["img"] = $img;
    $triples[] = ' usewod:'.$id.' schema:image <'.$img.'> ';
  }

  $authors = array();
  foreach ($names as $name) 
  {
    $name = trim($name);
    $aid = find_author_id($name);
    if ( !$aid ) 
    {
      $aid = uniqid("person/");
      $triples[] = ' usewod:'.$aid.' a schema:Person ';
      $triples[] = ' usewod:'.$aid.' schema:name "'.$name. '" '; 
      $triples[] = ' usewod:'.$id.' schema:author usewod:'.$aid.' '; 
    } 
    else 
    {
      $triples[] = ' usewod:'.$id.' schema:author <'.$aid.'> '; 
    }
    $authors[] = array("id" => $aid, "name" => $name);
  }
  $newpub["authors"] = $authors;

  $insert_q = prefix().'INSERT INTO usewod:'.$gid.' { '. implode(" . ", $triples) .'}';

  $rs = $store->query($insert_q);  
  if ($errs = $store->getErrors()) 
  {
    echo "{ 'error' : 'Error in add_paper_from_fields', 'returned':".var_dump($errs)." }";
    return;
  }
  add_graph_info($gid);
  $rez = array("added" => $newpub);
  echo json_encode($rez);
}

function find_author_id($name)
{
  global $store;

  $name_q = prefix().'
    SELECT ?aid WHERE {
      ?aid a schema:Person .
      ?aid schema:name "'.$name.'" .
    }
  ';
  if ($rows = $store->query($name_q, 'rows')) 
  {
    if ( count($rows) > 0 ) 
    {
      return $rows[0]['aid'];
    }
  }
  return False;
}

?>