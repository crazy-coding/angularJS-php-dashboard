//===========================
// start banking view factory
//===========================

window.angularApp.factory("BankingRowViewModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    return function (invoice, type) {
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"closeBankingRowViewModal();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\">{{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $http({
                  url: window.baseUrl + "/_inc/banking.php?invoice_id=" + invoice.ref_no + '&action_type=VIEW&view_type=' + type,
                  method: "GET"
                })
                .then(function (response, status, headers, config) {
                    $scope.modal_title = "View " + type + " details";
                    $scope.rawHtml = $sce.trustAsHtml(response.data);
                }, function (response) {
                    window.swal("Oops!", response.data.errorMsg, "error").then(function() {
                        $scope.closeBankingRowViewModal();
                    });
                });

                $scope.closeBankingRowViewModal = function () {
                    $uibModalInstance.dismiss("cancel");
                };
            },
            scope: $scope,
            size: "md",
            backdrop  : "static",
            keyboard: true, // ESC key close enable/disable
            resolve: {
                userForm: function () {
                    // return supplierForm;
                }
            }
        });

        uibModalInstance.result.catch(function () { 
            uibModalInstance.close(); 
        });
    };
}]);