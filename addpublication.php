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

  $insert_q = prefix().'
    INSERT INTO usewod:'.$gid.' { 
      usewod:'.$id.' a schema:ScholarlyArticle . 
      usewod:'.$id.' schema:name '.'"'.$title.'"'.' .
      usewod:'.$id.' schema:copyrightYear '.'"'.$year.'"'.' .
      usewod:'.$id.' schema:url <'.$url.'> .
      usewod:'.$id.' schema:image <'.$img.'> . ';
  foreach ($names as $name) 
  {
    $name = trim($name);
    $aid = find_author_id($name);
    if ( !$aid ) 
    {
      $aid = uniqid("person/");
      $insert_q = $insert_q.'
        usewod:'.$aid.' a schema:Person .
        usewod:'.$aid.' schema:name "'.$name.'" . 
        usewod:'.$id.' schema:author usewod:'.$aid.' . 
      ';
    } 
    else 
    {
      $insert_q = $insert_q.'
        usewod:'.$id.' schema:author <'.$aid.'> . 
      ';
    }
  }
  $insert_q = $insert_q.'}';

  $rs = $store->query($insert_q);  
  if ($errs = $store->getErrors()) 
  {
    echo "{ 'error' : 'Error in add_paper_from_fields', 'returned':".var_dump($errs)." }";
    return;
  }
  add_graph_info($gid);
  echo "{'added':[{'id':'".$usewod_url.$id."'}]";
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