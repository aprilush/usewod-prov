<?php

include_once("helper.php");

if ( isset($_POST['participant']) && !empty($_POST['participant'])) {
  export_user_prov($_POST['participant']);
} else {
  export_all_prov();
}

function export_user_prov($user) {
  $data = array();
  foreach (get_graph_ids_by_user($user) as $gid) {
    $meta = get_graph_meta($gid);
    $content = get_graph_content($gid);
    array_push($data, array("meta"=>$meta, "content"=>$content));
  }
  echo json_encode($data);
}

function export_all_prov() {
  $data = array();
  foreach (get_graph_ids() as $gid) {
    $meta = get_graph_meta($gid);
    $content = get_graph_content($gid);
    array_push($data, array("meta"=>$meta, "content"=>$content));
  }
  echo json_encode($data);
}

function get_graph_ids() {
  global $store;

  $graph_q = prefix().'
    SELECT DISTINCT $gid 
    FROM usewod:graph 
    WHERE {
      ?gid ?p ?o . 
    }
  ';
  $gids = array();
  if ($rows = $store->query($graph_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      array_push($gids,$row['gid']);
    }
  }
  if ($errs = $store->getErrors()) {
    echo "{ 'error' : 'Error in get_graph_ids', 'returned':".var_dump($errs)." }";
    return;
  }
  return $gids;
}

function get_graph_ids_by_user($user) {
  global $store;

  $graph_q = prefix().'
    SELECT DISTINCT $gid 
    FROM usewod:graph 
    WHERE {
      ?gid ?p ?o . ?gid dcterms:creator "'.$user.'" .  
    }
  ';
  $gids = array();
  if ($rows = $store->query($graph_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      array_push($gids,$row['gid']);
    }
  }
  if ($errs = $store->getErrors()) {
    echo "{ 'error' : 'Error in get_graph_ids', 'returned':".var_dump($errs)." }";
    return;
  }
  return $gids;
}

function get_graph_meta($gid) {
  global $store;

  $graph_q = prefix().'
    SELECT $who $when 
    FROM usewod:graph 
    WHERE {
      <'.$gid.'> dcterms:creator ?who . 
      <'.$gid.'> dcterms:created ?when .
    }
  ';
  $meta = array();
  if ($rows = $store->query($graph_q, 'rows')) 
  {
    $meta = array("id" => $gid, "who" => $rows[0]['who'], "when" => $rows[0]['when']);
  }
  if ($errs = $store->getErrors()) {
    echo "{ 'error' : 'Error in get_graph_meta', 'returned':".var_dump($errs)." }";
    return;
  }
  return $meta;
}

function get_graph_content($gid) {
  global $store;

  $entities = get_graph_entities($gid);
  $agents = get_graph_agents($gid);
  $derivs = get_graph_derivations($gid);
  $attribs = get_graph_attributions($gid);
  // $specs = get_graph_specializations($gid);

  $data = array("entities"=>$entities, "agents"=>$agents, "derivs"=>$derivs, "attribs"=>$attribs); // , "specs"=>$specs);
  // $graph_q = prefix().'
  //   SELECT ?s ?p ?o  
  //   FROM <'.$gid.'> 
  //   WHERE {
  //     ?s ?p ?o .
  //   }
  // ';
  // $data = array();
  // if ($rows = $store->query($graph_q, 'rows')) 
  // {
  //   foreach ($rows as $row) 
  //   {
  //     array_push($data, array("s"=>$row['s'], "p"=>$row['p'], "o"=>$row['o']));
  //   }
  // }
  // if ($errs = $store->getErrors()) {
  //   echo "{ 'error' : 'Error in get_graph_content', 'returned':".var_dump($errs)." }";
  //   return;
  // }
  return $data;
}

function get_graph_entities($gid) {
  global $store;

  $data = array();

  $ids_q = prefix().'
    SELECT DISTINCT ?s  
    FROM <'.$gid.'> 
    WHERE {
      ?s a prov:Entity .
    }
  ';

  if ($idrows = $store->query($ids_q, 'rows')) 
  {
    foreach ($idrows as $idrow) 
    {
      $s = $idrow['s'];
      $prop_q = prefix().'
        SELECT ?p ?o  
        FROM <'.$gid.'> 
        WHERE {
          <'.$s.'> ?p ?o .
        }
      ';
      if ($proprows = $store->query($prop_q, 'rows')) 
      {
        $sobj = array();
        foreach ($proprows as $proprow) 
        {
          if ($proprow['p'] == "http://www.w3.org/1999/02/22-rdf-syntax-ns#type") {
            if ($proprow['o'] != "http://www.w3.org/ns/prov#Entity") {
              $sobj["prov:type"] = $proprow['o'];
            }
          } else if ($proprow['p'] == "http://www.w3.org/ns/prov#qualifiedDerivation" ) { 
          } else if ($proprow['p'] == "http://www.w3.org/ns/prov#wasDerivedFrom" ) { 
          } else if ($proprow['p'] == "http://www.w3.org/ns/prov#wasAttributedTo" ) { 
          } else if ($proprow['p'] == "http://www.w3.org/ns/prov#specializationOf" ) { 
          } else if ($proprow['p'] == "http://www.w3.org/ns/prov#wasRevisionOf" ) { 
          } else {
            $sobj[$proprow['p']] = $proprow['o'];
          }
        }
        $data[$s] = $sobj;
      }
    }
  }
  if ($errs = $store->getErrors()) {
    echo "{ 'error' : 'Error in get_graph_content', 'returned':".var_dump($errs)." }";
    return;
  }
  return $data;
}

