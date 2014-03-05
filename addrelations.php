<?php

include_once("/var/www/html/usewod-prov/helper.php");

if ( isset($_POST['user']) && !empty($_POST['user']) ) {
  $username = $_POST['user'];
  if ( isset($_POST['source']) && isset($_POST['relations']) ) { 
    
    $triples = make_triples($_POST['source'], $_POST['relations']);
    add_relations($username, $triples);
  }
}

// $triples[] = ' usewod:'.$derid.' prov:atTime "'.time()->format(DateTime::ISO8601).'"^^xsd:dateTime ';
function make_triples($source, $relations) {
  $triples = [];
  $s = "";

  if (array_key_exists('id', $source) ) {
    $s = $source['id'];
  }

  if ( array_key_exists('type', $source) ) {
    if ( $source['type']='p' ) { 
      if ( array_key_exists('pub', $relations) ) {
        foreach ( $relations['pub'] as $rel ) {
          if ( !empty($rel['objects']) ) {
            $derid = uniqid("prov/");
            $triples[] = ' usewod:'.$derid.' a  prov:Derivation, usewod:Citation ';
            foreach ($rel['objects'] as $object) {
              if (array_key_exists('id', $object) ) {
                $o = $object['id'];
                $triples[] = ' usewod:'.$derid.' prov:entity <'.$o.'> ';
              }
            }
            $triples[] = ' <'.$s.'> prov:qualifiedDerivation usewod:'.$derid.' ';
          }
        }
      }
      if ( array_key_exists('ds', $relations) ) {
        foreach ( $relations['ds'] as $rel ) {
          if ( !empty($rel['objects']) ) {
            $derid = uniqid("prov/");
            $triples[] = ' usewod:'.$derid.' a  prov:Derivation ';
            switch ($rel['label']) {
              case 'mentions':
                $triples[] = ' usewod:'.$derid.' a  usewod:Mention ';
                break;
              case 'describes':
                $triples[] = ' usewod:'.$derid.' a  usewod:Description ';
                break;
              case 'evaluates':
                $triples[] = ' usewod:'.$derid.' a  usewod:Evaluation ';
                break;
              case 'analyses':
                $triples[] = ' usewod:'.$derid.' a  usewod:Analysis ';
                break;
              case 'compares':
                $triples[] = ' usewod:'.$derid.' a  usewod:Comparison ';
                break;
            }
            foreach ($rel['objects'] as $object) {
              if (array_key_exists('id', $object) ) {
                $o = $object['id'];
                $triples[] = ' usewod:'.$derid.' prov:entity <'.$o.'> ';
              }
            }
            $triples[] = ' <'.$s.'> prov:qualifiedDerivation usewod:'.$derid.' ';
          }
        }
      }
    } else if ($source['type']='d') {
      if (array_key_exists('pub', $relations) ) {
        foreach ( $relations['pub'] as $rel ) {
          if ( !empty($rel['objects']) ) {
            foreach ($rel['objects'] as $object) {
              if (array_key_exists('id', $object) ) {
                $o = $object['id'];
                $derid = uniqid("prov/");
                $triples[] = ' usewod:'.$derid.' a  prov:Derivation ';
                switch ($rel['label']) {
                  case 'is mentioned in':
                    $triples[] = ' usewod:'.$derid.' a  usewod:Mention ';
                    break;
                  case 'is described in':
                    $triples[] = ' usewod:'.$derid.' a  usewod:Description ';
                    break;
                  case 'is evaluated in':
                    $triples[] = ' usewod:'.$derid.' a  usewod:Evaluation ';
                    break;
                  case 'is analysed in':
                    $triples[] = ' usewod:'.$derid.' a  usewod:Analysis ';
                    break;
                  case 'is compared in':
                    $triples[] = ' usewod:'.$derid.' a  usewod:Comparison ';
                    break;
                }
                $triples[] = ' usewod:'.$derid.' prov:entity <'.$s.'> ';
                $triples[] = ' <'.$o.'> prov:qualifiedDerivation usewod:'.$derid.' ';
              }
            }
          }
        }
      }
      if (array_key_exists('ds', $relations) ) {
        foreach ( $relations['ds'] as $rel ) {
          if ( !empty($rel['objects']) ) {
            // to see what exact types of relations we support here 
          }
        }
      }
    } 
  } 
  return $triples;
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
    return;
  }

  add_graph_info($gid, $username);
  $rez = array("query" => $insert_q, "result" => $rs, "graph" => "usewod:".$gid);
  echo json_encode($rez);
}

?>