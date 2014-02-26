<?php

include_once("/var/www/html/usewod-prov/helper.php");

load_publications(get_ids());

function get_ids() {
  global $store;

  $pub_q = prefix().'
    SELECT ?id WHERE {
      ?id a schema:ScholarlyArticle . 
    }
  ';
  $pub_ids = [];
  if ($rows = $store->query($pub_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      $pub_ids[] = $row['id'];
    }
  }
  return $pub_ids;
}

function load_publications($pub_ids) {  
  global $store;
  $publications = [];

  $len = count($pub_ids);
  if ($len == 0) { return ; }

  $pub_q = 'DESCRIBE ';
  for ($i=0; $i < $len; $i++) { 
    $id = $pub_ids[$i];
    $pub_q = $pub_q.'<'.$id.'> ';
  }
  if ($rows = $store->query($pub_q)) 
  {
    foreach ($rows["result"] as $id => $row) 
    {    
      $publications[] = array(
        "id" => $id, 
        "title" => $row["http://schema.org/name"][0]["value"],
        "url" => $row["http://schema.org/url"][0]["value"],
        "img" => $row["http://schema.org/image"][0]["value"], 
        );
    }
  }
  if ($errs = $store->getErrors()) {
    echo "Error in load_publications";
    var_dump($errs);
  }

  return var_dump($publications);
}

?>