window.angularApp.controller("SupplierProfileController", [
    "$scope",
    "API_URL",
    "window",
    "jQuery",
    "$compile",
    "$uibModal",
    "$http",
    "$sce",
    "PurchasePaymentModal",
    "BuyingInvoiceViewModal",
    "BuyingInvoiceInfoEditModal",
    "PurchaseReturnModal",
    "BuyingProductModal",
function (
    $scope,
    API_URL,
    window,
    $,
    $compile,
    $uibModal,
    $http,
    $sce,
    PurchasePaymentModal,
    BuyingInvoiceViewModal,
    BuyingInvoiceInfoEditModal,
    PurchaseReturnModal,
    BuyingProductModal
) {
    "use strict";

    var dt = $("#product-product-list");
    var sup_id = dt.data("id");
    var i;

    var hideColums = dt.data("hide-colums").split(",");
    var hideColumsArray = [];
    if (hideColums.length) {
        for (i = 0; i < hideColums.length; i+=1) {     
           hideColumsArray.push(parseInt(hideColums[i]));
        }
    }

    var $type = window.getParameterByName("type");
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
        "ajax": API_URL + "/_inc/supplier_profile.php?sup_id="+sup_id+"&from="+$from+"&to="+$to+"&type="+$type,
        "fixedHeader": true,
        "order": [[ 2, "desc"]],
        "aLengthMenu": [
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        "columnDefs": [
            {"targets": [4, 5, 6, 7, 8],"orderable": false},
            {"visible": false,  "targets": hideColumsArray},
            {"className": "text-right", "targets": [3]},
            {"className": "text-center", "targets": [0, 4, 6, 7, 8, 9]},
            { 
                "targets": [0],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(0)").html());
                }
            },
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
            { 
                "targets": [13],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#product-product-list thead tr th:eq(13)").html());
                }
            },
        ],
        "aoColumns": [
            {data : "inv_type"},
            {data : "invoice_id"},
            {data : "created_at"},
            {data : "sup_name"},
            {data : "created_by"},
            {data : "invoice_amount"},
            {data : "paid_amount"},
            {data : "due"},
            {data : "status"},
            {data : "btn_pay"},
            {data : "btn_return"},
            {data : "btn_view"},
            {data : "btn_edit"},
            {data : "btn_delete"}
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
            // Total over all pages at column 3
            pageTotal = api
                .column( 3, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Update footer
            $( api.column( 3 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );
        },
        "pageLength": window.settings.datatable_item_limit,
        "buttons": [
            {
                extend:    "print",
                text:      "<i class=\"fa fa-print\"></i>",
                titleAttr: "Print",
                title: window.supplier.sup_name + " &raquo; Invoice List",
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
                    columns: [ 0, 1, 2, 3, 5, 6 ]
                }
            },
            {
                extend:    "copyHtml5",
                text:      "<i class=\"fa fa-files-o\"></i>",
                titleAttr: "Copy",
                title: window.store.name + " > " + window.supplier.sup_name + " &raquo; Invoice List",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 5, 6 ]
                }
            },
            {
                extend:    "excelHtml5",
                text:      "<i class=\"fa fa-file-excel-o\"></i>",
                titleAttr: "Excel",
                title: window.store.name + " > " + window.supplier.sup_name + " &raquo; Invoice List",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 5, 6 ]
                }
            },
            {
                extend:    "csvHtml5",
                text:      "<i class=\"fa fa-file-text-o\"></i>",
                titleAttr: "CSV",
                title: window.store.name + " > " + window.supplier.sup_name + " &raquo; Invoice List",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 5, 6 ]
                }
            },
            {
                extend:    "pdfHtml5",
                text:      "<i class=\"fa fa-file-pdf-o\"></i>",
                titleAttr: "PDF",
                download: "open",
                title: window.store.name + " > " + window.supplier.sup_name + " &raquo; Invoice List",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 5, 6 ]
                },
                customize: function (doc) {
                    doc.content[1].table.widths =  Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                    doc.pageMargins = [10,10,10,10];
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 8;
                    doc.styles.title.fontSize = 10;
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
        ],
    });

    //================
    // end datatable
    //================

    // delete invoice
    $(document).delegate("#edit-invoice-info", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        BuyingInvoiceInfoEditModal(d);
    });

    // view invoice
    $(document).delegate("#view-invoice-btn", "click", function (e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        BuyingInvoiceViewModal(d);
    });

    // delete invoice
    $(document).delegate("#delete-invoice", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        window.swal({
          title: "Delete!",
          text: "Are You Sure?",
          icon: "warning",
          buttons: true,
          showCancelButton: false,
        })
        .then(function (willDelete) {
            if (willDelete) {
                $http({
                    method: "POST",
                    url: API_URL + "/_inc/purchase.php",
                    data: "invoice_id="+d.id+"&action_type=DELETE",
                    dataType: "JSON"
                })
                .then(function(response) {
                    dt.DataTable().ajax.reload( null, false );
                    window.swal("success!", response.data.msg, "success");
                }, function(response) {
                    window.swal("Oops!", response.data.errorMsg, "error");
                });
            }
        });
    });

    // Payment From Table Selection Modal [for Dinein order type]
    $(document).delegate("#pay_now", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        PurchasePaymentModal(d);
    });

    // Return From
    $(document).delegate("#return_item", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();
        $http({
          url: window.baseUrl + "/_inc/purchase_payment.php?action_type=ORDERDETAILS&invoice_id="+d.invoice_id,
          method: "GET"
        })
        .then(function(response, status, headers, config) {
            $scope.order = response.data.order;
            $scope.order.datatable = dt;
            PurchaseReturnModal($scope);
        }, function(response) {
           window.swal("Oops!", response.data.errorMsg, "error");
        });
    });

    // // view invoice
    // $(document).delegate("#view-invoice-btn", "click", function (e) {
    //     e.stopPropagation();
    //     e.preventDefault();
    //     var d = dt.DataTable().row( $(this).closest("tr") ).data();
    //     BuyingInvoiceViewModal(d);
    // });

    // buy product
    $(document).delegate("#buy-btn", "click", function (e) {
        e.stopPropagation();
        e.preventDefault();
        var sup_id = $(this).data("id");
        var sup_name = $(this).data("name");
        BuyingProductModal({sup_id:sup_id,sup_name:sup_name, invoice_id:null});
    });

    // add product by query string
    if (window.getParameterByName("sup_id") && window.getParameterByName("sup_name") && window.getParameterByName("buy")) {
        var sup_id = window.getParameterByName("sup_id");
        var sup_name = window.getParameterByName("sup_name");
        BuyingProductModal({sup_id:sup_id,sup_name:sup_name,invoice_id:null});
    }

}]);