<?php

include_once("helper.php");

$author_data = get_people();
$publications = load_publications(get_pub_ids(), $author_data[0]);
$rez = array("publications" => $publications, "authornames" => $author_data[1]);
echo json_encode($rez);

function get_pub_ids() {
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

function get_people() {
  global $store;

  $people_q = prefix().'
    SELECT ?id ?name WHERE {
      ?id a schema:Person . ?id schema:name ?name . 
    }
  ';
  $people = array();
  $peoplenames = array();
  if ($rows = $store->query($people_q, 'rows')) 
  {
    foreach ($rows as $row) 
    { 
      $people[$row["id"]]=$row["name"];
      array_push($peoplenames, $row["name"]);
    }
  }
  return [$people, $peoplenames];
}

function load_publications($pub_ids, $authors) {  
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
      $as = array();
      foreach ($row["http://schema.org/author"] as $i => $value) {
        array_push($as, array("id"=> $value["value"], "name"=>$authors[$value["value"]]));
      }
      array_push($publications,array(
        "id" => $id, 
        "title" => $row["http://schema.org/name"][0]["value"],
        "url" => $row["http://schema.org/url"][0]["value"],
        "img" => $row["http://schema.org/image"][0]["value"], 
        "year" => $row["http://schema.org/copyrightYear"][0]["value"], 
        "authors" => $as,
        ));
    }
  }
  if ($errs = $store->getErrors()) {
    echo "{ 'error' : 'Error in load_publications', 'returned':".var_dump($errs)." }";
    return;
  }

  return $publications;
}

?>