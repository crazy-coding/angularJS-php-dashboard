<?php
/*
| -----------------------------------------------------
| PRODUCT NAME: 	Modern POS
| -----------------------------------------------------
| AUTHOR:     ITSOLUTION24.COM
| -----------------------------------------------------
| EMAIL:      info@itsolution24.com
| -----------------------------------------------------
| COPYRIGHT:    RESERVED BY ITSOLUTION24.COM
| -----------------------------------------------------
| WEBSITE:      http://itsolution24.com
| -----------------------------------------------------
*/
class ModelCron extends Model
{
  public $messages = array();

  public function run_cron()
  {
      if ($this->isHightTime()) {
        $this->health_checkup();
        $this->do_backup();
      } else {
        $this->messages[] = 'Schedule tasks already completed!';
      }
      return $this->messages;
  }

  private function isHightTime($store_id = null)
  {
    $store_id = $store_id ? $store_id : store_id();
    $statement = $this->db->prepare("SELECT * FROM `stores` WHERE `store_id` = ?");
    $statement->execute(array($store_id));
    $feedback = $statement->fetch(PDO::FETCH_ASSOC);
    $feedback_at = $feedback['feedback_at'];
    $next_feedback_at = date('Y-m-d H:i:s', strtotime($feedback_at . ' +1 day'));
    if (strtotime($next_feedback_at) > strtotime(date_time())) {
      return false;
    }
    return !$feedback['feedback_at'] || strtotime($feedback['feedback_at']) <= strtotime(date_time() . ' -1 day');
  }

  private function check_update($response = null)
  {      
    if (settings('is_update_available')) {return true;};
    $data = array('action' => 'update', 'version' => settings('version'), 'purchase_code' => get_pcode(), 'username' => get_pusername());   
    if (!$response) {
      $response = apiCall($data);
    }
    if($response->status == false) {
      return false;
    }
    $update_info = json_decode($response->update_info,true);
    if (isset($update_info['version']) && $update_info['version'] != settings('version')) {
      $statement = $this->db->prepare("UPDATE `settings` SET `is_update_available` = ?, `update_version` = ?, `update_link` = ? WHERE `id` = ?");
      $statement->execute(array(1, $update_info['version'], $update_info['link'], 1));
    } else {
      $statement = $this->db->prepare('UPDATE `settings` SET `is_update_available` = ?, `update_version` = ?, `update_link` = ? WHERE `id` = ?');
      $statement->execute(array(0, NULL, NULL, 1));
        return false;
    }
    return false;
  }

