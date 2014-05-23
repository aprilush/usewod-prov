<?php

include_once("/var/www/html/usewod-prov/helper.php");

if ( isset($_POST['username']) ) 
{
  $username = $_POST['username'];
  setcookie( 'username', $username, time()+60*60*24*5 );
}

if ( isset($_POST['obj-left']) && !empty($_POST['obj-left']) &&
    isset($_POST['obj-right']) && !empty($_POST['obj-right']) &&
    isset($_POST['link']) && !empty($_POST['link']) )
{
  add_relation($_POST['obj-left'], $_POST['link'], $_POST['obj-right']);
}

$ds_ids = array();
$datasets = array();
$pub_ids = array();
$publications = array();

if ( isset($_POST['ds-name']) && !empty($_POST['ds-name']) && // only the name is mandatory
    isset($_POST['ds-v']) && 
    isset($_POST['ds-url']) && 
    isset($_POST['ds-img']) && 
    isset($_POST['ds-about']) ) {
  add_dataset_from_fields($_POST['ds-name'], $_POST['ds-v'], $_POST['ds-url'], $_POST['ds-img'], $_POST['ds-size'], $_POST['ds-format'], $_POST['ds-about']);
}

if ( isset($_POST['pub-title']) && !empty($_POST['pub-title']) && // title and author is mandatory
    isset($_POST['pub-author']) && !empty($_POST['pub-author']) &&
    isset($_POST['pub-venue']) && 
    isset($_POST['pub-year']) && 
    isset($_POST['pub-url']) && 
    isset($_POST['pub-img']) ) {
  add_paper_from_fields($_POST['pub-title'], $_POST['pub-author'], $_POST['pub-venue'], $_POST['pub-year'], $_POST['pub-url'], $_POST['pub-img']);
}

if (( isset($_POST['ds_ids']) && !empty($_POST['ds_ids']) ) || ( isset($_POST['pub_ids']) && !empty($_POST['pub_ids']) ) ) {
  if ( isset($_POST['ds_ids']) && !empty($_POST['ds_ids']) ) {
    $ds_ids = $_POST['ds_ids'];
  }
  if ( isset($_POST['pub_ids']) && !empty($_POST['pub_ids']) ) {
    $pub_ids = $_POST['pub_ids'];
  }
} else {
  load_new_data();
}

load_datasets();
load_publications();

?>

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
  xmlns:foaf="http://xmlns.com/foaf/0.1/"
  xmlns:swc="http://data.semanticweb.org/ns/swc/ontology#"
  xmlns:swrc="http://swrc.ontoware.org/ontology#"
  xmlns:bibo="http://purl.org/ontology/bibo/"
  xmlns:ical="http://www.w3.org/2002/12/cal/icaltzid#"
  xmlns:dct="http://purl.org/dc/terms/"
  xmlns:sioc="http://rdfs.org/sioc/ns#"
  xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:xsd="http://www.w3.org/2001/XMLSchema#"
  xmlns:v="http://rdf.data-vocabulary.org/#"
  xml:lang="en">
  <head>
    <title property="rdfs:label">USEWOD2014 - 4th International Workshop on Usage Analysis and the Web of Data</title>
    <link rel="stylesheet" href="usewodStyle.css"/>
    <link rel="stylesheet" href="usewod2014.css"/>
    <link rel="dct:creator" href="http://aprilush.ro/card#laura" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
  </head>
    <body typeof="sioc:Site" about="" ng-app="usewod-prov">
    <div id="container">
      <span rel="foaf:primaryTopic" resource="http://data.semanticweb.org/workshop/usewod/2014" />
      <div id="content">
        <div typeof="swc:WorkshopEvent v:Event" about="http://data.semanticweb.org/workshop/usewod/2014">
          <h1 style="text-align: center">
            <span property="swc:hasAcronym v:summary">USEWOD2014</span> - 
            <span property="rdfs:label v:description">4th International Workshop on Usage Analysis and the Web of Data</span>
          </h1>
          <p style="text-align: center">
            <span rel="swc:isSubEventOf" href="http://data.semanticweb.org/conference/eswc/2014">
              co-located with the <i property="rdfs:label">11th Extended Semantic Web Conference</i> 
              (<a 
              about="http://data.semanticweb.org/conference/eswc/2014" 
              typeof="swc:ConferenceEvent"
              rel="ical:url" 
              href="http://2014.eswc-conferences.org" 
              property="swc:hasAcronym">ESWC2014</a>)
            </span>
          </p>
          <p style="text-align: center">Anissaras, Crete, Greece, 
            <span 
            property="ical:dtstart v:startDate" 
            content="2014-05-25T09:00:00+01:00"
            datatype="xsd:dateTime">May 25th, 2014</span>
          </p>
          <p style="text-align: center"><a href="http://people.cs.kuleuven.be/~bettina.berendt/USEWOD2014/">USEWOD2014</a> is part of the <a href="http://data.semanticweb.org/usewod/" title="USEWOD">USEWOD workshop series</a>.</p>
          <span rel="ical:url" href="http://data.semanticweb.org/usewod/2014"/>
        </div>

        <h2><a id="overview"></a>Session overview and goals</h2>
        <p>Paragraph from the workshop proposal, or some text from Markus.</p>
        <p>The special theme of the workshop: building a USEWOD Web Observatory to track the distribution of research based on the USEWOD data set.</p>

