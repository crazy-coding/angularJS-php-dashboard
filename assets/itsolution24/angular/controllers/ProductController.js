window.angularApp.controller("ProductController", [
    "$scope",
    "API_URL",
    "window",
    "jQuery",
    "$http",
    "ProductViewModal",
    "ProductEditModal",
    "ProductDeleteModal",
    "BuyingProductModal",
    "ProductReturnModal",
    "CategoryCreateModal",
    "SupplierCreateModal",
    "BoxCreateModal",
    "BarcodePrintModal",
    "UnitCreateModal",
    "TaxrateCreateModal",
    "EmailModal",
function (
    $scope,
    API_URL,
    window,
    $,
    $http,
    ProductViewModal,
    ProductEditModal,
    ProductDeleteModal,
    BuyingProductModal,
    ProductReturnModal,
    CategoryCreateModal,
    SupplierCreateModal,
    BoxCreateModal,
    BarcodePrintModal,
    UnitCreateModal,
    TaxrateCreateModal,
    EmailModal
) {
    "use strict";

    var dt = $("#product-product-list");
    var supplierId;
    var productId;
    var productLocation;

    var printColumns = dt.data("print-columns");
    var i;
    var hideColums = dt.data("hide-colums").split(",");
    var hideColumsArray = [];
    if (hideColums.length) {
        for (i = 0; i < hideColums.length; i+=1) {     
           hideColumsArray.push(parseInt(hideColums[i]));
        }
    }

    supplierId = window.getParameterByName("sup_id") || "";
    productLocation = window.getParameterByName("location") || "";

    $scope.day = window.getParameterByName("p_day");
    $scope.month = window.getParameterByName("p_month");
    $scope.year = window.getParameterByName("p_year");

    //================
    // start datatable
    //================

    dt.dataTable({
        "oLanguage": {sProcessing: "<img src='../assets/itsolution24/img/loading2.gif'>"},
        "processing": true,
        "dom": "lfBrtip",
        "serverSide": true,
        "ajax": API_URL + "/_inc/product.php?sup_id=" + supplierId + "&location=" + productLocation,
        "aLengthMenu": [
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        "columnDefs": [
            {"targets": [0, 2, 3, 4, 8, 9, 10, 11, 12], "orderable": false},
            {"className": "text-center", "targets": [0, 1, 8, 9, 10, 11, 12]},
            {"className": "text-right", "targets": [5, 6, 7]},
            {"visible": false, "targets": hideColumsArray},
            { 
                "targets": [1],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(1)").html());
                }
            },
            { 
                "targets": [2],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(2)").html());
                }
            },
            { 
                "targets": [3],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(3)").html());
                }
            },
            { 
                "targets": [4],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(4)").html());
                }
            },
            { 
                "targets": [5],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(5)").html());
                }
            },
            { 
                "targets": [6],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(6)").html());
                }
            },
            { 
                "targets": [7],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(7)").html());
                }
            },
            { 
                "targets": [8],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(8)").html());
                }
            },
            { 
                "targets": [9],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(9)").html());
                }
            },
            { 
                "targets": [10],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(10)").html());
                }
            },
            { 
                "targets": [11],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(11)").html());
                }
            },
            { 
                "targets": [12],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(12)").html());
                }
            },
        ],
        "aoColumns": [
            {data: "select"},
            {data: "p_code"},
            {data: "p_name"},
            {data: "supplier"},
            {data: "category_name"},
            {data: "quantity_in_stock"},
            {data: "buy_price"},
            {data: "sell_price"},
            {data: "view_btn"},
            {data: "edit_btn"},
            {data: "buy_btn"},
            {data: "barcode_btn"},
            {data: "delete_btn"}
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var pageTotal;
            var api = this.api();
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === "string" ?
                    i.replace(/[\$,]/g, "")*1 :
                    typeof i === "number" ?
                        i : 0;
            };
            // Total over all pages at column 5
            pageTotal = api
                .column( 5, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Update footer
            $( api.column( 5 ).footer() ).html(
                pageTotal
            );
             // Total over all pages at column 6
            pageTotal = api
                .column( 6, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Update footer
            $( api.column( 6 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );
             // Total over all pages at column 7
            pageTotal = api
                .column( 7, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Update footer
            $( api.column( 7 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );
        },
        "pageLength": window.settings.datatable_item_limit,
        "buttons": [
            {
                extend:    "print",
                text:      "<i class=\"fa fa-print\"></i>",
                titleAttr: "Print",
                title: "Product List",
                customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' )
                        .append(
                            '<div><b><i>Powered by: ITsolution24.com</i></b></div>'
                        )
                        .prepend(
                            '<div class="dt-print-heading"><img class="logo" src="'+window.logo+'"/><h2 class="title">'+window.store.name+'</h2><p>Printed on: '+window.formatDate(new Date())+'</p></div>'
                        );
 
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                },
                exportOptions: {
                    columns: [printColumns]
                }
            },
            {
                extend:    "copyHtml5",
                text:      "<i class=\"fa fa-files-o\"></i>",
                titleAttr: "Copy",
                title: window.store.name + " > Products",
                exportOptions: {
                    columns: [printColumns]
                }
            },
            {
                extend:    "excelHtml5",
                text:      "<i class=\"fa fa-file-excel-o\"></i>",
                titleAttr: "Excel",
                title: window.store.name + " > Products",
                exportOptions: {
                    columns: [printColumns]
                }
            },
            {
                extend:    "csvHtml5",
                text:      "<i class=\"fa fa-file-text-o\"></i>",
                titleAttr: "CSV",
                title: window.store.name + " > Products",
                exportOptions: {
                    columns: [printColumns]
                }
            },
            {
                extend:    "pdfHtml5",
                text:      "<i class=\"fa fa-file-pdf-o\"></i>",
                titleAttr: "PDF",
                download: "open",
                title: window.store.name + " > Products",
                exportOptions: {
                    columns: [printColumns]
                },
                customize: function (doc) {
                    doc.content[1].table.widths =  Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    doc.pageMargins = [10,10,10,10];
                    doc.defaultStyle.fontSize = 7;
                    doc.styles.tableHeader.fontSize = 7;
                    doc.styles.title.fontSize = 9;
                    // Remove spaces around page title
                    doc.content[0].text = doc.content[0].text.trim();
                    // Header
                    doc.content.splice( 1, 0, {
                        margin: [ 0, 0, 0, 12 ],
                        alignment: 'center',
                        fontSize: 8,
                        text: 'Printed on: '+window.formatDate(new Date()),
                    });
                    // Create a footer
                    doc['footer']=(function(page, pages) {
                        return {
                            columns: [
                                'Powered by ITSOLUTION24.COM',
                                {
                                    // This is the right column
                                    alignment: 'right',
                                    text: ['page ', { text: page.toString() },  ' of ', { text: pages.toString() }]
                                }
                            ],
                            margin: [10, 0]
                        };
                    });
                    // Styling the table: create style object
                    var objLayout = {};
                    // Horizontal line thickness
                    objLayout['hLineWidth'] = function(i) { return 0.5; };
                    // Vertikal line thickness
                    objLayout['vLineWidth'] = function(i) { return 0.5; };
                    // Horizontal line color
                    objLayout['hLineColor'] = function(i) { return '#aaa'; };
                    // Vertical line color
                    objLayout['vLineColor'] = function(i) { return '#aaa'; };
                    // Left padding of the cell
                    objLayout['paddingLeft'] = function(i) { return 4; };
                    // Right padding of the cell
                    objLayout['paddingRight'] = function(i) { return 4; };
                    // Inject the object in the document
                    doc.content[1].layout = objLayout;
                }
            }
        ]
    });

    //================
    // end datatable
    //================

    // open edit modal dialog box by query string
    if (window.getParameterByName("p_id") && window.getParameterByName("p_name")) {
        productId = window.getParameterByName("p_id");
        var productName = window.getParameterByName("p_name");
        dt.DataTable().search(productName).draw();
        dt.DataTable().ajax.reload(function(json) {
            $.each(json.data, function(index, obj) {
                if (obj.DT_RowId === "row_" + productId) {
                    ProductEditModal({p_id: productId, p_name: obj.p_name});
                    return false;
                }
            });
        }, false);
    }

    // view product
    $(document).delegate(".view-product", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        ProductViewModal(dt.DataTable().row($(this).closest("tr")).data());
    });

    // create product
    $(document).delegate("#create-product-submit", "click", function(e) {
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
            
            $btn.button("reset");
            $(":input[type=\"button\"]").prop("disabled", false);
            var alertMsg = response.data.msg;
            window.toastr.success(alertMsg, "Success!");

            productId = response.data.id;
            
            dt.DataTable().ajax.reload(function(json) {
                if ($("#row_"+productId).length) {
                    $("#row_"+productId).flash("yellow", 5000);
                }
            }, false);

            setTimeout(function() {
                // reset form
                $("#reset").trigger("click");
                $("#category_id").val(null).trigger("change");
                $("#sup_id").val(null).trigger("change");
                $("#box_id").val(null).trigger("change");
                $("#random_num").val(null).trigger("click");
                $("#p_thumb img").attr("src", "../assets/itsolution24/img/noimage.jpg");
                $("#p_image").val("");
            }, 100);


        }, function(response) {

            $btn.button("reset");
            $(":input[type=\"button\"]").prop("disabled", false);
            var alertMsg = "<div>";
            window.angular.forEach(response.data, function(value) {
                alertMsg += "<p>" + value + ".</p>";
            });
            alertMsg += "</div>";
            window.toastr.warning(alertMsg, "Warning!");
        });
    });

    // edit product
    $(document).delegate(".edit-product", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        ProductEditModal(dt.DataTable().row($(this).closest("tr")).data());
    });

    // product return button action
    $(document).delegate(".product-return", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        ProductReturnModal(dt.DataTable().row($(this).closest("tr")).data());
    });

    // create new category
    $scope.createNewCategory = function () {
        CategoryCreateModal($scope);
    };

    // create new supplier
    $scope.createNewSupplier = function () {
        SupplierCreateModal($scope);
    };

    // create new box
    $scope.createNewBox = function () {
        BoxCreateModal($scope);
    };

    // create new unit
    $scope.createNewUnit = function () {
        UnitCreateModal($scope);
    };

    // create new taxrate
    $scope.createNewTaxrate = function () {
        TaxrateCreateModal($scope);
    };

    // print barcode
    $(document).delegate(".print-barcode", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        BarcodePrintModal(dt.DataTable().row($(this).closest("tr")).data());
    });

    // delete product
    $(document).delegate(".product-delete", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        ProductDeleteModal(dt.DataTable().row($(this).closest("tr")).data());
    });

    // delete all product
    $("#delete-all").on("click", function(e) {
        e.preventDefault();

        var $tag = $(this);
        var form = $($tag.data("form"));
        var actionUrl = form.attr("action");

        // Sweet Alert
        window.swal({
          title: "Are You Sure?",
          text: "Delete All Selected Products!",
          icon: "warning",
          buttons: true,
          showCancelButton: false,
        })
        .then(function (willDelete) {
            if (willDelete) {
                var $btn = $tag.button("loading");
                $http({
                    url: window.baseUrl + "/_inc/" + actionUrl + "?action=delete",
                    method: "POST",
                    data: form.serialize(),
                    cache: false,
                    processData: false,
                    contentType: false,
                    dataType: "json"
                }).
                then(function(response) {

                    $btn.button("reset");
                    dt.DataTable().ajax.reload( null, false );

                    // alert box
                    window.swal(response.data.msg)
                    .then(function() {
                        window.location = window.location;
                    });
                }, function(response) {
                    $btn.button("reset");
                    $(":input[type=\"button\"]").prop("disabled", false);
                    var alertMsg = "<div>";
                    window.angular.forEach(response.data, function(value) {
                        alertMsg += "<p>" + value + ".</p>";
                    });
                    alertMsg += "</div>";
                    window.toastr.warning(alertMsg, "Warning!");
                });
            }
        });

    });

    // restore all product
    $("#restore-all").on("click", function(e) {
        e.preventDefault();

        var $tag = $(this);
        var $btn = $tag.button("loading");
        var form = $($tag.data("form"));
        var actionUrl = form.attr("action");
        $http({
            url: window.baseUrl + "/_inc/" + actionUrl + "?action=restore",
            method: "POST",
            data: form.serialize(),
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json"
        }).
        then(function(response) {

            $btn.button("reset");
            dt.DataTable().ajax.reload( null, false );
            
            // alert box
            window.swal(response.data.msg)
            .then(function() {
                window.location = window.location;
            });

        }, function(response) {

            $btn.button("reset");
            $(":input[type=\"button\"]").prop("disabled", false);
            var alertMsg = "<div>";
            window.angular.forEach(response.data, function(value) {
                alertMsg += "<p>" + value + ".</p>";
            });
            alertMsg += "</div>";
            window.toastr.warning(alertMsg, "Warning!");
        });
    });

    // buy product
    $(document).delegate(".buy-product", "click", function (e) {
        e.stopPropagation();
        e.preventDefault();
        var product = dt.DataTable().row($(this).closest("tr")).data();
        BuyingProductModal(product);
    });

    // append email button into datatable buttons
    $(".dt-buttons").append("<button id=\"email-btn\" class=\"btn btn-default buttons-email\" tabindex=\"0\" aria-controls=\"invoice-invoice-list\" type=\"button\" title=\"Email\"><span><i class=\"fa fa-envelope\"></i></span></button>");
    
    // send list through email
    $("#email-btn").on( "click", function (e) {
        e.stopPropagation();
        e.preventDefault();
        dt.find("thead th:nth-child(1), thead th:nth-child(9), thead th:nth-child(10), thead th:nth-child(11), thead th:nth-child(12), thead th:nth-child(13), thead th:nth-child(14), tbody td:nth-child(1), tbody td:nth-child(9), tbody td:nth-child(10), tbody td:nth-child(11), tbody td:nth-child(12), tbody td:nth-child(13), tbody td:nth-child(14), tfoot th:nth-child(1), tfoot th:nth-child(9), tfoot th:nth-child(10), tfoot th:nth-child(11), tfoot th:nth-child(12), tfoot th:nth-child(13), tfoot th:nth-child(14)").addClass("hide-in-mail");
        var thehtml = dt.html();
        EmailModal({template: "product-list", subject: "Product List", title:"Product List", html: thehtml});
    });
}]);