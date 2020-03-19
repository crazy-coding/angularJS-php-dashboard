window.angularApp.factory("GiftcardViewModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    return function (giftcard) {
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"closeGiftcardViewModal();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\">{{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $http({
                  url: window.baseUrl + "/_inc/giftcard.php?card_no=" + giftcard.card_no + "&action_type=VIEW",
                  method: "GET"
                })
                .then(function (response, status, headers, config) {
                    $scope.modal_title = "View Gift Card (" + giftcard.card_no + ")";
                    $scope.rawHtml = $sce.trustAsHtml(response.data);
                }, function (response) {
                    window.swal("Oops!", response.data.errorMsg, "error").then(function() {
                        $scope.closeGiftcardViewModal();
                    });
                });

                $scope.closeGiftcardViewModal = function () {
                    $uibModalInstance.dismiss("cancel");
                };
            },
            scope: $scope,
            size: "md",
            backdrop  : "static",
            keyboard: true,
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