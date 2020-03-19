window.angularApp.factory("PayNowModal", [ "API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope",  "InvoiceSMSModal", "PrintReceiptModal", "EmailModal", 
function ( API_URL, window, $, $http, $uibModal, $sce, $scope, InvoiceSMSModal, PrintReceiptModal, EmailModal
) {
    "use strict";
    return function($scope) {
        if (window.store.sound_effect == 1) {
            window.storeApp.playSound("modal_opened.mp3");
        }
        $scope.done = false;
        $scope.paidAmount = window.formatDecimal($scope.totalPayable, 2);
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-show=\"!done\" ng-click=\"backToPos();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                            "<h3 class=\"modal-title\" id=\"modal-title\"><span class=\"fa fa-fw fa-user\"></span> {{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $http({
                  url: window.baseUrl + "/_inc/template/pos_payment_form.php",
                  method: "GET"
                })
                .then(function(response, status, headers, config) {
                    $scope.modal_title = $scope.customerName;
                    $scope.rawHtml = $sce.trustAsHtml(response.data);

                    setTimeout(function() {
                        $(document).find("#payable-amount").focus().select();
                    }, 100);

                }, function(response) {
                    window.swal("Oops!", response.data.errorMsg, "error")
                    .then(function() {
                        $scope.closePayNowModal();
                    });
                });

                // pay button action
                $scope.pay = function(paymentId, paymentName) {
                    $(document).find(".modal").addClass("overlay-loader");
                    if (($scope.customerId == 1) && parseFloat($scope.paidAmount) < parseFloat($scope.totalPayable).toFixed(2)) {
                        if (window.store.sound_effect == 1) {
                            window.storeApp.playSound("error.mp3");
                        }
                        window.swal("Oops!", "Walking customer can't create a due.", "error")
                        .then(function() {
                            $(document).find(".modal").removeClass("overlay-loader");
                        });
                        return;
                    }
                    $scope.paymentMethodId = paymentId;
                    $scope.paymentMethod = paymentName;
                    $scope.change = 0;
                    $scope.balance = (parseFloat($scope.totalPayable) - $scope.paidAmount);
                    if ($scope.paidAmount > $scope.totalPayable) {
                        $scope.change = $scope.paidAmount - parseFloat($scope.totalPayable);
                        $scope.balance = (parseFloat($scope.totalPayable) - $scope.paidAmount) + parseFloat($scope.change);
                    }

                    // summit payment form
                    var form = $("#pay-form");
                    var actionUrl = form.attr("action");
                    var data = form.serialize() + "&payment-method-id=" + $scope.paymentMethodId + "&present-due=" + $scope.balance;
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
                        console.log(response);    
                        $(document).find(".modal").removeClass("overlay-loader");
                        $scope.invoiceId = response.data.invoice_id;
                        $scope.invoiceInfo = response.data.invoice_info;
                        $scope.invoiceItems = response.data.invoice_items;
                        $scope.done = true;
                        if (window.store.sound_effect == 1) {
                            window.storeApp.playSound("modify.mp3");
                        }

                        // Auto SMS
                        if (window.settings.invoice_auto_sms == 1) {
                            $http({
                                url: window.baseUrl + "/_inc/sms/index.php",
                                method: "POST",
                                data: "action_type=SEND&invoice_id="+$scope.invoiceId,
                                cache: false,
                                processData: false,
                                contentType: false,
                                dataType: "json"
                            }).
                            then(function(response) {
                                console.log(response.data.msg);
                            }, function(response) {
                                console.log(response.data.errorMsg);
                            });
                        }

                        // Auto Print
                        if (window.store.auto_print == 1 && window.store.remote_printing == 1) {
                            PrintReceiptModal($scope);
                        } else if (window.store.auto_print == 1) {
                            window.open(window.baseUrl + "/"+window.adminDir+"/view_invoice.php?invoice_id=" + $scope.invoiceId);
                        }

                        $scope.showProductList();
                    }, function(response) {
                        if (window.store.sound_effect == 1) {
                            window.storeApp.playSound("error.mp3");
                        }
                        window.swal("Oops!", response.data.errorMsg, "error");
                        $(document).find(".modal").removeClass("overlay-loader");
                    });
                };

                $scope.payByEnterKey = function($event) {
                    if(($event.keyCode || $event.which) == 13){
                        $scope.pay(1, 'Cash on Delivery');
                    }
                };

                $scope.closePayNowModal = function () {
                    if (!window.getParameterByName("customer_id") || !window.getParameterByName("invoice_id")) {
                        $scope.resetPos();
                    }
                    $uibModalInstance.dismiss("cancel");
                    if (window.settings.after_sell_page == "invoice" || (window.getParameterByName("customer_id") && window.getParameterByName("invoice_id"))) {
                        window.location = window.baseUrl + "/"+window.adminDir+"/view_invoice.php?invoice_id=" + $scope.invoiceId;
                    }
                };

                // back to pos button
                $scope.backToPos = function() {
                    $uibModalInstance.dismiss("cancel");
                };
            },
            scope: $scope,
            size: "lg",
            backdrop  : "static",
            keyboard: true, // ESC key close enable/disable
            resolve: {
                userForm: function () {
                    // return customerForm;
                }
            }
        });

        uibModalInstance.result.catch(function () { 
            uibModalInstance.close(); 
            if (window.store.sound_effect == 1) {
                window.storeApp.playSound("modal_closed.mp3");
            }
        });

        // sending invoice via email
        $scope.sendInvoiceViaEmail = function (invoiceId) {

            if (!invoiceId) {
                if (window.store.sound_effect == 1) {
                    window.storeApp.playSound("error.mp3");
                }
                window.swal("Oops!", "Invalid invoice id", "error");
                return false;
            }

            // fetch invocie and send thought the provided email
            $http({
                url: API_URL + "/"+window.adminDir+"/view_invoice.php?invoice_id=" + invoiceId,
                method: "GET",
                cache: false,
                processData: false,
                dataType: "html"
            }).
            then(function(response) {
                var invoiceContent = $(response.data).find("#invoice").html();
                if (!invoiceContent || invoiceContent == "undefined") {
                    if (window.store.sound_effect == 1) {
                        window.storeApp.playSound("error.mp3");
                    }
                    window.swal("Oops!", "Invalid invoice id", "error");
                    return false;
                }
                var recipientName = $scope.customerName;
                var invoice = {
                    template: "invoice", 
                    subject: "Invoice", 
                    title: "Invoice", 
                    recipientName: recipientName, 
                    senderName: window.store.name,
                    html: invoiceContent
                };
                EmailModal(invoice);
            }, function(response) {
                if (window.store.sound_effect == 1) {
                    window.storeApp.playSound("error.mp3");
                }
                window.swal("Oops!", "an Unknown error occured!", "error");
            });
        };
    };
}]);