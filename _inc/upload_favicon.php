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

if(isset($_FILES["faviconFile"]["type"]))
{	
	// Check permission
	if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'upload_favicon')) {
      throw new Exception($language->get('error_upload_favicon_permission'));
    }

    // Validate store id
    if (!validateInteger($request->post['store_id'])) {
    	throw new Exception($language->get('error_store_id'));
    }

    $store_id = $request->post['store_id'];

    // if (DEMO && $store_id == 1 || $store_id == 2) {
    //   throw new Exception($language->get('error_upload_favicon_permission'));
    // }

    $Hooks->do_action('Before_Upload_Favicon', $request);

	$validextensions = array("ico", "png");
	$temporary = explode(".", $_FILES["faviconFile"]["name"]);
	$file_extension = end($temporary);
	
	if ((($_FILES["faviconFile"]["type"] == "image/png") || ($_FILES["faviconFile"]["type"] == "image/ico")) && ($_FILES["faviconFile"]["size"] < 100000) //Approx. 100kb files can be uploaded.
	&& in_array($file_extension, $validextensions)) {
		
		if ($_FILES["faviconFile"]["error"] > 0) {
			echo "Return Code: " . $_FILES["faviconFile"]["error"] . "<br/><br/>";
		} else {
			$temp = explode(".", $_FILES["faviconFile"]["name"]);
			$newfilename = $store_id . '_favicon.' . end($temp);
			$sourcePath = $_FILES["faviconFile"]["tmp_name"]; //Storing source path of the file in a variable
			$targetPath = "../assets/itsolution24/img/logo-favicons/".$newfilename; //Target path where file is to be stored
			if(move_uploaded_file($sourcePath,$targetPath)) {

				$statement = $db->prepare("UPDATE `stores` SET `favicon` = ? WHERE `store_id` = ?");
				$statement->execute(array($newfilename, $store_id));
			}; 
			echo "<span class='success'>Favicon Successfully Uploaded...!!</span><br/>";
			echo "<br/><b>Favicon Name:</b> " . $_FILES["faviconFile"]["name"] . "<br>";
			echo "<b>Type:</b> " . $_FILES["faviconFile"]["type"] . "<br>";
			echo "<b>Size:</b> " . ($_FILES["faviconFile"]["size"] / 1024) . " kB<br>";
		}

		$Hooks->do_action('After_Upload_Favicon', $request);

	} else {

		echo "<span class='invalid'>***Invalid file Size or Type***<span>";
		
	}
}