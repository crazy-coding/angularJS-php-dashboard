window.angularApp.factory("AddCustomerMobileNumberModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    "use strict";
    return function($scope) {
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"closeAddCustomerMobileNumberModal();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\"><span class=\"fa fa-fw fa-pencil\"></span> {{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<input type=\"text\" id=\"cnumber\" class=\"form-control\" placeholder=\"Mobile Number...\" value=\"{{ customerMobileNumber }}\" autocomplete=\"off\">" +
                        "</div>" +
                        "<div class=\"modal-footer\">" +
                            "<button ng-click=\"clear();\" class=\"btn btn-warning pull-left\" type=\"button\">Cancel</button>" +
                            "<button ng-click=\"closeAddCustomerMobileNumberModal();\" id=\"reset-btn\" name=\"reset-btn\" class=\"btn btn-success\">Ok</button>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {

                $scope.modal_title = $scope.customerName;            
                var customerMobileNumber;
                $(document).on("change keyup blur", "#cnumber", function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    var $this = $(this);
                    customerMobileNumber = $this.val();
                    $("#customer-mobile-number").val(customerMobileNumber);
                    $scope.customerMobileNumber = customerMobileNumber;
                    // $scope.addCustomer($scope.customerId);
                });

                $scope.closeAddCustomerMobileNumberModal = function () {
                    $uibModalInstance.dismiss("cancel");
                };

                $scope.clear = function () {
                    $("#customer-mobile-number").val("");
                    $uibModalInstance.dismiss("cancel");
                    $scope.addCustomer($scope.customerId);
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