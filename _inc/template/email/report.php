<!Doctype html>
<head>
    <title><?php echo $title; ?></title>
    <meta charset='UTF-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <!-- This CSS use for E-mail that's why internal css is mandatory-->
    <style>
        @media screen and (max-width: 767px) {
            .table-responsive {
                width: 100%;
                margin-bottom: 15px;
                overflow-y: hidden;
                -ms-overflow-style: -ms-autohiding-scrollbar;
                border: 1px solid #ddd;
            }
        }
        .table-responsive {
            min-height: .01%;
            overflow-x: auto;
        }
        #report {
            font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        #report-heading {
            text-align: center;
        }
        #report-heading .title {
            padding: 0;
            margin-bottom: 5px;
        }
        #report td, #report th {
            border: 1px solid #ddd;
            padding: 8px;
        }
        #report th[class~=text-right],
        #report td[class~=text-right] {
            text-align: right;
        }
        #report th[class~=text-center],
        #report td[class~=text-center] {
            text-align: center;
        }
        #report tr:nth-child(even){background-color: #f2f2f2;}
        #report tr:hover {background-color: #ddd;}
        #report th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <div id='report-heading'>
        <h1 class='title'>
            <?php echo $title; ?>
        </h1>
        <p class='date'>
            <strong>Date: </strong>
            <?php echo date('F j, Y, g:i a'); ?>
        </p>
    </div>
    <div class='table-responsive'>
        <table id='report'>
            <?php echo html_entity_decode($body); ?>
        </table>
    </div>
</body>
</html>