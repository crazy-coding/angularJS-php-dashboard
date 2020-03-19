window.angularApp.factory("POSFilemanagerModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    return function (data) {
        var uibModalInstance = $uibModal.open({
            animation: false,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"closeFileManager()\" type=\"button\" id=\"close-filemanger\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\">{{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-bodyx\" id=\"filemanager\">" +
                            "<div bind-html-compile=\"rawHtml\"><div style=\"padding:10px;\">Loading...</div></div>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $http({
                  url: window.baseUrl + "/"+window.adminDir+"/filemanager.php?ajax=1&target=" + data.target + "&thumb=" + data.thumb,
                  dataType: "html",
                  method: "GET"
                })
                .then(function (response, status, headers, config) {
                    $("body").addClass("filemanager-open");
                    $scope.rawHtml = $sce.trustAsHtml(response.data);
                }, function (response) {
                    window.swal("Oops!", response.data.errorMsg, "error").then(function() {
                        $scope.closeFileManager();
                    });
                });

                $scope.closeFileManager = function () {
                    $uibModalInstance.dismiss("cancel");
                    setTimeout(function() {
                        console.log($(document).find('.modal').length);
                        if ($(document).find('.modal').length) {
                            $("body").addClass("modal-open");
                        }
                    }, 1000);
                };
            },
            scope: $scope,
            size: "lg",
            backdrop  : "static",
            keyboard: false,
        });
        
        uibModalInstance.result.catch(function () { 
            setTimeout(function() {
                $("body").removeClass("modal-open");
                $("body").removeClass("filemanager-open");
            }, 500);
            uibModalInstance.close(); 
        });
    };
}]);