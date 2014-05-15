<?php

include_once("helper.php");

echo load_publications(get_ids());

function get_ids() {
  global $store;

  $pub_q = prefix().'
    SELECT ?id WHERE {
      ?id a schema:ScholarlyArticle . 
    }
  ';
  $pub_ids = array();
  if ($rows = $store->query($pub_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      array_push($pub_ids,$row['id']);
    }
  }
  return $pub_ids;
}

function load_publications($pub_ids) {  
  global $store;
  $publications = array();

  $len = count($pub_ids);
  if ($len == 0) { return "{'publications':[]}"; }

  $pub_q = 'DESCRIBE ';
  for ($i=0; $i < $len; $i++) { 
    $id = $pub_ids[$i];
    $pub_q = $pub_q.'<'.$id.'> ';
  }
  if ($rows = $store->query($pub_q)) 
  {
    foreach ($rows["result"] as $id => $row) 
    {    
      array_push($publications,array(
        "id" => $id, 
        "title" => $row["http://schema.org/name"][0]["value"],
        "url" => $row["http://schema.org/url"][0]["value"],
        "img" => $row["http://schema.org/image"][0]["value"], 
        ));
    }
  }
  if ($errs = $store->getErrors()) {
    echo "{ 'error' : 'Error in load_publications', 'returned':".var_dump($errs)." }";
    return;
  }

  $rez = array("publications" => $publications);
  return json_encode($rez);
}

?>