<?php
include ("_init.php");

// Load Cron Model
$cron_model = $registry->get('loader')->model('cron');

if (!isset($request->get['action_type']) 
	&& !isset($request->post['action_type']) 
		&& (!isset($argc) || !isset($argv[1]))) {

	exit();
}

$action_type = '';
if (isset($request->get['action_type'])) {
	$action_type = $request->get['action_type'];
} elseif (isset($request->post['action_type'])) {
	$action_type = $request->post['action_type'];
} elseif (isset($argv[1])) {
	$action_type = $argv[1];
}

$log->write('Cron: '.$action_type.' Started.');

if ($action_type == 'PUSHSQLTOREMOVESERVER') {
	if ($m = $cron_model->pushSqlToRemoteServer()) {
	    echo 'ok';
	} else {
		echo 'error';
	}
	exit;
}

if ($action_type == 'backup') {
	if ($m = $cron_model->do_table_backup()) {
	    echo 'ok';
	} else {
		echo 'error';
	}
	exit;
}

if ($m = $cron_model->run_cron()) {
    echo '<!doctype html><html><head><title>Cron Job</title><style>p{background:#F5F5F5;border:1px solid #EEE; padding:15px;}</style></head><body>';
    echo '<p>Corn job successfully run.</p>';
    foreach($m as $msg) {
        echo '<p>'.$msg.'</p>';
    }
    echo '</body></html>';
    exit;
}

/*
----------------------------------------
| Usages
----------------------------------------
|   Cron Job (run at 1:00 AM daily):
|   0 1 * * * wget -qO- http://pos/admin/cron.php >/dev/null 2>&1
*/