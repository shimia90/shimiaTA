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

  $agg        	=   json_encode($update, JSON_PRETTY_PRINT);

  if(strpos($text, "+") !== false) {
    sendMessage($chatId, eval('return '.$text.';'));
    exit();
  }

  // Khoi Tao Danh Sach Cac Nut
  $initKeyboard   	= 		'["Xem Danh Sách Plan"],["Yêu Cầu Rút Coin"]';

  // Lay mang tu Google
  $userInfo       	=    	getDataUser('user', '1NgZq41xShwrIkxDxX5XpWlI7QL0D8npnfN7slj_gIK0'); // Array User From Google

  $currentUser    	=    	getUserInfo('497434187', $userInfo);// Array Current User 483198952 - thay bang $chatID khi test xong

  $userInline     	=    	convertUserData($currentUser);

  // Tao nut yeu cau rút coin
  $keyboardRequest  =   	getRequestButton($currentUser, '497434187');

// Kiem Tra User
$query          =   $update['callback_query'];
$queryid        =   $query['id'];
$queryUserId    =   $query['from']['id'];
$queryUsername  =   $query['from']['username'];
$queryData      =   $query['data'];
$querymsgId     =   $query['message']['message_id'];


// Truy xuat thong tin Plan
$arrayCurrentPlan    =   getCurrentPlan();
foreach($arrayCurrentPlan as $k => $v) {
  if($queryData   ==   "print_$v") {
    $result   =   getResultPlan($v, '497434187');
    answerQuery($queryid, $result);
    exit();
  }

  if($queryData   ==   "request_$v") {
    $answerButton   =   createRequestCoin($v);
    editMessageText($queryUserId, $querymsgId, "Vui lòng chọn yêu cầu cho plan ". strtoupper($v) ." của bạn", $answerButton);
  }
}

if($queryData   ==   "answer_back") {
	editMessageText($queryUserId, $querymsgId, 'Chọn Plan bạn muốn rút Coin', $keyboardRequest);
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
      if(!empty($currentUser)) {
        keyboard($chatId, "Chọn Plan bạn muốn rút Coin", $keyboardRequest, 'inline');
      } else {
        sendMessage($chatId, "Bạn chưa tham gia Plan nào");
      }
      break;
    default:

      break;
  }
?>