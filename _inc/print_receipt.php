<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// if user is not logged in then return error
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// if user have not reading permission return error
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'pos_print')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_print_permission')));
  exit();
}

//  Load Language File
$language->load('pos');

// LOAD INVOICE MODEL
$invoice_model = $registry->get('loader')->model('invoice');

if (!isset($request->get['invoice_id'])) {
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $language->get('error_pos_printer')));
    exit();
}
$invoice_id = $request->get['invoice_id'];
$invoice_info = $invoice_model->getInvoiceInfo($invoice_id);
if (!$invoice_info) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_pos_printer')));
  exit();
}

// fetch invoice info
$invoice_info     = $invoice_model->getInvoiceInfo($invoice_id);
$inv_type         = $invoice_info['inv_type'];
$created_at         = format_date($invoice_info['created_at']);
$customer_id      = $invoice_info['customer_id'];
$customer_name    = $invoice_info['customer_name'];
$customer_contact = $invoice_info['customer_mobile'] 
                      ? $invoice_info['customer_mobile'] 
                      : $invoice_info['customer_email'];
$currency_code    = $invoice_info['currency_code'];
$payment_method   = get_the_payment_method($invoice_info['payment_method'], 'name');
$invoice_note     = $invoice_info['invoice_note'];

// fetch invocie items
$invoice_items = $invoice_model->getInvoiceItems($invoice_id);

// fetch invoice price
$selling_price = $invoice_model->getSellingPrice($invoice_id);

// get receipt printer
$printer_id = store('printer');
$statement = $db->prepare("SELECT * FROM `printers` WHERE `id` = ?");
$statement->execute(array($printer_id));
$printer = $statement->fetch(PDO::FETCH_ASSOC);
$char_per_line = ($printer ? $printer['char_per_line'] : 42);

if (store('remote_printing') != 1) {
    ?>
    <script type="text/javascript">

        function receiptData() {

            receipt = {};
            receipt.store_name = "<?php echo printText(store('name'), $char_per_line);?>\n";

            receipt.header = "";
            receipt.header += "<?php echo printText(store('name'), $char_per_line);?>\n";
            <?php
            if (store('address')) { ?>
                receipt.header += "<?php echo printText(store('address'), $char_per_line);?>\n";
                <?php
            } ?>
            receipt.header += "<?php echo printText(store('mobile'), $char_per_line);?>";
            receipt.header += "\n";

            receipt.info = "";
            receipt.info += "<?php echo $language->get("label_datetime") . ": " . format_date($invoice_info['created_at']); ?>" + "\n";
            receipt.info += "<?php echo $language->get("label_invoice_id") . ": " . $invoice_id; ?>" + "\n";
            receipt.info += "<?php echo $language->get("label_created_by") . ": " .get_the_user($invoice_info['created_by'], 'username'); ?>" + "\n\n";
            receipt.info += "<?php echo $language->get("label_customer") . ": " . ($customer_name); ?>" + "\n";
            receipt.info += "<?php echo $language->get("label_mobile_no") . ": " . ($customer_contact); ?>" + "\n";
            receipt.info += "\n";

            receipt.items = "";
            <?php $r = 1; foreach ($invoice_items as $row): ?>
            receipt.items += "<?php echo printLine(product_name(addslashes("#".$r." ".$row['item_name']).' ', $char_per_line).": ", $char_per_line, ' '); ?>" + "\n";
            receipt.items += "<?php echo printLine("   ".($row['item_quantity'])." x ".currency_format($row['item_price']) . ":  ". currency_format($row['item_quantity']*$row['item_price']), $char_per_line, ' '); ?>" + "\n";
            <?php $r++; endforeach; ?>

            receipt.totals = "";
            receipt.totals += "<?php echo printLine($language->get('label_subtotal') . ": " . currency_format($invoice_info['payable_amount']), $char_per_line); ?>" + "\n";
            <?php
            receipt.totals += "<?php echo printLine($language->get("label_discount") . ": " . currency_format($invoice_info['discount_amount']), $char_per_line); ?>" + "\n";
            <?php
            receipt.totals += "<?php echo printLine($language->get("label_tax") . ": " . currency_format($invoice_info['tax_amount']), $char_per_line); ?>" + "\n";
            <?php
            ?>
            receipt.totals += "<?php echo printLine($language->get("label_grand_total") . ": " . currency_format($invoice_info['payable_amount']), $char_per_line); ?>" + "\n";
            receipt.totals += "<?php echo printLine($language->get("label_paid_amount") . ": " . currency_format($invoice_info['paid_amount']), $char_per_line); ?>" + "\n";
            receipt.totals += "<?php echo printLine($language->get("label_due_amount") . ": " . currency_format($invoice_info['todays_due']), $char_per_line); ?>" + "\n";

            receipt.footer = "";
            <?php
            if ($invoice_info['invoice_note']) { ?>
                receipt.footer += "<?php echo printText(strip_tags(preg_replace('/\s+/',' ', $invoice_info['invoice_note'])), $char_per_line); ?>" + "\n\n";
                <?php
            }
            ?>
            return receipt;
        }

        var socket = null;

    </script>

    <?php
    if ( ! store('remote_printing')) {
        ?>
        <script type="text/javascript">

            function printReceipt() {
                var receipt_data = receiptData();
                var socket_data = {
                    'printer': <?php echo json_encode($printer); ?>,
                    'logo': '',
                    'text': receipt_data,
                    'cash_drawer': '',
                };
                $.get('<?php echo root_url().'/_inc/print.php'; ?>', {data: JSON.stringify(socket_data)});
                var msg = {"msg":"Success","status":"OK"};
                console.log(msg);
                return msg;
            }
        </script>
        <?php
    } 
    ?>
    <script type="text/javascript">
        <?php
        if (store('auto_print')) {
            ?>
            $(document).ready(function() {
                console.log(receiptData());
                setTimeout(printReceipt, 1000);
            });
            <?php
        }
        ?>
    </script>
    <?php
}