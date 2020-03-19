<?php
function is_admin()
{
	global $user;
	return $user->getGroupId() == 1;
}

function user($field) 
{
	return isset($_SESSION['user'][$field]) ? $_SESSION['user'][$field] : null;
}

function user_id() 
{
	global $user;

	return $user->getId();

}

function get_users() 
{
	global $registry;

	$model = $registry->get('loader')->model('user');

	return $model->getUsers();
}

function get_the_user($id, $field = null) 
{
	global $registry;

	$model = $registry->get('loader')->model('user');

	$user = $model->getUser($id);

	if ($field && isset($user[$field])) {
		return $user[$field];
	} elseif ($field) {
		return;
	}

	return $user;
}

function count_user_store($user_id = false) 
{
	global $user;

	$user_id  = $user_id ? $user_id : user_id();

	return $user->countBelongsStore($user_id);
}