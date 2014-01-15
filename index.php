<?php

include_once("/var/www/html/usewod-prov/helper.php");

if ( isset($_POST['username']) ) 
{
  $username = $_POST['username'];
  setcookie( 'username', $username, time()+60*60*24*5 );
}

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
    <script src="http://code.jquery.com/jquery-1.10.1.js"></script>
    <script src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="usewod-prov.js" type="text/javascript"></script>
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

  $names = get_all_people(); 

?>

        <h2>Workspace (using name: <?php echo $username; ?>)</h2>
        <table width="100%">
 
          <tr>
            <td width="40%">
              <form action="index.php" method="post">
                <div class='button' name='refresh'><input type='image' src="img/view-refresh.png" /></div>
              </form>
              <div class='button' id='addds'><input type='image' src="img/edit-table-insert-row-big-plus.png" /></div>
              <div class='button' id='addpub'><input type='image' src="img/document-new.png" /></div>
<!--               <div class='button' id='addperson'><input type='image' src="img/user-group-new.png" /></div> -->
              <div class='clear-both'></div>
            </td>
            <td>
              <div class='button' id='link'><input type='image' src="img/insert-link.png" /></div>
              <div class='button' id='reset'><input type='image' src="img/edit-clear.png" /></div>
              <div class='clear-both'></div>
            </td>
          </tr>

          <tr>
            <td>
              <h3>Datasets</h3>
<!--               <div class='data' id='refresh'><input type='image' src="img/table-usewod2011-48.png" title="USEWOD 2011 Dataset" /></div>
              <div class='data' id='refresh'><input type='image' src="img/table-usewod2012-48.png" title="USEWOD 2012 Dataset" /></div>
              <div class='data' id='refresh'><input type='image' src="img/table-2013-48.png" title="USEWOD 2013 Dataset" /></div>
              <div class='data' id='refresh'><input type='image' src="img/table-2014-48.png" title="USEWOD 2014 Dataset" /></div> -->
              <div class='clear-both'></div>        
              <h3>Publications</h3>
<!--               <div class='data' id='addperson'><input type='image' src="img/c.png" /></div>
              <div class='data' id='load'><input type='image' src="img/d.png" /></div> -->
              <div class='clear-both'></div>        
<!--               <h3>People</h3>
              <div class='data' id='addpub'><input type='image' src="http://people.cs.kuleuven.be/~bettina.berendt/bettina2.png" title="Bettina Berendt" /></div>
              <div class='data' id='addpub'><input type='image' src="http://www.cs.vu.nl/~laurah/images/me_in_atacama.jpg" title="Laura Hollink" /></div>
              <div class='data' id='addpub'><input type='image' src="https://lh5.googleusercontent.com/-DgE9PJxCjl4/UipM0qKaseI/AAAAAAAAAJE/q9WyOjCme38/w414-h415-no/20130822_095104_resized.jpg" title="Markus Luczak-Roesch" /></div>
              <div class='clear-both'></div> -->
            </td>
            <td rowspan="3">
              <h3>New links</h3>
            </td>
          </tr>

          <tr class="hidden" id="newds">
            <td>
              <p><h3>Adding a new dataset</h3>
                <form action="index.php" method="post">
                <label for="ds-id">Name (dataset id)</label><br/>
                <input type="text" name="ds-id" id="ds-id" /><br/>
                <label for="ds-v">Version (or date)</label></br>
                <input type="text" name="ds-v" id="ds-v" /><br/>
                <label for="ds-url">Location (url)</label><br/>
                <input type="text" name="ds-url" id="ds-url" /><br/>
                <label for="ds-img">Logo (url)</label><br/>
                <input type="text" name="ds-img" id="ds-img" /><br/>
                <label for="ds-size">Size (mb)</label><br/>
                <input type="text" name="ds-size" id="ds-size" /><br/>
                <label for="ds-format">Format</label><br/>
                <select name="ds-format" id="ds-format">
                  <option value="csv">CSV</option>
                  <option value="json-ld">JSON-LD</option>
                  <option value="other">add more.. </option>
                </select><br/>
                <label for="ds-about">Description</label><br/>
                <textarea rows="3" name="ds-about" id="ds-about"></textarea><br/>
                <input type="submit" value="Add dataset"/>
              </form></p>
            </td>
          </tr>
          <tr class="hidden" id="newpub">
            <td>
              <p><h3>Adding a new publication</h3>
                <form action="index.php" method="post">
                <label for="pub-title">Title</label><br/>
                <input type="text" name="pub-title" id="pub-title" /><br/>
                <label for="pub-author">Authors (each on a new line, "firstname lastname")</label></br>
                <textarea rows="3" name="pub-author" id="pub-author" ></textarea><br/>
                <script>
                  var people = <?php echo $names; ?>;
                  $( "#pub-author" ).autocomplete({ minLength: 2,
                      source: function( request, response ) {
                        var terms = request.term.split("\n");
                        var lastTerm = terms[terms.length-1].trim();
                        var matcher = new RegExp( $.ui.autocomplete.escapeRegex( lastTerm ), "i" );
                        response( $.grep( people, function( item ){ return matcher.test( item ); }) );
                      }, 
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
                <label for="pub-year">Year</label><br/>
                <input type="text" name="pub-year" id="pub-year" /><br/>
                <label for="pub-img">Logo (url)</label><br/>
                <input type="text" name="pub-img" id="pub-img" /><br/>
                <label for="pub-venue">Publication venue</label><br/>
                <input type="text" name="pub-venue" id="pub-venue" /><br/>
                <input type="submit" value="Add publication"/>
              </form></p>
            </td>
          </tr>
<!--           <tr class="hidden" id="newperson">
            <td>
            </td>
          </tr> -->
<?php

}

?>

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
