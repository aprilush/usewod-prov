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

function prefix() 
{
  return '
    PREFIX usewod: <http://data.usewod.org/> 
    PREFIX schema: <http://schema.org/> 
    PREFIX dcterms: <http://purl.org/dc/terms/> 
  ';
}

function load_new_data() 
{
  // how many objects are shown? 
  // what are the types of the objects shown? 
  // do we have / set a smart alg for picking objects from the db?

  global $ds_ids;
  // global $datasets;
  global $pub_ids;
  // global $publications;
  global $store;
  // $ds_q = '
  //   PREFIX schema: <http://schema.org/> 
  //   SELECT ?id ?name ?img ?url WHERE {
  //     ?id a schema:Dataset . 
  //     ?id schema:name ?name .
  //     ?id schema:url ?url .
  //     ?id schema:image ?img .
  //   }
  // ';
  $ds_q = prefix().'
    SELECT ?id WHERE {
      ?id a schema:Dataset . 
    }
  ';
  if ($rows = $store->query($ds_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      $ds_ids[] = $row['id'];
      // $datasets[] = array(
      //   "id" => $id, 
      //   "name" => $row['name'],
      //   "url" => $row['url'],
      //   "img" => $row['img'], 
      //   );
    }
  }

  // $pub_q = '
  //   PREFIX schema: <http://schema.org/> 
  //   SELECT ?id ?title ?img ?url WHERE {
  //     ?id a schema:ScholarlyArticle . 
  //     ?id schema:name ?title .
  //     ?id schema:url ?url .
  //     ?id schema:image ?img .
  //   }
  // ';
  $pub_q = prefix().'
    SELECT ?id WHERE {
      ?id a schema:ScholarlyArticle . 
    }
  ';
  
  if ($rows = $store->query($pub_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      $pub_ids[] = $row['id'];
      // $publications[] = array(
      //   "id" => $row['id'], 
      //   "title" => $row['title'],
      //   "url" => $row['url'],
      //   "img" => $row['img'], 
      //   );
    }
  }
}

function load_datasets() 
{
  global $ds_ids;
  global $datasets;
  global $store;

  $len = count($ds_ids);
  if ($len == 0) { return ; }
  
  $ds_q = 'DESCRIBE ';
  for ($i=0; $i < $len; $i++) { 
    $id = $ds_ids[$i];
    $ds_q = $ds_q.'<'.$id.'> ';
  }
  if ($rows = $store->query($ds_q)) 
  {
    foreach ($rows["result"] as $id => $row) 
    {
      $datasets[] = array(
        "id" => $id, 
        "name" => $row["http://schema.org/name"][0]["value"],
        "url" => $row["http://schema.org/url"][0]["value"],
        "img" => $row["http://schema.org/image"][0]["value"], 
        );
    }
  } 
  if ($errs = $store->getErrors()) {
    echo "Error in load_datasets";
    var_dump($errs);
  }
}

function load_publications() 
{
  global $pub_ids;
  global $publications;
  global $store;

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
}

function add_graph_info($gid) 
{
  global $store;
  global $username;

  $now = time();
  $ng_q = prefix().'
    INSERT INTO usewod:graph {
      usewod:'.$gid.' dcterms:creator '.'"'.$username.'"'.' .
      usewod:'.$gid.' dcterms:created '.'"'.$now.'"'.' .
    }
  ';
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
  global $ds_ids;

  $gid = uniqid("graph/");
  $id = uniqid("dataset/");
  $insert_q = prefix().'
    INSERT INTO usewod:'.$gid.' { 
      usewod:'.$id.' a schema:Dataset . 
      usewod:'.$id.' schema:name '.'"'.$name.'"'.' .
      usewod:'.$id.' schema:version '.'"'.$v.'"'.' . 
      usewod:'.$id.' schema:url <'.$url.'> .
      usewod:'.$id.' schema:image <'.$img.'> .
      usewod:'.$id.' schema:description '.'"'.$about.'"'.' .
    }
  ';
  $rs = $store->query($insert_q);  
  if ($errs = $store->getErrors()) 
  {
    echo "Error in add_dataset_from_fields";
    var_dump($errs);
  }
  add_graph_info($gid);
  $ds_ids[] = $id;
}

function add_paper_from_fields($title, $authors, $venue, $year, $url, $img)
{
  global $store;
  global $pub_ids;
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
    echo "Error in add_paper_from_fields";
    var_dump($errs);
  }
  add_graph_info($gid);
  $pub_ids[] = $id;
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

function add_paper_from_bib($bib)
{
  //TODO convenience option for people who want to just paste in the bib file of a paper
}

function add_relation($s, $p, $o) 
{
  global $store;

  $gid = uniqid("graph/");

  $insert_q = prefix().'
    INSERT INTO usewod:'.$gid.' { 
      <'.$s.'> <'.$p.'> <'.$o.'> .
    }
  ';

  $rs = $store->query($insert_q);  
  if ($errs = $store->getErrors()) 
  {
    echo "Error in add_relation";
    var_dump($errs);
  }
  add_graph_info($gid);
}

function get_all_people_names()
{
  global $store;

  $all_names_q = prefix().'
    SELECT ?name WHERE {
      ?id a schema:Person ; schema:name ?name .
    }
  ';
  
  $all_names = '[ ';
  if ($rows = $store->query($all_names_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      $all_names = $all_names . '"' . $row['name'] . '",' ;
    }
  }
  $all_names[strlen($all_names)-1] = ']';

  return $all_names;
}

?>