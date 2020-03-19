window.angularApp.factory("AddInvoiceNoteModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    return function($scope) {
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"closeAddNoteModal();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\"><span class=\"fa fa-fw fa-pencil\"></span> {{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<textarea id=\"note\" class=\"form-control\" rows=\"3\" placeholder=\"Type invoice note here...\">{{ invoiceNote }}</textarea>" +
                        "</div>" +
                        "<div class=\"modal-footer\">" +
                            "<button ng-click=\"clear();\" class=\"btn btn-warning pull-left\" type=\"button\">Cancel</button>" +
                            "<button ng-click=\"closeAddNoteModal();\" id=\"reset-btn\" name=\"reset-btn\" class=\"btn btn-success\">Ok</button>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {

                $scope.modal_title = "Add Invoice Note";            
                 var invoiceNote;

                $(document).on("change keyup blur", "#note", function () {

                    var $this = $(this);
                    invoiceNote = $this.val();

                    $("#invoice-note").data("note", invoiceNote);
                    $scope.invoiceNote = invoiceNote;
                });

                $scope.closeAddNoteModal = function () {
                    $uibModalInstance.dismiss("cancel");
                };

                $scope.clear = function () {
                    $("#invoice-note").data("note", "");
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