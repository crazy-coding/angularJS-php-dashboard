<!Doctype html>
<head>
    <title>Shop</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- This CSS use for E-mail that's why internal css is mandatory-->
    <style type="text/css">
        .table-responsive {
            min-height: .01%;
            overflow-x: auto;
        }
        #template {
            font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        #template-heading {
            text-align: center;
        }
        #template-heading .title {
            padding: 0;
            margin-bottom: 5px;
        }
        #template thead tr.active {background-color: #f9f9f9;}
        #template tr.odd{background-color: #f9f9f9;}
        #template tr:hover {background-color: #ddd;}
        #template th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
        }
        #template td, #template th {
            border: 1px solid #ddd;
            padding: 8px;
        }
        #template th[class~=text-right],
        #template td[class~=text-right] {
            text-align: right;
        }
        #template th[class~=text-center],
        #template td[class~=text-center] {
            text-align: center;
        }
        #template th.hide-in-mail,
        #template td.hide-in-mail {
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
    <div id="template-heading">
        <h1 class="title">
            <?php echo $title; ?>
        </h1>
        <p class="date">
            <strong>Date: </strong>
            <?php echo date("F j, Y, g:i a"); ?>
        </p>
    </div>
    <div class="table-responsive">
        <table id="template">
            <?php echo html_entity_decode($body); ?>
        </table>
    </div>
</body>
</html>