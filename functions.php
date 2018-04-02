<?php
// Cac phuong thuc telegram

function keyboard($chatId, $text, $keyboard, $type) {
  if(isset($keyboard)) {
      if($type == 'physical') {
        $keypad   = '&reply_markup={"keyboard":['.$keyboard.'],"resize_keyboard":true}';
      } else {
        $keypad   = '&reply_markup={"inline_keyboard":['.$keyboard.'],"resize_keyboard":true}';
      }   
  }
  $url      =   $GLOBALS[website]. "/sendMessage?chat_id=$chatId&parse_mode=HTML&text=".urlencode($text).$keypad;
  file_get_contents($url);
}

function sendMessage($chatId, $text) {
  $url      =   $GLOBALS[website]. "/sendMessage?chat_id=$chatId&parse_mode=HTML&text=".urlencode($text);
  file_get_contents($url);
}

function answerQuery($callback_query_id, $text) {
  $url  = $GLOBALS[website]."/answerCallbackQuery?callback_query_id=$callback_query_id&show_alert=true&text=".urlencode($text);
  file_get_contents($url);
}

function editMessageText($chatId, $message_id, $newText, $replyMarkup) {
    $url      =   $GLOBALS[website]. "/editMessageText?chat_id=$chatId&message_id=$message_id&text=".urlencode($newText);
    if(!empty($replyMarkup)) {
      $url  .=    '&reply_markup={"inline_keyboard":['.$replyMarkup.'],"resize_keyboard":true}';
    }
  file_get_contents($url);
}

function editMessageReplyMarkup($chatId, $message_id, $replyMarkup) {
    $url      =   $GLOBALS[website]. '/editMessageReplyMarkup?chat_id='.$chatId.'&message_id='.$message_id.'&reply_markup={"inline_keyboard":'.$replyMarkup.',"resize_keyboard":true}';
  file_get_contents($url);
}

/**
*
* Lay Mang Cac Plan Hien Tai
*/
function getCurrentPlan($tenSheet = '') {
  require 'vendor/autoload.php';

  $service_account_file = 'client_services.json';

    if($tenSheet == '') {
      $spreadsheet_id = '1m_zf3zUJa4iHemxzDSHPJ9KHhN0868ShNoeqc7tQ-kQ';
    }

    $arrayData  = array();

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $service = new Google_Service_Sheets($client);
    $range  = $service->spreadsheets->get($spreadsheet_id);
    foreach($range->getSheets() as $s) {
      $arrayData[] = $s['properties']['title'];
    }

  return $arrayData;
}

//Lay Mang User Tren Google Doc
function getDataUser($tenPlan, $tenSheet) {
  require 'vendor/autoload.php';

  $service_account_file = 'client_services.json';

    //$spreadsheet_id = '1m_zf3zUJa4iHemxzDSHPJ9KHhN0868ShNoeqc7tQ-kQ';
    $spreadsheet_id = $tenSheet;

    //$spreadsheet_range = 'Buzz kì 6';
    $spreadsheet_range = $tenPlan;

    $arrayData  = array();

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $service = new Google_Service_Sheets($client);
    $result = $service->spreadsheets_values->get($spreadsheet_id, $spreadsheet_range);
    $arrayData = $result->getValues(); // Mang du lieu

  return $arrayData;
}

/**
*
* Lay Du Lieu Tu Bang User
*/
function getUserInfo($username, $arrayUserInfo = array()) {

    $arrayCurrentUser   =   array();
    $arrayCurrentPlan   =   array();

    for($i = 0; $i < 4; $i++) {
      unset($arrayUserInfo[0][$i]);
    }
    $arrayCurrentPlan   = $arrayUserInfo[0];
    
    foreach($arrayUserInfo as $key => $value) {
      if(in_array($username, $value) && $username != '') {
        $arrayCurrentUser   =   $arrayUserInfo[$key];
        break;
      } 
    }

    foreach($arrayCurrentPlan as $k => $v) {
      for($j = 0; $j < count($arrayCurrentUser); $j++) {
        if($k == $j) {
          $arrayCurrentUser[$v] = $arrayCurrentUser[$j];
        }
      }
      unset($arrayCurrentUser[$k]);
    }

    return $arrayCurrentUser;
  
}