function get_graph_agents($gid) {
  global $store;

  $data = array();

  $ids_q = prefix().'
    SELECT DISTINCT ?s  
    FROM <'.$gid.'> 
    WHERE {
      ?s a prov:Agent .
    }
  ';

  if ($idrows = $store->query($ids_q, 'rows')) 
  {
    foreach ($idrows as $idrow) 
    {
      $s = $idrow['s'];
      $prop_q = prefix().'
        SELECT ?p ?o  
        FROM <'.$gid.'> 
        WHERE {
          <'.$s.'> ?p ?o .
        }
      ';
      if ($proprows = $store->query($prop_q, 'rows')) 
      {
        $sobj = array();
        foreach ($proprows as $proprow) 
        {
          if ($proprow['p'] == "http://www.w3.org/1999/02/22-rdf-syntax-ns#type") {
            if ($proprow['o'] != "http://www.w3.org/ns/prov#Agent") {
              $sobj["prov:type"] = $proprow['o'];
            }
          } else {
            $sobj[$proprow['p']] = $proprow['o'];
          }
        }
        $data[$s] = $sobj;
      }
    }
  }
  if ($errs = $store->getErrors()) {
    echo "{ 'error' : 'Error in get_graph_content', 'returned':".var_dump($errs)." }";
    return;
  }
  return $data;
}

function get_graph_derivations($gid) {
  global $store;

  $data = array();

  $ids_q = prefix().'
    SELECT DISTINCT ?s  
    FROM <'.$gid.'> 
    WHERE {
      ?s a prov:Derivation . 
    }
  ';

  if ($idrows = $store->query($ids_q, 'rows')) 
  {
    foreach ($idrows as $idrow) 
    {
      $s = $idrow['s'];
      $sobj = array();
      
      $prop_q = prefix().'
        SELECT ?p ?o  
        FROM <'.$gid.'> 
        WHERE {
          <'.$s.'> ?p ?o .
        } 
      ';
      if ($proprows = $store->query($prop_q, 'rows')) 
      {
        foreach ($proprows as $proprow) 
        {
          if ($proprow['p'] == "http://www.w3.org/1999/02/22-rdf-syntax-ns#type") {
            if ($proprow['o'] != "http://www.w3.org/ns/prov#Derivation") {
              $sobj["prov:type"] = $proprow['o'];
            }
          } else if ($proprow['p'] == "http://www.w3.org/ns/prov#entity") {
            $sobj["prov:usedEntity"] = $proprow['o'];
          } else {
            $sobj[$proprow['p']] = $proprow['o'];
          }
        }
      }
      $revprop_q = prefix().'
        SELECT ?r 
        FROM <'.$gid.'> 
        WHERE {
          ?r prov:qualifiedDerivation <'.$s.'> .
        } 
      ';
      if ($revproprows = $store->query($revprop_q, 'rows')) 
      {
        foreach ($revproprows as $revproprow) 
        {
          $sobj["prov:generatedEntity"] = $revproprow['r'];
        }
      }
      $data[$s] = $sobj;
    }
  }
  if ($errs = $store->getErrors()) {
    echo "{ 'error' : 'Error in get_graph_content', 'returned':".var_dump($errs)." }";
    return;
  }
  return $data;
}

function get_graph_attributions($gid) {
  global $store;

  $data = array();

  $rels_q = prefix().'
    SELECT DISTINCT ?s ?o  
    FROM <'.$gid.'> 
    WHERE {
      ?s prov:wasAttributedTo ?o .
    }
  ';

  $count = 1;
  if ($rows = $store->query($rels_q, 'rows')) 
  {
    foreach ($rows as $row) 
    {
      $s = $row['s'];
      $o = $row['o'];
      $aobj = array();
      $aobj["prov:entity"] = $s;
      $aobj["prov:agent"] = $o;
      $data["_:attrib_".$count] = $aobj;
      $count = $count + 1;
    }
  }
  if ($errs = $store->getErrors()) {
    echo "{ 'error' : 'Error in get_graph_content', 'returned':".var_dump($errs)." }";
    return;
  }
  return $data;
}

// function get_graph_specializations($gid) {
//   return array();
// }


?>
