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