// Get Button of Plan
function convertUserData($arrayCurrentUser) {
  $arrayKeyboard    = array();
  $result       = '';
  foreach($arrayCurrentUser as $key => $value) {
    
    if($value == '' || is_numeric($key)) {
      continue;
    } else {
      $arrayKeyboard[][]   = array(
        "text" => strtoupper($key),
        "callback_data" => "print_$key"
      );
    }
  }

  $result   = json_encode($arrayKeyboard);
  $result   = substr($result, '1');
  $result   = substr($result, '0', '-1');
  return $result;
}

// Tra ve thong tin user khi tim thay
function getResultPlan($tenPlan, $userId) {
  $result   = '';
  $tenDK    = '';
  $soCoinDao  =   ''; 
  $coPhan   = '';
  $laiTuan  = '';
  $userPlanDetail   = getDataUser($tenPlan, '1m_zf3zUJa4iHemxzDSHPJ9KHhN0868ShNoeqc7tQ-kQ');
  foreach($userPlanDetail as $k => $v) {
    if(in_array($userId, $v)) {
      $tenDK      = $userPlanDetail[$k][2];
      $soCoinDao  = $userPlanDetail[$k][3];
      $coPhan     = $userPlanDetail[$k][5];
      $laituan    = end($userPlanDetail[$k]);
      $result         = "Thông tin plan ".(strtoupper($tenPlan))." của bạn:\nTên Đăng Ký: ".ucwords($tenDK)."\nSố Coin Đào PoS: ".$soCoinDao."\nCổ Phần: ".$coPhan."\nLãi Tuần: ".$laituan;
    }
  }
  return $result;
}

//Kiem Tra Trang Thai Tái hay Rút
function checkPlanStatus($userId, $tenPlan) {

  require 'vendor/autoload.php';

  $result     = '';

  $service_account_file = 'client_services.json';

    //$spreadsheet_id = '1m_zf3zUJa4iHemxzDSHPJ9KHhN0868ShNoeqc7tQ-kQ';
    $spreadsheet_id = '1NgZq41xShwrIkxDxX5XpWlI7QL0D8npnfN7slj_gIK0';

    //$spreadsheet_range = 'Buzz kì 6';
    $spreadsheet_range = trim($tenPlan);

    $arrayData  = array();

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $service = new Google_Service_Sheets($client);
    $result = $service->spreadsheets_values->get($spreadsheet_id, $spreadsheet_range);
    $arrayData = $result->getValues(); // Mang du lieu

    foreach($arrayData as $key => $value) {
      if(in_array($userId, $value)) {
        $result   = $value[8];
        break;
      }

    }

  return $result;
}

/**
* Tao Nut Cac Plan Hien Co Cua User
**/
function getRequestButton($currentUser = array(), $userId) {
  $arrayUserPlan  =   array();
  $arrayResult  =   array();
  $result     = '';
  $tenPlan    = '';

  if(!empty($currentUser)) {
    foreach($currentUser as $key => $value) {
      if(is_numeric($key) || empty($value)) {
        continue;
      } else {
        $arrayUserPlan[]  = ucfirst($key);
      }
    } // foreach

    foreach($arrayUserPlan as $k => $v) {
      $plans    = strtolower(trim($v));
      $status   = ucfirst(checkPlanStatus($userId, $plans));
      $arrayResult[][]   = array(
        "text"      =>    $v. " - Trạng Thái: " .$status . " Tái",
        "callback_data" =>    "request_$plans"
      );
    }
  }

  $result   = json_encode($arrayResult);
  $result   = substr($result, '1');
  $result   = substr($result, '0', '-1');

  return $result;
}

function createRequestCoin($tenPlan) {

  $result   = '[{"text":"Có","callback_data":"'.$tenPlan.'_yes"}, {"text":"Không","callback_data":"'.$tenPlan.'_no"}],[{"text":"Quay Lại","callback_data":"answer_back"}]';
  

  return $result;

}

