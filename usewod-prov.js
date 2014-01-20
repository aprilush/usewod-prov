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
    $("#pub-img-div").prepend('<div class="button"><img src="'+imgSearcher.results[0].url+'" /><input type="hidden" name="pub-img" value="'+imgSearcher.results[0].url+'" /></div>');
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
    $("#ds-img-div").prepend('<div class="button"><img src="'+imgSearcher.results[0].url+'" /><input type="hidden" name="ds-img" value="'+imgSearcher.results[0].url+'" /></div>');
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

  // function create_temp_rel(el1, el2, rel) {
  //   console.log(el1);
  //   console.log(el2);
  //   console.log(rel);
    
  //   $("#temp-rels").append(copy_data(el1));
  //   $("#temp-rels").append("<div class='ws'>"+rel+"</div>");
  //   $("#temp-rels").append(copy_data(el2));
  //   $("#temp-rels").append("<div class='clear-both'></div>");
  // }

  $(document).on("click", function(event) {
    var div = $(event.target).closest("div");
    if (div.hasClass("button")) {
      if (div.attr("id")=="addds") {
        $("#newds").toggleClass("hidden");
      } else if (div.attr("id")=="addpub") {
        $("#newpub").toggleClass("hidden");
      } else if (div.attr("id")=="reset") {
        $("#obj-left").empty();
        $("#link-selector").empty();
        $("#obj-right").empty();
        $.obj1orig.removeClass("selected");
        $.obj2orig.removeClass("selected");
        $.obj1copy = null;
        $.obj2copy = null;
        $("#link > input").attr("disabled",true);
      } else {
        console.log("click clack on a button");
      }
    } else if (div.hasClass("data")) {
      if (!$.obj1copy) {
        div.addClass("selected");
        $.obj1orig = div;
        $.obj1copy = copy_data(div);
        $.obj1copy.append("<input type='hidden' name='obj-left' value='"+div.attr("id")+"' />");
      } else if (!$.obj2copy) {
        div.addClass("selected");
        $.obj2orig = div;
        $.obj2copy = copy_data(div);
        $.obj2copy.append("<input type='hidden' name='obj-right' value='"+div.attr("id")+"' />");
        $("#link > input").attr("disabled",false);
      } else {
        // both objects are filled, shift obj2 to obj1
        div.addClass("selected");
        $.obj1orig.removeClass("selected");
        $.obj1orig = $.obj2orig;
        $.obj1copy = copy_data($.obj1orig);
        $.obj1copy.append("<input type='hidden' name='obj-left' value='"+$.obj1orig.attr("id")+"' />");
        div.addClass("selected");
        $.obj2orig = div;
        $.obj2copy = copy_data(div);
        $.obj2copy.append("<input type='hidden' name='obj-right' value='"+div.attr("id")+"' />");
      }
      if ($.obj1copy && $.obj2copy) {

        $("#obj-left").empty().append($.obj1copy);
        $("#obj-right").empty().append($.obj2copy);
        // also find the possible relations and show them here
        $("#link-selector").html(create_link_selector());
      }
    } else {
      console.log("click clack");
    }
  });

}) 
