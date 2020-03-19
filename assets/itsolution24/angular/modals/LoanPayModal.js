//================================
// start loan pay factory
//================================

window.angularApp.factory("LoanPayModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    return function(loan) {
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"closeLoanPayModal();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\"><small style=\"color:#fff;\"><i>Loan Pay</i></small> {{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $http({
                  url: window.baseUrl + "/_inc/loan.php?action_type=PAY&loan_id="+loan.loan_id,
                  method: "GET"
                })
                .then(function(response, status, headers, config) {
                    $scope.modal_title = loan.ref_no;
                    $scope.rawHtml = $sce.trustAsHtml(response.data);
                    setTimeout(function() {
                        window.storeApp.datePicker();
                        window.storeApp.timePicker();
                    }, 100);
                }, function(response) {
                    window.swal("Oops!", response.data.errorMsg, "error").then(function() {
                        $scope.closeLoanPayModal();
                    });
                });

                // Confirm pay
                $(document).delegate("#pay-confirm-btn", "click", function(e) {
                    e.stopImmediatePropagation();
                    e.stopPropagation();
                    e.preventDefault();

                    var $tag = $(this);
                    var $btn = $tag.button("loading");
                    var form = $($tag.data("form"));
                    var datatable = $tag.data("datatable");
                    form.find(".alert").remove();
                    var actionUrl = form.attr("action");
                    $http({
                        url: window.baseUrl + "/_inc/" + actionUrl,
                        method: "POST",
                        data: form.serialize(),
                        cache: false,
                        processData: false,
                        contentType: false,
                        dataType: "json"
                    }).
                    then(function(response) {
                        $btn.button("reset");
                        var alertMsg = "<div class=\"alert alert-success\">";
                            alertMsg += "<p><i class=\"fa fa-check\"></i> " + response.data.msg + ".</p>";
                            alertMsg += "</div>";
                        form.find(".box-body").before(alertMsg);

                        // Sweet Alert
                        swal("Success", response.data.msg, "success").then(function(value) {
                            $scope.closeLoanPayModal();
                            $(document).find(".close").click(); 
                            // flash update row    
                            var rowId = response.data.id;
                            $(datatable).DataTable().ajax.reload(function(json) {
                                if ($("#row_"+rowId).length) {
                                    $("#row_"+rowId).flash("yellow", 5000);
                                }
                            }, false); 
                        });

                    }, function(response) {
                        $btn.button("reset");
                        var alertMsg = "<div class=\"alert alert-danger\">";
                        window.angular.forEach(response.data, function(value, key) {
                            alertMsg += "<p><i class=\"fa fa-warning\"></i> " + value + ".</p>";
                        });
                        alertMsg += "</div>";
                        form.find(".box-body").before(alertMsg);
                        $(":input[type=\"button\"]").prop("disabled", false);
                        window.swal("Oops!", response.data.errorMsg, "error");
                    });
                });
                $scope.closeLoanPayModal = function () {
                    $uibModalInstance.dismiss("cancel");
                };
            },
            scope: $scope,
            size: "md",
            backdrop  : "static",
            keyboard: true, // ESC key close enable/disable
            resolve: {
                userForm: function () {
                    // return pcForm;
                }
            }
        });

        uibModalInstance.result.catch(function () { 
            uibModalInstance.close(); 
        });
    };
}]);

//===============================
// end loan pay factory
//===============================