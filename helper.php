<?php

include_once("/var/www/html/usewod-prov/arc2/ARC2.php");

global $store;
global $datasets;
global $publications;
global $people;

$config = array(
  /* db */
  'db_host' => 'localhost',
  'db_name' => 'usewod',
  'db_user' => 'root',
  'db_pwd' => 'root',
  /* store */
  'store_name' => 'usewod-prov'
);

$store = ARC2::getStore($config);
if (!$store->isSetUp()) {
  $store->setUp();
}

$datasets = array();
$publications = array();
$people = array();

function load_new_data() 
{
  // how many objects are shown? 
  // what are the types of the objects shown? 
  // do we have / set a smart alg for picking objects from the db?
}

function add_paper_from_bib($bib)
{

}

function add_paper_from_fields($title, $authors, $year, $venue)
{

}

function add_person($name)
{

}

function add_dataset_from_fields($dsname, $year, $logo)
{

}

function get_all_people()
{
  global $store;

  // $all_people_q = '
  //   PREFIX foaf: <http://xmlns.com/foaf/0.1/> .
  //   SELECT ?id ?fn ?ln WHERE {
  //     ?id a foaf:Person ; foaf:firstName ?fn ; foaf:lastName ?ln .
  //   }
  // ';
  $all_people_q = '
    PREFIX foaf: <http://xmlns.com/foaf/0.1/> .
    SELECT ?name WHERE {
      ?id a foaf:Person ; foaf:name ?name .
    }
  ';
  
  $all_people = '[ ';
  if ($rows = $store->query($all_people_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      // $all_people = $all_people . '{label:"' . $row['fn'] . ' ' . $row['ln'] . '", value:"' . $row['id'] . '"},'  ;
      $all_people = $all_people . '"' . $row['name'] . '",' ;
    }
  }
  $all_people[strlen($all_people)-1] = ']';

  return $all_people;
}

?>