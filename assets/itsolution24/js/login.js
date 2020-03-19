window.$(window.document).ready(function ($) {
    "use strict";

    //Notification options
    window.toastr.options = {
      "closeButton": false,
      "debug": false,
      "newestOnTop": false,
      "progressBar": false,
      "positionClass": "toast-top-center",
      "preventDuplicates": true,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    };

    var username, password; 

    // Login button click event
    $("#login-btn").on("click", function (e) {
        e.preventDefault();

        // Remove alert box, if exist
        $(window.document).find(".alert").remove();

        var $tag = $(this);
        var $usernameInput = $("input[name=\"username\"]");
        var $passwordInput = $("input[name=\"password\"]");

        // Disable Button & Input fields
        var $btn = $tag.button("loading");
        $usernameInput.attr("disabled", "disabled");
        $passwordInput.attr("disabled", "disabled");

        if (!window.isDemo || (!username || password)) {
          username = $usernameInput.val();
          password = $passwordInput.val();
        }

        // Ajax request to verify access
        $.ajax({
            url: "index.php?action_type=LOGIN",
            type: "POST",
            dataType: "json",
            data: {username: username, password: password},
            success: function (response) {
              if (response.count_user_store > 1) {
                window.location = "store_select.php?redirect_to=" + (getParameterByName('redirect_to') && getParameterByName('redirect_to') !== "undefined" && getParameterByName('redirect_to') !== "null" ? getParameterByName('redirect_to') : '');
              } else {
                $.ajax({
                  url: window.baseUrl + "/"+window.adminDir+"/dashboard.php?active_store_id=" + response.store_id,
                  method: "GET",
                  dataType: "json"
                }).
                then(function(response) {
                  var alertMsg = response.msg;
                  window.toastr.success(alertMsg, "Success!");
                  window.location = getParameterByName('redirect_to') && getParameterByName('redirect_to') !== "undefined" && getParameterByName('redirect_to') !== "null" ? getParameterByName('redirect_to') : window.baseUrl + "/"+window.adminDir+"/dashboard.php";
                }, function(response) {
                  var errorMsg = JSON.parse(response.responseText);
                  var alertMsg = "<div>";
                      alertMsg += "<p>" + errorMsg.errorMsg + ".</p>";
                      alertMsg += "</div>";
                  window.toastr.warning(alertMsg, "Warning!");
                });
              }
            },
            error: function (response) {

                // Enable Button & Input fields
                $btn.button("reset");
                $usernameInput.attr("disabled", false);
                $passwordInput.attr("disabled", false);

                // Show error message
                window.toastr.warning(JSON.parse(response.responseText).errorMsg, "Warning!");
            }
        });
    });

    $("#credentials table tbody tr").on("click", function (e) {
      e.preventDefault();
      username = $(this).find(".username").data("username"); 
      password = $(this).find(".password").data("password");
      $("input[name=\"username\"]").val(username); 
      $("input[name=\"password\"]").val(password); 
      $("#login-btn").trigger("click");
    });

    $(document).delegate(".activate-store", "click", function(e) {
        e.preventDefault();

        var $tag = $(this);
        var actionUrl = $tag.attr("href");
        
        $.ajax({
            url: actionUrl,
            method: "GET",
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json"
        }).
        then(function(response) {
            $(":input[type=\"button\"]").prop("disabled", false);
            var alertMsg = response.msg;
            window.toastr.success(alertMsg, "Success!");
            window.location = getParameterByName('redirect_to') && getParameterByName('redirect_to') !== "undefined" && getParameterByName('redirect_to') !== "null" ? getParameterByName('redirect_to') : window.baseUrl + "/"+window.adminDir+"/dashboard.php";
        }, function(response) {

          var errorMsg = JSON.parse(response.responseText);
          $(":input[type=\"button\"]").prop("disabled", false);
          var alertMsg = "<div>";
              alertMsg += "<p>" + errorMsg.errorMsg + ".</p>";
              alertMsg += "</div>";
          window.toastr.warning(alertMsg, "Warning!");

        });
    });

    // Send Resent Button
    $("#reset-btn").on("click", function (e) {
        e.preventDefault();

        // Remove alert box, if exist
        $(document).find(".alert").remove();

        // Declare button and input fields
        var $tag = $(this);
        var $emailInput = $("input[name=\"email\"]");

        // Disable Button & Input fields
        var $btn = $tag.button("loading");
        $emailInput.attr("disabled", "disabled");
        $("body").addClass("overlay-loader");

        // Ajax request to verify access
        $.ajax({
            url: "index.php?action_type=SEND_PASSWORD_RESET_CODE",
            type: "POST",
            dataType: "json",
            data: {email: $emailInput.val()},
            success: function (response) {
                $("body").removeClass("overlay-loader");
                $btn.button("reset");
                $("input[name=\"email\"]").attr("disabled", false);
                // show success message
                var successMsg = "<div class=\"alert alert-success\">";
                successMsg += "<p><i class=\"fa fa-check\"></i> " + response.msg + ".</p>";
                successMsg += "</div>";
                $(window.document).find(".modal-body").before(successMsg);
            },
            error: function (response) {
                $("body").removeClass("overlay-loader");
                // enable Button & Input fields
                $btn.button("reset");
                $("input[name=\"email\"]").attr("disabled", false);
                // show error message
                var alertMsg = "<div class=\"alert alert-danger\">";
                alertMsg += "<p><i class=\"fa fa-warning\"></i> " + JSON.parse(response.responseText).errorMsg + ".</p>";
                alertMsg += "</div>";
                $(window.document).find(".modal-body").before(alertMsg);
            }
        });
    });

    // Reset Confirm Button
    $("#reset-confirm-btn").on("click", function (e) {
        e.preventDefault();

        // Remove alert box, if exist
        $(document).find(".alert").remove();

        // Declare button and input fields
        var $tag = $(this);

        var $resetCodeInput = $("input[name=\"fp_code\"]");
        var $passwordInput = $("input[name=\"password\"]");
        var $passwordConfirmInput = $("input[name=\"password_confirm\"]");

        // Disable Button & Input fields
        var $btn = $tag.button("loading");
        $passwordInput.attr("disabled", "disabled");
        $passwordConfirmInput.attr("disabled", "disabled");
        $("body").addClass("overlay-loader");

        // Ajax request to verify access
        $.ajax({
            url: "password_reset.php?action_type=RESET",
            type: "POST",
            dataType: "json",
            data: {fp_code: $resetCodeInput.val(),password: $passwordInput.val(),password_confirm: $passwordConfirmInput.val()},
            success: function (response) {
                $("body").removeClass("overlay-loader");
                $btn.button("reset");
                $passwordInput.attr("disabled", false);
                $passwordConfirmInput.attr("disabled", false);
                window.toastr.success(response.msg, "Success!");
                window.location.href = 'index.php';
            },
            error: function (response) {

                $("body").removeClass("overlay-loader");
                // Enable Button & Input fields
                $btn.button("reset");
                $passwordInput.attr("disabled", false);
                $passwordConfirmInput.attr("disabled", false);
                window.toastr.warning(JSON.parse(response.responseText).errorMsg, "Warning!");
            }
        });
    });

    // Centering Loginbox horizontally
    var BoxCentering = function () {
        var $loginBox = $(".login-box");
        var $windowHeight = $(window).height();
        var $loginBoxHeight = $loginBox.innerHeight();
        var $marginTop = ($windowHeight / 2) - ($loginBoxHeight / 2);
        $loginBox.css("marginTop", $marginTop + "px");
    };

    // Login box keeps at center always
    BoxCentering();

    // Centering Login box during scroll
    $(window).on("resize", function () {
        BoxCentering();
    });



});