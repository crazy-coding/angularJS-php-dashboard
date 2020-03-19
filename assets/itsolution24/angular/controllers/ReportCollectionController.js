window.angularApp.controller("ReportCollectionController", [
    "$scope",
    "API_URL",
    "window",
    "jQuery",
    "$http",
    "UserInvoiceDetailsModal",
    "DueCollectionDetailsModal",
    "UserInvoiceDueDetailsModal",
    "EmailModal",
function (
    $scope,
    API_URL,
    window,
    $,
    $http,
    UserInvoiceDetailsModal,
    DueCollectionDetailsModal,
    UserInvoiceDueDetailsModal,
    EmailModal
) {
    "use strict";

    var $from = window.getParameterByName("from");
    var $to = window.getParameterByName("to");
    var $isExpanded = window.getParameterByName("isExpanded");

    //=======================================
    // start user collection report datatable
    //=======================================
    var collectionReportDT = $("#report_collection");
    var i;
   
    var hideColums = collectionReportDT.data("hide-colums").split(",");
    var hideColumsArray = [];
    if (hideColums.length) {
        for (i = 0; i < hideColums.length; i+=1) {     
           hideColumsArray.push(parseInt(hideColums[i]));
        }
    }
    collectionReportDT.dataTable({
        "oLanguage": {sProcessing: "<img src='../assets/itsolution24/img/loading2.gif'>"},
        "processing": true,
        "dom":"lfBrtip",
        "serverSide": true,
        "ajax": API_URL + "/~sunny/_inc/report_collection.php?from=" + $from + "&to=" + $to,
        "fixedHeader": $isExpanded,
        "aaSorting": [],
        "aLengthMenu": [
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        "columnDefs": [
            {"visible": false,  "targets": hideColumsArray},
            {"className": "text-right", "targets": [3, 4, 5, 6, 7]},
            {"className": "text-center", "targets": [0, 2]},
            { 
                "targets": [0],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report_collection thead tr th:eq(0)").html());
                }
            },
            { 
                "targets": [1],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report_collection thead tr th:eq(1)").html());
                }
            },
            { 
                "targets": [2],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report_collection thead tr th:eq(2)").html());
                }
            },
            { 
                "targets": [3],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report_collection thead tr th:eq(3)").html());
                }
            },
            { 
                "targets": [4],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report_collection thead tr th:eq(4)").html());
                }
            },
            { 
                "targets": [5],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report_collection thead tr th:eq(5)").html());
                }
            },
            { 
                "targets": [6],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report_collection thead tr th:eq(6)").html());
                }
            },
            { 
                "targets": [6],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#report_collection thead tr th:eq(6)").html());
                }
            },
        ],
        "aoColumns": [
            {data : "sl"},
            {data : "username"},
            {data : "invoice_count"},
            {data : "net_amount"},
            {data : "prev_due_collection"},
            {data : "due_collection"},
            {data : "due_given"},
            {data : "received_amount"},
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

            // Total over this page
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

            // Total over this page
            pageTotal = api
                .column( 4, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Update footer
            $( api.column( 4 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );

            // Total over this page
            pageTotal = api
                .column( 5, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            $( api.column( 5 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );

            // Total over this page
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

            // Total over this page
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
                title: window.store.name + " > Collection Report",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
                }
            },
            {
                extend:    "copyHtml5",
                text:      "<i class=\"fa fa-files-o\"></i>",
                titleAttr: "Copy",
                title: window.store.name + " > Collection Report",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
                }
            },
            {
                extend:    "pdfHtml5",
                text:      "<i class=\"fa fa-file-pdf-o\"></i>",
                titleAttr: "PDF",
                download: "open",
                title: window.store.name + " > Collection Report",
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
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

    var inc2 = 0;
    var t2 = setInterval(function() {
        if ($("#report_collection tbody tr").length) {
            $("#report_collection tbody tr").each(function(i){
                if (!$(this).find("td:first-child").hasClass("dataTables_empty")) {
                    $(this).find("td:first-child").text((i + 1));
                }
            });
        }

        if (inc2) {
            clearInterval(t2);
        }

    }, 1000);

    $(document).delegate("#user_invoice_details", "click", function(e) {
        e.preventDefault();
        $scope.userID = $(this).data("id");
        $scope.username = $(this).data("name");
        UserInvoiceDetailsModal($scope);
    });

    $(document).delegate("#user_invoice_due_details", "click", function(e) {
        e.preventDefault();
        $scope.userID = $(this).data("id");
        $scope.username = $(this).data("name");
        UserInvoiceDueDetailsModal($scope);
    });

    $(document).delegate("#due_collection_details", "click", function(e) {
        e.preventDefault();
        $scope.createdBy = $(this).data("id");
        DueCollectionDetailsModal($scope);
    });

    //=======================================
    // end user collection report datatable
    //=======================================

    // append email button into datatable buttons
    $(".dt-buttons").append("<button id=\"email-btn\" class=\"btn btn-default buttons-email\" tabindex=\"0\" aria-controls=\"invoice-invoice-list\" type=\"button\" title=\"Email\"><span><i class=\"fa fa-envelope\"></i></span></button>");
    
    // send list through email
    $("#email-btn").on( "click", function (e) {
        e.stopPropagation();
        e.preventDefault();
        var thehtml = collectionReportDT.html();
        EmailModal({template: "report", subject: "Collection Report", title:"Collection Report", html: thehtml});
    });
}]);
