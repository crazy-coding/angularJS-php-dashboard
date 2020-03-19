window.angularApp.factory("BarcodePrintModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    return function(product) {

        var symbology = 'code_39';
        var limit = 20;

        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"closePrintBarcodeModal();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\"><span class=\"fa fa-fw fa-barcode\"></span> {{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $http({
                  url: window.baseUrl + "/_inc/barcode.php?code=" + product.p_id + "&symbology=" + symbology + "&limit=" + limit, // upc_a, code_39,code_93,  code_128, ean_2 
                  method: "GET",
                  dataType: "HTML",
                })
                .then(function(response, status, headers, config) {
                    $scope.modal_title = product.p_name;
                    $scope.rawHtml = $sce.trustAsHtml(response.data);

                    setTimeout(function() {
                        $(document).find(".modal-lg").css("width", "90%");
                        window.storeApp.select2();
                    }, 100);

                }, function(data) {
                   window.swal("Oops!", "an error occured!", "error");
                });

                // Load specified number of barcode
                $(document.body).delegate('.generate-barcode', 'change', function() {

                    $scope.rawHtml = 'Loading...';

                    limit = parseInt($("#barcode-limit").val());
                    symbology = $("#barcode-symbology").val();

                    $http({
                      url: window.baseUrl + "/_inc/barcode.php?code=" + product.p_id + "&symbology=" + symbology + "&limit=" + limit,
                      method: "GET",
                      dataType: "HTML",
                    })
                    .then(function(response, status, headers, config) {
                        $scope.rawHtml = $sce.trustAsHtml(response.data);

                        setTimeout(function() {
                            $(document).find(".modal-lg").css("width", "90%");
                            window.storeApp.select2();
                        }, 100);

                    }, function(data) {

                       window.swal("Oops!", "an error occured!", "error");
                    });
                });

                $scope.closePrintBarcodeModal = function () {
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