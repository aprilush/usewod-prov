<?php

include_once("helper.php");

$who = "aprilush@gmail.com";
$files = array("datasets.ttl","papers.ttl");

foreach ( $files as $f ) {
  $triples_f = load_triples($f);
  echo "<br><br>read ".count($triples_f)." triples from ".$f."<br>" ;
  // echo var_dump($triples_f);
  echo add_relations($who, $triples_f);
  // echo "";
}

function load_triples($f) {
  $triples_formatted = array();
  $parser = ARC2::getTurtleParser();
  $parser->parse($f);
  $triples = $parser->getTriples();
  foreach ($triples as $triple) {
    $o_type = $triple["o_type"];
    if ($o_type == "uri") {
      $triple_formatted = ' <'.$triple["s"].'> <'.$triple["p"].'> <'.$triple["o"].'> ';
      array_push($triples_formatted,$triple_formatted);
    } else if ($o_type == "literal") {
      $triple_formatted = ' <'.$triple["s"].'> <'.$triple["p"].'> "'.$triple["o"].'" ';
      array_push($triples_formatted,$triple_formatted);
    }
  }
  return $triples_formatted;
}

function add_relations($username, $triples)
{
  global $store;

  $gid = uniqid("graph/");

  $insert_q = prefix().'INSERT INTO usewod:'.$gid.' { '. implode(" . ", $triples) .'}';

  $rs = $store->query($insert_q);  
  if ($errs = $store->getErrors()) 
  {
    echo "{ 'error' : 'Error in add_relations', 'returned':".var_dump($errs)." }";
    echo $insert_q;
    return;
  }

  add_graph_info($gid, $username);
  $rez = array("result" => $rs, "graph" => "usewod:".$gid);
  return json_encode($rez);
}

?>