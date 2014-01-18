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

  $.obj1orig = null;
  $.obj1copy = null;
  $.obj2orig = null;
  $.obj2copy = null;

  // how do we make this into proper provenance?
  $.allLinksPubPub = ["cites", "contradicts", "extends"];
  $.allLinksPubDs = ["uses", "describes", "evaluates", "analyses"];
  $.allLinksDsPub = ["contains"];
  $.allLinksDsDs = ["extends", "includes", "overlaps", "isTransformation"];

  function copy_data(el) {
      var cl = el.clone().removeClass("selected").removeClass("data").addClass("ws");
      cl.attr("id", el.attr("id"));
      return cl;
  };

  function create_link_selector() {
    var ls;
    if ($.obj1copy.hasClass("pub") && $.obj2copy.hasClass("pub")) {
      ls = $.allLinksPubPub;
    } else if ($.obj1copy.hasClass("pub") && $.obj2copy.hasClass("ds")) {
      ls = $.allLinksPubDs;
    } else if ($.obj1copy.hasClass("ds") && $.obj2copy.hasClass("pub")) {
      ls = $.allLinksDsPub;
    } else if ($.obj1copy.hasClass("ds") && $.obj2copy.hasClass("ds")) {
      ls = $.allLinksDsDs;
    }
    var sel = "<select name='link'>";
    for (l in ls) {
      sel = sel+ "<option value='"+ls[l]+"'>"+ls[l]+"</option>";
    }
    sel = sel+"</select>";
    return sel;
  }

  function create_temp_rel(el1, el2, rel) {
    console.log(el1);
    console.log(el2);
    console.log(rel);
    
    $("#temp-rels").append(copy_data(el1));
    $("#temp-rels").append("<div class='ws'>"+rel+"</div>");
    $("#temp-rels").append(copy_data(el2));
    $("#temp-rels").append("<div class='clear-both'></div>");
  }

  $(document).on("click", function(event) {
    var div = $(event.target).closest("div");
    if (div.hasClass("button")) {
      if (div.attr("id")=="addds") {
        $("tr[id='newds']").removeClass("hidden");
      } else if (div.attr("id")=="addpub") {
        $("tr[id='newpub']").removeClass("hidden");
      } else if (div.attr("id")=="link") {
        temp_rel_row = create_temp_rel($.obj1copy, $.obj2copy, $("#link-selector > select").val());
      } else {
        console.log("click clack on a button");
      }
    } else if (div.hasClass("data")) {
      if (!$.obj1copy) {
        div.addClass("selected");
        $.obj1copy = copy_data(div);
        $.obj1orig = div;
      } else if (!$.obj2copy) {
        div.addClass("selected");
        $.obj2copy = copy_data(div);
        $.obj2orig = div;
      } else {
        // both objects are filled, shift obj2 to obj1
        div.addClass("selected");
        $.obj1orig.removeClass("selected");
        $.obj1copy = $.obj2copy;
        $.obj1orig = $.obj2orig;
        div.addClass("selected");
        $.obj2copy = copy_data(div);
        $.obj2orig = div;
      }
      if ($.obj1copy && $.obj2copy) {

        $("#obj-left").empty().append($.obj1copy);
        $("#obj-right").empty().append($.obj2copy);
        // also find the possible relations and show them here
        $("#link-selector").html(create_link_selector());
      }
    } else if (div.hasClass("ws")) {
      // this happens in the workspace
    } else {
      console.log("click clack");
    }
  });

}) 