function updateRequest($userId, $tenPlan, $updateText) {

  require 'vendor/autoload.php';

  $service_account_file = 'client_services.json';

  $spreadsheet_id = '1NgZq41xShwrIkxDxX5XpWlI7QL0D8npnfN7slj_gIK0';

  $spreadsheet_range = $tenPlan;

  $status   = false;

  putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);
  $client = new Google_Client();
  $client->useApplicationDefaultCredentials();
  $client->addScope(Google_Service_Sheets::SPREADSHEETS);
  $service = new Google_Service_Sheets($client);

  $result = $service->spreadsheets_values->get($spreadsheet_id, $spreadsheet_range);

  $valueRange= new Google_Service_Sheets_ValueRange($client);
  $valueRange->setValues(["values" => [$updateText]]);
  $conf = ["valueInputOption" => "RAW"];
  $arrayData  = $result->getValues();

  foreach($arrayData as $key => $value) {
      if(in_array($userId, $value)) {
          $updateRange  =$spreadsheet_range.'!i'.($key+1);
          $service->spreadsheets_values->update($spreadsheet_id, $updateRange, $valueRange, $conf);
          $status   = true;
          break;
      }
    }

    return $status;

}

function processLogin($arrayUpdate, $initKeyboard) {
  $result     	  =   '';
  $chatId         =   $arrayUpdate['message']['from']['id'];
  $firstName      =   $arrayUpdate['message']['from']['first_name'];
  $lastName       =   $arrayUpdate['message']['from']['last_name'];
  $text           =   $arrayUpdate['message']['text'];
  $step           =   getData('step-'.$chatId);
  $verified       =   setData('verified','no');

  switch ($text) {
    case '/start':
      setData('step-'.$chatId,'1');
      sendMessage($chatId, "Bạn chưa đăng nhập\n Vui lòng nhập Username của bạn:");
      // "Welcome!\nSend me your first name now:\n\nsend /cancel to cancel."
      break;
    case '/cancel':
      setData('step-'.$chatId,'0');
      sendMessage($chatId, "Thông tin đã hủy ! Vui lòng nhấn /start để đăng nhập lại");
      break;
    default:
      switch ($step) {
        case '1':
          setData('username-'.$chatId,$text);
          sendMessage($chatId, "Vui lòng nhập Password của bạn:");
          setData('step-'.$chatId,'2');
          break;
        case '2':
          setData('password-'.$chatId, $text);
          $username   =   getData('username-'.$chatId);
          $password   =   getData('password-'.$chatId);
          if(checkLogin($username, $password) == 'ok') {
            //sendMessage($chatId, "Đăng nhập thành công !");
            //sendMessage($chatId, "Thông tin của bạn:\n<b>Your name:</b> " . "<code>$username</code>" . "\n<b>Your password:</b> " . "<code>$password</code>");
            keyboard($chatId, "Bạn đã đăng nhập thành công" , $initKeyboard, "physical");
            insertIdUser($chatId, $username);
            removeData('username-'.$chatId);
            removeData('password-'.$chatId);
            setData('step-'.$chatId,'0');
            setData('verified','yes');
          } else {
            sendMessage($chatId, "Đăng nhập không thành công ! Vui lòng nhấn /start để đăng nhập lại");
            setData('step-'.$chatId,'0');
            setData('verified','no');
          }
          
          break;
        /*case '3':
          $user_name  = getData('username-'.$chatId);
          $password   = getData('password-'.$chatId);
          sendMessage($chatId, "Thông tin của bạn:\n<b>Your name:</b> " . "<code>$user_name</code>" . "\n<b>Your password:</b> " . "<code>$password</code>");
          setData('step-'.$chatId,'0');
          break;
*/        default:
            if($verified == 'no') {
              sendMessage($chatId, "Vui lòng nhấn /start để đăng nhập");
            } 
          
          break;
      }
      break;
  }
}

function getData($id){
    $cached = apc_fetch($id);
    return $cached?$cached:'Flase';
}

function setData($id,$step){
    apc_store($id, $step, 60*60*12);
}

function removeData($id){
    apc_delete ($id);
}

