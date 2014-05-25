<?php

include_once("helper.php");
if ( isset($_POST['user']) && !empty($_POST['user']) ) {
  $username = $_POST['user'];
  if ( isset($_POST['pub-title']) && !empty($_POST['pub-title']) && // title and author are mandatory
      isset($_POST['pub-author']) && !empty($_POST['pub-author']) ) {
    add_paper_from_fields($username, $_POST['pub-title'], $_POST['pub-author'], $_POST['pub-year'], $_POST['pub-url'], $_POST['pub-img']);
  }
}

function add_paper_from_fields($username, $title, $authors, $year, $url, $img) {
  global $store;
  global $usewod_url;
  $names = explode("\n",$authors);
  $gid = uniqid("graph/");
  $id = uniqid("publication/");

  $newpub = array("id" => $usewod_url.$id, "title" => $title);
  $triples = array(' usewod:'.$id.' a schema:ScholarlyArticle, prov:Entity ', 
              ' usewod:'.$id.' schema:name '.'"'.$title.'" ' );
  if ( isset($year) && !empty($year) ) {
    $newpub["year"]=$year;
    array_push($triples,' usewod:'.$id.' schema:copyrightYear '.'"'.$year.'" ');
  }
  if ( isset($url) && !empty($url) ) {
    $newpub["url"] = $url;
    array_push($triples,' usewod:'.$id.' schema:url <'.$url.'> ');
  }
  if ( isset($img) && !empty($img) ) {
    $newpub["img"] = $img;
    array_push($triples,' usewod:'.$id.' schema:image <'.$img.'> ');
  }

  $authors = array();
  foreach ($names as $name) 
  {
    $name = trim($name);
    $aid = find_author_id($name);
    if ( !$aid ) 
    {
      $aid = "person/".trim(md5($name));
      array_push($triples,' usewod:'.$aid.' a schema:Person ');
      array_push($triples,' usewod:'.$aid.' a prov:Agent ');
      array_push($triples,' usewod:'.$aid.' schema:name "'.$name. '" '); 
      array_push($triples,' usewod:'.$id.' schema:author usewod:'.$aid.' '); 
      array_push($triples,' usewod:'.$id.' prov:wasAttributedTo usewod:'.$aid.' '); 
    } 
    else 
    {
      array_push($triples,' usewod:'.$id.' schema:author <'.$aid.'> '); 
      array_push($triples,' usewod:'.$id.' prov:wasAttributedTo <'.$aid.'> '); 
    }
    array_push($authors,array("id" => $aid, "name" => $name));
  }
  $newpub["authors"] = $authors;

  $insert_q = prefix().'INSERT INTO usewod:'.$gid.' { '. implode(" . ", $triples) .'}';

  $rs = $store->query($insert_q);  
  if ($errs = $store->getErrors()) 
  {
    echo "{ 'error' : 'Error in add_paper_from_fields', 'returned':".var_dump($errs)." }";
    return;
  }
  add_graph_info($gid, $username);
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