window.angularApp.factory("SummaryReportModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    return function (invoice, type) {
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"closeSummaryReportModal();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\"><span class=\"fa fa-fw fa-eye\"></span> {{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $scope.loadSummary = function(duration, title) {
                    $(document).find("body").addClass("overlay-loader");
                    $http({
                      url: window.baseUrl + "/_inc/summary_report.php?action_type=VIEW&duration="+duration,
                      method: "GET"
                    })
                    .then(function (response, status, headers, config) {
                        $scope.modal_title = "Summary Report > " + title;
                        $scope.rawHtml = $sce.trustAsHtml(response.data);
                        setTimeout(function() {
                            $(document).find(".btn").removeClass("selected").css('opacity', '1');
                            $(document).find("#btn_"+duration).addClass("selected").css('opacity', '0.2');
                        }, 100);
                        
                        $(document).find("body").removeClass("overlay-loader");
                    }, function (response) {
                        window.swal("Oops!", response.data.errorMsg, "error").then(function() {
                            $scope.closeSummaryReportModal();
                        });
                        $(document).find("body").removeClass("overlay-loader");
                    });
                };
                $scope.loadSummary("today", "Today");
                $scope.closeSummaryReportModal = function () {
                    $uibModalInstance.dismiss("cancel");
                };
            },
            scope: $scope,
            size: "lg",
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