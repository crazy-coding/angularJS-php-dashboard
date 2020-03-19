window.angularApp.factory("PaymentOnlyModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    return function($parentScope) {
        $scope.order = $parentScope.order;
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button id=\"payment_only_modal\" ng-click=\"closePaymentOnlyModal();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\">" +
                                "<span class=\"fa fa-fw fa-list\"></span> {{ modal_title }}" +
                                "<span ng-click=\"loadModal();\" class=\"fa fa-fw fa-refresh pointer\"></span>" +
                            "</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\" style=\"padding: 0px;overflow-x: hidden;\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>" +
                        "<div class=\"modal-footer\">" +
                            "<button ng-click=\"closePaymentOnlyModal();\" type=\"button\" class=\"btn btn-danger radius-50\">Close</button>" +
                            "<button  ng-click=\"payNow();\" type=\"button\" class=\"btn btn-success radius-50\">Pay Now &rarr;</button>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $scope.loadModal = function() {
                    $(document).find("body").addClass("overlay-loader");
                    $http({
                      url: window.baseUrl + "/_inc/template/payment_only_form.php",
                      method: "GET"
                    })
                    .then(function(response, status, headers, config) {
                        $scope.modal_title = "Order Payment >> " + $scope.order.customer_name;
                        $scope.rawHtml = $sce.trustAsHtml(response.data);
                        setTimeout(function() {
                            storeApp.bootBooxHeightAdjustment();
                            $(document).find("body").removeClass("overlay-loader");
                        }, 500);                 
                    }, function(response) {
                       window.swal("Oops!", response.data.errorMsg, "error");
                       $(document).find("body").removeClass("overlay-loader");
                    });
                };
                $scope.loadModal();

                $scope.selectPaymentMethod = function(pmethodId,pmethodName) {
                    $(document).find("body").addClass("overlay-loader");
                    $scope.pmethodId = pmethodId;
                    $scope.pmethodName = pmethodName;
                    $(".pmethod_item").removeClass("active");
                    $("#pmethod_"+pmethodId).addClass("active");
                    $http({
                      url: window.baseUrl + "/_inc/payment.php?action_type=FIELD&pmethod_id=" + pmethodId,
                      method: "GET"
                    })
                    .then(function(response, status, headers, config) {
                        $scope.modal_title = "Order Payment >> " + $scope.order.customer_name;
                        $scope.rawPaymentMethodHtml = $sce.trustAsHtml(response.data);
                        $(document).find("body").removeClass("overlay-loader");
                    }, function(response) {
                       window.swal("Oops!", response.data.errorMsg, "error");
                       $(document).find("body").removeClass("overlay-loader");
                    });
                };

                $scope.payNow = function() {
                    $(document).find(".modal").addClass("overlay-loader");
                    var form = $("#checkout-form");
                    var data = form.serialize();
                    $http({
                        url: window.baseUrl + "/_inc/payment.php?action_type=PAYMENT",
                        method: "POST",
                        data: data,
                        cache: false,
                        processData: false,
                        contentType: false,
                        dataType: "json"
                    }).
                    then(function(response) {
                        window.swal("Success", response.data.msg, "success")
                        .then(function(value) {
                            if ($scope.order.datatable) {
                                $($scope.order.datatable).DataTable().ajax.reload(null, false);
                            };
                            if ($parentScope.loadOrders) {
                                $parentScope.loadOrders();
                            }
                            $(document).find("#payment_only_modal").trigger("click");
                        });
                        $(document).find(".modal").removeClass("overlay-loader");
                    }, function(response) {
                        if (window.store.sound_effect == 1) {
                            window.storeApp.playSound("error.mp3");
                        }
                        window.swal("Oops!", response.data.errorMsg, "error");
                        $(document).find(".modal").removeClass("overlay-loader");
                    });
                };

                $scope.payNowWithFullPaid = function() {
                    $scope.paidAmount = $scope.order.due;
                    setTimeout(function() {
                        $scope.payNow();
                    }, 100);
                };

                $scope.payNowWhilePressEnter = function($event) {
                    if(($event.keyCode || $event.which) == 13){
                        $scope.payNow();
                    }
                };

                $scope.closePaymentOnlyModal = function () {
                    $uibModalInstance.dismiss("cancel");
                };
            },
            scope: $scope,
            size: "lg",
            backdrop  : "static",
            keyboard: true,
        });

        uibModalInstance.result.catch(function () { 
            uibModalInstance.close(); 
        });
    };
}]);