  public function do_backup() 
  {
    $statement = $this->db->prepare("SHOW TABLES");
    $statement->execute();
    $tables = $statement->fetchAll(PDO::FETCH_NUM);
    $backup = $this->make_backup($tables);
    // $db_name = 'mpos-backup-on-' . date("Y-m-d-H-i-s") . '.txt';
    $db_name = 'mpos-backup-on-' . date("Y-m-d") . '.txt';
    $save = DIR_BACKUP . $db_name;
    write_file($save, $backup);
    $files = glob(DIR_BACKUP.'*.txt', GLOB_BRACE);
    $now   = time();
    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 60 * 60 * 24 * 30) {
                unlink($file);
            }
        }
    }
    $this->messages[] = 'Backup file saved.';
    return true;
  }

  public function do_table_backup() 
  {
    $dir_name = date("Y-m-d").DIRECTORY_SEPARATOR;
    if (!is_dir(DIR_BACKUP.$dir_name)) {
      @mkdir(DIR_BACKUP.$dir_name);
    }
    $statement = $this->db->prepare("SHOW TABLES");
    $statement->execute();
    $tables = $statement->fetchAll(PDO::FETCH_NUM);
    foreach ($tables as $table) {
      $table = $table[0];
      $backup = $this->make_table_backup($table);
      $db_name = $table.'.txt';
      $save = DIR_BACKUP.$dir_name.$db_name;
      write_file($save, $backup);
      $files = glob(DIR_BACKUP.'*.txt', GLOB_BRACE);
      $now   = time();
      foreach ($files as $file) {
          if (is_file($file)) {
              if ($now - filemtime($file) >= 60 * 60 * 24 * 30) {
                  @unlink($file);
              }
          }
      }
    }
    $this->messages[] = 'Backup file saved.';
    return true;
  }

  public function make_backup($tables = array())
  {
    $output = '';

    if (empty($tables)) {
      $statement = $this->db->prepare("SHOW TABLES");
      $statement->execute();
      $tables = $statement->fetchAll(PDO::FETCH_NUM);
    }


    foreach ($tables as $table) 
    {

      $table = is_array($table) && isset($table[0]) ? $table[0] : $table;
      $output .= 'TRUNCATE TABLE `' . $table . '`;' . "\n\n";

      $statement = $this->db->prepare("SELECT * FROM `" . $table . "`");
      $statement->execute();
      $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

      foreach ($rows as $result) {
        $fields = '';

        foreach (array_keys($result) as $value) {
          $fields .= '`' . $value . '`, ';
        }

        $values = '';

        foreach (array_values($result) as $value) {
          $value = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $value);
          $value = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $value);
          $value = str_replace('\\', '\\\\',  $value);
          $value = str_replace('\'', '\\\'',  $value);
          $value = str_replace('\\\n', '\n',  $value);
          $value = str_replace('\\\r', '\r',  $value);
          $value = str_replace('\\\t', '\t',  $value);

          $values .= '\'' . $value . '\', ';
        }

        $output .= 'INSERT INTO `' . $table . '` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
      }

      $output .= "\n\n";
    }
    $this->messages[] = 'Backup file created.';
    return $output;
  }

  public function make_table_backup($table)
  {
    $output = '';
    $table = is_array($table) && isset($table[0]) ? $table[0] : $table;
    $output .= 'TRUNCATE TABLE `' . $table . '`;' . "\n\n";
    $statement = $this->db->prepare("SELECT * FROM `" . $table . "`");
    $statement->execute();
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $result) {
      $fields = '';
      foreach (array_keys($result) as $value) {
        $fields .= '`' . $value . '`, ';
      }

      $values = '';
      foreach (array_values($result) as $value) {
        $value = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $value);
        $value = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $value);
        $value = str_replace('\\', '\\\\',  $value);
        $value = str_replace('\'', '\\\'',  $value);
        $value = str_replace('\\\n', '\n',  $value);
        $value = str_replace('\\\r', '\r',  $value);
        $value = str_replace('\\\t', '\t',  $value);

        $values .= '\'' . $value . '\', ';
      }
      $output .= 'INSERT INTO `' . $table . '` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
    }
    $output .= "\n\n";
    return $output;
  }

  public function readSyncFileData($path) 
  {
    // $dbhost = $sql_details['host'];
    // $dbname = $sql_details['db'];
    // $dbuser = $sql_details['user'];
    // $dbpass = $sql_details['pass'];
    // try {
    //   $db = new PDO("mysql:host={$dbhost};dbname={$dbname};charset=utf8",$dbuser,$dbpass);
    //   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // }
    // Catch(PDOException $e) {
    //   die('Connection error: '.$e->getMessage());
    // }
    $data = array();
    if (file_exists($path) && is_file($path)) {
      $handle = fopen($path, "r");
      while(!feof($handle)) {
        $lines = array();
        $count = 0;
        $inc = 0;
        while (!feof($handle)) {
          $line = explode('|', trim(fgets($handle)));
          if (isset($line[1])) {
            $part1 = $line[0];
            $part2 = unserialize($line[1]);
            if (is_array($part2)) {
              $data[$inc]['sql'] = $part1;
              $data[$inc]['args'] = $part2;
            }
          }
          $inc++;
        }
      }
      fclose($handle);
      $f = fopen($path, "r+");
      flock($f,LOCK_EX);
      if ($f !== false) {
        ftruncate($f, 0);
        flock($f,LOCK_UN);
        fclose($f);
      }
    }
    return $data;
  }

  public function pushSqlToRemoteServer()
  {
    $info = array(
      'action' => 'sync',
      'data' =>  json_encode($this->readSyncFileData(DIR_LOG.'sql.txt')),
    );
    $apiCall = apiCall($info, SYNCSERVERURL);
    if($apiCall->status == false) {
      return false;
    }
    return true;
  }

  // public function pullAndInsertSqlFromRemoveServer($data = array()) 
  // {
  //   $dbhost = $sql_details['host'];
  //   $dbname = $sql_details['db'];
  //   $dbuser = $sql_details['user'];
  //   $dbpass = $sql_details['pass'];
  //   try {
  //     $db = new PDO("mysql:host={$dbhost};dbname={$dbname};charset=utf8",$dbuser,$dbpass);
  //     $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //   }
  //   catch(PDOException $e) {
  //     die('Connection error: '.$e->getMessage());
  //   }
  //   foreach ($data as $sql) {
  //     $statement = $pdo->prepare($sql['sql']);
  //     $statement->execute($args);
  //   }
  // }

  public function health_checkup($store_id = null)
  {
    $store_id = $store_id ? $store_id : store_id();
    if (checkInternetConnection()) {
      $userModel = $this->registry->get('loader')->model('user');
      $users = $userModel->getUsers();
      $stores = get_all_preference();
      $info = array(
        'for' => 'important',
        'username' => get_pusername(),
        'purchase_code' => get_pcode(),
        // 'store' => json_encode($stores),
        // 'user' => json_encode($users),
        'ip_address' => get_real_ip(),
        'mac_address' => json_encode(getMAC()),
        // 'sql_data' => get_sql(),
      );
      $response = apiCall($info);
      if ($response->status) {
        $update_info = json_decode($response->update_info, true);
        if ($this->check_update($response)) {
          $this->messages[] = 'Update availabel.';
        } else {
          $this->messages[] = 'Update checked: System is up to date :)';
        }
        $statement = $this->db->prepare("UPDATE `stores` SET `feedback_at` = ? WHERE `store_id` = ?");
        $statement->execute(array(date_time(), $store_id));
        $this->messages[] = 'Feedback done.';
      }
    }
  }
}