// Kiem Tra User
function checkLogin($username, $password) {

    $result   =   'not ok';

    require 'vendor/autoload.php';

    $service_account_file = 'client_services.json';

    //$spreadsheet_id = '1m_zf3zUJa4iHemxzDSHPJ9KHhN0868ShNoeqc7tQ-kQ';
    $spreadsheet_id = '1NgZq41xShwrIkxDxX5XpWlI7QL0D8npnfN7slj_gIK0';

    //$spreadsheet_range = 'Buzz kì 6';
    $spreadsheet_range = 'user';

    $arrayData  = array();

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $service = new Google_Service_Sheets($client);
    $result = $service->spreadsheets_values->get($spreadsheet_id, $spreadsheet_range);
    $arrayData = $result->getValues(); // Mang du lieu

    /*echo '<pre>';
    print_r($arrayData);
    echo '</pre>';*/

    foreach($arrayData as $key => $value) {
      if(trim($username) == trim($value[0]) && trim($password) == trim($value[1])) {
        $result   =   'ok';
        break;
      }
    }

  return $result;
}
//$userId, $userName
// Insert ID User mới vào danh sách các plan
function insertIdUser($userId, $userName) {
  $currentPlan    =   getCurrentPlan();
  $sheetPlan      =   '1m_zf3zUJa4iHemxzDSHPJ9KHhN0868ShNoeqc7tQ-kQ';
  $sheetBangTinh  =   '1NgZq41xShwrIkxDxX5XpWlI7QL0D8npnfN7slj_gIK0';
  $result         =   'not ok';


  foreach ($currentPlan as $id => $plan) {
     $result  =   updateIdUser($userId, $userName, $plan, $sheetPlan, $sheetBangTinh);
  }
  return $result;
}

// Update Id New User
function updateIdUser($userId, $userName, $tenPlan, $sheetPlan, $sheetBangTinh) {

  require 'vendor/autoload.php';

  $service_account_file = 'client_services.json';

  $spreadsheet_id       = $sheetPlan;

  $spreadsheet_bangtinh = $sheetBangTinh;

  $spreadsheet_range    = $tenPlan;

  $status   = 'not ok';

  putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);
  $client = new Google_Client();
  $client->useApplicationDefaultCredentials();
  $client->addScope(Google_Service_Sheets::SPREADSHEETS);
  $service = new Google_Service_Sheets($client);

  $result = $service->spreadsheets_values->get($spreadsheet_id, $spreadsheet_range);

  $valueRange= new Google_Service_Sheets_ValueRange($client);
  $valueRange->setValues(["values" => [$userId]]);
  $conf = ["valueInputOption" => "RAW"];
  $arrayData  = $result->getValues();

  $userInfo           =     getDataUser('user', '1NgZq41xShwrIkxDxX5XpWlI7QL0D8npnfN7slj_gIK0');
  $arrayPlanConvert   =     convertAlphabetKey($userInfo[0]);

  foreach($arrayData as $key => $value) {
      if(in_array($userName, $value)) {
          $updateRange  =     $spreadsheet_range.'!a'.($key+1);
          $service->spreadsheets_values->update($spreadsheet_id, $updateRange, $valueRange, $conf);
          $service->spreadsheets_values->update($spreadsheet_bangtinh, $updateRange, $valueRange, $conf);

          foreach($arrayPlanConvert as $alpha => $plan) {
            if($spreadsheet_range  ==   $plan) {
              $userKey    =   getUserLocationKey($userName);
              $updateUserRange  =     'user!'.$alpha.($userKey+1);
              $service->spreadsheets_values->update($spreadsheet_bangtinh, $updateUserRange, $valueRange, $conf);
            }
          }

          $status   = 'ok';
          break;
      }
    }

    return $status;

}

function convertAlphabetKey($arrayConvert) {
  $arrayAlphas = range('A', 'Z');
  for($i = 0; $i < count($arrayConvert); $i++) {
    $arrayConvert[$arrayAlphas[$i]]   =   $arrayConvert[$i];
    unset($arrayConvert[$i]);
  }
  return $arrayConvert;
}

// Lấy vị trí của user để thêm vào bảng user
function getUserLocationKey($userName) {
    $result   =   '';

    require 'vendor/autoload.php';

    $service_account_file = 'client_services.json';

    //$spreadsheet_id = '1m_zf3zUJa4iHemxzDSHPJ9KHhN0868ShNoeqc7tQ-kQ';
    $spreadsheet_id = '1NgZq41xShwrIkxDxX5XpWlI7QL0D8npnfN7slj_gIK0';

    //$spreadsheet_range = 'Buzz kì 6';
    $spreadsheet_range = 'user';

    $arrayData  = array();

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $service = new Google_Service_Sheets($client);
    $result = $service->spreadsheets_values->get($spreadsheet_id, $spreadsheet_range);
    $arrayData = $result->getValues(); // Mang du lieu

    foreach($arrayData as $key => $value) {
      if(in_array($userName, $value)) {
        $result   =   $key;
        break;
      }
    }

    return $result;
}