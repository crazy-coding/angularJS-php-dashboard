<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if your logged in or not
// if user is not logged in then return an alert message
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

//  Load Language File
$language->load('sms');

// update store
if ($request->server['REQUEST_METHOD'] == 'POST' && isset($request->post['action_type']) && $request->post['action_type'] == 'UPDATE')
{
  try {

    // Check update permission
    if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'update_sms_setting')) {
      throw new Exception($language->get('error_update_permission'));
    }

    // Clickatell
    $setting = $request->post['setting']['clickatell'];
    $statement = $db->prepare("UPDATE `sms_setting` SET `username` = ?, `password` = ?, `api_id` = ? WHERE `type` = ?");
    $statement->execute(array($setting['username'], $setting['password'], $setting['api_id'], 'Clickatell'));

    // Twilio
    $setting = $request->post['setting']['twilio'];
    $statement = $db->prepare("UPDATE `sms_setting` SET `username` = ?, `password` = ?, `api_id` = ? WHERE `type` = ?");
    $statement->execute(array($setting['sender_id'], $setting['auth_key'], $setting['contact'], 'Twilio'));

    // Msg91
    $setting = $request->post['setting']['msg91'];
    $statement = $db->prepare("UPDATE `sms_setting` SET `auth_key` = ?, `sender_id` = ? WHERE `type` = ?");
    $statement->execute(array($setting['auth_key'], $setting['sender_id'], 'Msg91'));

    // Onnorokomsms
    $setting = $request->post['setting']['onnorokomsms'];
    $statement = $db->prepare("UPDATE `sms_setting` SET `username` = ?, `password` = ?, `maskname` = ?, `campaignname` = ? WHERE `type` = ?");
    $statement->execute(array($setting['username'], $setting['password'], $setting['maskname'], $setting['campaignname'], 'Onnorokomsms'));

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_update_success')));
    exit();

  } catch (Exception $e) {

    header('HTTP/1.1 422 Unprocessable Entity');
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('errorMsg' => $e->getMessage()));
    exit();

  }
}