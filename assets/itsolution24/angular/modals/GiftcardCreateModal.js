window.angularApp.factory("GiftcardCreateModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    return function($scope) {
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"closeGiftcardCreateModal();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\"><span class=\"fa fa-fw fa-plus\"></span> {{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $http({
                  url: window.baseUrl + "/_inc/giftcard.php?action_type=CREATE",
                  method: "GET"
                })
                .then(function(response, status, headers, config) {
                    $scope.modal_title = "Create New Giftcard";
                    $scope.rawHtml = $sce.trustAsHtml(response.data);
                    setTimeout(function() {
                        window.storeApp.datePicker();
                        window.storeApp.select2();
                        $(".random_card_no").click(function(){
                            $(this).parent(".input-group").children("input").val(window.storeApp.generateCardNo(16));
                        });
                         $(".random_card_no").trigger("click");
                    }, 500);
                }, function(response) {
                   window.swal("Oops!", response.data.errorMsg, "error");
                });

                // Submit Giftcard Form
                $(document).delegate("#create-giftcard-submit", "click", function(e) {
                    e.stopImmediatePropagation();
                    e.stopPropagation();
                    e.preventDefault();

                    var $tag = $(this);
                    var $btn = $tag.button("loading");
                    var form = $($tag.data("form"));
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
                        $scope.card_no = response.data.giftcard.card_no;
                        $btn.button("reset");
                        var alertMsg = "<div class=\"alert alert-success\">";
                        alertMsg += "<p><i class=\"fa fa-check\"></i> " + response.data.msg + ".</p>";
                        alertMsg += "</div>";
                        form.find(".giftcard-body").before(alertMsg);

                        // Sweet Alert
                        window.swal("Success", response.data.msg, "success")
                        .then(function(value) {

                            // close modalwindow
                            $scope.closeGiftcardCreateModal();
                            $(document).find(".close").trigger("click");

                            // insert giftcard into select2
                            var select = $(document).find("#giftcard_id");
                            if (select.length) {
                            
                                var option = $("<option></option>").
                                     attr("selected", true).
                                     text(response.data.giftcard.card_no).
                                     val(response.data.id);
                                option.appendTo(select);
                                select.trigger("change");
                            }

                            // increase store count
                            var giftcardCount = $(document).find("#giftcard-count h3");
                            if (giftcardCount) {
                                giftcardCount.text(parseInt(giftcardCount.text()) + 1);
                            }

                            // Callback
                            if ($scope.GiftcardCreateModalCallback) {
                                $scope.GiftcardCreateModalCallback($scope);
                            }

                        });

                    }, function(response) {
                        $btn.button("reset");
                        var alertMsg = "<div class=\"alert alert-danger\">";
                        window.angular.forEach(response.data, function(value, key) {
                            alertMsg += "<p><i class=\"fa fa-warning\"></i> " + value + ".</p>";
                        });
                        alertMsg += "</div>";
                        form.find(".giftcard-body").before(alertMsg);
                        $(":input[type=\"button\"]").prop("disabled", false);
                        window.swal("Oops!", response.data.errorMsg, "error");
                    });
                });

                $scope.closeGiftcardCreateModal = function () {
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