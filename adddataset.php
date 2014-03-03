<?php

include_once("/var/www/html/usewod-prov/helper.php");

if ( isset($_POST['ds-name']) && !empty($_POST['ds-name']) ) { // only the name is mandatory
  add_dataset_from_fields($_POST['ds-name'], $_POST['ds-v'], $_POST['ds-url'], $_POST['ds-img'], $_POST['ds-size'], $_POST['ds-format'], $_POST['ds-about']);
}

function add_dataset_from_fields($name, $v, $url, $img, $about)
{
  global $store;
  global $usewod_url;

  $gid = uniqid("graph/");
  $id = uniqid("dataset/");
  
  $newds = array("id" => $id, "name" => $name);
  $triples = [' usewod:'.$id.' a schema:Dataset ', 
              ' usewod:'.$id.' schema:name '.'"'.$name.'" ' ];
  if ( isset($v) && !empty($v) ) {
    $newdds["version"] = $v;  
    $triples[] = ' usewod:'.$id.' schema:version '.'"'.$v.'" ';
  }
  if ( isset($url) && !empty($url) ) {
    $newds["url"] = $url;
    $triples[] = ' usewod:'.$id.' schema:url <'.$url.'> ';
  }
  if ( isset($img) && !empty($img) ) {
    $newds["img"] = $img;
    $triples[] = ' usewod:'.$id.' schema:image <'.$img.'> ';
  }
  if ( isset($about) && !empty($about) ) {
    $newds["about"] = $about;
    $triples[] = ' usewod:'.$id.' schema:description '.'"'.$about.'" ';
  }

  $insert_q = prefix().'INSERT INTO usewod:'.$gid.' { '. implode(" . ", $triples) .'}';

  $rs = $store->query($insert_q);  
  if ($errs = $store->getErrors()) 
  {
    echo "{ 'error' : 'Error in add_dataset_from_fields', 'returned':".var_dump($errs)." }";
    return;
  }
  add_graph_info($gid);
  $rez = array("added" => $newds);
  echo json_encode($rez);
}

?>