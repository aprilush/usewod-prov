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

  function copy_data(el) {
      var cl = el.clone().removeClass("selected");
      cl.attr("id", el.attr("id"));
      return cl;
  };

  $(document).on("click", function(event) {
    var div = $(event.target).closest("div");
    if (div.hasClass("button")) {
      if (div.attr("id")=="addds") {
        $("tr[id='newds']").removeClass("hidden");
      } else if (div.attr("id")=="addpub") {
        $("tr[id='newpub']").removeClass("hidden");
      } else {
        console.log("click clack on a button");
      }
    } else if (div.hasClass("data")) {
      div.toggleClass("selected");
      if (div.hasClass("selected")) {
        // add to the right of the workspace
        var copy = copy_data(div);
        copy.removeClass("data");
        copy.addClass("ws");
        $("#workspace").append(copy);
      } else {
        var id = div.attr("id");
        console.log(id);
        $("#workspace > div[id='"+id+"']").remove();
      }
    } else if (div.hasClass("ws")) {
      // this happens in the workspace
    } else {
      console.log("click clack");
    }
  });

}) 
