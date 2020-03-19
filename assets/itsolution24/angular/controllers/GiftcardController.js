window.angularApp.controller("GiftcardController", [
    "$scope", 
    "API_URL",
    "window",
    "jQuery",
    "$compile",
    "$uibModal",
    "$http",
    "$sce",
    "GiftcardEditModal",
    "GiftcardViewModal",
    "GiftcardTopupModal",
function (
    $scope,
    API_URL,
    window,
    $,
    $compile,
    $uibModal,
    $http,
    $sce,
    GiftcardEditModal,
    GiftcardViewModal,
    GiftcardTopupModal
) {
    "use strict";

    var dt = $("#giftcard-giftcard-list");
    var i, giftcardId;

    var hideColums = dt.data("hide-colums").split(",");
    var hideColumsArray = [];
    if (hideColums.length) {
        for (i = 0; i < hideColums.length; i+=1) {     
           hideColumsArray.push(parseInt(hideColums[i]));
        }
    }

    //================
    // start datatable
    //================

    dt.dataTable({
        "oLanguage": {sProcessing: "<img src='../assets/itsolution24/img/loading2.gif'>"},
        "processing": true,
        "dom": "lfBrtip",
        "serverSide": true,
        "ajax": API_URL + "/_inc/giftcard.php",
        "order": [[ 5, "desc"]],
        "aLengthMenu": [
            [10, 25, 50, 100, 200, -1],
            [10, 25, 50, 100, 200, "All"]
        ],
        "columnDefs": [
            {"visible": false,  "targets": hideColumsArray},
            {"className": "text-right", "targets": [1, 2]},
            {"className": "text-center", "targets": [0, 3, 4, 5, 6, 7, 8, 9]},
            { 
                "targets": [0],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#giftcard-giftcard-list thead tr th:eq(0)").html());
                }
            },
            { 
                "targets": [1],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#giftcard-giftcard-list thead tr th:eq(1)").html());
                }
            },
            { 
                "targets": [2],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#giftcard-giftcard-list thead tr th:eq(2)").html());
                }
            },
            { 
                "targets": [3],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#giftcard-giftcard-list thead tr th:eq(3)").html());
                }
            },
            { 
                "targets": [4],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#giftcard-giftcard-list thead tr th:eq(4)").html());
                }
            },
            { 
                "targets": [5],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#giftcard-giftcard-list thead tr th:eq(5)").html());
                }
            },
            { 
                "targets": [6],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#giftcard-giftcard-list thead tr th:eq(6)").html());
                }
            },
            { 
                "targets": [7],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#giftcard-giftcard-list thead tr th:eq(7)").html());
                }
            },
            { 
                "targets": [8],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#giftcard-giftcard-list thead tr th:eq(8)").html());
                }
            },
            { 
                "targets": [9],
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).attr('data-title', $("#giftcard-giftcard-list thead tr th:eq(9)").html());
                }
            },
        ],
        "aoColumns": [
            {data:"card_no"},
            {data:"value"},
            {data:"balance"},
            {data:"created_by"},
            {data:"customer"},
            {data:"expiry"},
            {data:"btn_view"},
            {data:"btn_topup"},
            {data:"btn_edit"},
            {data:"btn_delete"},
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

            // Total over all pages at column 1
            pageTotal = api
                .column( 1, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Update footer
            $( api.column( 1 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );

            // Total over all pages at column 2
            pageTotal = api
                .column( 2, { page: "current"} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            // Update footer
            $( api.column( 2 ).footer() ).html(
                window.formatDecimal(pageTotal, 2)
            );

        },
        "pageLength": window.settings.datatable_item_limit,
        "buttons": [
            {
                extend:    "print",
                text:      "<i class=\"fa fa-print\"></i>",
                titleAttr: "Print",
                title: "Giftcard List",
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
                    columns: [ 0, 1, 2, 4, 5 ]
                }
            },
            {
                extend:    "copyHtml5",
                text:      "<i class=\"fa fa-files-o\"></i>",
                titleAttr: "Copy",
                title: window.store.name + " > Giftcard List",
                exportOptions: {
                    columns: [ 0, 1, 2, 4, 5 ]
                }
            },
            {
                extend:    "excelHtml5",
                text:      "<i class=\"fa fa-file-excel-o\"></i>",
                titleAttr: "Excel",
                title: window.store.name + " > Giftcard List",
                exportOptions: {
                    columns: [ 0, 1, 2, 4, 5 ]
                }
            },
            {
                extend:    "csvHtml5",
                text:      "<i class=\"fa fa-file-text-o\"></i>",
                titleAttr: "CSV",
                title: window.store.name + " > Giftcard List",
                exportOptions: {
                    columns: [ 0, 1, 2, 4, 5 ]
                }
            },
            {
                extend:    "pdfHtml5",
                text:      "<i class=\"fa fa-file-pdf-o\"></i>",
                titleAttr: "PDF",
                download: "open",
                title: window.store.name + " > Giftcard List",
                exportOptions: {
                    columns: [ 0, 1, 2, 4, 5 ]
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
        ]
    });

    //==============
    // end datatable
    //==============

    // create new giftcard
    $(document).delegate("#create-giftcard-submit", "click", function(e) {
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

            $("#reset").trigger("click");
            $btn.button("reset");
            $(":input[type=\"button\"]").prop("disabled", false);
            var alertMsg = response.data.msg;
            window.toastr.success(alertMsg, "Success!");

            giftcardId = response.data.id;
            dt.DataTable().ajax.reload(function(json) {
                if ($("#row_"+giftcardId).length) {
                    $("#row_"+giftcardId).flash("yellow", 5000);
                }
            }, false);

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

    // delete giftcard
    $(document).delegate("#delete-giftcard", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row( $(this).closest("tr") ).data();

        // Sweet Alert
        window.swal({
          title: "Delete!",
          text: "Are you sure?",
          icon: "warning",
          buttons: true,
          dangerMode: false,
        })
        .then(function(willDelete) {
            if (willDelete) {
                $http({
                    method: "POST",
                    url: API_URL + "/_inc/giftcard.php",
                    data: "id="+d.id+"&action_type=DELETE",
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

    // view giftcard
    $(document).delegate("#topup-giftcard", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row($(this).closest("tr") ).data();
        GiftcardTopupModal(d);
    });

    // view giftcard
    $(document).delegate("#view-giftcard", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row($(this).closest("tr") ).data();
        GiftcardViewModal(d);
    });

    // edit giftcard
    $(document).delegate("#edit-giftcard", "click", function(e) {
        e.stopPropagation();
        e.preventDefault();
        var d = dt.DataTable().row($(this).closest("tr") ).data();
        GiftcardEditModal(d);
    });

    // Open edit modal dialog giftcard by query string
    if (window.getParameterByName("id") && window.getParameterByName("giftcard_name")) {
        giftcardId = window.getParameterByName("id");
        var giftcardName = window.getParameterByName("giftcard_name");
        dt.DataTable().search(giftcardName).draw();
        dt.DataTable().ajax.reload(function(json) {
            $.each(json.data, function(index, obj) {
                if (obj.DT_RowId === "row_" + giftcardId) {
                    GiftcardEditModal({id: giftcardId, giftcard_name: obj.giftcard_name});
                    return false;
                }
            });
        }, false);
    }

}]);