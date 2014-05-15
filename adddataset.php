<?php

include_once("helper.php");

if ( isset($_POST['user']) && !empty($_POST['user']) ) {
  $username = $_POST['user'];
  if ( isset($_POST['ds-name']) && !empty($_POST['ds-name']) ) { // only the name is mandatory
    add_dataset_from_fields($username, $_POST['ds-name'], $_POST['ds-v'], $_POST['ds-url'], $_POST['ds-img'], $_POST['ds-size'], $_POST['ds-format'], $_POST['ds-about']);
  }
}

function add_dataset_from_fields($username, $name, $v, $url, $img, $about)
{
  global $store;
  global $usewod_url;

  $gid = uniqid("graph/");
  $id = uniqid("dataset/");
  
  $newds = array("id" => $id, "name" => $name);
  $triples = array(' usewod:'.$id.' a schema:Dataset, prov:Entity ', 
              ' usewod:'.$id.' schema:name '.'"'.$name.'" ' );
  if ( isset($v) && !empty($v) ) {
    $newdds["version"] = $v;  
    array_push($triples,' usewod:'.$id.' schema:version '.'"'.$v.'" ');
  }
  if ( isset($url) && !empty($url) ) {
    $newds["url"] = $url;
    array_push($triples,' usewod:'.$id.' schema:url <'.$url.'> ');
  }
  if ( isset($img) && !empty($img) ) {
    $newds["img"] = $img;
    array_push($triples,' usewod:'.$id.' schema:image <'.$img.'> ');
  }
  if ( isset($about) && !empty($about) ) {
    $newds["about"] = $about;
    array_push($triples,' usewod:'.$id.' schema:description '.'"'.$about.'" ');
  }

  $insert_q = prefix().'INSERT INTO usewod:'.$gid.' { '. implode(" . ", $triples) .'}';

  $rs = $store->query($insert_q);  
  if ($errs = $store->getErrors()) 
  {
    echo "{ 'error' : 'Error in add_dataset_from_fields', 'returned':".var_dump($errs)." }";
    return;
  }
  add_graph_info($gid, $username);
  $rez = array("added" => $newds);
  echo json_encode($rez);
}

?>