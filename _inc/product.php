<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// if user is not logged in then return an alert message
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// if user have not reading permission return an alert message
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_product')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('product');

// LOAD PRODUCT MODEL
$product_model = $registry->get('loader')->model('product');

// Validate post data
function validate_request_data($request, $language) 
{
  // Validate product name
  if (!validateString($request->post['p_name'])) {
    throw new Exception($language->get('error_product_name'));
  }

  // Validate product code
  if (empty($request->post['p_code'])) {
    throw new Exception($language->get('error_xproduct_code'));
  }

  // Validate category id
  if (!validateInteger($request->post['category_id'])) {
    throw new Exception($language->get('error_category_name'));
  }

  // Validate unit id
  if (!validateInteger($request->post['unit_id'])) {
    throw new Exception($language->get('error_unit_id'));
  }

  global $session;
  if (isset($session->data['stock_check']) && isset($session->data['quantity_check'])) {
    throw new Exception($language->get('error_invalid_purchase_code'));
  }
  
  // Validate product tax
  if (!validateInteger($request->post['taxrate_id'])) {
    throw new Exception($language->get('error_product_tax'));
  } 

  // Validate supplier id
  if (!validateInteger($request->post['sup_id'])) {
    throw new Exception($language->get('error_sup_id'));
  }

  // Validate box id
  if (!validateInteger($request->post['box_id'])) {
    throw new Exception($language->get('error_box_id'));
  }  

  // Validate expired date
  if (!isItValidDate($request->post['e_date'])) {
    throw new Exception($language->get('error_expired_date'));
  }

  // expired date must be greater than today
  if (!validateExpireDate($request->post['e_date'])) {
    throw new Exception($language->get('error_expired_date_below'));
  }

  // Validate store
  if (!isset($request->post['product_store']) || empty($request->post['product_store'])) {
    throw new Exception($language->get('error_store'));
  }

  // Validate status
  if (!is_numeric($request->post['status'])) {
    throw new Exception($language->get('error_status'));
  }

  // Validate sort order
  if (!is_numeric($request->post['sort_order'])) {
    throw new Exception($language->get('error_sort_order'));
  }
}

// Check product existance by id
function validate_existance($request, $language, $p_id = 0)
{
  global $db;

  $statement = $db->prepare("SELECT * FROM `products` WHERE `p_name` = ? AND `p_id` != ?");
  $statement->execute(array($request->post['p_name'], $p_id));
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_product_exist'));
  }
}

// Check product code
function validate_product_code($request, $language, $p_id = NULL)
{
  global $db;

  if ($p_id) {
    $statement = $db->prepare("SELECT * FROM `products` WHERE `p_code` = ? AND `p_id` != ?");
    $statement->execute(array($request->post['p_code'], $p_id));
  } else {
    $statement = $db->prepare("SELECT * FROM `products` WHERE `p_code` = ?");
    $statement->execute(array($request->post['p_code']));
  }
  if ($statement->rowCount() > 0) {
    throw new Exception($language->get('error_product_code_exist'));
  }
}

