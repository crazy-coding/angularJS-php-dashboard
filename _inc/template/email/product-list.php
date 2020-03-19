<!Doctype html>
<head>
    <title>Product List</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- This CSS use for E-mail that's why internal css is mandatory-->
    <style type="text/css">
        .table-responsive {
            min-height: .01%;
            overflow-x: auto;
        }
        #product {
            font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        #product-heading {
            text-align: center;
        }
        #product-heading .title {
            padding: 0;
            margin-bottom: 5px;
        }
        #product thead tr.active {background-color: #f9f9f9;}
        #product tr.odd{background-color: #f9f9f9;}
        #product tr:hover {background-color: #ddd;}
        #product th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
        }
        #product td, #product th {
            border: 1px solid #ddd;
            padding: 8px;
        }
        #product th[class~=text-right],
        #product td[class~=text-right] {
            text-align: right;
        }
        #product th[class~=text-center],
        #product td[class~=text-center] {
            text-align: center;
        }
        #product th.hide-in-mail,
        #product td.hide-in-mail {
            display: none;
        }
        @media screen and (max-width: 767px) {
            .table-responsive {
                width: 100%;
                margin-bottom: 15px;
                overflow-y: hidden;
                -ms-overflow-style: -ms-autohiding-scrollbar;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <div id="product-heading">
        <h1 class="title">
            <?php echo $title; ?>
        </h1>
        <p class="date">
            <strong>Date: </strong>
            <?php echo date("F j, Y, g:i a"); ?>
        </p>
    </div>
    <div class="table-responsive">
        <table id="product">
            <?php echo html_entity_decode($body); ?>
        </table>
    </div>
</body>
</html>