<?php
if ( !isset($username) && (!isset($_COOKIE['username']) || $_COOKIE['username']==='' ) )   
{
?>
          <tr>
            <td>
              <h2>Please choose a username</h2>
              <form action="index.php" method="post">
                <p><input type="text" name="username" /><input type="submit" /></p>
              </form>
            </td>
          </tr>

<?php
}
else {
  if (!isset($username))
  {
    $username = $_COOKIE['username'];
  }

  $names = get_all_people_names(); 
?>
        <h2>Workspace (using name: <?php echo $username; ?>)</h2>
        <form action="index.php" method="post">
        <table width="100%">
          <tr>
            <td width="50%">
              <div class='button' id='addpub'><img src="img/document-new.png" /></div><h3>Publications</h3>
              <div class='spacer'>
<?php
                $len = count($publications);
                for ($i=0; $i < $len; $i++) 
                { 
                  $pub = $publications[$i];
                  echo '<div class="pub data" id="'.$pub["id"].'"><img src="'.$pub["img"].'" title="'.$pub["title"].'" /></div>' ;
                }
?>
              </div>
              <div class='clear-both'></div>
              <div class="hidden" id="newpub">
                <!-- <form action="index.php" method="post"> -->
                  <div class="button" id=""><input type="image" src="img/list-add.png"/></div>
                  <div id="pub-img-div"></div>
                  <h3>Add a new publication</h3>
                  <div class='spacer'>
                  <label for="pub-title">Title</label><br/>
                  <input type="text" name="pub-title" id="pub-title" onblur="imgSearchForPaper()" /><br/>
                  <label for="pub-author">Authors (each on a new line, "firstname lastname")</label></br>
                  <textarea rows="3" name="pub-author" id="pub-author" ></textarea><br/>
                  <script>
                    var people = <?php echo $names; ?>;
                    $( "#pub-author" ).autocomplete({ minLength: 2,
                        source: scope[attrs.uiItems], 
                        //   function( request, response ) {
                        //   var terms = request.term.split("\n");
                        //   var lastTerm = terms[terms.length-1].trim();
                        //   var matcher = new RegExp( $.ui.autocomplete.escapeRegex( lastTerm ), "i" );
                        //   response( $.grep( people, function( item ){ return matcher.test( item ); }) );
                        // }, 
                        select: function( event, ui ) {
                          event.isDefaultPrevented = function() {return true;}
                          var txt = $("#pub-author").val();
                          var terms = txt.split("\n");
                          terms.pop();
                          terms.push(ui.item.value);
                          $("#pub-author").val(terms.join('\n'));
                        },
                        focus: function( event, ui ) {
                          event.isDefaultPrevented = function() {return true;}
                        }
                      });
                  </script>
                  <label for="pub-venue">Publication venue</label><br/>
                  <input type="text" name="pub-venue" id="pub-venue" /><br/>
                  <label for="pub-year">Year</label><br/>
                  <input type="text" name="pub-year" id="pub-year" /><br/>
                  <label for="pub-url">Link (url)</label><br/>
                  <input type="text" name="pub-url" id="pub-url" /><br/>
                  <!-- <input type="submit" value="Add publication"/> -->
                <!-- </form> -->
              </div>
            </td>
            <td width="50%">
              <div class='button' id='addds'><img src="img/edit-table-insert-row-big-plus.png" /></div><h3>Datasets</h3>
              <div class='spacer'>
<?php
                $len = count($datasets);
                for ($i=0; $i < $len; $i++) 
                { 
                  $ds = $datasets[$i];
                  echo '<div class="ds data" id="'.$ds["id"].'"><img src="'.$ds["img"].'" title="'.$ds["name"].'" /></div>' ;
                }
?>
              </div>
              <div class='clear-both'></div>
              <div class="hidden" id="newds">
                <!-- <form action="index.php" method="post"> -->
                  <div class="button" id=""><input type="image" src="img/list-add.png"/></div>
                  <div id="ds-img-div"></div>
                  <h3>Add a new dataset</h3>
                  <label for="ds-name">Name</label><br/>
                  <input type="text" name="ds-name" id="ds-name" onblur="imgSearchForDataset()" /><br/>
                  <label for="ds-v">Version (or date)</label></br>
                  <input type="text" name="ds-v" id="ds-v" /><br/>
                  <label for="ds-url">Link (url)</label><br/>
                  <input type="text" name="ds-url" id="ds-url" /><br/>
                  <label for="ds-about">Description</label><br/>
                  <textarea rows="3" name="ds-about" id="ds-about"></textarea><br/>
                  <!-- <input type="submit" value="Add dataset"/> -->
                <!-- </form> -->
              </div>
            </td>
          <tr>
          <tr>
            <td colspan="2">
            <div class='button' id='reset'><img src="img/edit-clear.png" /></div>
            <div class='button' id='link'><input type="image" src="img/insert-link.png" disabled="true"/></div>
            <h3>Connections</h3>
            <div class="workspace">
              <table>
                <tr>
                  <td id="source-obj" class="source"></td>
                  <td id="relations"></td>
                  <td id="target-obj"></td>
                </tr>
              </table>
            </div>
            </td>
          </tr>

<!--          <tr>
            <td>
              <h3>New data</h3>
              <form action="index.php" method="post">
                <div class='button' name='refresh'><input type='image' src="img/view-refresh.png" /></div>
              </form>
              <div class='button' id='addds'><input type='image' src="img/edit-table-insert-row-big-plus.png" /></div>
              <div class='button' id='addpub'><input type='image' src="img/document-new.png" /></div>
              <div class='clear-both'></div>
            </td>
            <td></td>
          </tr> -->

<?php
}
?>

        </table></p>
        </form>
      </div> <!-- content -->

      <div id="footer">
				<div style="text-align: center">
					For questions and comments e-mail 
					<a href="mailto:usewod2013-chairs@googlegroups.com">USEWOD Chairs</a><br/>
					<p about="" resource="http://www.w3.org/TR/rdfa-syntax" rel="dct:conformsTo">
					  <a href="http://validator.w3.org/check?uri=referer">XHTML</a>
					  <a href="http://www.w3.org/2007/08/pyRdfa/extract?uri=referer">with RDFa</a>
          </p>
          <br/>
			  </div>
      </div> <!-- footer -->
	  </div> <!-- container -->
    <script src="http://code.jquery.com/jquery-1.10.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.min.js"></script>
    <script src="https://www.google.com/jsapi"></script>
    <script src="usewod-prov.js" type="text/javascript"></script>
	</body>
</html>
