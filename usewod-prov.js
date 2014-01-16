google.load('search', '1');

var imgSearcher;
var set = false;

function setSearcher() {
  imgSearcher = new google.search.ImageSearch();
  imgSearcher.setRestriction(google.search.ImageSearch.RESTRICT_FILETYPE, google.search.ImageSearch.FILETYPE_PNG);
  imgSearcher.setRestriction(google.search.ImageSearch.RESTRICT_IMAGESIZE, google.search.ImageSearch.IMAGESIZE_MEDIUM);
  imgSearcher.setRestriction(google.search.Search.RESTRICT_SAFESEARCH, google.search.Search.SAFESEARCH_ON);
  imgSearcher.setNoHtmlGeneration();
  imgSearcher.setResultSetSize(1);
  set = true;
}

function imgSearchCompleteForPaper() {
  if (imgSearcher.results && imgSearcher.results.length > 0) {
    console.log("found image with url: ", imgSearcher.results[0].url);
    $("#pub-img-div").empty();
    $("#pub-img-div").prepend('<img src="'+imgSearcher.results[0].url+'" /><input type="hidden" name="pub-img" value="'+imgSearcher.results[0].url+'" />');
  } else {
    console.log("no image found, searching again");
    imgSearchForPaper();
  }
}

function imgSearchForPaper() {
  if (!set) { setSearcher(); }
  var title = $("#pub-title").val();
  imgSearcher.setSearchCompleteCallback(this, imgSearchCompleteForPaper, null);
  imgSearcher.execute(title);
}

function imgSearchCompleteForDataset() {
  if (imgSearcher.results && imgSearcher.results.length > 0) {
    console.log("found image with url: ", imgSearcher.results[0].url);
    $("#ds-img-div").empty();
    $("#ds-img-div").prepend('<img src="'+imgSearcher.results[0].url+'" /><input type="hidden" name="ds-img" value="'+imgSearcher.results[0].url+'" />');
  } else {
    console.log("no image found, searching again");
    imgSearchForDataset();
  }
}

function imgSearchForDataset() {
  if (!set) { setSearcher(); }
  var title = $("#ds-name").val();
  imgSearcher.setSearchCompleteCallback(this, imgSearchCompleteForDataset, null);
  imgSearcher.execute(title);
}

$(function() {

  $(document).on("click", function(event) {
    var div = $(event.target).closest("div");
    if (div.hasClass("button")) {
        if (div.attr("id")=="addds") {
          $("tr[id='newds']").removeClass("hidden");
        } else if (div.attr("id")=="addpub") {
          $("tr[id='newpub']").removeClass("hidden");
        // } else if (div.attr("id")=="addperson") {
        //   $("tr[id='newperson']").removeClass("hidden");
        } else {
          console.log("click clack on a button");
        }
    } else {
      console.log("click clack");
    }
  });

}) 
