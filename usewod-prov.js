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
            if (scope.sourceObject && obj["id"]!=scope.sourceObject.id ) {
              angular.forEach(scope.relationsToPub, function(val) {
                if (val["label"]==rel["label"]) {
                  if (val["objects"].map(function(o){return o["id"]}).indexOf(obj["id"]) < 0) { 
                    scope.$apply( function() {
                      val["objects"].push(obj); 
                    });
                  }
                } 
              }); 
            }
          }
        });        
      } else if (element.hasClass("dataset")) {
        element.droppable({
          accept: ".dataset",
          hoverClass: "drop-hover",
          drop:function(event,ui) {
            // console.log("dropped something");
            var obj = angular.fromJson(ui.draggable.attr("data"));
            if (scope.sourceObject && obj["id"]!=scope.sourceObject.id ) {
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
          }
        });        
      } 
    }
  }
});
usewodModule.controller('prov', function($scope, $sce) {
  var imgSearcher;

  $scope.username = getCookie("username");
  if ($scope.username && $scope.username != "") {
    $scope.loggedin = true;
  }
  
  $scope.setUsername = function() {
    console.log($scope.username);
    setCookie("username", $scope.username, 14);
    $scope.loggedin = true;
  }

  $scope.removeUsername = function() {
    $scope.username = "";
    setCookie("username", $scope.username, 0);
    $scope.loggedin = false;
  }

  function setCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime()+(exdays*24*60*60*1000));
    var expires = "expires="+d.toGMTString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
  }

  function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) 
      {
      var c = ca[i].trim();
      if (c.indexOf(name)==0) return c.substring(name.length,c.length);
    }
    return "";
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
        $scope.statusMessage = "Successfully saved publication '"+$scope.pubTitle+"'.";
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
    var data = {"user":$scope.username,"ds-name":$scope.dsName, "ds-v":$scope.dsV, "ds-released":$scope.dsReleased, "ds-url":$scope.dsUrl, "ds-img":$scope.dsImg};
    var suc = function(respData, status, jqXHR) {
      console.log("received: ", respData, status, jqXHR);
      $("#newds").toggleClass("hidden");
      $scope.$apply( function() {
        $scope.datasets.push(respData["added"]);
        $scope.statusMessage = "Successfully saved dataset '"+$scope.dsName+"'.";
        $scope.dsName = undefined;
        $scope.dsV = undefined;
        $scope.dsReleased = undefined;
        $scope.dsUrl = undefined;
        $scope.dsImg = undefined;
        // $scope.dsAbout = undefined;
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

  $scope.toggleForm = function(which) {
    $scope.statusMessage = undefined;
    if (which == 'pub') {
      $("#newpub").toggleClass("hidden");
    } else if (which == 'ds') {
      $("#newds").toggleClass("hidden");
    }
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
    $scope.statusMessage = undefined;
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
      if (status == "success") {
        $scope.$apply( function() {
          $scope.statusMessage = "Successfully saved "+respData['result']['result']['t_count']+" triples";
          $scope.resetWorkspace();
        });
      } else {
        $scope.$apply( function() {
          $scope.statusMessage = "Save not successful!";
        });
      }
    };
    $.post("addrelations.php", data, suc, "json");
  }

  $scope.$watch('loggedin', function() { 
    if ($scope.loggedin) {
      loadData();
    }
  });
});
