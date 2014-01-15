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
	</head>
		<body typeof="sioc:Site" about="">
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

        <h2><a id="workspace"></a>Workspace</h2>
        <table width="100%">

<?php

include_once("/var/www/html/usewod-prov/arc2/ARC2.php");

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

if (isset($_POST['username'])) 
{
  $username = $_POST['username'];
}
else
{

?>
          <tr>
            <td>
              <h3>Please choose a username (session id: <?php $session_id ?> )</h3>
              <form action="index.php" method="post">
                <input type="text" name="username" /><input type="submit" />
              </form>
            </td>
          </tr>
<?php

}

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

function add_person_from_foaf($foaf_url)
{

}

function add_person_from_fields($fn, $ln, $img_url, $foaf_url)
{

}

function add_dataset_from_fields($dsname, $year, $logo)
{

}

?>
 
<!--          <tr>
            <td width="40%">
              <div class='button' id='refresh'><input type='image' src="img/view-refresh.png" /></div>
              <div class='button' id='addds'><input type='image' src="img/edit-table-insert-row-big-plus.png" /></div>
              <div class='button' id='addpub'><input type='image' src="img/document-new.png" /></div>
              <div class='button' id='addperson'><input type='image' src="img/user-group-new.png" /></div>
              <div class='clear-both'></div>
            </td>
            <td>
              <div class='button' id='connect'><input type='image' src="img/insert-link.png" /></div>
              <div class='button' id='reset'><input type='image' src="img/edit-clear.png" /></div>
              <div class='clear-both'></div>
            </td>
          </tr>
          <tr>
            <td>
              <h3>Datasets</h3>
              <div class='data' id='refresh'><input type='image' src="img/table-usewod2011-48.png" title="USEWOD 2011 Dataset" /></div>
              <div class='data' id='refresh'><input type='image' src="img/table-usewod2012-48.png" title="USEWOD 2012 Dataset" /></div>
              <div class='data' id='refresh'><input type='image' src="img/table-2013-48.png" title="USEWOD 2013 Dataset" /></div>
              <div class='data' id='refresh'><input type='image' src="img/table-2014-48.png" title="USEWOD 2014 Dataset" /></div>
              <div class='clear-both'></div>        
              <h3>Publications</h3>
              <div class='data' id='addperson'><input type='image' src="img/c.png" /></div>
              <div class='data' id='load'><input type='image' src="img/d.png" /></div>
              <div class='clear-both'></div>        
              <h3>People</h3>
              <div class='data' id='addpub'><input type='image' src="http://people.cs.kuleuven.be/~bettina.berendt/bettina2.png" title="Bettina Berendt" /></div>
              <div class='data' id='addpub'><input type='image' src="http://www.cs.vu.nl/~laurah/images/me_in_atacama.jpg" title="Laura Hollink" /></div>
              <div class='data' id='addpub'><input type='image' src="https://lh5.googleusercontent.com/-DgE9PJxCjl4/UipM0qKaseI/AAAAAAAAAJE/q9WyOjCme38/w414-h415-no/20130822_095104_resized.jpg" title="Markus Luczak-Roesch" /></div>
              <div class='clear-both'></div>
            </td>
            <td>
              <h3>New links</h3>
            </td>
          </tr>
 -->

        </table></p>
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
	</body>
</html>
