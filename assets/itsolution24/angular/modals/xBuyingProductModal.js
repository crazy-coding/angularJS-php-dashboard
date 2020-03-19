window.angularApp.factory("BuyingProductModal", ["API_URL", "window", "jQuery", "$http", "$uibModal", "$sce", "$rootScope", function (API_URL, window, $, $http, $uibModal, $sce, $scope) {
    return function ($parentData) {
        var invoice = $parentData;
        if ($parentData.product) {
            invoice = $parentData.product
        }
        var id;
        var sup_id = invoice.sup_id;
        var sup_name = invoice.sup_name;
        var taxAmount;
        var subTotal;
        var quantity;
        var unitPrice;
        var filenameDisplayer;
        var uibModalInstance = $uibModal.open({
            animation: true,
            ariaLabelledBy: "modal-title",
            ariaDescribedBy: "modal-body",
            template: "<div class=\"modal-header\">" +
                            "<button ng-click=\"closeBuyProductModal();\" type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                           "<h3 class=\"modal-title\" id=\"modal-title\"><span class=\"fa fa-fw fa-money\"></span> <small style=\"color:#fff;\"><i>Invoice for</i></small> {{ modal_title }}</h3>" +
                        "</div>" +
                        "<div class=\"modal-body\" id=\"modal-body\">" +
                            "<div bind-html-compile=\"rawHtml\">Loading...</div>" +
                        "</div>",
            controller: function ($scope, $uibModalInstance) {
                $http({
                  url: window.baseUrl + "/_inc/buying.php?sup_id=" + sup_id + "&invoice_id=" + invoice.invoice_id + '&action_type=EDIT',
                  method: "GET"
                })
                .then(function (response, status, headers, config) {
                    $(document).find("#poTable tbody").html("");
                    if (invoice.invoice_id) {
                        $scope.modal_title = sup_name + ' > ' + invoice.invoice_id;
                    } else {
                        $scope.modal_title = sup_name;
                    }
                    $scope.rawHtml = $sce.trustAsHtml(response.data);
                    setTimeout(function() {
                        window.storeApp.datePicker();
                        window.storeApp.timePicker();
                        filenameDisplayer = $(document).find('.bootstrap-filestyle input');
                        if (invoice.invoice_url) {
                            filenameDisplayer.val($(invoice.invoice_url).attr('href'));
                        }
                    }, 100);
                }, function (response) {
                    window.swal("Oops!", response.data.errorMsg, "error")
                    .then(function() {
                        $scope.closeBuyProductModal();
                    });
                });

                $http({
                    url: API_URL + "/_inc/ajax.php?type=QUANTITYCHECK",
                    method: "GET",
                    cache: false,
                    processData: false,
                    contentType: false,
                    dataType: "json"
                }).
                then(function(response) {
                    if (response.data.error == true) {
                        window.location = window.baseUrl+'/maintenance.php';
                    }
                });

                $scope.totalTax = "0";
                $scope.total = "0";
                $scope.searchBoxText = "";
                var total = "0";

                //autocomplete script
                $(document).on("focus", ".autocomplete-product", function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    var $this = $(this);
                    $this.attr('autocomplete', 'off');
                    var type = $this.data("type");
                    var autoTypeNo; 
                    if(type =="p_id" ) autoTypeNo = 0;
                    if(type =="p_name" ) autoTypeNo = 1;
                    $this.autocomplete({
                        source: function (request, response) {
                            return $http({
                                url: window.baseUrl + "/_inc/ajax.php",
                                dataType: "json",
                                method: "post",
                                data: $.param({
                                   sup_id: sup_id,
                                   name_starts_with: request.term,
                                   type: type
                                }),
                            })
                            .then(function (data) {
                                return response( $.map( data.data, function (item) {
                                    var code = item.split("|");
                                    return {
                                        label: code[autoTypeNo].replace(/&amp;/g, "&"),
                                        value: code[autoTypeNo],
                                        data : item
                                    };
                                }));
                            }, function (data) {
                               window.swal("Oops!", response.data.errorMsg, "error");
                            });
                        },
                        focusOpen: true,
                        autoFocus: true,
                        minLength: 0,
                        select: function ( event, ui ) {
                            var names = ui.item.data.split("|");
                            var data = {
                                itemId: names[0],
                                itemName: names[1],
                                itemCode: names[2],
                                categoryId: names[3],
                                itemQuantity: names[4],
                                itemPrice: names[5],
                                itemSellPrice: names[6],
                                itemTaxAmount: names[7],
                                itemTaxMethod: names[8],
                                itemTaxrate: names[9],
                            };
                            $scope.addProduct(data);
                        }, 
                        open: function () {
                            $(".ui-autocomplete").perfectScrollbar();
                        }, 
                        close: function () {
                            $(document).find(".autocomplete-product").blur();
                            $(document).find(".autocomplete-product").val("");
                        },
                    }).bind("focus", function() { 
                        $(this).autocomplete("search"); 
                    });
                });

                $(document).on("change keyup blur",".rquantity, .rcost",function (){
                    id = $(this).data("id");
                    $scope.calculate(id);
                });

                $(document).delegate(".spodel", "click", function () {
                    id = $(this).data("id");
                    $("#"+id).remove();
                    $scope.calculate(id);
                });

                var totalTax;
                var total;
                var quantity;
                var unitPrice;
                var taxAmount;
                var itemTaxMethod;
                var itemTaxAmount;
                var realItemTaxAmount;
                $scope.calculate = function (id) {
                    totalTax = 0;
                    total = 0;
                    quantity = $(document).find("#quantity_"+id);
                    unitPrice = $(document).find("#cost_"+id);
                    itemTaxMethod = $(document).find("#itemTaxMethod_"+id);
                    itemTaxrate = $(document).find("#itemTaxrate_"+id);
                    itemTaxAmount = $(document).find("#itemTaxAmount_"+id);
                    taxAmount = $(document).find("#taxAmount_"+id);
                    realItemTaxAmount = parseFloat((itemTaxrate.val() / 100 ) * parseFloat(unitPrice.val()));
                    itemTaxAmount.val(parseInt(quantity.val()) * realItemTaxAmount);
                    taxAmount.text(parseFloat(parseInt(quantity.val()) * realItemTaxAmount).toFixed(2));
                    $(document).find(".stax").each(function (i, obj) {
                        totalTax = totalTax + parseFloat($(this).text());
                    });
                    subTotal = $(document).find("#subTotal_"+id);
                    if (itemTaxMethod.val() == 'exclusive') {
                        subTotal.text(parseFloat((parseInt(quantity.val()) * parseFloat(unitPrice.val())) + parseFloat(taxAmount.text())).toFixed(2));
                    } else {
                        subTotal.text(parseFloat(parseInt(quantity.val()) * parseFloat(unitPrice.val())).toFixed(2));
                    }
                    $(document).find(".ssubTotal").each(function (i, obj) {
                        total = total + parseFloat($(this).text());
                    });
                    $scope.$apply(function () {
                        $scope.totalTax = totalTax;
                        $scope.total = total;
                    });
                };

                // add product
                var itemPrice = 0;
                $scope.addProduct = function(data) {
                    if (data.itemTaxMethod == 'exclusive') {
                        itemPrice = parseFloat(data.itemPrice) + parseFloat(data.itemTaxAmount);
                    } else {
                        itemPrice = data.itemPrice;
                    }
                    var html = "<tr id=\""+data.itemId+"\" class=\""+data.itemId+"\" data-item-id=\""+data.itemId+"\">";
                    html += "<td style=\"min-width:100px;\" data-title=\"Product Name\">";
                    html += "<input name=\"product["+data.itemId+"][id]\" type=\"hidden\" class=\"rid\" value=\""+data.itemId+"\">";
                    html += "<input name=\"product["+data.itemId+"][name]\" type=\"hidden\" class=\"rname\" value=\""+data.itemName+"\">";
                    html += "<input name=\"product["+data.itemId+"][category_id]\" type=\"hidden\" class=\"categoryid\" value=\""+data.categoryId+"\">";
                    html += "<span class=\"sname\" id=\"name_"+data.itemId+"\">"+data.itemName+"-"+data.itemCode+"</span>";
                    html += "</td>";
                    html += "<td class=\"text-center\" data-title=\"Available\">";
                    html += "<span class=\"savailable\" id=\"available_"+data.itemId+"\">"+data.itemQuantity+"</span>";
                    html += "</td>";
                    html += "<td style=\"padding:2px;\" data-title=\"Product Name\">";
                    html += "<input class=\"form-control input-sm text-center rquantity\" name=\"product["+data.itemId+"][quantity]\" type=\"number\" value=\"1\" data-id=\""+data.itemId+"\" id=\"quantity_"+data.itemId+"\" onclick=\"this.select();\" onKeyUp=\"if(this.value<0){this.value=0;}\">";
                    html += "</td>";
                    html += "<td style=\"padding:2px; min-width:80px;\" data-title=\"Buy Price\">";
                    html += "<input class=\"form-control input-sm text-center rcost\" name=\"product["+data.itemId+"][cost]\" type=\"text\" value=\""+data.itemPrice+"\" data-id=\""+data.itemId+"\" data-item=\""+data.itemId+"\" id=\"cost_"+data.itemId+"\" onclick=\"this.select();\">";
                    html += "</td>";
                    html += "<td style=\"padding:2px; min-width:80px;\" data-title=\"Sell Price\">";
                    html += "<input class=\"form-control input-sm text-center rsell\" name=\"product["+data.itemId+"][sell]\" type=\"text\" value=\""+data.itemSellPrice+"\" data-id=\""+data.itemId+"\" data-item=\""+data.itemId+"\" id=\"sell_"+data.itemId+"\" onclick=\"this.select();\">";
                    html += "</td>";
                    html += "<td class=\"text-right\" data-title=\"Tax\">";
                    html += "<input id=\"itemTaxMethod_"+data.itemId+"\" name=\"product["+data.itemId+"][item_tax_method]\" type=\"hidden\" value=\""+data.itemTaxMethod+"\">";
                    html += "<input id=\"itemTaxrate_"+data.itemId+"\" name=\"product["+data.itemId+"][item_taxrate]\" type=\"hidden\" value=\""+data.itemTaxrate+"\">";
                    html += "<input id=\"itemTaxAmount_"+data.itemId+"\" name=\"product["+data.itemId+"][item_tax_amount]\" type=\"hidden\" value=\""+data.itemTaxAmount+"\">";
                    html += "<span class=\"stax\" id=\"taxAmount_"+data.itemId+"\">"+window.formatDecimal(data.itemTaxAmount,2)+"</span>";
                    html += "</td>";
                    html += "<td class=\"text-right\" data-title=\"Total\">";
                    html += "<span class=\"ssubTotal\" id=\"subTotal_"+data.itemId+"\">"+window.formatDecimal(itemPrice,2)+"</span>";
                    html += "</td>";    
                    html += "<td class=\"text-center\">";
                    html += "<i class=\"fa fa-close text-red pointer spodel\" data-id=\""+data.itemId+"\" title=\"Remove\"></i>";
                    html += "</td>";
                    html += "</tr>";

                    totalTax = parseFloat($scope.totalTax) + parseFloat(data.itemTaxAmount);
                    total = parseFloat($scope.total) + parseFloat(itemPrice);
                    var quantity, unitPrice;
                    // update existing if find
                    if ($("#"+data.itemId).length) {
                        quantity = $(document).find("#quantity_"+data.itemId);
                        unitPrice = $(document).find("#cost_"+data.itemId);
                        itemTaxAmount = $(document).find("#itemTaxAmount_"+data.itemId);
                        taxAmount = $(document).find("#taxAmount_"+data.itemId);
                        subTotal = $(document).find("#subTotal_"+data.itemId);
                        quantity.val(parseInt(quantity.val()) + 1);
                        itemTaxAmount.val(parseFloat(taxAmount.text()) + parseFloat(data.itemTaxAmount));
                        taxAmount.text(parseFloat(taxAmount.text()) + parseFloat(data.itemTaxAmount));
                        subTotal.text(parseFloat(subTotal.text()) + parseFloat(itemPrice));
                    } else {
                        $(document).find("#poTable tbody").append(html);
                    }
                    $scope.$apply(function () {
                        $scope.totalTax = totalTax;
                        $scope.total = total;
                    });
                };

                // add product to invoice when call from product
                if (invoice.p_id) {
                    if (timer) {
                        window.clearInterval(timer);
                    }
                    var timer = window.setInterval(function(){
                        if ($(document).find("#poTable").length) {
                            var data = {
                                itemId: invoice.p_id,
                                categoryId: invoice.category_id,
                                itemName: invoice.p_name,
                                itemCode: invoice.p_code,
                                itemQuantity: invoice.quantity_in_stock,
                                itemPrice: invoice.buy_price,
                                itemSellPrice: invoice.sell_price,
                                itemTaxMethod: invoice.tax_method,
                                itemTaxrate: invoice.taxrate,
                                itemTaxAmount: invoice.buy_tax_amount,
                            };
                            $scope.addProduct(data);
                            window.clearInterval(timer);
                        }
                    }, 100);
                }

                // Buying Confirm
                $(document).delegate("#buying-confirm-btn", "click", function (e) {
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
                        data: new FormData(form[0]),
                        cache: false,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        headers: {
                            'Content-Type': undefined
                        }
                    }).
                    then(function (response) {
                        var invoiceId = response.data.id;
                        $btn.button("reset");
                        var alertMsg = "<div class=\"alert alert-success\">";
                        alertMsg += "<p><i class=\"fa fa-check\"></i> " + response.data.msg + "</p>";
                        alertMsg += "</div>";
                        form.find(".box-body").before(alertMsg);

                        // Alertbox
                        window.swal({
                          title: "Success!",
                          text: response.data.msg,
                          icon: "success",
                          buttons: true,
                          dangerMode: false,
                        })
                        .then(function (willDelete) {
                            if (willDelete) {
                                $scope.closeBuyProductModal();
                                $(document).find(".close").trigger("click");

                                // Callback
                                if ($parentData.BuyingProductModalCallback) {
                                    $parentData.BuyingProductModalCallback($scope);
                                }

                                if ($(datatable).length) {
                                    $(datatable).DataTable().ajax.reload(function (json) {
                                        if ($("#row_"+invoiceId).length) {
                                            $("#row_"+invoiceId).flash("yellow", 5000);
                                        }
                                    }, false);
                                }
                                    
                            } else {
                                if ($(datatable)) {
                                    $(datatable).DataTable().ajax.reload(null, false);
                                }
                            }
                        });
                    }, function (response) {
                        $btn.button("reset");
                        var alertMsg = "<div class=\"alert alert-danger\">";
                        window.angular.forEach(response.data, function (value, key) {
                            alertMsg += "<p><i class=\"fa fa-warning\"></i> " + value + ".</p>";
                        });
                        alertMsg += "</div>";
                        form.find(".box-body").before(alertMsg);
                        $(":input[type=\"button\"]").prop("disabled", false);
                        window.swal("Oops!", response.data.errorMsg, "error");
                    });
                });

                $(document).delegate("#attachment", 'change', function() {
                    var file = this.files[0];
                    var imagefile = file.type;
                    var match= ["image/jpeg","image/png","image/jpg","image/png","image/gif"];
                    if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2]))) {
                        alert("Invalid file type");
                        return false;
                    } else {
                        $scope.$apply(function () {
                            filenameDisplayer.val(file.name);
                        });
                    }
                });
                
                $scope.closeBuyProductModal = function () {
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