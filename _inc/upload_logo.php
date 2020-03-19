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

if(isset($_FILES["file"]["type"]))
{
	// Check permission
	if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'upload_logo')) {
      throw new Exception($language->get('error_upload_logo_permission'));
    }

    // Validate store id
    if (!validateInteger($request->post['store_id'])) {
    	throw new Exception($language->get('error_store_id'));
    }

    $Hooks->do_action('Before_Upload_Logo', $request);

    $store_id = $request->post['store_id'];

    // if (DEMO && $store_id == 1 || $store_id == 2) {
    //   throw new Exception($language->get('error_upload_logo_permission'));
    // }

	$validextensions = array("jpeg", "jpg", "png");
	$temporary = explode(".", $_FILES["file"]["name"]);
	$file_extension = end($temporary);
	
	if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
	) && ($_FILES["file"]["size"] < 100000)//Approx. 100kb files can be uploaded.
	&& in_array($file_extension, $validextensions)) {
		
		if ($_FILES["file"]["error"] > 0) {
			
			echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";

		} else {

			$temp = explode(".", $_FILES["file"]["name"]);
			$newfilename = $store_id . '_logo.' . end($temp);
			$sourcePath = $_FILES["file"]["tmp_name"]; // Storing source path of the file in a variable
			$targetPath = "../assets/itsolution24/img/logo-favicons/".$newfilename; // Target path where file is to be stored
			if(move_uploaded_file($sourcePath,$targetPath)) {
				$statement = $db->prepare("UPDATE `stores` SET `logo` = ? WHERE `store_id` = ?");
				$statement->execute(array($newfilename, $store_id));
			}; 
			echo "<span class='success'>Logo Successfully Uploaded...!!</span><br/>";
			echo "<br/><b>File Name:</b> " . $_FILES["file"]["name"] . "<br>";
			echo "<b>Type:</b> " . $_FILES["file"]["type"] . "<br>";
			echo "<b>Size:</b> " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
		}

		$Hooks->do_action('After_Upload_Logo', $request);

	} else {

		echo "<span class='invalid'>***Invalid file Size or Type***<span>";
	}
}