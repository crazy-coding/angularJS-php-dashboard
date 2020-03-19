<?php
function get_the_invoice($invoice_id)
{
    global $registry;
    $invoice_model = $registry->get('loader')->model('invoice');
    return $invoice_model->getInvoiceInfo($invoice_id);
}

function get_invoice_items($invoice_id, $store_id = null)
{
    global $registry;
    $invoice_model = $registry->get('loader')->model('invoice');
    return $invoice_model->getInvoiceItems($invoice_id, $store_id);
}

function get_invoice_due_amount($invoice_id)
{
	global $registry;
	$invoice_model = $registry->get('loader')->model('invoice');
	return $invoice_model->getDueAmount($invoice_id);
}

function get_invoice_last_edited_versiont_id($invoice_id)
{
	global $registry;
	$invoice_model = $registry->get('loader')->model('invoice');
	return $invoice_model->getInvoiceLastEditedVersionId($invoice_id);
}

function get_invoice_due_paid_amount($invoice_id)
{
	global $registry;
	$invoice_model = $registry->get('loader')->model('invoice');
	return $invoice_model->getDuePaidAmount($invoice_id);
}

function get_invoice_due_paid_discount_amount($invoice_id)
{
	global $registry;
	$invoice_model = $registry->get('loader')->model('invoice');
	return $invoice_model->getDuePaidDiscountAmount($invoice_id);
}

function get_invoice_due_paid_amount_rows($invoice_id)
{
	global $registry;
	$invoice_model = $registry->get('loader')->model('invoice');
	return $invoice_model->getDuePaidAmountRows($invoice_id);
}

function total_invoice_today($store_id = null)
{
	global $registry;
	$invoice_model = $registry->get('loader')->model('invoice');
	return $invoice_model->totalToday($store_id);
}

function total_invoice($from = null, $to = null, $store_id = null)
{
    global $registry;
    $invoice_model = $registry->get('loader')->model('invoice');
    return $invoice_model->total($from, $to, $store_id);

}

function unique_invoice_id()
{
    global $db;
    $statement = $db->prepare("SELECT `auto_increment` FROM INFORMATION_SCHEMA.TABLES WHERE `table_name` = 'selling_info'");
    $statement->execute(array());
    $invoice_id = $statement->fetch(PDO::FETCH_ASSOC)["auto_increment"];
    $invoice_id += 100000; 
    return $invoice_id;
}

function generate_invoice_id($type = 'sell', $invoice_id = null)
{
    global $registry;
    global $invoice_init_prefix;
    $init_prefix = get_preference('invoice_prefix') ? get_preference('invoice_prefix') : $invoice_init_prefix[$type];
    $prefix = !get_preference('invoice_prefix') ? date('y').date('m').date('d') : '';
    $invoice_model = $registry->get('loader')->model('invoice');
    if (!$invoice_id) {
        $last_invoice = $invoice_model->getLastInvoice($type);
        $invoice_id = isset($last_invoice['invoice_id']) ? $last_invoice['invoice_id'] : $init_prefix.$prefix.'1';
    }
    if ($invoice_model->hasInvoice($invoice_id)) {
        $invoice_id = str_replace(array('A','B','C','D','E','F','G','H','I','G','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'), '', $invoice_id);
        $invoice_id = (int)substr($invoice_id,-4) +1;
        $temp_invoice_id = $init_prefix.$prefix.$invoice_id;
        $zero_length = 11 - strlen($temp_invoice_id);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $invoice_id = $init_prefix.$prefix.store_id().$zeros.$invoice_id;
        generate_invoice_id($type, $invoice_id);
    } else {
        $zero_length = 11 - strlen($invoice_id);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $invoice_id = $init_prefix.$prefix.store_id().$zeros.'1';
    }
    return $invoice_id;
}

