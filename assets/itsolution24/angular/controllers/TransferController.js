window.angularApp.controller("TransferController", [
    "$scope",
    "API_URL",
    "window",
    "jQuery",
    "$compile",
    "$uibModal",
    "$http",
    "$sce",
    "TransferEditModal",
    "EmailModal", 
function (
    $scope,
    API_URL,
    window,
    $,
    $compile,
    $uibModal,
    $http,
    $sce,
    TransferEditModal,
    EmailModal
) {
    "use strict";

    var dt = $("#transfer-transfer-list");
    var transferId = null;
    var i;

    var hideColums = dt.data("hide-colums").split(",");
    var hideColumsArray = [];
    if (hideColums.length) {
        for (i = 0; i < hideColums.length; i+=1) {     
           hideColumsArray.push(parseInt(hideColums[i]));
        }
    }

    var $from = window.getParameterByName("from");
    var $to = window.getParameterByName("to");

    //================
    // start datatable
    //================

    dt.dataTable({
        "oLanguage": {sProcessing: "<img src='../assets/itsolution24/img/loading2.gif'>"},
        "processing": true,
        "dom": "lfBrtip",
        "serverSide": true,
        "ajax": API_URL + "/_inc/transfer.php?from=" + $from + "&to=" + $to,
		"fixedHeader": true,
        "order": [[ 1, "desc"]],
        "aLengthMenu": [
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        "columnDefs": [
            {"visible": false,  "targets": hideColumsArray},
            {"className": "text-right", "targets": [4, 5]},
            {"className": "text-center", "targets": [0, 1, 6]},
            { 
                "targets": [0],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#transfer-transfer-list thead tr th:eq(1)").html());
                }
            },
            { 
                "targets": [1],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#transfer-transfer-list thead tr th:eq(1)").html());
                }
            },
            { 
                "targets": [2],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#transfer-transfer-list thead tr th:eq(2)").html());
                }
            },
            { 
                "targets": [3],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#transfer-transfer-list thead tr th:eq(3)").html());
                }
            },
            { 
                "targets": [4],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#transfer-transfer-list thead tr th:eq(4)").html());
                }
            },
            { 
                "targets": [5],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#transfer-transfer-list thead tr th:eq(5)").html());
                }
            },
            { 
                "targets": [6],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#transfer-transfer-list thead tr th:eq(6)").html());
                }
            },
        ],
        "aoColumns": [
            {data : "created_at"},
            {data : "ref_no"},
            {data : "from_store"},
            {data : "to_store"},
            {data : "total_item"},
            {data : "total_quantity"},
            {data : "btn_edit"},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var total;
            var pageTotal;
            var api = this.api();
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === "string" ?
                    i.replace(/[\$,]/g, "")*1 :
                    typeof i === "number" ?
                        i : 0;
            };

            // Total over this page
            pageTotal = api
                .column( 4, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Update footer
            $( api.column( 4 ).footer() ).html(
                formatDecimal(pageTotal, 2)
            );

            // Total over this page
            pageTotal = api
                .column( 5, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Update footer
            $( api.column( 5 ).footer() ).html(
                formatDecimal(pageTotal, 2)
            );
        },
        "pageLength": window.settings.datatable_item_limit,
        "buttons": [
            {
                extend:    "print",
                text:      "<i class=\"fa fa-print\"></i>",
                titleAttr: "Print",
                title: "Bank Statement",
                customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' )
                        .append(
                            '<div><b><i>Developed & Maintained by: ITsolution24.com</i></b></div>'
                        )
                        .prepend(
                            '<div class="dt-print-heading"><img class="logo" src="'+baseUrl+'/assets/itsolution24/img/logo-favicons/1_logo.png"/><h2 class="title">'+window.store.name+'</h2><p><b><i>From</i> '+window.from +' <i>To</i> '+window.to+'</b><br>Printed on: '+window.formatDate(new Date())+'</p></div>'

                        );
 
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                },
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5 ]
                }
            },
            {
                extend:    "copyHtml5",
                text:      "<i class=\"fa fa-files-o\"></i>",
                titleAttr: "Copy",
                title: window.store.name + " > Transter List",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5 ]
                }
            },
            {
                extend:    "excelHtml5",
                text:      "<i class=\"fa fa-file-excel-o\"></i>",
                titleAttr: "Excel",
                title: window.store.name + " > Transter List",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5 ]
                }
            },
            {
                extend:    "csvHtml5",
                text:      "<i class=\"fa fa-file-text-o\"></i>",
                titleAttr: "CSV",
                title: window.store.name + " > Transter List",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5 ]
                }
            },
            {
                extend:    "pdfHtml5",
                text:      "<i class=\"fa fa-file-pdf-o\"></i>",
                titleAttr: "PDF",
                download: "open",
                title: window.store.name + " > Transter List",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5 ]
                }
            }
        ],
    });

    $scope.showStockList = function(toStoreID) {
        // if (fromStoreID == toStoreID) {
        //     $("#from_store_id").val("").trigger("change");
        //     window.toastr.warning("From and To store can not be same", "Warning!");
        //     return false;
        // }
        $scope.productsArray = [];  
        $scope.transferProductArray = [];      
        $http({
            url: window.baseUrl + "/_inc/ajax.php?type=STOCKITEMS&store_id="+toStoreID,
            method: "GET",
            data: "",
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json"
        }).
        then(function(response) {
            window._.map(response.data.products, function (item) {
                item.quantity = item.item_quantity-item.total_sell;
                $scope.productsArray.push(item);
            });
            $scope.totalStockItem = window._.size($scope.productsArray);

        }, function(response) {

            var alertMsg = "<div>";
            window.angular.forEach(response.data, function(value) {
                alertMsg += "<p>" + value + ".</p>";
            });
            alertMsg += "</div>";
            window.toastr.warning(alertMsg, "Warning!");
        });
    };

    var fromStoreID = "";
    $('#from_store_id').on('select2:select', function (e) {
        var data = e.params.data;
        fromStoreID = data.element.value;
        if (toStoreID == fromStoreID) {
            $("#from_store_id").val("").trigger("change");
            window.toastr.warning("From and To store can not be same", "Warning!");
            return false;
        } else {
            $("#from_store_id").val(fromStoreID).trigger("change");
        }
        $scope.showStockList(fromStoreID);
    });

    if (window.store.store_id && window.store.store_id != "undefined") {
        $scope.showStockList(window.store.store_id);
        fromStoreID = window.store.store_id;
        console.log(fromStoreID);
        $("#from_store_id").val(window.store.store_id).trigger("change");
    };

    var toStoreID = "";
    $('#to_store_id').on('select2:select', function (e) {
        var data = e.params.data;
        toStoreID = data.element.value;
        if (toStoreID == fromStoreID) {
            $("#to_store_id").val("").trigger("change");
            window.toastr.warning("From and To store can not be same", "Warning!");
            return false;
        } else {
            $("#to_store_id").val(toStoreID).trigger("change");
        }
    });

    $scope.transferItemArray = [];
    var stopProcess = false;
    $scope.addItemToTransferList = function (id, qty, index) {
        if (!qty) { qty = 1; }
        $http({
            url: API_URL + "/_inc/ajax.php?type=STOCKITEM&id="+id+"&quantity="+qty,
            method: "GET",
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json"
        }).
        then(function(response) {
            if (id) {
                var find = window._.find($scope.transferItemArray, function (item) { 
                    return item.id == response.data.products.id;
                });
                if (find) {
                    window._.map($scope.productsArray, function (sitem) {
                        if (sitem.id == response.data.products.id) {
                            if (sitem.quantity <= 0) {
                                window.toastr.warning('Out of Stock', "Warning!");
                                stopProcess = true;
                            } else {
                                sitem.quantity = sitem.quantity-1;
                            }
                        }
                    });
                    if (stopProcess == false) {
                        window._.map($scope.transferItemArray, function (item) {
                            if (item.id == response.data.products.id) {
                                item.quantity = item.quantity+1;
                                $scope.totalItem = window._.size($scope.transferItemArray);
                            }
                        });
                    }
                } else {
                    response.data.products.quantity = 1;
                    $scope.transferItemArray.push(response.data.products);
                    $scope.totalItem = window._.size($scope.transferItemArray);

                    window._.map($scope.productsArray, function (sitem) {
                        if (sitem.id == response.data.products.id) {
                            sitem.quantity = sitem.quantity-1;
                        }
                    });
                }  
            } 
        }, function(response) {
            window.toastr.warning(response.data.errorMsg, "Warning!");
        });
    };

    $scope.removeItemFromList = function (index, id) {
        window._.map($scope.transferItemArray, function (item, key) {
            var quantity = parseInt($("#quantity-"+item.id).val());
            if (isNaN(quantity)) {
                quantity = 1;
                $("#quantity-"+item.id).val(quantity);
            }
            if (item.id == id) {
                $scope.totalItem = $scope.totalItem - 1;
            }
            window._.map($scope.productsArray, function (sitem) {
                if (sitem.id == item.id) {
                    sitem.quantity = sitem.quantity+quantity;
                }
            });
        });
        $scope.transferItemArray.splice(index, 1);
        $scope.totalItem = window._.size($scope.transferItemArray);
    };

    $(document).on('click', function(e) {
        if(e.target.id.indexOf('stock-item') !== -1) {
            return true;
        }
        window._.map($scope.transferItemArray, function (item) {
            var quantity = parseInt($("#quantity-"+item.id).val());
            if (isNaN(quantity)) {
                quantity = 1;
                $("#quantity-"+item.id).val(quantity);
            }
            window._.map($scope.productsArray, function (sitem) {
                if (sitem.id == item.id) {
                    var stockQuantity = sitem.item_quantity - sitem.total_sell;
                    if (stockQuantity < quantity) {
                        window.toastr.warning('Out of Stock', "Warning!");
                        stopProcess = true;
                        sitem.quantity = 0;
                        $("#quantity-"+sitem.id).val(stockQuantity);
                    } else {
                        sitem.quantity = stockQuantity - quantity;
                    }
                }
            });
            $scope.$apply(function() {
                $scope.productsArray = $scope.productsArray;
            });
        });
    });

    // var requestSuccess = false;
    // Transfer confirm
    $(document).delegate("#transfer-confirm-btn", "click", function(e) {
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
            $("#from_store_id").val("").trigger("change");
            $("#to_store_id").val("").trigger("change");
            $("#status").val("complete").trigger("change");
            $("#reset").trigger("click");
            $btn.button("reset");
            $(":input[type=\"button\"]").prop("disabled", false);
            var alertMsg = response.data.msg;
            window.toastr.success(alertMsg, "Success!");
            dt.DataTable().ajax.reload(function(json) {
                if ($("#row_"+response.data.id).length) {
                    $("#row_"+response.data.id).flash("yellow", 5000);
                }
            }, false);
            // requestSuccess = true;
            $scope.$applyAsync(function() {
                $scope.productsArray = [];
                $scope.transferItemArray = [];
            });
        }, function(response) {

            // requestSuccess = false;
            $btn.button("reset");
            $(":input[type=\"button\"]").prop("disabled", false);
            var alertMsg = "<div>";
            window.angular.forEach(response.data, function(value) {
                alertMsg += "<p>" + value + ".</p>";
            });
            alertMsg += "</div>";
            window.toastr.warning(alertMsg, "Warning!");
        });
 
        // if (requestSuccess) {
        //     clearInterval(t);
        //     requestSuccess = false;
        // }   
        
    });

    // Transfer confirm
    $(document).delegate("#transfer-edit", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();

        var d = dt.DataTable().row($(this).closest("tr") ).data();
        TransferEditModal(d);
    });


    // append email button into datatable buttons
    $(".dt-buttons").append("<button id=\"email-btn\" class=\"btn btn-default buttons-pdf buttons-email\" tabindex=\"0\" aria-controls=\"transfer-transfer-list\" type=\"button\" title=\"Email\"><span><i class=\"fa fa-envelope\"></i></span></button>");
    
    // send transfer list through email
    $("#email-btn").on( "click", function (e) {
        e.stopPropagation();
        e.preventDefault();
        dt.find("thead th:nth-child(5), thead th:nth-child(6), thead th:nth-child(7), tbody td:nth-child(5), tbody td:nth-child(6), tbody td:nth-child(7), tfoot th:nth-child(5), tfoot th:nth-child(6), tfoot th:nth-child(7)").addClass("hide-in-mail");
        var thehtml = dt.html();
        EmailModal({template: "default", subject: "Transter List", title:"Transter List", html: thehtml});
    });
}]);

