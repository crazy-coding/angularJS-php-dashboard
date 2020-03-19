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

//  Load Language File
$language->load('product');

// LOAD PRODUCT MODEL
$product_model = $registry->get('loader')->model('product');

if($request->server['REQUEST_METHOD'] == 'POST' && isset($request->get['action'])) 
{
  try {

    // Check permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'product_bulk_action') || DEMO) {
      throw new Exception($language->get('error_bulk_permission'));
    }

    $action = $request->get['action'];

    // Check, if there has selected item or not
    if (!isset($request->post['selected']) || empty($request->post['selected'])) {
      throw new Exception($language->get('error_no_selected'));
    }

    $Hooks->do_action('After_Product_Bulk_Action', $action);

    $ids = $request->post['selected'];
    if (!is_array($ids)) {
      $ids = array($ids);
    }
    
    $id_length = count($ids);

    switch ($action) {
      case 'delete':

          // Check delete product permission
          if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'delete_all_product')) {
            throw new Exception(sprintf($language->get('error_delete_permission'), $language->get('text_product')));
          }

          for ($i=0; $i < $id_length; $i++) { 
            $id = $ids[$i];

            if (DEMO && $id == 1) {
              continue;
            }

            // delete product with all relevant content
            $product_model->deleteWithRelatedContent($id);
          }
          
          $success_message = $language->get('success_delete_all');
          
        break;
      
      case 'restore':
          
          // Check product restore permission
          if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'restore_all_product')) {
            throw new Exception(sprintf($language->get('error_restore_permission'), $language->get('text_product')));
          }

          for ($i=0; $i < $id_length; $i++) { 
            $id = $ids[$i];

            if (DEMO && $id == 1) {
              continue;
            }

            // update product status
            $product_model->updateStatus($id, 1, store_id());
          }

          $success_message = $language->get('success_restore_all');

        break;

      default:
        # code...
        break;
    }

    $Hooks->do_action('After_Product_Bulk_Action', $action);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $success_message));
    exit();

  } catch (Exception $e) {

    $error_message = $e->getMessage();
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $error_message));
    exit();
  }
}
