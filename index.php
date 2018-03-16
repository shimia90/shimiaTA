<?php
  include 'token.php';
  require_once 'functions.php';

  $update     =   file_get_contents('php://input');
  $update     =   json_decode($update, TRUE);


  // Thong tin  
  
  $chatId       =   $update['message']['from']['id'];
  $firstName    =   $update['message']['from']['first_name'];
  $lastName     =   $update['message']['from']['last_name'];
  $text         =   $update['message']['text'];
  
  $currentUser  =   array();

  $agg        =   json_encode($update, JSON_PRETTY_PRINT);

  
  /*if($queryData   == "print_magic") {
    answerQuery($queryid, "testtttttttttttttttt");
    exit();
  }*/

  if(strpos($text, "+") !== false) {
    sendMessage($chatId, eval('return '.$text.';'));
    exit();
  }

  // Khoi Tao Danh Sach Cac Nut
  $initKeyboard   = '["Xem Danh Sách Plan"],["Yêu Cầu Rút Coin"]';

  // Lay mang tu Google
  $userInfo       =    getDataUser('user', '1NgZq41xShwrIkxDxX5XpWlI7QL0D8npnfN7slj_gIK0'); // Array User From Google

  $currentUser    =    getUserInfo($chatId, $userInfo);// Array Current User 483198952 - thay bang $chatID khi test xong

  $userInline     =    convertUserData($currentUser);



// Kiem Tra User
$query          =   $update['callback_query'];
$queryid        =   $query['id'];
$queryUserId    =   $query['from']['id'];
$queryUsername  =   $query['from']['username'];
$queryData      =   $query['data'];
$querymsgId     =   $query['message']['message_id'];

if($queryData   ==   "print_buzz") {
  $result   =   getResultPlan('buzz', $queryUserId);
  answerQuery($queryid, $result);
  exit();
}

if($queryData   ==   "print_xgox") {
  $result   =   getResultPlan('xgox', $queryUserId);
  answerQuery($queryid, $result);
  exit();
}

if($queryData   ==   "print_xgod") {
  $result   =   getResultPlan('xgod', $queryUserId);
  answerQuery($queryid, $result);
  exit();
}

if($queryData   ==   "print_hold") {
  $result   =   getResultPlan('hold', $queryUserId);
  answerQuery($queryid, $result);
  exit();
}

if($queryData   ==   "print_magic") {
  $result   =   getResultPlan('magic', $queryUserId);
  answerQuery($queryid, $result);
  exit();
}

if($queryData   ==   "print_opc") {
  $result   =   getResultPlan('opc', $queryUserId);
  answerQuery($queryid, $result);
  exit();
}

  //Xu ly cac nut khi duoc nhan
  switch ($text) {
    case '/start':
      keyboard($chatId, "Xin chào $firstName $lastName" , $initKeyboard, "physical");
      break;
    case 'Xem Danh Sách Plan':
      if(!empty($currentUser)) {
      keyboard($chatId, "Danh sách plan bạn đang tham gia", $userInline, 'inline');
      //  keyboard($chatId, $agg, $userInline, 'inline');
    } else {
       sendMessage($chatId, "Bạn chưa tham gia Plan nào");
    }
      break;
    case 'Yêu Cầu Rút Coin':
      sendMessage($chatId, "Coming Soon...");
      break;
    default:

      break;
  }
?>