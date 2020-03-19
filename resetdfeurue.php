<?php 
ob_start();
session_start();
include ("config.php");
include ("_inc/helper/common.php");
include ("_inc/helper/file.php");
include ("_inc/helper/network.php");

// if DEMO constant is not true, then redirect to dashboard
if (!DEMO) {
  redirect(root_url() . '/'.ADMINDIRNAME.'/dashboard.php');
}

// start maintenance
write_file(ROOT.DIRECTORY_SEPARATOR.'.maintenance', date('Y-m-d H:i:s'));

$dbhost = $sql_details['host'];
$dbname = $sql_details['db'];
$dbuser = $sql_details['user'];
$dbpass = $sql_details['pass'];

$info['username'] = get_pusername();
$info['purchase_code'] = get_pcode();
$info['action'] = 'demo';
$apiCall = apiCall($info);

if($apiCall->status == false) {
  $errors['database_import'] = 'Check internet connection.';
  die($errors['database_import']);
}

if(empty($apiCall->schema)) {
  $errors['database_import'] = 'Schema not found.';
  die($errors['database_import']);
}

$schemes = $apiCall->schema;

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

$mysqli->query('SET foreign_key_checks = 0');
if ($result = $mysqli->query("SHOW TABLES"))
{
    while($row = $result->fetch_array(MYSQLI_NUM))
    {
        $mysqli->query('TRUNCATE '.$row[0]);
    }
}

// Check for errors
if (mysqli_connect_errno()) {
  $errors['database_import'] = 'Schema is Not Valid.';
  die($errors['database_import']);
}

$mysqli->multi_query($schemes);

do {} while (mysqli_more_results($mysqli) && mysqli_next_result($mysqli));
$mysqli->close();

// end maintenance
@unlink(ROOT.DIRECTORY_SEPARATOR.'.maintenance');