function generate_customer_transacton_ref_no($type = 'purchase', $reference_no = null)
{
    global $registry;
    $store_id = store_id();
    $prfix = 'CT';
    
    $invoice_model = $registry->get('loader')->model('invoice');
    if (!$reference_no) {

        $store_id = $store_id ? $store_id : store_id();
        $statemtnt = $registry->get('db')->prepare("SELECT * FROM `customer_transactions` WHERE `store_id` = ? AND `type` = ? ORDER BY `id` DESC");
        $statemtnt->execute(array($store_id, $type));
        $last_transaction = $statemtnt->fetch(PDO::FETCH_ASSOC);

        $reference_no = isset($last_transaction['reference_no']) ? $last_transaction['reference_no'] : date('y').date('m').date('d').'1';
    }
    $statement = $registry->get('db')->prepare("SELECT * FROM `customer_transactions` WHERE `store_id` = ? AND `reference_no` = ?");
    $statement->execute(array($store_id, $reference_no));
    $invoice = $statement->fetch(PDO::FETCH_ASSOC);

    if (isset($invoice['reference_no'])) {
        $reference_no = str_replace(array('A','B','C','D','E','F','G','H','I','G','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'), '', $reference_no);
        $reference_no = (int)substr($reference_no,-4) +1;
        $temp_reference_no = $prfix.date('y').date('m').date('d').$reference_no;
        $zero_length = 11 - strlen($temp_reference_no);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $reference_no = $prfix.date('y').date('m').date('d').store_id().$zeros.$reference_no;
        generate_customer_transacton_ref_no($type, $reference_no);
    } else {
        $zero_length = 11 - strlen($reference_no);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $reference_no = $prfix.date('y').date('m').date('d').store_id().$zeros.'1';
    }
    return $reference_no;
}

function generate_supplier_transacton_ref_no($type = 'purchase', $reference_no = null)
{
    global $registry;
    $store_id = store_id();
    $prfix = 'CT';
    
    $invoice_model = $registry->get('loader')->model('invoice');
    if (!$reference_no) {

        $store_id = $store_id ? $store_id : store_id();
        $statemtnt = $registry->get('db')->prepare("SELECT * FROM `supplier_transactions` WHERE `store_id` = ? AND `type` = ? ORDER BY `id` DESC");
        $statemtnt->execute(array($store_id, $type));
        $last_transaction = $statemtnt->fetch(PDO::FETCH_ASSOC);

        $reference_no = isset($last_transaction['reference_no']) ? $last_transaction['reference_no'] : date('y').date('m').date('d').'1';
    }
    $statement = $registry->get('db')->prepare("SELECT * FROM `supplier_transactions` WHERE `store_id` = ? AND `reference_no` = ?");
    $statement->execute(array($store_id, $reference_no));
    $invoice = $statement->fetch(PDO::FETCH_ASSOC);

    if (isset($invoice['reference_no'])) {
        $reference_no = str_replace(array('A','B','C','D','E','F','G','H','I','G','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'), '', $reference_no);
        $reference_no = (int)substr($reference_no,-4) +1;
        $temp_reference_no = $prfix.date('y').date('m').date('d').$reference_no;
        $zero_length = 11 - strlen($temp_reference_no);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $reference_no = $prfix.date('y').date('m').date('d').store_id().$zeros.$reference_no;
        generate_supplier_transacton_ref_no($type, $reference_no);
    } else {
        $zero_length = 11 - strlen($reference_no);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $reference_no = $prfix.date('y').date('m').date('d').store_id().$zeros.'1';
    }
    return $reference_no;
}

function generate_return_reference_no($reference_no = null)
{
    global $registry;
    $store_id = store_id();
    $prfix = 'R';
    
    $invoice_model = $registry->get('loader')->model('invoice');
    if (!$reference_no) {

        $store_id = $store_id ? $store_id : store_id();
        $statemtnt = $registry->get('db')->prepare("SELECT * FROM `returns` WHERE `store_id` = ?  ORDER BY `id` DESC");
        $statemtnt->execute(array($store_id));
        $last_transaction = $statemtnt->fetch(PDO::FETCH_ASSOC);

        $reference_no = isset($last_transaction['reference_no']) ? $last_transaction['reference_no'] : date('y').date('m').date('d').'1';
    }
    $statement = $registry->get('db')->prepare("SELECT * FROM `returns` WHERE `store_id` = ? AND `reference_no` = ?");
    $statement->execute(array($store_id, $reference_no));
    $invoice = $statement->fetch(PDO::FETCH_ASSOC);

    if (isset($invoice['reference_no'])) {
        $reference_no = str_replace(array('A','B','C','D','E','F','G','H','I','G','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'), '', $reference_no);
        $reference_no = (int)substr($reference_no,-4) +1;
        $temp_reference_no = $prfix.date('y').date('m').date('d').$reference_no;
        $zero_length = 11 - strlen($temp_reference_no);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $reference_no = $prfix.date('y').date('m').date('d').store_id().$zeros.$reference_no;
        generate_return_reference_no($reference_no);
    } else {
        $zero_length = 11 - strlen($reference_no);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $reference_no = $prfix.date('y').date('m').date('d').store_id().$zeros.'1';
    }
    return $reference_no;
}

