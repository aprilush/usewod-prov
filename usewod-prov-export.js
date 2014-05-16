
var usewodModule = angular.module('usewod', []);

usewodModule.controller('provexport', function($scope) {


  $scope.exportProv = function(data) {
    console.log("username:", $scope.storeuser);
    console.log("apikey:", $scope.storekey);
    console.log("document name:", $scope.docname);
    console.log("public: ", $scope.ispublic)
    console.log("data:", data);

    $scope.storeuser = "aprilush";
    $scope.storekey = "19823df34aa1bef67612a2f7c1d470d0a8860640";
    if (!$scope.docname) {
      $scope.docname = "usewod_export_"+Date.now();
    }

    var provstore = new $.provStoreApi({username: $scope.storeuser, key: $scope.storekey});

    provstore.submitDocument($scope.docname, createPrefixes(), $scope.ispublic,
      function(id) {
        console.log("Created document with id: ", id); 
        addBundles(provstore, id, data);
      },
      function(error) {
        console.error("Error creating new document: ", error); 
      });
  }

  var createPrefixes = function() {
    prefixes = {
      "prefix": {
        "schema": "http://schema.org/", 
        "usewod": "http://data.usewod.org/prov/", 
        "usewodp": "http://data.usewod.org/prov/person/", 
        "usewodds": "http://data.usewod.org/prov/dataset/", 
        "usewodpub": "http://data.usewod.org/prov/publication/",
      }
    }
    return  prefixes;
  }

  var addBundles = function(provstore, docid, data) {
    for (i in data) {
      bundle = data[i];
      provstore.addBundle(docid, bundle['meta']['id'], createBundle(bundle),
        function(id) {
          console.log("Created bundle with id: ", bundle['meta']['id']);
        },
        function(error) {
          console.error("Error creating new bundle with id: ", bundle['meta']['id'], error);
        });
      setTimeout(function() {console.log("slept");}, 5000);
    }
  }

  var createBundle = function(bundle) {
    var pid = ("http://data.usewod.org/prov/person/"+createHash(bundle['meta']['who']));
    var pname = bundle['meta']['who'];
    var bid = bundle['meta']['id'];
    var when = new Date(bundle['meta']['when']*1000);
    var content = bundle['content'];

    // console.log(bid, pid, pname, when);
    // console.log(content);
    
    var agent = new Object();
    var entity = new Object();
    var attrib = new Object();
    var deriv = new Object();
    // var spec = new Object();
    var gen = new Object();

    agent[pid] = {"schema:name":pname, "prov:type":"schema:Person"};
    entity[bid] = {"prov:type":"prov:Bundle"};
    attrib["_:bundle_attrib"] = {"prov:entity":bid, "prov:agent": pid};
    gen["_:bundle_gen"] = {"prov:entity":bid, "prov:time": when};

    for (a in content["agents"]) {
      agent[a] = content["agents"][a];
    }
    for (e in content["entities"]) {
      entity[e] = content["entities"][e];
    }
    for (at in content["attribs"]) {
      attrib[at] = content["attribs"][at];
    }
    for (d in content["derivs"]) {
      deriv[d] = content["derivs"][d];
    }
    // for (s in content["specs"]) {
    //   spec[s] = content["specs"][s];
    // }
    
    var out = new Object();
    if (!$.isEmptyObject(agent)) out["agent"]=agent;
    if (!$.isEmptyObject(entity)) out["entity"]=entity;
    if (!$.isEmptyObject(attrib)) out["wasAttributedTo"]=attrib;
    if (!$.isEmptyObject(deriv)) out["wasDerivedFrom"]=deriv;
    // if (!$.isEmptyObject(spec)) out["specializationOf"]=spec;
    if (!$.isEmptyObject(gen)) out["wasGeneratedBy"]=gen;

    console.log(out);

    return out;
  }

  var createHash = function(s) {
    return md5(s);
  }


});
