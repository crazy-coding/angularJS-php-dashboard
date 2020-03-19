window.angularApp.controller("ReportStockController", [
    "$scope",
    "API_URL",
    "window",
    "jQuery",
    "$http",
function (
    $scope,
    API_URL,
    window,
    $,
    $http
) {
    "use strict";

    var $from = window.getParameterByName("from");
    var $to = window.getParameterByName("to");

    //======================================
    // start stock report datatable
    //=====================================

    var stockReportDT = $("#report_stock");
    var i;
   
    var hideColums = stockReportDT.data("hide-colums").split(",");
    var hideColumsArray = [];
    if (hideColums.length) {
        for (i = 0; i < hideColums.length; i+=1) {     
           hideColumsArray.push(parseInt(hideColums[i]));
        }
    }

    $scope.loadStockReport = function(supID) {
        var supID = supID ? supID : '';
        stockReportDT.dataTable().fnDestroy();
        stockReportDT.dataTable({
            "oLanguage": {sProcessing: "<img src='../assets/itsolution24/img/loading2.gif'>"},
            "processing": true,
            "dom": "lfBrtip",
            "serverSide": true,
            "ajax": API_URL + "/_inc/report_stock.php?sup_id=" + supID + "&from=" + $from + "&to=" + $to,
            "fixedHeader": true,
            "aaSorting": [],
            "aLengthMenu": [
                [10, 25, 50, 100, 200, -1],
                [10, 25, 50, 100, 200, "All"]
            ],
            "columnDefs": [
                {"targets": [0, 1, 2, 3, 4], "orderable": false},
                {"visible": false,  "targets": hideColumsArray},
                {"className": "text-right", "targets": [3, 4]},
                { 
                    "targets": [0],
                    'createdCell':  function (td, cellData, rowData, row, col) {
                       $(td).attr('data-title', $("#report_stock thead tr th:eq(0)").html());
                    }
                },
                { 
                    "targets": [2],
                    'createdCell':  function (td, cellData, rowData, row, col) {
                       $(td).attr('data-title', 'Product Name');
                    }
                },
                { 
                    "targets": [3],
                    'createdCell':  function (td, cellData, rowData, row, col) {
                       $(td).attr('data-title', $("#report_stock thead tr th:eq(2)").html());
                    }
                },
                { 
                    "targets": [4],
                    'createdCell':  function (td, cellData, rowData, row, col) {
                       $(td).attr('data-title', $("#report_stock thead tr th:eq(3)").html());
                    }
                },
            ],
            "aoColumns": [
                {data : "sl"},
                {data : "sup_name"},
                {data : "product_name"},
                {data : "available"},
                {data : "total"},
            ],
            "drawCallback": function ( settings ) {

                // with sub total
                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;

                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                               i : 0;
                };

                var hashCode = function (str){
                    var hash = 0;
                    var char;
                    if (str.length == 0) return hash;
                    for (i = 0; i < str.length; i++) {
                        char = str.charCodeAt(i);
                        hash = ((hash<<5)-hash)+char;
                        hash = hash & hash;
                    }
                    return hash;
                };

                var available = [];
                var total = [];
                              
                api.column(1, {page:'current'} ).data().each( function ( group, i ) {

                    var availabilityRow = hashCode(group) + '_tavailable';
                    if(typeof available[availabilityRow] != 'undefined'){
                        available[availabilityRow] = available[availabilityRow]+intVal(api.column(3).data()[i]);
                    }else{
                        available[availabilityRow]=intVal(api.column(3).data()[i]);
                    }

                    var totalAmountRow = hashCode(group) + '_tamount';
                    if(typeof total[totalAmountRow] != 'undefined'){
                        total[totalAmountRow] = total[totalAmountRow]+intVal(api.column(4).data()[i]);
                    }else{
                        total[totalAmountRow] = intVal(api.column(4).data()[i]);
                    }

                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group warning" data-child="'+hashCode(group)+'"><td class="bg-green" colspan="2" data-title="Supplier Name"><i class="fa fa-fw fa-angle-right"></i> '+group+'</td><td class="bg-olive '+availabilityRow+'  text-right" data-title="Total Availabel"></td><td class="bg-black '+totalAmountRow+'  text-right" data-title="Total Price"></td></tr>'
                        );

                        last = group;
                    } 
                    
                    $(rows).eq(i).addClass(hashCode(group)+"_child");
                });
                
                var key;
                for(key in available) {
                    $("."+key).html(available[key]);
                }
                for(key in total) {
                    $("."+key).html(window.formatDecimal(total[key], 2));
                }
            },
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
                    pageTotal
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
            },
            "pageLength": 200,
            "buttons": [
                {
                    extend:    "print",
                    text:      "<i class=\"fa fa-print\"></i>",
                    titleAttr: "Print",
                    title: window.store.name + " > Ref. Doctor Commission Report",
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4 ]
                    }
                },
                {
                    extend:    "copyHtml5",
                    text:      "<i class=\"fa fa-files-o\"></i>",
                    titleAttr: "Copy",
                    title: window.store.name + " > Ref. Doctor Commission Report",
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4 ]
                    }
                },
                {
                    extend:    "pdfHtml5",
                    text:      "<i class=\"fa fa-file-pdf-o\"></i>",
                    titleAttr: "PDF",
                    download: "open",
                    title: window.store.name + " > Ref. Doctor Commission Report",
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4 ]
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

       setInterval(function() {
            if ($("#report_stock tbody tr").length) {
                var sl = 1;
                $("#report_stock tbody tr").each(function(i){
                    if ($(this).find('td').length > 3) {
                        $(this).find("td:first-child").text((sl));
                        sl++;
                    } else {
                        sl = 1;
                    }
                });
            }
        }, 1000);
    };
    $scope.loadStockReport();

    $("#sup_id").on('select2:selecting', function(e) {
        var supID = e.params.args.data.id;
        $scope.loadStockReport(supID);
    });

    // Expand row On Click
    $(document).delegate("#report_stock tbody tr.group", "click", function() {
        var child = $(this).data("child");
        if ($("."+child+"_child").hasClass('table-row')) {
            $("."+child+"_child").removeClass('table-row');
        } else {
            $("."+child+"_child").addClass('table-row');
        }
    });

    //===================================
    // end stock report datatable
    //===================================

}]);