function generate_transfer_reference_no($reference_no = null)
{
    global $registry;
    $store_id = store_id();
    $prfix = 'R';
    
    $invoice_model = $registry->get('loader')->model('invoice');
    if (!$reference_no) {

        $store_id = $store_id ? $store_id : store_id();
        $statemtnt = $registry->get('db')->prepare("SELECT * FROM `returns` WHERE `store_id` = ?  ORDER BY `id` DESC");
        $statemtnt->execute(array($store_id));
        $last_transaction = $statemtnt->fetch(PDO::FETCH_ASSOC);

        $reference_no = isset($last_transaction['reference_no']) ? $last_transaction['reference_no'] : date('y').date('m').date('d').'1';
    }
    $statement = $registry->get('db')->prepare("SELECT * FROM `returns` WHERE `store_id` = ? AND `reference_no` = ?");
    $statement->execute(array($store_id, $reference_no));
    $invoice = $statement->fetch(PDO::FETCH_ASSOC);

    if (isset($invoice['reference_no'])) {
        $reference_no = str_replace(array('A','B','C','D','E','F','G','H','I','G','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'), '', $reference_no);
        $reference_no = (int)substr($reference_no,-4) +1;
        $temp_reference_no = $prfix.date('y').date('m').date('d').$reference_no;
        $zero_length = 11 - strlen($temp_reference_no);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $reference_no = $prfix.date('y').date('m').date('d').store_id().$zeros.$reference_no;
        generate_transfer_reference_no($reference_no);
    } else {
        $zero_length = 11 - strlen($reference_no);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $reference_no = $prfix.date('y').date('m').date('d').store_id().$zeros.'1';
    }
    return $reference_no;
}

function generate_purchase_return_reference_no($reference_no = null)
{
    global $registry;
    $store_id = store_id();
    $prfix = 'R';
    
    if (!$reference_no) {

        $store_id = $store_id ? $store_id : store_id();
        $statemtnt = $registry->get('db')->prepare("SELECT * FROM `buying_returns` WHERE `store_id` = ?  ORDER BY `id` DESC");
        $statemtnt->execute(array($store_id));
        $last_transaction = $statemtnt->fetch(PDO::FETCH_ASSOC);

        $reference_no = isset($last_transaction['reference_no']) ? $last_transaction['reference_no'] : date('y').date('m').date('d').'1';
    }
    $statement = $registry->get('db')->prepare("SELECT * FROM `buying_returns` WHERE `store_id` = ? AND `reference_no` = ?");
    $statement->execute(array($store_id, $reference_no));
    $invoice = $statement->fetch(PDO::FETCH_ASSOC);

    if (isset($invoice['reference_no'])) {
        $reference_no = str_replace(array('A','B','C','D','E','F','G','H','I','G','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'), '', $reference_no);
        $reference_no = (int)substr($reference_no,-4) +1;
        $temp_reference_no = $prfix.date('y').date('m').date('d').$reference_no;
        $zero_length = 11 - strlen($temp_reference_no);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $reference_no = $prfix.date('y').date('m').date('d').store_id().$zeros.$reference_no;
        generate_purchase_return_reference_no($reference_no);
    } else {
        $zero_length = 11 - strlen($reference_no);
        $zeros = '';
        for ($i=0; $i < $zero_length; $i++) { 
            $zeros .= '0';
        }
        $reference_no = $prfix.date('y').date('m').date('d').store_id().$zeros.'1';
    }
    return $reference_no;
}

function total_trash_invoice($from, $to)
{
    global $registry;
    $invoice_model = $registry->get('loader')->model('invoice');
    return $invoice_model->totalTrash($from, $to);
}

function invoice_edit_lifespan()
{
    $lifespan = get_preference('invoice_edit_lifespan');
    $lifespan_unit = get_preference('invoice_edit_lifespan_unit');
    switch ($lifespan_unit) {
        case 'minute':
            $lifespan =  time() - ($lifespan * 60);
            break;
        case 'second':
                $lifespan =  time() - $lifespan;
            break;
        default:
            $lifespan = time()-(60*60*24);
            break;
    }
    return $lifespan;
}

function invoice_delete_lifespan()
{
    $lifespan = get_preference('invoice_delete_lifespan');
    $lifespan_unit = get_preference('invoice_delete_lifespan_unit');
    switch ($lifespan_unit) {
        case 'minute':
            $lifespan =  time() - ($lifespan * 60);
            break;
        case 'second':
                $lifespan =  time() - $lifespan;
            break;
        default:
            $lifespan = time()-(60*60*24);
            break;
    }
    return $lifespan;
}