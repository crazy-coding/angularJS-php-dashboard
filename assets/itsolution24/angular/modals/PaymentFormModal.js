window.angularApp.factory("PaymentFormModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "PrintReceiptModal", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, PrintReceiptModal, $scope) {
    return function($scope) {
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"closePaymentFormModal();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                            "<h3 class=\"modal-title\" id=\"modal-title\">" + 
                                "<span class=\"fa fa-fw fa-list\"></span> {{ modal_title }}" +
                            "</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\" style=\"padding: 0px;overflow-x: hidden;\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>" +
                        "<div class=\"modal-footer\">" +
                            "<button ng-click=\"closePaymentFormModal();\" type=\"button\" class=\"btn btn-danger radius-50\">Close</button>" +
                            "<button  ng-click=\"checkout();\" type=\"button\" class=\"btn btn-success radius-50\">Checkout &rarr;</button>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $(document).find("body").addClass("overlay-loader");
                $http({
                  url: window.baseUrl + "/_inc/template/payment_form.php",
                  method: "GET"
                })
                .then(function(response, status, headers, config) {
                    $scope.modal_title = "Order Payment >> " + $scope.customerName;
                    $scope.rawHtml = $sce.trustAsHtml(response.data);
                    setTimeout(function() {
                        storeApp.bootBooxHeightAdjustment();
                        $(document).find("body").removeClass("overlay-loader");
                    }, 500);                 
                }, function(response) {
                   window.swal("Oops!", response.data.errorMsg, "error");
                   $(document).find("body").removeClass("overlay-loader");
                });

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
                        $scope.modal_title = "Order Payment >> " + $scope.customerName;
                        $scope.rawPaymentMethodHtml = $sce.trustAsHtml(response.data);
                        $(document).find("body").removeClass("overlay-loader");
                    }, function(response) {
                       window.swal("Oops!", response.data.errorMsg, "error");
                       $(document).find("body").removeClass("overlay-loader");
                    });
                };

                $scope.checkout = function() {
                    $(document).find(".modal").addClass("overlay-loader");
                    var form = $("#checkout-form");
                    var actionUrl = form.attr("action");
                    var data = form.serialize();
                    $http({
                        url: window.baseUrl + "/_inc/" + actionUrl,
                        method: "POST",
                        data: data,
                        cache: false,
                        processData: false,
                        contentType: false,
                        dataType: "json"
                    }).
                    then(function(response) {
                        $(document).find(".modal").removeClass("overlay-loader");
                        $scope.invoiceId = response.data.invoice_id;
                        $scope.invoiceInfo = response.data.invoice_info;
                        $scope.invoiceItems = response.data.invoice_items;
                        $scope.done = true;
                        if (window.store.sound_effect == 1) {
                            window.storeApp.playSound("modify.mp3");
                        }
                        if (window.store.auto_print == 1 && window.store.remote_printing == 1) {
                            PrintReceiptModal($scope);
                        } else if (window.store.auto_print == 1) {
                            window.open(window.baseUrl + "/admin/view_invoice.php?invoice_id=" + $scope.invoiceId);
                        }
                        $scope.resetPos();
                        window.swal({
                            title: "Invoice Created!",
                            text: "Invoice ID: " + $scope.invoiceId,
                            icon: "success",
                        });
                        $scope.closePaymentFormModal();
                    }, function(response) {
                        if (window.store.sound_effect == 1) {
                            window.storeApp.playSound("error.mp3");
                        }
                        window.swal("Oops!", response.data.errorMsg, "error");
                        $(document).find(".modal").removeClass("overlay-loader");
                    });
                };

                $scope.checkoutWithFullPaid = function() {
                    $scope.paidAmount = $scope.totalPayable;
                    setTimeout(function() {
                        $scope.checkout();
                    }, 100);
                };

                $scope.checkoutWithFullDue = function() {
                    $scope.paidAmount = 0;
                    setTimeout(function() {
                        $scope.checkout();
                    }, 100);
                };

                $scope.checkoutWhilePressEnter = function($event) {
                    if(($event.keyCode || $event.which) == 13){
                        $scope.checkout();
                    }
                };

                $scope.closePaymentFormModal = function () {
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