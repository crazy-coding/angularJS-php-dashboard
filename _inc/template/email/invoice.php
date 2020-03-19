<!Doctype html>
<head>
    <title>Invoice &rarr; <?php echo $recipient_name; ?></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- This CSS use for E-mail that's why internal css is mandatory-->
    <style type="text/css">
        .barcodes {
            display: none;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-responsive {
            min-height: .01%;
            overflow-x: auto;
        }
        #invoice {
            font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            font-size: 90%;
            padding: 0;
            width: 520px;
            margin: 0;
            line-height: 1.4;
        }
        #invoice .logo {
            display: none;
        }
        #invoice thead tr.active {
            background-color: #f9f9f9;
        }
        #invoice th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
        }
        #invoice td, #invoice th {
            padding: 3px;
        }
        #invoice th[class~=text-right],
        #invoice td[class~=text-right] {
            text-align: right;
        }
        #invoice th[class~=text-center],
        #invoice td[class~=text-center] {
            text-align: center;
        }
        #invoice th.hide-in-mail,
        #invoice td.hide-in-mail {
            display: none;
        }
        #invoice .table {
            border: none;
        }
        #invoice .table td {
            padding: 2px;
            border: none;
        }
        #invoice .logo img{
            width: 60px;
        }
        #invoice .invoice-header {
            text-align: center;
        }
        #invoice .invoice-header h4 {
            margin: 0;
        }
        #invoice .invoice-header p {
            padding: 0;
            margin: 0;
        }   
        #invoice tr.invoice-address td {
            padding-bottom: 20px!important;
        }
        #invoice .invoice-items table tr:last-child td  {
            border-bottom: 2px solid #ddd;
        }
        #invoice tfoot {
            display: none;
        }
        #invoice .invoice_authority {
            margin-top: 200px;
            text-align: center;
        }
        #invoice .invoice_authority .name {
            padding: 0;
            font-weight: 700;
            border-bottom: 1px dotted #ddd;
        }
        #invoice .invoice_authority .name span {
            display: inline-block;
            border-bottom: 1px solid #ddd;
            padding: 5px;
        }
        #invoice .invoice_authority {
            position: relative;
            text-align: center;
            margin-top: 50px;
        }
        #selling_bill {
            float: right;
        }
        #selling_bill tr td {
            border-bottom: 1px solid #ddd!important;
        }
        #selling_bill tr:last-child td {
            border: 0!important;
        }
        #invoice .footer-actions {
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
    <h4>
        <strong>Dear <?php echo $recipient_name; ?>,</strong>
    </h4>
    <p>Thank you for choosing <?php echo $store_name; ?>. Here's a summary of your purchased invoice.</p>

    <div id="invoice">
        <?php echo html_entity_decode($body); ?>
    </div>

    <br/><br/>Regards, 
    <br/>Admin, <?php echo $store_name; ?>, <?php echo $store_address; ?>
</body>
</html>