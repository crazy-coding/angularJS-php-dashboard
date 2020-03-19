<?php
session_start();
include ("../_init.php");
$user->logout();

// REDIRECT IF USER LOGGED IN
if (!$user->isLogged()) {
  redirect(root_url());
}