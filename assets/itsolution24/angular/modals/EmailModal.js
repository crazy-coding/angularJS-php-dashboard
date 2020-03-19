window.angularApp.factory("EmailModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    return function(content) {
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"cancel();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\"><span class=\"fa fa-fw fa-envelope\"></span> {{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>" +
                        "<div class=\"modal-footer\">" +
                            "<a id=\"sendEmailBtn\" class=\"btn-success btn btn-sm\" data-loading-text=\"Sending...\"><span class=\"fa fa-fw fa-send-o\"></span> SEND</a>" +
                            "<a ng-click=\"cancel();\" class=\"btn-danger btn btn-sm\" data-dismiss=\"modal\" aria-label=\"Close\"><span class=\"fa fa-fw- fa-close\"></span> CLOSE</a>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $scope.modal_title = content.title;
                var form = "<form method=\"post\" action=\"#\" id=\"email-form\">";
                form += "<input type=\"hidden\" name=\"template\" value=\""+content.template+"\">";
                form += "<input type=\"hidden\" name=\"subject\" value=\""+content.subject+"\">";
                form += "<input type=\"hidden\" name=\"title\" value=\""+content.title.trim()+"\">";
                form += "<input type=\"email\" name=\"email\" class=\"form-control\" placeholder=\"Please, type a valid email address\" required>";
                form += "<textarea style=\"display:none;\" name=\"emailbody\">"+content.html.trim()+"</textarea>";
                form += "</form>";
                $scope.rawHtml = $sce.trustAsHtml(form);
                $scope.content = content;

                // Send Email
                $(document).delegate("#sendEmailBtn", "click", function(e) {
                    e.stopImmediatePropagation();
                    e.stopPropagation();
                    e.preventDefault();
 
                    var $tag = $(this);
                    var $btn = $tag.button("loading");
                    $("body").addClass("overlay-loader");
                    
                    $http({
                        url: window.baseUrl + "/_inc/send_email.php",
                        method: "POST",
                        data: $("#email-form").serialize(),
                        cache: false,
                        processData: false,
                        contentType: false,
                        dataType: "json"
                    }).
                    then(function(response) {

                        $("body").removeClass("overlay-loader");
                        $btn.button("reset");

                        // Success alert
                        window.swal("Success", response.data.msg, "success").then(function() {
                            $scope.cancel();
                        });

                    }, function(response) {
                        $("body").removeClass("overlay-loader");
                        $btn.button("reset");
                        window.swal("Oops!", response.data.errorMsg, "error");
                    });

                });

                $scope.cancel = function () {
                    $uibModalInstance.dismiss("cancel");
                };
            },
            scope: $scope,
            size: "md",
            backdrop  : "static",
            keyboard: true,
        });

        uibModalInstance.result.catch(function () { 
            uibModalInstance.close(); 
        });
    };
}]);