<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// if user is not logged in then return error
if (!$user->isLogged()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_login')));
  exit();
}

// Check, if user has reading permission or not
// if user have not reading permission return error
if ($user->getGroupId() != 1 && !$user->hasPermission('access', 'read_language_sync')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $language->get('error_read_permission')));
  exit();
}

//  Load Language File
$language->load('language_sync');

require_once(DIR_INCLUDE.'/vendor/gtranslator/autoload.php');
use Stichoza\GoogleTranslate\GoogleTranslate;


function getParts($string, $positions){
    $parts = array();

    foreach ($positions as $position){
      $parts[] = substr($string, 0, $position);
      $string = substr($string, $position);
      if (!$string) {
        break;
      }
    }
    return $parts;
}

try {
  
  $base_lang_dir = 'english';
  $target_lang_dir = $request->get['langname'];
  $file = $request->get['file'];
  $lang_code = 'en';

  switch ($target_lang_dir) {
    case 'arabic':
      $lang_code = 'ar';
      break;
    case 'french':
      $lang_code = 'fr';
      break;
    case 'germany':
      $lang_code = 'de';
      break;
    case 'hindi':
      $lang_code = 'hi';
      break;
    case 'spanish':
      $lang_code = 'es';
      break;
    default:
      $lang_code = 'en';
      break;
  }



  if ($request->get['action_type'] == 'WRITE_KEY_VALUE') {
    $source_files = get_filenames(ROOT.'/language/'.$base_lang_dir);
    $lang_keys = '';
    $values = file_get_contents(ROOT.'/_db/translate.txt');
    $values = explode('|', $values);
    foreach ($source_files as $filename) {
        include ROOT.'/language/'.$base_lang_dir.'/'.$filename;
    }
    foreach ($_ as $key => $value) {
      $lang_keys .= $key.'|';
    }

    if (!is_file(ROOT.'/language/'.$target_lang_dir.'/'.$file) || !file_exists(ROOT.'/language/'.$target_lang_dir.'/'.$file)) {
      $fh = fopen(ROOT.'/language/'.$target_lang_dir.'/'.$file, 'w');
      fclose($fh);
    }
    @file_put_contents(ROOT.'/language/'.$target_lang_dir.'/'.$file, "");
    $inc=0;
    foreach ($_ as $key => $value) {
      if (!isset($values[$inc])) {
        break;
      }
      if ($inc == 0) {
        $txt = '<?php';
        @file_put_contents(ROOT.'/language/'.$target_lang_dir.'/'.$file, $txt. PHP_EOL, FILE_APPEND | LOCK_EX)."\n";
      }
      $txt = "\$_['".$key."']='".addslashes(trim($values[$inc]))."';";
      @file_put_contents(ROOT.'/language/'.$target_lang_dir.'/'.$file, $txt. PHP_EOL, FILE_APPEND | LOCK_EX)."\n";
      $inc++;
    }
    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_done')));
    exit();
  }



  if ($request->get['action_type'] == 'EXPORT_KEY') {
    $source_files = get_filenames(ROOT.'/language/'.$base_lang_dir);
    $lang_keys = '';
    foreach ($source_files as $file) {
      include ROOT.'/language/'.$base_lang_dir.'/'.$file;
    }
    foreach ($_ as $key => $value) {
      $lang_keys .= $key.'|';
    }
    $chunks = getParts($lang_keys, array(2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500));
    dd($chunks);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_export_key'), 'key' => $lang_keys));
    exit();
  }



  if ($request->get['action_type'] == 'EXPORT_VALUE') {
    $source_files = get_filenames(ROOT.'/language/'.$base_lang_dir);
    $lang_values = '';
    foreach ($source_files as $file) {
      include ROOT.'/language/'.$base_lang_dir.'/'.$file;
    }
    foreach ($_ as $key => $value) {
      $lang_values .= $value.'|';
    }
    $chunks = getParts($lang_values, array(2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500,2500));
    dd($chunks);

    header('Content-Type: application/json');
    echo json_encode(array('msg' => $language->get('text_export_key'), 'value' => $lang_values));
    exit();
  }


  if ($request->get['action_type'] == 'GTRANSLATE') {

    if (!checkInternetConnection($domain = 'www.google.com')) {
      throw new Exception($language->get('error_internet_connection'));
    }

    if (!isset($request->get['langname']) || !isset($request->get['file'])) {
      throw new Exception($language->get('error_invalid_request'));
    }

    $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
    $tr->setSource('en'); // Translate from English
    $tr->setSource(); // Detect language automatically
    $tr->setTarget($lang_code); // Translate to

    // $source_files = get_filenames(ROOT.'/language/'.$base_lang_dir);
    // foreach ($source_files as $file) {
        if (!is_file(ROOT.'/language/'.$target_lang_dir.'/'.$file) || !file_exists(ROOT.'/language/'.$target_lang_dir.'/'.$file)) {
            $fh = fopen(ROOT.'/language/'.$target_lang_dir.'/'.$file, 'w');
            fclose($fh);
        }
        include ROOT.'/language/'.$base_lang_dir.'/'.$file;
        file_put_contents(ROOT.'/language/'.$target_lang_dir.'/'.$file, "");
        $inc = 1;
        foreach ($_ as $key => $value) {
          if ($inc == 1) {
            $txt = '<?php';
            @file_put_contents(ROOT.'/language/'.$target_lang_dir.'/'.$file, $txt. PHP_EOL, FILE_APPEND | LOCK_EX)."\n";
          }
          try{
             $translation = $tr->translate($value);
          } catch (Exception $e) {
            throw new Exception($e->getMessage());
            break;
          }
          $$txt = "\$_['".$key."']='".addslashes($translation)."';";
          @file_put_contents(ROOT.'/language/'.$target_lang_dir.'/'.$file, $txt. PHP_EOL, FILE_APPEND | LOCK_EX)."\n";
          $inc++;
        }
    // }
  }

  header('Content-Type: application/json');
  echo json_encode(array('msg' => $language->get('text_language_sync_success')));
  exit();

} catch (Exception $e) { 
  
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => $e->getMessage()));
  exit();
}