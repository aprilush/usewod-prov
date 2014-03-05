google.load('search', '1');

var usewodModule = angular.module('usewod', []);
usewodModule.directive('draggable', function() {
  return {
    restrict:'A', 
    require: '?ngModel',
    link: function(scope, element, attrs) {
      // console.log("data: ", attrs['data']);
      element.draggable({
        revert:true, 
        helper: 'clone',
        // start: function(event, ui) {
        //   console.log("started dragging"); 
        // },
        // stop: function(event, ui) {
        //   console.log("stoped dragging"); 
        // }
      });
    }
  }
});
usewodModule.directive('droppable', function() {
  return {
    restrict:'A', 
    require: '?ngModel',
    link: function(scope, element, attrs) {
      var rel = angular.fromJson(attrs["data"]);
      if (element.hasClass("publication")) {
        element.droppable({
          accept: ".publication",
          hoverClass: "drop-hover",
          drop:function(event,ui) {
            // console.log("dropped something");
            var obj = angular.fromJson(ui.draggable.attr("data"));
            angular.forEach(scope.relationsToPub, function(val) {
              if (val["label"]==rel["label"]) {
                if (val["objects"].map(function(o){return o["id"]}).indexOf(obj["id"]) < 0) { 
                  scope.$apply( function() {
                    val["objects"].push(obj); // check first if it's already added
                  });
                }
              } 
            }); 
          }
        });        
      } else if (element.hasClass("dataset")) {
        element.droppable({
          accept: ".dataset",
          hoverClass: "drop-hover",
          drop:function(event,ui) {
            // console.log("dropped something");
            var obj = angular.fromJson(ui.draggable.attr("data"));
            angular.forEach(scope.relationsToDs, function(val) {
              if (val["label"]==rel["label"]) {
                if (val["objects"].map(function(o){return o["id"]}).indexOf(obj["id"]) < 0) {
                  scope.$apply( function() {
                    val["objects"].push(obj); // check first if it's already added
                  });
                }
              } 
            }); 
          }
        });        
      } 
    }
  }
});
usewodModule.controller('prov', function($scope, $sce) {
  var imgSearcher;

  $scope.setUsername = function() {
    console.log($scope.username);
    $scope.loggedin = true;
  }

  var setSearcher = function() {
    imgSearcher = new google.search.ImageSearch();
    imgSearcher.setRestriction(google.search.ImageSearch.RESTRICT_FILETYPE, google.search.ImageSearch.FILETYPE_PNG);
    imgSearcher.setRestriction(google.search.ImageSearch.RESTRICT_IMAGESIZE, google.search.ImageSearch.IMAGESIZE_MEDIUM);
    imgSearcher.setRestriction(google.search.Search.RESTRICT_SAFESEARCH, google.search.Search.SAFESEARCH_ON);
    imgSearcher.setNoHtmlGeneration();
    imgSearcher.setResultSetSize(1);
  }

  var imgSearchCompleteForPaper = function() {
    if (imgSearcher.results && imgSearcher.results.length > 0) {
      console.log("found image with url: ", imgSearcher.results[0].url);
      $scope.$apply( function() {
        $scope.pubImg = $sce.getTrustedUrl($sce.trustAsUrl(imgSearcher.results[0].url))
      });
      console.log($scope.pubImg);
    } else {
      console.log("no image found, searching again");
      $scope.imgSearchForPaper();
    }
  }

  $scope.imgSearchForPaper = function() {
    if (!imgSearcher) { setSearcher(); }
    var title = $scope.pubTitle;
    if (title.length == 0) return;
    imgSearcher.setSearchCompleteCallback(this, imgSearchCompleteForPaper, null);
    imgSearcher.execute(title);
  }

  var imgSearchCompleteForDataset = function() {
    if (imgSearcher.results && imgSearcher.results.length > 0) {
      console.log("found image with url: ", imgSearcher.results[0].url);
      $scope.$apply( function() {
        $scope.dsImg = $sce.getTrustedUrl($sce.trustAsUrl(imgSearcher.results[0].url))
      });
      console.log($scope.pubImg);
    } else {
      console.log("no image found, searching again");
      $scope.imgSearchForDataset();
    }
  }

  $scope.imgSearchForDataset = function() {
    if (!imgSearcher) { setSearcher(); }
    var title = $scope.dsName;
    if (title.length == 0) return;
    imgSearcher.setSearchCompleteCallback(this, imgSearchCompleteForDataset, null);
    imgSearcher.execute(title);
  }

  $scope.addPublication = function() {
    console.log("Adding a new paper! ");
    var data = {"user":$scope.username,"pub-title":$scope.pubTitle, "pub-author":$scope.pubAuthor, "pub-venue":$scope.pubVenue, "pub-year":$scope.pubYear, "pub-url":$scope.pubUrl, "pub-img":$scope.pubImg};
    var suc = function(respData, status, jqXHR) {
      console.log("received: ", respData, status, jqXHR);
      $("#newpub").toggleClass("hidden");
      $scope.$apply( function() {
        $scope.publications.push(respData["added"]);
        $scope.pubTitle = undefined;
        $scope.pubAuthor = undefined;
        $scope.pubVenue = undefined;
        $scope.pubYear = undefined;
        $scope.pubUrl = undefined;
        $scope.pubImg = undefined;
        // console.log($scope.publications);
      });
    };
    $.post("addpublication.php", data, suc, "json");
  }

  $scope.addDataset = function() {
    console.log("Adding a new dataset! ");
    var data = {"user":$scope.username,"ds-name":$scope.dsName, "ds-v":$scope.dsV, "ds-url":$scope.dsUrl, "ds-img":$scope.dsImg, "ds-about":$scope.dsAbout};
    var suc = function(respData, status, jqXHR) {
      console.log("received: ", respData, status, jqXHR);
      $("#newds").toggleClass("hidden");
      $scope.$apply( function() {
        $scope.datasets.push(respData["added"]);
        $scope.dsName = undefined;
        $scope.dsV = undefined;
        $scope.dsUrl = undefined;
        $scope.dsImg = undefined;
        $scope.dsAbout = undefined;
        // console.log($scope.datasets);
      });
    };
    $.post("adddataset.php", data, suc, "json");
  }

  var loadData = function() {
    console.log("started loading data");
    $scope.publications = [];
    $scope.datasets = [];
    var sucpub = function(data) {
      console.log("received publications : ", data);
      $scope.$apply( function() {
        $scope.publications = data["publications"];
      });
    };
    $.get("loadpublications.php", sucpub, "json");
    var sucds = function(data) {
      console.log("received datasets : ", data);
      $scope.$apply( function() {
        $scope.datasets = data["datasets"];
      });
    };
    $.get("loaddatasets.php", sucds, "json");
    console.log("finished loading data", $scope.publications, $scope.datasets);
  }

  $scope.selectedSource = function(obj, objtype) {
    if (!$scope.sourceObject) {
      $scope.sourceObject = obj;
      $scope.sourceObject.type = objtype;
      if (objtype == "p") {
        $scope.sourceObject.name = obj.title;
        $scope.relationsToPub = [{"label":"cites", "objects":[]}];
        $scope.relationsToDs = [{"label":"mentions", "objects":[]}, {"label":"describes", "objects":[]}, {"label":"evaluates", "objects":[]}, {"label":"analyses", "objects":[]}, {"label":"compares", "objects":[]}];
      } else if (objtype == "d") {
        $scope.relationsToPub = [{"label":"is mentioned in", "objects":[]}, {"label":"is described in", "objects":[]}, {"label":"is evaluated in", "objects":[]}, {"label":"is analysed in", "objects":[]}, {"label":"is compared in", "objects":[]}];
        $scope.relationsToDs = [{"label":"extends", "objects":[]}, {"label":"includes", "objects":[]}, {"label":"overlaps", "objects":[]}, {"label":"is transformation of", "objects":[]}];
      }
    }
  }

  $scope.resetWorkspace = function() {
    $scope.sourceObject = undefined;
    $scope.relationsToPub = [];
    $scope.relationsToDs = [];
  }

  $scope.saveLinks = function() {
    console.log("Saving links! ");
    var data = {"source":$scope.sourceObject, "relations":{"pub":$scope.relationsToPub, "ds":$scope.relationsToDs}, "user":$scope.username};
    var suc = function(respData, status, jqXHR) {
      console.log("received: ", respData, status, jqXHR);
      // $scope.$apply( function() {
      //   $scope.resetWorkspace();
      // });
    };
    $.post("addrelations.php", data, suc, "json");
  }

  $scope.$watch('loggedin', function() { 
    if ($scope.loggedin) {
      loadData();
    }
  });
});

