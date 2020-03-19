window.angularApp.controller("PosController", [
    "$scope", 
    "API_URL", 
    "window", 
    "jQuery",
    "$compile",
    "$uibModal",
    "$http",
    "$sce",
    "ProductCreateModal",
    "CategoryCreateModal",
    "BoxCreateModal",
    "SupplierCreateModal",
    "ProductViewModal",
    "ProductEditModal",
    "CustomerCreateModal",
    "CustomerEditModal",
    "BuyingProductModal",
    "AddInvoiceNoteModal",
    "AddCustomerMobileNumberModal",
    "PaymentFormModal",
    "GiftcardCreateModal",
    "GiftcardViewModal",
function (
    $scope,
    API_URL,
    window,
    $,
    $compile,
    $uibModal,
    $http,
    $sce,
    ProductCreateModal,
    CategoryCreateModal,
    BoxCreateModal,
    SupplierCreateModal,
    ProductViewModal,
    ProductEditModal,
    CustomerCreateModal,
    CustomerEditModal,
    BuyingProductModal,
    AddInvoiceNoteModal,
    AddCustomerMobileNumberModal,
    PaymentFormModal,
    GiftcardCreateModal,
    GiftcardViewModal
) {
    "use strict";

    $scope._percentage = function (amount, per)
    {
        if(false === $scope._isNumeric(amount) || false === $scope._isNumeric(per)) {
            if (window.store.sound_effect == 1) {
                window.storeApp.playSound("error.mp3");
            }
            window.toastr.warning("The discount amount isn't numeric!", "Warning!");
            return 0;
        }
        return (amount/100)*per;
    };
    $scope._isNumeric = function (val) {
      return !isNaN(parseFloat(val)) && 'undefined' !== typeof val ? parseFloat(val) : false;
    };
    $scope._isInt = function (value) {
        return !isNaN(value) && 
             parseInt(Number(value)) == value && 
             !isNaN(parseInt(value, 10));
    };
    $scope.error = false;
    $scope.isEditMode = false;
    $scope.invoiceId = null;
    $scope.orderData = {};
    $scope.billData = {};
    $scope.orderData.store_name = window.store.name;
    $scope.orderData.header = "\nOrder\n\n";
    $scope.billData.store_name = window.store.name;
    $scope.billData.header = "\nBill\n\n";
    $scope.billData.footer = "\nMerchant Copy\n\n";

    // ===============================================
    // Start Customer dropdown list
    // ===============================================

    $scope.customerName = "";
    $scope.customerMobileNumber = "";
    $scope.dueAmount = 0;
    $scope.hideCustomerDropdown = false;
    $scope.customerArray = [];
    $scope.showCustomerList = function (isClick) {
        if ($scope.isEditMode) { return false; }
        if (isClick) { $scope.customerName = ""; }
        if (window.deviceType == 'computer') {
            $("#customer-name").focus();
        }
        $http({
            url: API_URL + "/_inc/pos.php?query_string=" + $scope.customerName + "&field=customer_name&action_type=CUSTOMERLIST&limit=30",
            method: "GET",
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json"
        }).
        then(function(response) {
            $scope.hideCustomerDropdown = false;
            $scope.customerArray = [];
            window.angular.forEach(response.data, function(customerItem, key) {
                if (customerItem) {
                    $("#customer-dropdown").scrollTop($("#customer-dropdown").offset().top);
                    $("#customer-dropdown").perfectScrollbar("update");
                    $scope.customerArray.push(customerItem);
                }
            });

        }, function(response) {
            if (window.store.sound_effect == 1) {
                window.storeApp.playSound("error.mp3");
            }
            window.toastr.warning(response.data.errorMsg, "Warning!");
        });
    };
    $("#customer-name").on("click", function() {
        $scope.showCustomerList(true);
    });
    $scope.addCustomer = function (customer) {
        if ($scope._isInt(customer)) {
            $http({
                url: API_URL + "/_inc/pos.php?customer_id="+customer+"&action_type=CUSTOMER",
                method: "GET",
                cache: false,
                processData: false,
                contentType: false,
                dataType: "json"
            }).
            then(function(response) {
                if (response.data.customer_id) {
                    $scope.addCustomer(response.data);
                }
            }, function(response) {
                if (window.store.sound_effect == 1) {
                    window.storeApp.playSound("error.mp3");
                }
                window.toastr.warning(response.data.errorMsg, "Warning!");
            });
        } else if (customer.customer_id) {
            var contact = customer.customer_mobile || customer.customer_email;
            $scope.customerName = customer.customer_name + " (" + contact + ")";
            $scope.customerEmail = customer.customer_email;
            $scope.customerId = customer.customer_id;
            if (window._.includes(customer.balance, '-')) {
                $scope.dueAmount = parseFloat(Math.abs(customer.balance));
            } else {
                $scope.dueAmount = 0;
            }
            $scope.hideCustomerDropdown = true;
            var pos_customer = "C: " + $scope.customerName + "\n";
            var ob_info = pos_customer+ "\n";
            $scope.orderData.info = ob_info;
            $scope.billData.info = ob_info;
            $scope._calcTotalPayable();
        } else {
            if (window.store.sound_effect == 1) {
                window.storeApp.playSound("error.mp3");
            }
            window.toastr.warning("Oops!, Invalid customer", "Warning!");
        }
    };
    if (window.getParameterByName("customer_id")) {
        $scope.addCustomer(window.getParameterByName("customer_id"));
    } else {
        // add walking customer to invoice
        $scope.addCustomer(1);
    }

    // ===============================================
    // End Customer dropdown list
    // ===============================================

    // ===============================================
    // Start Product list
    // ===============================================

    $scope.productName = "";
    $scope.productArray = [];
    $scope.showLoader = !1;
    $scope.showAddProductBtn = !1;
    $scope.totalProduct = 0;
    var page = 1;
    $scope.showProductList = function (page,url) {
        $(".pos-product-pagination").empty();
        page = page ? page : 1;
        $scope.productArray = [];
        $scope.showLoader = 1;
        var productCode = $scope.productName;
        var categoryId = $scope.ProductCategoryID ? $scope.ProductCategoryID : '';
        if ($scope._isInt(productCode)) {
            if (productCode.length == 16 || productCode.length == 14 || productCode.length == 13 || productCode.length == 8) {
                $scope.addItemToInvoice(productCode);
                $scope.productName = null;
            }     
        } else {
            $http({
                url: url ? url : API_URL + "/_inc/pos.php?query_string=" + productCode + "&category_id=" + categoryId + "&field=p_name&action_type=PRODUCTLIST&page="+page,
                method: "GET",
                cache: false,
                processData: false,
                contentType: false,
                dataType: "json"
            }).
            then(function(response) {
                window.angular.forEach(response.data.products, function(productItem, key) {
                  if (productItem) {
                    $scope.productArray.push(productItem);
                  }
                });
                $("#item-list").perfectScrollbar('update');
                $scope.totalProduct = $scope.productArray.length;
                $scope.showLoader = !1;
                setTimeout(function () {
                    $scope.$apply(function(){
                        $scope.showAddProductBtn = !parseInt($scope.totalProduct);
                    });
                }, 100);
                $(".pos-product-pagination").html(response.data.pagination);
            }, function(response) {
                if (window.store.sound_effect == 1) {
                    window.storeApp.playSound("error.mp3");
                }
                window.toastr.warning(response.data.errorMsg, "Warning!");
            });
        }
    };
    $scope.showProductList();

    $(document).delegate(".pos-product-pagination li > a", "click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        var $this = $(this);
        var actionURL = $this.attr("href");
        $scope.showProductList(null,actionURL);
    });

    $scope.CustomerEditModal = function() {
        $scope.customer_name = $scope.customerName;
        $scope.customer_id = $scope.customerId;
        CustomerEditModal($scope);
    };

    $("#category-search-select").on('select2:selecting', function(e) {
        var categoryID = e.params.args.data.id;
        $scope.ProductCategoryID = categoryID;
        $scope.showProductList();
    });

    // ===============================================
    // End Product list
    // ===============================================

    // ===============================================
    // Start Invoice Calculation
    // ===============================================

    $scope.itemArray        = [];
    $scope.totalItem        = 0;
    $scope.totalQuantity    = 0;
    $scope.totalAmount      = 0;
    $scope.discountAmount   = 0;
    $scope.taxAmount        = 0;
    $scope.totalPayable     = 0;
    $scope.taxInput         = 0;
    $scope.discountInput    = 0;
    $scope.discountType    = 'plain';
    $scope._calcDisAmount = function () {
        if (window._.includes($scope.discountInput, '%')) {
            $scope.discountType = 'percentage';
        } else {
            $scope.discountType = 'plain';
        }
        if ($scope.discountInput < 1) {
            $scope.discountAmount = 0;
            $scope.discountInput = 0;
        } else {
            $scope.discountAmount = parseFloat($scope.discountInput);
        }
    };
    $scope._calcTaxAmount = function () {
        if ($scope.taxInput < 1 || $scope.taxInput > 100) {
            $scope.taxAmount = 0;
            $scope.taxInput = 0;
        } else {
            $scope.taxAmount = (parseFloat($scope.taxInput) / 100) * parseFloat($scope.totalAmount);
        }
    };
    $scope._calcTotalPayable = function () {
        var discountPercentage = 0;
        $scope._calcDisAmount();
        $scope._calcTaxAmount();
        $scope.totalPayable = ($scope.totalAmount  + $scope.taxAmount);
        if ($scope.totalPayable != 0 && ($scope.discountAmount >= $scope.totalPayable)) {
            $scope.discountAmount = 0;
            $scope.discountInput = 0;
            if (window.store.sound_effect == 1) {
                window.storeApp.playSound("error.mp3");
            }
            window.toastr.warning("Discount amount must be less than payable amount", "Warning!");
        }
        if ($scope.discountType == 'percentage') {
            discountPercentage =  parseFloat($scope._percentage($scope.totalPayable, $scope.discountAmount));
        } else {
            discountPercentage =  parseFloat($scope.discountAmount);
        }
        $scope.totalPayable =  $scope.totalPayable - discountPercentage;
        $scope.billData.totals = "Grand Total   " + $scope.totalPayable;
    };
    $scope.addDiscount = function () {
        $scope._calcTotalPayable();
    };
    $scope.addTax = function () {
        $scope._calcTotalPayable();
    };
    $scope.itemQuantity = 0;
    $scope.isPrevQuantityCalcculate = false;
    $scope.prevQuantity = 0;
    $scope.itemListHeight = 0;

    // ===============================================
    // End Invoice Calculation
    // ===============================================


    // ===============================================
    // Start Add Product to Invoice
    // ===============================================

    $scope.addItemToInvoice = function (id, qty, index) {
        var qty = parseInt(qty);
        if (!qty) {
            qty = 1;
        }
        if (index != null) {
            var selectItem = $("#"+index);
            $("#item-list .item").removeClass("select");
            if (selectItem.length) {
                selectItem.addClass("select");
            }
        }
        var $queryString = "p_id=" + id + "&action_type=PRODUCTITEM";
        if (window.getParameterByName("invoice_id")) {
            $queryString += "&is_edit_mode=1";
        }
        $http({
            url: API_URL + "/_inc/pos.php?" + $queryString,
            method: "GET",
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json"
        }).
        then(function(response) {
            if (response.data.p_id) {
                var find = window._.find($scope.itemArray, function (item) { 
                    return item.id == response.data.p_id;
                });
                if (find) {
                    window._.map($scope.itemArray, function (item) {
                        if (item.id == response.data.p_id) {
                            if (!$scope.isPrevQuantityCalcculate && window.getParameterByName("customer_id") && window.getParameterByName("invoice_id")) {
                                $scope.isPrevQuantityCalcculate = true;
                                $scope.prevQuantity = item.quantity;
                            }
                            $scope.itemQuantity = item.quantity - $scope.prevQuantity;
                            if (qty > response.data.quantity_in_stock || $scope.itemQuantity >= response.data.quantity_in_stock) {
                                if (window.store.sound_effect == 1) {
                                    window.storeApp.playSound("error.mp3");
                                }
                                window.toastr.warning("This product is out of stock!", "Warning!");
                                return false;
                            }
                            if (window.store.sound_effect == 1) {
                                window.storeApp.playSound("access.mp3");
                            }
                            item.quantity = parseInt(item.quantity) + qty;
                            $("#item_quantity_"+item.id).val(item.quantity);
                            var taxamount = 0;
                            if (response.data.tax_method == 'exclusive') {
                                taxamount = parseFloat(response.data.tax_amount);
                            }
                            item.subTotal = (item.subTotal + (parseFloat(response.data.sell_price) * qty)) + taxamount;
                            $scope.totalQuantity = $scope.totalQuantity + qty;
                            $scope.totalAmount = $scope.totalAmount + (parseFloat(response.data.sell_price) * qty) + taxamount;
                        }
                    });
                } else {
                    if (qty > response.data.quantity_in_stock) {
                        if (window.store.sound_effect == 1) {
                            window.storeApp.playSound("error.mp3");
                        }
                        window.toastr.warning("This product is out of stock!", "Warning!");
                        return false;
                    }
                    if (window.store.sound_effect == 1) {
                        window.storeApp.playSound("access.mp3");
                    }
                    var taxamount = 0;
                    if (response.data.tax_method == 'exclusive') {
                        taxamount = parseFloat(response.data.tax_amount);
                    }
                    var item = [];
                    item.id = response.data.p_id;
                    item.categoryId = response.data.category_id;
                    item.supId = response.data.sup_id;
                    item.name = response.data.p_name;
                    item.taxamount = taxamount;
                    item.price = parseFloat(response.data.sell_price) + taxamount;
                    item.quantity = qty;
                    item.subTotal = (parseFloat(response.data.sell_price) * qty) + taxamount;
                    $scope.totalQuantity = $scope.totalQuantity + qty;
                    $scope.totalAmount = $scope.totalAmount + (parseFloat(response.data.sell_price) * qty) + item.taxamount;
                    $scope.itemArray.push(item);
                }
                $scope.totalItem = window._.size($scope.itemArray);
                $scope._calcTotalPayable();
                $scope.productName = '';
                if (window.deviceType == 'computer') {
                    $("#product-name").focus();
                }
                var ele = $("#invoice-item-"+response.data.p_id).parent();  
                if (ele.length) {
                    $scope.itemListHeight = ele.position().top;
                } else {
                    $scope.itemListHeight += 61;
                }    
                $("#invoice-item-list").animate({ scrollTop: $scope.itemListHeight }, 1).perfectScrollbar("update");
                setTimeout(function() {
                    if (!ele.length) {
                        ele = $("#invoice-item-list table tr:last-child");
                    }
                    var flashColor = "#26ff9c";
                    var originalColor = ele.css("backgroundColor");
                    ele.css("backgroundColor", flashColor);
                    setTimeout(function (){
                      ele.css("backgroundColor", originalColor);
                    }, 300);
                }, 100);
                $scope.billData.totals += "\n\nItems: " + $scope.totalItem + " (" + $scope.totalQuantity +")\n";
                $scope.setBillandOrderItems();
            }
            $scope.showLoader = !1;

            // window.onbeforeunload = function() {
            //   return "Data will be lost if you leave the page, are you sure?";
            // };
            
        }, function(response) {
            if (window.store.sound_effect == 1) {
                window.storeApp.playSound("error.mp3");
            }
            window.toastr.warning(response.data.errorMsg, "Warning!");
            $scope.showLoader = !1;
        });
    };

    // ===============================================
    // End Add Product to Invoice
    // ===============================================


    // ============================================
    // Start Decrease Invoice Item Quantity
    // ============================================

    $scope.DecreaseItemFromInvoice = function (id, qty) {
        var qty = parseInt(qty);
        if (!qty) {
            qty = 1;
        }
        if (id) {
            var find = window._.find($scope.itemArray, function (item) {
                return item.id == id;
            });
            if (find) {
                window._.map($scope.itemArray, function (item) {
                    if (item.id == id) {
                        if (item.quantity > 1) {
                            if (window.store.sound_effect == 1) {
                                window.storeApp.playSound("modify.mp3");
                            }
                            item.quantity = parseInt(item.quantity) - qty;
                            $("#item_quantity_"+item.id).val(item.quantity);
                            item.subTotal = item.subTotal - (parseFloat(item.price) * qty);
                            $scope.totalQuantity = $scope.totalQuantity - qty;
                            $scope.totalAmount = $scope.totalAmount - parseFloat(item.price);
                        } else {
                            if (window.store.sound_effect == 1) {
                                window.storeApp.playSound("error.mp3");
                            }
                            window.toastr.warning("Quantity can't be less than 1", "Warning!");
                        }
                    }
                });
            }
            $scope.totalItem = window._.size($scope.itemArray);
            $scope._calcTotalPayable();
        }
    };

    // ============================================
    // End Decrease Invoice Item Quantity
    // ============================================


    // ====================================================
    // Start Add Product to Invoice by Invoice ID
    // ====================================================

    if (window.getParameterByName("invoice_id")) {
        $scope.invoiceId = window.getParameterByName("invoice_id");
        $http({
            url: API_URL + "/_inc/invoice.php?action_type=EDIT&invoice_id=" + $scope.invoiceId,
            method: "GET",
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json"
        }).
        then(function(response) {
            $scope.customerName = response.data.invoice.customer_name;
            $scope.customerId = response.data.invoice.customer_id;
            $scope.totalPayable = window.getNumber(response.data.invoice.payable_amount);
            // $scope.dueAmount = window.getNumber(response.data.invoice.due);
            $scope.totalPayable = window.getNumber(response.data.invoice.payable_amount);
            $scope.totalAmount = window.getNumber(response.data.invoice.subtotal);
            $scope.taxInput = window.formatDecimal((window.getNumber(response.data.invoice.order_tax) / window.getNumber(response.data.invoice.subtotal)) * 100, 2);
            if (response.data.invoice.discount_type == 'percentage') {
                $scope.discountType = 'percentage';
                $scope.discountAmount = (window.getNumber(response.data.invoice.discount_amount) / $scope.totalAmount) * 100;
                $scope.discountInput = $scope.discountAmount+'%';
            } else {
                $scope.discountType = 'plain';
                $scope.discountAmount = window.getNumber(response.data.invoice.discount_amount);
                $scope.discountInput = $scope.discountAmount;
            }
            $scope.taxAmount = response.data.invoice.order_tax;
            window.angular.forEach(response.data.invoice.items, function(productItem) {
                if (productItem) {
                    var item = [];
                    item.id = productItem.item_id;
                    item.categoryId = productItem.category_id;
                    item.supId = productItem.sup_id;
                    item.name = productItem.item_name;
                    item.price = window.getNumber(productItem.item_price);
                    item.quantity = window.getNumber(productItem.item_quantity);
                    item.subTotal = parseFloat(productItem.item_price) * productItem.item_quantity;
                    $scope.totalItem = $scope.totalItem + 1;
                    $scope.totalQuantity = $scope.totalQuantity + parseInt(productItem.item_quantity);
                    $scope.itemArray.unshift(item);
                }
            });

        }, function(response) {
            if (window.store.sound_effect == 1) {
                window.storeApp.playSound("error.mp3");
            }
            window.toastr.warning(response.data.errorMsg, "Warning!");
            window.location = window.baseUrl + "/"+window.adminDir+"/pos.php";
        });
    }

    // ====================================================
    // End Add Product to Invoice by Invoice ID
    // ====================================================


    // ===================================================
    // Start Remove Item from Invoice
    // ===================================================

    $scope.removeItemFromInvoice = function (index, id) {
        if (window.store.sound_effect  == 1) {
            window.storeApp.playSound("modify.mp3");
        }
        if ($scope.isEditMode) {
            if ($scope.itemArray.length <= 1) {
                if (window.store.sound_effect  == 1) {
                    window.storeApp.playSound("error.mp3");
                }
                window.toastr.warning("Last item can not be removed!", "Warning!");
                return false;
            }
        }
        window._.map($scope.itemArray, function (item, key) {
            if (item.id == id) {
                $scope.totalQuantity = $scope.totalQuantity - item.quantity;
                $scope.totalAmount = $scope.totalAmount - parseFloat(item.subTotal);
                $scope.totalItem = $scope.totalItem - 1;
            }
        });
        $scope._calcTotalPayable();
        $scope.itemArray.splice(index, 1);
        $scope.totalItem = window._.size($scope.itemArray);
        $scope.setBillandOrderItems();
    };

    // if invocie edit mode then disable customer dropdown
    if (window.getParameterByName("customer_id") && window.getParameterByName("invoice_id")) {
        $scope.isEditMode = true;
    }

    // ===================================================
    // End Remove Item from Invoice
    // ===================================================

    
    // ============================================
    // Start Reset POS
    // ============================================

    $scope.resetPos = function () {
        if (window.getParameterByName("customer_id")) {
            window.location = "pos.php";
        } else {
            $scope.customerArray    = [];
            $scope.itemArray        = [];
            $scope.invoiceId        = "";
            $scope.invoiceNote      = "";
            $scope.hideCustomerDropdown = true;
            $scope.dueAmount        = 0;
            $scope.customerName     = "";
            $scope.customerId       = "";
            $scope.totalItem        = 0;
            $scope.totalQuantity    = 0;
            $scope.totalAmount      = 0;
            $scope.discountAmount   = 0;
            $scope.totalPayable     = 0;
            $scope.discountInput    = 0;
            $scope.addCustomer(1);
            $("#invoice-note").data("note", "");
            $scope.resetBillandOrderItems();
            $scope.showProductList();
        }
    };

    // ============================================
    // End Reset POS
    // ============================================


    // =============================================
    // Start Context Menu by Pay Button Right Click
    // =============================================

    $('#pay-button').contextMenu({
        selector: 'button', 
        callback: function(key, options) {
            switch(key) {
                case "reset":
                    $scope.resetPos();
                break;
            }
        },
        items: {
            "reset": {name: "Reset Invoice", icon: "fa-circle-o"},
        }
    });

    // =============================================
    // End Context Menu by Pay Button Right Click
    // =============================================


    // =============================================
    // Start Print Popup Window
    // =============================================

    $scope.popupWindow = function(data) {
        var mywindow = window.open('', 'pos_print', 'height=500,width=300');
        mywindow.document.write('<html><head><title>Print</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        mywindow.close();
        return true;
    };

    // =============================================
    // End Print Popup Window
    // =============================================


    // ============================================
    // Start Bill and Order Items
    // ============================================

    $scope.setBillandOrderItems = function() {
        $scope.orderData.items = '';
        $scope.billData.items = '';
        var billaOrderinc = 1;
        var orderItem = '';
        var billItem = '';
        window._.map($scope.itemArray, function (item, key) {
            if (item.id) {
                orderItem += "#";
                orderItem += billaOrderinc;
                orderItem += " - " + item.name;
                orderItem += "\n   ";
                orderItem += '[' + item.quantity + ']';
                orderItem += "\n\n";
                billItem += "#";
                billItem += billaOrderinc;
                billItem += " - " + item.name;
                billItem += "\n   ";
                billItem += item.quantity;
                billItem += " x " + item.price;
                billItem += "   " + item.subTotal;
                billItem += "\n";
            }
            billaOrderinc++;
        });
        $scope.orderData.items = orderItem;
        $scope.billData.items = billItem;
    };
    $scope.resetBillandOrderItems = function() {
        $scope.orderData.header = '';
        $scope.orderData.footer = '';
        $scope.orderData.info = '';
        $scope.orderData.items = {};
        $scope.billData.items = {};
        $scope.orderData.store_name = '';
        $scope.orderData.totals = '';
    };

    // ============================================
    // End Bill and Order Items
    // ============================================


    // =============================================
    // Start Order and Bill Print
    // =============================================

    $scope.printOrder = function () {
        $.each(window.orderPrinters, function() {
            var socket_data = { 
                'printer': this,
                'logo': '',
                'text': $scope.orderData 
            };
            $.get(window.baseUrl+'_inc/print.php', {data: JSON.stringify(socket_data)});
        });
        return false;
    };

    $scope.printBill = function () {
        var socket_data = {
            'printer': window.printer,
            'logo': '',
            'text': $scope.billData
        };
        $.get(window.baseUrl+'/_inc/print.php', {data: JSON.stringify(socket_data)});
        return false;
    };

    $scope.clickOnPrintOrder = function () {
        if ($scope.itemArray.length <= 0) {
            if (window.store.sound_effect == 1) {
                window.storeApp.playSound("error.mp3");
            }
            window.toastr.warning("Please, select at least one product item", "Warning!");
            return false;
        }
        if (window.store.remote_printing != 1) {
            $scope.printOrder();
        } else {
            $scope.popupWindow($('#order_tbl').html());
        }
    };

    $scope.clickOnPrintBill = function () {
        if ($scope.itemArray.length <= 0) {
            if (window.store.sound_effect == 1) {
                window.storeApp.playSound("error.mp3");
            }
            window.toastr.warning("Please, select at least one product item", "Warning!");
            return false;
        }
        if (window.store.remote_printing != 1) {
            $scope.printBill();
        } else {
            $scope.popupWindow($('#bill_tbl').html());
        }
    };

    // =============================================
    // End Order and Bill Print
    // =============================================


    // =============================================
    // Start Popup Invoice Payment Form
    // =============================================

    $scope.payNow = function() {
        $scope.invoiceNote = $("#invoice-note").data("note");
        if ($scope.itemArray.length <= 0) {
            if (window.store.sound_effect == 1) {
                window.storeApp.playSound("error.mp3");
            }
            window.toastr.warning("Please, select at least one product item", "Warning!");
            return false;
        }
        if (!$scope.customerName) {
            if (window.store.sound_effect == 1) {
                window.storeApp.playSound("error.mp3");
            }
            window.toastr.warning("Please, select a customer", "Warning!");
            return false;
        } 
        $scope.customerId = $(document).find("input[name=\"customer-id\"]").val();
        if ($("#customer-mobile-number").val()) {
            $scope.customerMobileNumber = $("#customer-mobile-number").val();
        }
        setTimeout(function() {
            PaymentFormModal($scope);
        }, 300);
    }

    // =============================================
    // End Popup Invoice Payment Form
    // =============================================


    // =============================================
    // Start Input Item Quantity Manually
    // =============================================

    $scope.triggerKeyup = false;
    $(document).delegate(".item_quantity", "keyup", function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        var  itemid = $(this).data("itemid");
        var  itemquantity = $(this).val();
        var totalAmount = 0;
        window._.map($scope.itemArray, function (item) {
            if (item.id == itemid) {
                item.quantity = itemquantity;
                item.subTotal = item.price * itemquantity;
                $scope.$applyAsync(function() {
                    $scope.itemArray = $scope.itemArray;
                });
            }
            totalAmount += item.subTotal;
            $scope.$applyAsync(function() {
                $scope.totalAmount = totalAmount;
                $scope._calcTotalPayable();
            });
        });
        if ($scope.triggerKeyup == false) {
            $scope.error = false;
        } else {
            $scope.triggerKeyup = false;
        }
    });
    $(document).on('click', function(e) {
        if ($scope.error == false) {
            window._.map($scope.itemArray, function (item) {
                var itemquantity = parseInt($("#item_quantity_"+item.id).val());
                var $queryString = "p_id=" + item.id + "&action_type=PRODUCTITEM";
                $http({
                    url: API_URL + "/_inc/pos.php?" + $queryString,
                    method: "GET",
                    cache: false,
                    processData: false,
                    contentType: false,
                    dataType: "json"
                }).
                then(function(response) {
                    if (response.data.p_id) {
                        if (itemquantity > response.data.quantity_in_stock || $scope.itemQuantity >= response.data.quantity_in_stock) {
                            if ($scope.error == false) {
                                $scope.error = true;
                                $scope.triggerKeyup = true;
                                $("#item_quantity_"+item.id).val(response.data.quantity_in_stock).trigger("keyup");
                                $(document).trigger("click");
                                if (window.store.sound_effect == 1) {
                                    window.storeApp.playSound("error.mp3");
                                }
                                window.toastr.warning("This product is out of stock!", "Warning!");
                            }
                        } else {
                            $scope.error = true;
                            $scope.triggerKeyup = true;
                        }
                    }
                });
            });
        }
    });

    // =============================================
    // End Input Item Quantity Manually
    // =============================================    


    // =============================================
    // Start Input Item Price Manually
    // =============================================    

    if (window.settings.change_item_price_while_billing == 1) {
        $(document).delegate(".item_price", "keyup", function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            var  itemid = $(this).data("itemid");
            var  itemprice = $(this).val();
            var totalAmount = 0;
            window._.map($scope.itemArray, function (item) {
                if (item.id == itemid) {
                    item.price = itemprice;
                    item.subTotal = item.quantity * itemprice;
                    $scope.$apply(function() {
                        $scope.itemArray = $scope.itemArray;
                    });
                }
                totalAmount += item.subTotal;
                $scope.$apply(function() {
                    $scope.totalAmount = totalAmount;
                    $scope._calcTotalPayable();
                });
            });
        });
    }

    // =============================================
    // End Input Item Price Manually
    // =============================================    

    
    // // =============================================
    // // start opening customer due paid form
    // // =============================================

    // $scope.duePaid = function() {
    //     if ($scope.dueAmount > 0) {
    //         var customer = {
    //             id: $scope.customerId,
    //             name: $scope.customerName,
    //             dueAmount: $scope.dueAmount,
    //         };
    //         CustomerDuePaidModal(customer);
    //     }
    // };

    // // =============================================
    // // start opening customer due paid form
    // // =============================================


    // =============================================
    // Start Popup Customer Mobile Number Edit Modal
    // =============================================

    $(document).delegate("#add-customer-mobile-number-handler", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        $scope.customerMobileNumber = $("#customer-mobile-number").val();
        AddCustomerMobileNumberModal($scope);
    })

    // =============================================
    // End Popup Customer Mobile Number Edit Modal
    // =============================================


    // =============================================
    // Start Popup Invoice Note Modal
    // =============================================

    $scope.addInvoiceNote = function() {
        $scope.invoiceNote = $("#invoice-note").data("note");
        AddInvoiceNoteModal($scope);
    };

    // =============================================
    // End Popup Invoice Note Modal
    // =============================================


    // =============================================
    // Start Custom Command Handler for Context Menu
    // =============================================

    $.contextMenu.types.label = function(item, opt, root) {
        $("<span>Quantity<div>"
        + "<div class=\"input-group input-group-sm\">"
        + "<input class=\"form-control\" type=\"text\" name=\"add-quantity\" value=\"1\" onClick=\"this.select()\" onKeyUp=\"if(this.value<0 || this.value>100000){this.value=1}\">"
        +   "<span class=\"input-group-btn\">"
        +    "<button class=\"btn btn-default add\" type=\"button\">Add</button>"
        +  "</span>"
        +  "</div>")
        .appendTo(this)
        .on("click", "button", function() {
            var itemQuantity = $(this).parent().parent().find("input[name=\"add-quantity\"]").val();
            if (!itemQuantity || !$scope._isInt(itemQuantity) || itemQuantity <= 0) {

                if (window.store.sound_effect == 1) {
                    window.storeApp.playSound("error.mp3");
                }
                window.toastr.warning("Quantity must be greater than 0!", "Warning!");
                return false;
            }
            $scope.addItemToInvoice($scope.productItemId, parseInt(itemQuantity));
            // hide the menu
            root.$menu.trigger("contextmenu:hide");
        });
    };

    // =============================================
    // End Custom Command Handler for Context Menu
    // =============================================

    // =============================================
    // Start Product Item Context Menu
    // =============================================

    $("#item-list").contextMenu({
        selector: "div.item", 
        callback: function(key, options) {
            var p_id = $(this).find(".item-info").data("id");
            var p_name = $(this).find(".item-info").data("name");
            switch(key) {
                case "view":
                    ProductViewModal({p_id:p_id,p_name:p_name});
                break;
                case "edit":
                    ProductEditModal({p_id:p_id,p_name:p_name});
                break;
                case "add":
                    $scope.addItemToInvoice(p_id, 1);
                break;
            }
        },
        items: {
            "add": {name: "Add 1 (one) Item", icon: "fa-plus"},
            "sep1": "---------",
            "add_specific_amount": {name: "Add Specific Quantity", icon: "fa-th", disabled: true},
            "quantity": {
                type: "label", 
                customName: "Quantity", callback: function() { 
                    $scope.productItemId = $(this).find(".item-info").data("id");
                    return false; 
                },
            },
            "view": {name: "View", icon: "fa-eye"},
            "sep2": "---------",
            "edit": {name: "Edit", icon: "fa-pencil"}
        }
    });

    // =============================================
    // End Product Item Context Menu
    // =============================================

    $scope.BuyingProductModalCallback = function ($scopeData) {
        $scope.showProductList();
    };
    $scope.OpenBuyingProductModal = function() {
        $scope.sup_id = '';
        $scope.invoice_id = '';
        BuyingProductModal($scope);
    };
    $scope.ProductCreateModalCallback = function ($scopeData) {
        $scope.product = $scopeData.product;
        BuyingProductModal($scope);
    };
    $scope.createNewProduct = function () {
        $scope.hideCategoryAddBtn = true;
        $scope.hideSupAddBtn = true;
        $scope.hideBoxAddBtn = true;
        $scope.hideUnitAddBtn = true;
        $scope.hideTaxrateAddBtn = true;
        ProductCreateModal($scope);
    };

    $scope.CustomerCreateModalCallback = function(data) {
        $scope.$apply(function() {
            $scope.customerName = data.customerName;
            $scope.customerMobileNumber =  data.customerMobileNumber;
            $scope.customerId = data.customerId;
            $scope.dueAmount = data.dueAmount;
        });
    };

    // Create new product
    $scope.createNewCustomer = function () {
        if ($scope.invoiceId) return false;
        $scope.dueAmount = 0;
        $scope.addCustomer(1);
        CustomerCreateModal($scope);
    };

    // Create new category
    $scope.createNewCategory = function () {
        CategoryCreateModal($scope);
    };

    // Create new supplier
    $scope.createNewSupplier = function () {
        SupplierCreateModal($scope);
    };

    // Create new box
    $scope.createNewBox = function () {
        BoxCreateModal($scope);
    };

    // Giftcard callback
    $scope.GiftcardCreateModalCallback = function(giftcard) {
        GiftcardViewModal(giftcard);
    };
    
    // Giftcard crete modal
    $scope.GiftcardCreateModal = function() {
        GiftcardCreateModal($scope);
    };

    $http({
        url: API_URL + "/_inc/pos.php?type=STOCKCHECK",
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

    // =============================================
    // Start Keyboard Shortcut
    // =============================================

    $(document).keydown(function(e) {
        if (e.altKey && e.which == 80) { //searchbox [p]
          $("[name=\"product-name\"]").focus().select();
        } else if (e.altKey && e.which == 65) { // Create new customer [a]
            $scope.createNewCustomer();
        } else if (e.which != 16 && e.altKey && e.which == 67) { // customer search box [c]
          $("[name=\"customer-name\"]").focus().select();
        } else if (e.altKey && e.which == 73) { // discount field [i]
          $("[name=\"discount-amount\"]").focus().select();
        } else if (e.altKey && e.which == 84) { // tax field [t]
            $("[name=\"tax-amount\"]").focus().select();
        } else if (e.altKey && e.which == 78) { // invoice note [n]
            $scope.addInvoiceNote();
        } else if (e.altKey && e.which == 90) { // paynow fowm [z]
            $scope.payNow();
        }
        if (e.which == 37) { // left arrow
          var selectedItem = $("#item-list").find(".select");
          $("#item-list .item").removeClass("select");
          if (selectedItem.length) {
            var itemId = parseInt(selectedItem.attr("id"))-1;
            $("#"+itemId).addClass("select");
          } else {
            $("#0").addClass("select");
          }
        }
        if (e.which == 39) { // right arrow
          var selectedItem1 = $("#item-list").find(".select");
          $("#item-list .item").removeClass("select");
          if (selectedItem1.length) {
            var itemId1 = parseInt(selectedItem1.attr("id"))+1;
            $("#"+itemId1).addClass("select");
          } else {
            $("#0").addClass("select");
          }
        }
        if (e.which == 13 && !$('.modal').length) { // add item when press enter key, if selected any
            var selectedItem2 = $("#item-list").find(".select");
            if (selectedItem2.length) {
                var itemId2 = selectedItem2.find(".item-info").data("id");
                $scope.addItemToInvoice(itemId2, 1);
            }
        }
    });

    // =============================================
    // End Keyboard Shortcut
    // =============================================
}]);