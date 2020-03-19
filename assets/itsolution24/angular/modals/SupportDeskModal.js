window.angularApp.factory("SupportDeskModal", ["API_URL", "$http", "$uibModal", "$sce", "$rootScope", function(API_URL, $http, $uibModal, $sce, $scope) {
    return function() {
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"cancel();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\"><span class=\"fa fa-fw fa-headphones\"></span> Support Desk</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $http({
                  url: API_URL + "/_inc/template/partials/supportdesk_modal.php",
                  method: "GET"
                })
                .then(function(response, status, headers, config) {
                    $scope.rawHtml = $sce.trustAsHtml(response.data);
                }, function(data) {
                   window.swal("Oops!", "an unknown error occured!", "error");
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