$(function() {

  // $(".datapub").draggable({
  //   appendTo: "body",
  //   cursor: "move",
  //   helper: 'clone',
  //   revert: "invalid",
  //   opacity: 0.5,
  //   start: function( event, ui ) {
  //     console.log("starting drag", ui, event);
  //     // parent_div_data = $(this).parent().attr("id");
  //   }
  // });
  // // $(".data > img").draggable("option", "cursor", "zoom-in" );
  // // $(".data,.publication").draggable("option", "scope", "publication");
  // // $(".data,.dataset").draggable("option", "scope", "dataset");
  // $(".rel").droppable({
  //   tolerance: "intersect",
  //   accept: ".datapub",
  //   drop: function(event, ui) {                       
  //     console.log("dropped something", ui, event);
  //     // $(this).append($(ui.draggable)).clone();               
  //   }
  // });


  // $.sourceobjorig = null;
  // $.sourceobjcopy = null;
  // $.obj2orig = null;
  // $.obj2copy = null;

  // how do we make this into proper provenance?
  // $.allLinksPubPub = ["cites", "is cited by"];
  // $.allLinksPubDs = ["uses", "describes", "evaluates", "analyses", "compares"];
  // $.allLinksDsPub = ["is used in", "is described in", "is evaluated in", "is analysed in", "is compared in"];
  // $.allLinksDsDs = ["extends", "includes", "overlaps", "isTransformation"];

  // function copy_data(el) {
  //     var cl = el.clone().removeClass("selected").removeClass("data").addClass("ws");
  //     cl.attr("id", el.attr("id"));
  //     return cl;
  // };

  // function create_links_drop_zones() {
  //   var ls;
  //   if ($.obj1copy.hasClass("pub") && $.obj2copy.hasClass("pub")) {
  //     ls = $.allLinksPubPub;
  //   } else if ($.obj1copy.hasClass("pub") && $.obj2copy.hasClass("ds")) {
  //     ls = $.allLinksPubDs;
  //   } else if ($.obj1copy.hasClass("ds") && $.obj2copy.hasClass("pub")) {
  //     ls = $.allLinksDsPub;
  //   } else if ($.obj1copy.hasClass("ds") && $.obj2copy.hasClass("ds")) {
  //     ls = $.allLinksDsDs;
  //   }
  //   var sel = "<select name='link'>";
  //   for (l in ls) {
  //     sel = sel+ "<option value='"+ls[l]+"'>"+ls[l]+"</option>";
  //   }
  //   sel = sel+"</select>";
  //   return sel;
  // };

  // function create_source() {
  //   var d = $("#source-obj").empty().append($.sourceobjcopy);
  //   var title = $.sourceobjcopy.children("img:first-of-type").attr("title");
  //   console.log("got title: ", title);
  //   d.append("<strong>"+title+"</strong>").append("<input type='hidden' name='source-obj' value='"+$.sourceobjcopy.attr("id")+"' />");
  //   return d;
  // }

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
      } 
    // } else if (div.hasClass("data")) {
    //   if (!$.sourceobjcopy) {
    //     div.addClass("selected");
    //     $.sourceobjorig = div;
    //     $.sourceobjcopy = copy_data(div);
      // } else if (!$.obj2copy) {
      //   div.addClass("selected");
      //   $.obj2orig = div;
      //   $.obj2copy = copy_data(div);
      //   $.obj2copy.append("<input type='hidden' name='obj-right' value='"+div.attr("id")+"' />");
      //   $("#link > input").attr("disabled",false);
      // } else {
        // both objects are filled, shift obj2 to obj1
        // div.addClass("selected");
        // $.obj1orig.removeClass("selected");
        // $.obj1orig = $.obj2orig;
        // $.obj1copy = copy_data($.obj1orig);
        // $.obj1copy.append("<input type='hidden' name='obj-left' value='"+$.obj1orig.attr("id")+"' />");
        // div.addClass("selected");
        // $.obj2orig = div;
        // $.obj2copy = copy_data(div);
        // $.obj2copy.append("<input type='hidden' name='obj-right' value='"+div.attr("id")+"' />");
      // }
      // if ($.sourceobjcopy) {

      //   create_source();
        // $("#obj-right").empty().append($.obj2copy);
        // also find the possible relations and show them here
        // $("#link-selector").html(create_link_selector());
      // }
    }
  });

}) 
