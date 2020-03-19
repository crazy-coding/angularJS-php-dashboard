<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// if user is not logged in then an alert message
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// if user have not reading permission an alert message
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'send_sms')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('sms');

// update sms
if ($request->server['REQUEST_METHOD'] == 'GET' && isset($request->get['action_type']) && $request->get['action_type'] == 'PEOPLE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'send_sms')) {
      throw new Exception($language->get('error_update_permission'));
    }

    $people_type = $request->get['people_type'];

    $peoples = '';

    switch ($people_type) {
      case 'all_customer':
        $statement = $db->prepare("SELECT `c`.`customer_id` AS `id`, `c`.`customer_name` AS `name`, `c`.`customer_mobile` AS `mobile` FROM `customers` c LEFT JOIN `customer_to_store` c2s ON (`c`.`customer_id` = `c2s`.`customer_id`) WHERE `c2s`.`store_id` = ? AND `status` = ?");
        $statement->execute(array(store_id(), 1));
        $peoples = $statement->fetchAll(PDO::FETCH_ASSOC);
        break;

      case 'all_user':
        $statement = $db->prepare("SELECT `u`.`id`, `u`.`username` AS `name`, `u`.`mobile` FROM `users` u LEFT JOIN `user_to_store` u2s ON (`u`.`id` = `u2s`.`user_id`) WHERE `u2s`.`store_id` = ? AND `status` = ?");
        $statement->execute(array(store_id(), 1));
        $peoples = $statement->fetchAll(PDO::FETCH_ASSOC);
        break;
      
      default:
        $statement = $db->prepare("SELECT `u`.`id`, `u`.`username` AS `name`, `u`.`mobile` FROM `users` u LEFT JOIN `user_to_store` u2s ON (`u`.`id` = `u2s`.`user_id`) WHERE `u2s`.`store_id` = ? AND `status` = ? AND `u`.`group_id` = ?");
        $statement->execute(array(store_id(), 1, $people_type));
        $peoples = $statement->fetchAll(PDO::FETCH_ASSOC);
        break;
    }

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_update_success'), 'peoples' => $peoples));
    exit();

  } catch (Exception $e) { 
    
    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();
  }
} 