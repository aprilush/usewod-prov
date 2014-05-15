<?php

include_once("helper.php");

echo load_datasets(get_ids());

function get_ids() {
  global $store;

  $pub_q = prefix().'
    SELECT ?id WHERE {
      ?id a schema:Dataset . 
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

function load_datasets($ds_ids) {
  global $store;
  $datasets = array();

  $len = count($ds_ids);
  if ($len == 0) { return "{'datasets':[]}"; }

  $ds_q = 'DESCRIBE ';
  for ($i=0; $i < $len; $i++) { 
    $id = $ds_ids[$i];
    $ds_q = $ds_q.'<'.$id.'> ';
  }
  if ($rows = $store->query($ds_q)) 
  {
    foreach ($rows["result"] as $id => $row) 
    {
      array_push($datasets,array(
        "id" => $id, 
        "name" => $row["http://schema.org/name"][0]["value"],
        "url" => $row["http://schema.org/url"][0]["value"],
        "img" => $row["http://schema.org/image"][0]["value"], 
        ));
    }
  } 
  if ($errs = $store->getErrors()) {
    echo "{ 'error' : 'Error in load_datasets', 'returned':".var_dump($errs)." }";
    return;
  }

  $rez = array("datasets" => $datasets);
  return json_encode($rez);

}

?>