// Create product
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'CREATE')
{
  try {

    // Check create permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'create_product')) {
      throw new Exception($language->get('error_create_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate product code
    validate_product_code($request, $language);
    
    // Validate existance
    validate_existance($request, $language);

    $Hooks->do_action('Before_Create_Product');
  
    // insert product into database    
    $product_id = $product_model->addProduct($request->post);

    // get product info
    $product = $product_model->getProduct($product_id);

    $Hooks->do_action('After_Create_Product', $product);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_success'), 'id' => $product_id, 'product' => $product));
    exit();

  } catch (Exception $e) {
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 

// update product
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_product')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate product id
    if (!validateInteger($request->post['p_id'])) {
      throw new Exception($language->get('error_product_id'));
    }

    // Validate sell price
    if (!is_numeric($request->post['sell_price'])) {
      throw new Exception($language->get('error_sell_price'));
    }

    $p_id = $request->post['p_id'];

    if (DEMO && $p_id == 1) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Validate post data
    validate_request_data($request, $language);

    // Validate product code
    validate_product_code($request, $language, $p_id);

    // Validate existance
    validate_existance($request, $language, $p_id);

    $Hooks->do_action('Before_Update_Product', $p_id);
    
    // edit product        
    $product_model->editProduct($p_id, $request->post);

    $Hooks->do_action('After_Update_Product', $p_id);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_update_success'), 'id' => $p_id));
    exit();

  } catch (Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 

// delete product
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'DELETE')
{
  try {

    // Check delete product permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_product')) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // Validate product id
    if (!validateInteger($request->post['p_id'])) {
      throw new Exception($language->get('error_product_id'));
    }

    $p_id = $request->post['p_id'];

    if (DEMO && $p_id == 1) {
      throw new Exception($language->get('error_delete_permission'));
    }

    // validte delete action
    if (empty($request->post['delete_action'])) {
      throw new Exception($language->get('error_delete_action'));
    }

    if ($request->post['delete_action'] == 'insert_to' && empty($request->post['p_id'])) {
      throw new Exception($language->get('error_delete_product_name'));
    }

    // fetch product by id
    $product = $product_model->getProduct($p_id);

    // Check product exist or not
    if (!isset($product['p_id'])) {
      throw new Exception($language->get('text_not_found'));
    }

    $Hooks->do_action('Before_Delete_Product', $request);

    $action_type = $request->post['delete_action'];

    switch ($action_type) {
      case 'soft_delete':

        $product_model->updateStatus($p_id, 0);
        $message = $language->get('text_soft_delete');

        break;
      case 'delete_all':

        $belongs_stores = $product_model->getBelongsStore($p_id);
        foreach ($belongs_stores as $the_store) {

          $store_id = $the_store['store_id'];
          $stock_status = $product_model->isStockAvailable($p_id, $store_id);
          if ($stock_status) {
            throw new Exception("Oops!, Unbale to delete, Stock available in the " . store_field('name', $store_id));
          }
        }

        $product_model->deleteProduct($p_id); 
        $message = $language->get('text_delete');

        break;
    }

    $Hooks->do_action('After_Delete_Product', $product);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $message, 'id' => $p_id, 'action_type' => $action_type));
    exit();

  } catch (Exception $e) { 

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
}

// product create form
if (isset($request->get['action_type']) && $request->get['action_type'] == 'CREATE') 
{

  $Hooks->do_action('Before_Showing_Product_Form');
  include 'template/product_create_form.php';
  $Hooks->do_action('After_Showing_Product_Form');

  exit();
}

// product edit form
if (isset($request->get['p_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'EDIT') {
    
  // fetch product info
  $product = $product_model->getProduct($request->get['p_id']);

  $Hooks->do_action('Before_Showing_Product_Edit_Form', $product);
  include 'template/product_form.php';
  $Hooks->do_action('After_Showing_Product_Edit_Form', $product);

  exit();
}

// product delete form
if (isset($request->get['p_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'DELETE') {
    
  // fetch product info
  $product = $product_model->getProduct($request->get['p_id']);

  $Hooks->do_action('Before_Showing_Product_Delete_Form', $product);
  include 'template/product_del_form.php';
  $Hooks->do_action('After_Showing_Product_Delete_Form', $product);

  exit();
}

// product view template
if (isset($request->get['p_id']) AND isset($request->get['action_type']) && $request->get['action_type'] == 'VIEW') {

  // fetch product info
  $product = $product_model->getProduct($request->get['p_id']);

  $Hooks->do_action('Before_Showing_Product_View_Form', $product);
  include 'template/product_view_form.php';
  $Hooks->do_action('After_Showing_Product_View_Form', $product);

  exit();
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$Hooks->do_action('Before_Showing_Product_List');

$where_query = 'p2s.store_id = ' . store_id();
 
// DB table to use
$table = "(SELECT products.*, p2s.*, suppliers.sup_mobile, suppliers.sup_name as supplier, boxes.box_name FROM products 
  LEFT JOIN product_to_store p2s ON (products.p_id = p2s.product_id) 
  LEFT JOIN suppliers ON (p2s.sup_id = suppliers.sup_id) 
  LEFT JOIN boxes ON (p2s.box_id = boxes.box_id) 
  WHERE $where_query GROUP by products.p_id
  ORDER BY p2s.p_date DESC
  ) as products";
 
// Table's primary key
$primaryKey = 'p_id';
$columns = array(
  array(
    'db' => 'p_id',
    'dt' => 'DT_RowId',
    'formatter' => function( $d, $row ) {
        return 'row_'.$d;
    }
  ),
  array( 
    'db' => 'p_id',   
    'dt' => 'select' ,
    'formatter' => function($d, $row) {
        return '<input type="checkbox" name="selected[]" value="' . $row['p_id'] . '">';
    }
  ),
  array( 'db' => 'p_id', 'dt' => 'p_id' ),
  array( 'db' => 'p_code', 'dt' => 'p_code' ),
  array( 
    'db' => 'p_name',   
    'dt' => 'p_name' ,
    'formatter' => function($d, $row) {
        return html_entity_decode($row['p_name']);
    }
  ),
  array( 'db' => 'category_id',  'dt' => 'category_id' ),
  array( 'db' => 'sup_id',  'dt' => 'sup_id' ),
  array( 
    'db' => 'supplier',   
    'dt' => 'sup_name' ,
    'formatter' => function($d, $row) {
        return $row['supplier'];
    }
  ),
  array( 
    'db' => 'supplier',   
    'dt' => 'supplier' ,
    'formatter' => function($d, $row) {
        return "<a href=\"supplier_profile.php?sup_id=" . $row['sup_id'] . "\">" . ucfirst($row['supplier']) . "</a>";
    }
  ),
  array( 'db' => 'sup_mobile',  'dt' => 'supplier_mobile' ),
  array( 'db' => 'box_id',  'dt' => 'box_id' ),
  array( 
    'db' => 'box_name',   
    'dt' => 'box' ,
    'formatter' => function($d, $row) {
        return "<a href=\"box.php?box_id=" . $row['box_id'] . "&box_name=" . $row['box_name'] . "\">" . $row['box_name'] . "</a>";
    }
  ),
  array( 
    'db' => 'category_id',   
    'dt' => 'category_name' ,
    'formatter' => function($d, $row) {
        return get_the_category($row['category_id'], 'category_name');
    }
  ),
  array( 
    'db' => 'unit_id',   
    'dt' => 'unit' ,
    'formatter' => function($d, $row) {
        return get_the_unit($row['unit_id'], 'unit_name');
    }
  ),
  array( 'db' => 'quantity_in_stock',   'dt' => 'quantity_in_stock' ),
  array( 
    'db' => 'buy_price',   
    'dt' => 'buy_price' ,
    'formatter' => function($d, $row) use($currency) {
      return number_format($row['buy_price'],2,'.','');
    }
  ),
  array( 
    'db' => 'sell_price',   
    'dt' => 'sell_price' ,
    'formatter' => function($d, $row) use($currency) {
      return number_format($row['sell_price'],2,'.','');
    }
  ),
  array( 'db' => 'tax_method',   'dt' => 'tax_method' ),
  array( 
    'db' => 'taxrate_id',   
    'dt' => 'taxrate' ,
    'formatter' => function($d, $row) {
      $taxrate = get_the_taxrate($row['taxrate_id']);
      if ($taxrate) {
        return $taxrate['taxrate'];
      }
      return 0;
    }
  ),
  array( 
    'db' => 'taxrate_id',   
    'dt' => 'buy_tax_amount' ,
    'formatter' => function($d, $row) {
      $taxrate = get_the_taxrate($row['taxrate_id']);
      if ($taxrate) {
        return ($taxrate['taxrate'] / 100) * $row['buy_price'];
      }
      return '0.00';
    }
  ),
  array( 
    'db' => 'e_date',   
    'dt' => 'e_date' ,
    'formatter' => function($d, $row) {
      return $row['e_date'];
    }
  ),
  array( 
    'db' => 'status',   
    'dt' => 'status',
    'formatter' => function($d, $row) use($language) {
      return $row['status'] 
        ? '<span class="label label-success">'.$language->get('text_active').'</span>' 
        : '<span class="label label-warning">' .$language->get('text_inactive').'</span>';
    }
  ),
  array( 
    'db' => 'p_id',   
    'dt' => 'danger_stock',
    'formatter' => function($d, $row) {
      return "<span class=\"label label-warning\">" . $row['quantity_in_stock'] . "</span>";
    }
  ),
  array( 
    'db' => 'sup_id',   
    'dt' => 'btn_supplier_view',
    'formatter' => function($d, $row) {
      return "<a href=\"supplier_profile.php?sup_id=" . $row['sup_id'] . "&sup_name=" . $row['supplier'] . "&buy=1\" class=\"btn btn-sm btn-block btn-info\"><i class=\"fa fa-plus\"></i></a>";
    }
  ),
  array( 
    'db' => 'p_id',   
    'dt' => 'view_btn' ,
    'formatter' => function($d, $row) use($language) {
      return '<a class="btn btn-sm btn-block btn-warning" title="'.$language->get('button_view').'" href="product_details.php?p_id='.$row['p_id'].'"><i class="fa fa-eye"></i></a>';
    }
  ),
  array( 
    'db' => 'p_id',   
    'dt' => 'edit_btn' ,
    'formatter' => function($d, $row) use($language) {
      if ((DEMO && $row['p_id'] == 1 || !$row['status'])) {          
        return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-pencil"></i></button>';
      }
      if ($row['status']) {
        return'<button class="btn btn-sm btn-block btn-primary edit-product" type="button" title="'.$language->get('button_edit').'"><i class="fa fa-pencil"></i></button>';
      }
    }
  ),
  array( 
    'db' => 'p_id',   
    'dt' => 'delete_btn' ,
    'formatter' => function($d, $row) use($language) {
      if ((DEMO && $row['p_id'] == 1 || !$row['status'])) {          
        return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-trash"></i></button>';
      }
      return'<button class="btn btn-sm btn-block btn-danger product-delete" type="button" title="'.$language->get('button_delete').'"><i class="fa fa-trash"></i></button>';
    }
  ),
  array( 
    'db' => 'p_id',   
    'dt' => 'buy_btn' ,
    'formatter' => function($d, $row) use($language) {
      if ($row['status']) {
        return '<button class="btn btn-block btn-sm btn-primary buy-product" title="'.$language->get('button_buy_product').'"><i class="fa fa-plus"></i></button>';
      }
      return'<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-plus"></i></button>';
    }
  ),
  array( 
    'db' => 'p_id',   
    'dt' => 'barcode_btn' ,
    'formatter' => function($d, $row) use($language) {
      if ($row['status']) {
        return '<button class="btn btn-sm btn-block btn-success print-barcode" type="button" title="'.$language->get('button_barcode').'"><i class="fa fa-barcode"></i></button>';
      }

      return '<button class="btn btn-sm btn-block btn-default" type="button" disabled><i class="fa fa-barcode"></i></button>';
    }
  )
);
 
$where_query = '1=1';
if (isset($request->get['location']) && $request->get['location'] == 'trash') {
  $location = (int)$request->get['location'];
  $where_query .= ' AND status = 0';
} else {
  $where_query .= ' AND status = 1';
}
if (isset($request->get['sup_id']) && $request->get['sup_id']) {
  $sup_id = (int)$request->get['sup_id'];
  $where_query .= ' AND sup_id = ' . $sup_id;
}
if (isset($request->get['stock_query']) && $request->get['stock_query']) {
  $where_query .= " AND (quantity_in_stock <= alert_quantity)";
}
if (isset($request->get['expired_query']) && $request->get['expired_query']) {
  $where_query .= ' AND e_date <= NOW()';
}

// output for datatable
echo json_encode(
  SSP::complex($request->get, $sql_details, $table, $primaryKey, $columns, null, $where_query)
);

$Hooks->do_action('After_Showing_Product_List');

/**
 *===================
 * END DATATABLE
 *===================
 */