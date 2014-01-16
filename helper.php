<?php

include_once("/var/www/html/usewod-prov/arc2/ARC2.php");

$store;

$config = array(
  /* db */
  'db_host' => 'localhost',
  'db_name' => 'usewod',
  'db_user' => 'root',
  'db_pwd' => 'root',
  /* store */
  'store_name' => 'usewodprov'
);

$store = ARC2::getStore($config);
if (!$store->isSetUp()) {
  $store->setUp();
}

function load_new_data() 
{
  // how many objects are shown? 
  // what are the types of the objects shown? 
  // do we have / set a smart alg for picking objects from the db?

  global $ds_ids;
  global $datasets;
  global $pub_ids;
  global $publications;
  global $store;

  $ds_q = '
    PREFIX schema: <http://schema.org/> 
    SELECT ?id ?name ?img ?url WHERE {
      ?id a schema:Dataset . 
      ?id schema:name ?name .
      ?id schema:url ?url .
      ?id schema:image ?img .
    }
  ';
  
  if ($rows = $store->query($ds_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      $ds_ids[] = $row['id'];
      $datasets[] = array(
        "id" => $row['id'], 
        "name" => $row['name'],
        "url" => $row['url'],
        "img" => $row['img'], 
        );
    }
  }

  $pub_q = '
    PREFIX schema: <http://schema.org/> 
    SELECT ?id ?title ?img ?url WHERE {
      ?id a schema:ScholarlyArticle . 
      ?id schema:name ?title .
      ?id schema:url ?url .
      ?id schema:image ?img .
    }
  ';
  
  if ($rows = $store->query($pub_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      $pub_ids[] = $row['id'];
      $publications[] = array(
        "id" => $row['id'], 
        "title" => $row['title'],
        "url" => $row['url'],
        "img" => $row['img'], 
        );
    }
  }
}

function load_datasets() 
{
  global $ds_ids;
  global $datasets;

  $len = count($ds_ids);
  for ($i=0; $i < $len; $i++) { 
    $id = $ds_ids[$i];
    $ds_q = '
      PREFIX schema: <http://schema.org/> 
      SELECT ?name ?img ?url WHERE {
        '.$id.' a schema:Dataset . 
        '.$id.' schema:name ?name .
        '.$id.' schema:url ?url .
        '.$id.' schema:image ?img .
      } 
    ';
    if ($rows = $store->query($ds_q, 'rows')) 
    {
      foreach ($rows as $row) 
      {
        $datasets[] = array(
          "id" => $id, 
          "name" => $row['name'],
          "url" => $row['url'],
          "img" => $row['img'], 
          );
      }
    }
  }
}

function load_publications() 
{
  global $pub_ids;
  global $publications;

  $len = count($pub_ids);
  for ($i=0; $i < $len; $i++) { 
    $id = $pub_ids[$i];
    $pub_q = '
      PREFIX schema: <http://schema.org/> 
      SELECT ?title ?img ?url WHERE {
        '.$id.' a schema:ScholarlyArticle . 
        '.$id.' schema:name ?title .
        '.$id.' schema:url ?url .
        '.$id.' schema:image ?img .
      } 
    ';
    if ($rows = $store->query($pub_q, 'rows')) 
    {
      foreach ($rows as $row) 
      {
        $publications[] = array(
          "id" => $id, 
          "title" => $row['title'],
          "url" => $row['url'],
          "img" => $row['img'], 
          );
      }
    }
  }
}

function add_graph_info($gid) 
{
  global $store;
  global $username;

  $now = time();
  $ng_q = '
    PREFIX up: <http://data.semanticweb.org/usewod/2014/prov/> 
    PREFIX dcterms: <http://purl.org/dc/terms/> 
    INSERT INTO up:graphs {
      up:'.$gid.' dcterms:creator '.'"'.$username.'"'.' .
      up:'.$gid.' dcterms:created '.'"'.$now.'"'.' .
    }
  ';
  // echo $ng_q ;
  $rs = $store->query($ng_q);
  if ($errs = $store->getErrors()) {
    echo "Error in add_graph_info";
    var_dump($errs);
  }
  return $rs;
}

function add_dataset_from_fields($name, $v, $url, $img, $about)
{
  global $store;
  global $datasets;

  $gid = uniqid("graph_");
  $id = uniqid("dataset_");
  $insert_q = '
    PREFIX up: <http://data.semanticweb.org/usewod/2014/prov/> 
    PREFIX schema: <http://schema.org/> 
    INSERT INTO up:'.$gid.' { 
      up:'.$id.' a schema:Dataset . 
      up:'.$id.' schema:name '.'"'.$name.'"'.' .
      up:'.$id.' schema:version '.'"'.$v.'"'.' . 
      up:'.$id.' schema:url <'.$url.'> .
      up:'.$id.' schema:image <'.$img.'> .
      up:'.$id.' schema:description '.'"'.$about.'"'.' .
    }
  ';
  $rs = $store->query($insert_q);  
  if ($errs = $store->getErrors()) 
  {
    echo "Error in add_dataset_from_fields";
    var_dump($errs);
  }
  add_graph_info($gid);

  if (is_null($datasets)) 
  { 
    $datasets = array(); 
  }
  $datasets[] = array(
    "id" => $id, 
    "name" => $name,
    "url" => $url,
    "img" => $img, 
    );
}

function add_paper_from_fields($title, $authors, $venue, $year, $img)
{

}

function add_person($name)
{

}

function add_paper_from_bib($bib)
{
  //TODO convenience option for people who want to just paste in the bib file of a paper
}

function get_all_people()
{
  global $store;

  $all_people_q = '
    PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
    SELECT ?name WHERE {
      ?id a foaf:Person ; foaf:name ?name .
    }
  ';
  
  $all_people = '[ ';
  if ($rows = $store->query($all_people_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      $all_people = $all_people . '"' . $row['name'] . '",' ;
    }
  }
  $all_people[strlen($all_people)-1] = ']';

  return $all_people;
}

?>