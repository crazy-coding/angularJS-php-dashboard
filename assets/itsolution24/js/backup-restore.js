window.$(document).ready(function($) {

  $("#button-restore").on("click", function() {

    $("#form-upload").remove();
    
    $("body").prepend("<form enctype=\"multipart/form-data\" id=\"form-upload\" style=\"display: none;\"><input type=\"hidden\" name=\"action_type\" value=\"IMPORTFILE\"><input type=\"file\" name=\"restore\" /></form>");
    
    $("#form-upload input[name=\"restore\"]").trigger("click");
    
    if (typeof timer != "undefined") {
      clearInterval(timer);
    }
    
    var timer = setInterval(function() {
      if ($("#form-upload input[name=\"restore\"]").val() != "") {
        clearInterval(timer);
    
        $("#progress-restore .progress-bar").attr("aria-valuenow", 0);
        $("#progress-restore .progress-bar").css("width", "0%");
    
        $.ajax({
          url: window.baseUrl + "/_inc/restore.php?action_type=IMPORTFILE",
          type: "post",
          dataType: "json",
          data: new FormData($("#form-upload")[0]),
          cache: false,
          contentType: false,
          processData: false,
          beforeSend: function() {
            $("#button-restore").button("loading");
            $("#restore").find('.alert').remove();
            $("#restore form").prepend("<div class=\"alert alert-warning\"><i class=\"fa fa-fw fa-refresh\"></i> Enjoy a cup of coffee while you are waiting for restoring :)</div>");
          },
          complete: function() {
            $("#button-restore").button("reset");
          },
          success: function(json) {
            $(".alert-dismissible").remove();
            
            if (json["error"]) {
              $("#restore form").find('.alert').remove();
              $("#restore form").prepend("<div class=\"alert alert-danger\"><i class=\"fa fa-exclamation-circle\"></i> " + json["error"] + " </div>");
            }
            
            if (json["success"]) {
              $("#restore form").find('.alert').remove();
              $("#restore form").prepend("<div class=\"alert alert-success\"><i class=\"fa fa-check-circle\"></i> " + json["success"] + " </div>");
            }
            
            if (json["total"]) {
              $("#progress-restore .progress-bar").attr("aria-valuenow", json["total"]);
              $("#progress-restore .progress-bar").css("width", json["total"] + "%");
            }
            
            if (json["next"]) {
              $("#button-restore").button("loading");
              next(json["next"]);
            }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
      }
    }, 500);
  });

  function next(url) {
    $.ajax({
      url: url,
      dataType: "json",
      success: function(json) {
        $(".alert-dismissible").remove();
        $("#button-restore").button("loading");
        
        if (json["error"]) {
          $("#restore form").find('.alert').remove();
          $("#restore form").prepend("<div class=\"alert alert-danger\"><i class=\"fa fa-fw fa-exclamation-circle\"></i> " + json["error"] + "</div>");
        }
        
        if (json["success"]) {
          $("#restore form").find('.alert').remove();
          $("#restore form").prepend("<div class=\"alert alert-success\"><i class=\"fa fa-check-circle\"></i> " + json["success"] + " </div>");
            $("#button-restore").button("reset");
        }
        
        if (json["total"]) {
          $("#progress-restore .progress-bar").attr("aria-valuenow", json["total"]);
          $("#progress-restore .progress-bar").css("width", json["total"] + "%");
        }
        
        if (json["next"]) {
          next(json["next"]);
        }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  }
});