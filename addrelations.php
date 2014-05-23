<?php

include_once("helper.php");

if ( isset($_POST['user']) && !empty($_POST['user']) ) {
  $username = $_POST['user'];
  if ( isset($_POST['source']) && isset($_POST['relations']) ) { 
    
    $triples = make_triples($_POST['source'], $_POST['relations']);
    add_relations($username, $triples);
  }
}

// $triples[] = ' usewod:'.$derid.' prov:atTime "'.time()->format(DateTime::ISO8601).'"^^xsd:dateTime ';
function make_triples($source, $relations) {
  $triples = array();
  $s = "";

  if (array_key_exists('id', $source) ) {
    $s = $source['id'];
  }

  if ( array_key_exists('type', $source) ) {
    if ( $source['type']=='p' ) { 
      if ( array_key_exists('pub', $relations) ) {
        foreach ( $relations['pub'] as $rel ) {
          if ( !empty($rel['objects']) ) {
            $derid = uniqid("prov/");
            array_push($triples,' usewod:'.$derid.' a  prov:Derivation, usewod:Citation ');
            foreach ($rel['objects'] as $object) {
              if (array_key_exists('id', $object) ) {
                $o = $object['id'];
                array_push($triples,' usewod:'.$derid.' prov:entity <'.$o.'> ');
              }
            }
            array_push($triples,' <'.$s.'> prov:qualifiedDerivation usewod:'.$derid.' ');
          }
        }
      }
      if ( array_key_exists('ds', $relations) ) {
        foreach ( $relations['ds'] as $rel ) {
          if ( !empty($rel['objects']) ) {
            $derid = uniqid("prov/");
            array_push($triples,' usewod:'.$derid.' a  prov:Derivation ');
            switch ($rel['label']) {
              case 'mentions':
                array_push($triples,' usewod:'.$derid.' a  usewod:Mention ');
                break;
              case 'describes':
                array_push($triples,' usewod:'.$derid.' a  usewod:Description ');
                break;
              case 'evaluates':
                array_push($triples,' usewod:'.$derid.' a  usewod:Evaluation ');
                break;
              case 'analyses':
                array_push($triples,' usewod:'.$derid.' a  usewod:Analysis ');
                break;
              case 'compares':
                array_push($triples,' usewod:'.$derid.' a  usewod:Comparison ');
                break;
            }
            foreach ($rel['objects'] as $object) {
              if (array_key_exists('id', $object) ) {
                $o = $object['id'];
                array_push($triples,' usewod:'.$derid.' prov:entity <'.$o.'> ');
              }
            }
            array_push($triples,' <'.$s.'> prov:qualifiedDerivation usewod:'.$derid.' ');
          }
        }
      }
    } else if ($source['type']=='d') {
      if (array_key_exists('pub', $relations) ) {
        foreach ( $relations['pub'] as $rel ) {
          if ( !empty($rel['objects']) ) {
            foreach ($rel['objects'] as $object) {
              if (array_key_exists('id', $object) ) {
                $o = $object['id'];
                $derid = uniqid("prov/");
                array_push($triples,' usewod:'.$derid.' a  prov:Derivation ');
                switch ($rel['label']) {
                  case 'mentioned in':
                    array_push($triples,' usewod:'.$derid.' a  usewod:Mention ');
                    break;
                  case 'described in':
                    array_push($triples,' usewod:'.$derid.' a  usewod:Description ');
                    break;
                  case 'evaluated in':
                    array_push($triples,' usewod:'.$derid.' a  usewod:Evaluation ');
                    break;
                  case 'analysed in':
                    array_push($triples,' usewod:'.$derid.' a  usewod:Analysis ');
                    break;
                  case 'compared in':
                    array_push($triples,' usewod:'.$derid.' a  usewod:Comparison ');
                    break;
                }
                array_push($triples,' usewod:'.$derid.' prov:entity <'.$s.'> ');
                array_push($triples,' <'.$o.'> prov:qualifiedDerivation usewod:'.$derid.' ');
              }
            }
          }
        }
      }
      if (array_key_exists('ds', $relations) ) {
        foreach ( $relations['ds'] as $rel ) {
          if ( !empty($rel['objects']) ) {
            foreach ($rel['objects'] as $object) {
              if (array_key_exists('id', $object) ) {
                $o = $object['id'];
                $derid = uniqid("prov/");
                array_push($triples,' usewod:'.$derid.' a  prov:Derivation ');
                switch ($rel['label']) {
                  case 'extends':
                    array_push($triples,' usewod:'.$derid.' a  usewod:Extension ');
                    break;
                  case 'includes':
                    array_push($triples,' usewod:'.$derid.' a  usewod:Inclusion ');
                    break;
                  case 'overlaps':
                    array_push($triples,' usewod:'.$derid.' a  usewod:Overlap ');
                    break;
                  case 'transformation of':
                    array_push($triples,' usewod:'.$derid.' a  usewod:Transformation ');
                    break;
                  case 'generalisation of':
                    array_push($triples,' usewod:'.$derid.' a  usewod:Generalisation ');
                    break;
                }
                array_push($triples,' usewod:'.$derid.' prov:entity <'.$s.'> ');
                array_push($triples,' <'.$o.'> prov:qualifiedDerivation usewod:'.$derid.' ');
              }
            }
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