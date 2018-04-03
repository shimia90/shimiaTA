<?php
  session_start();

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

  $agg          =   json_encode($update, JSON_PRETTY_PRINT);

  if(strpos($text, "+") !== false) {
    sendMessage($chatId, eval('return '.$text.';'));
    exit();
  }

  // Khoi Tao Danh Sach Cac Nut
  $nutDanhSach      =     '📋 Xem Danh Sách Plan';
  $nutYeuCauTuan    =     '💰 Yêu Cầu Rút Coin';
  $nutYeuCauThang   =     '📤 Yêu Cầu Cuối Tháng';
  $initKeyboard     =     '["'.$nutDanhSach.'"],["'.$nutYeuCauTuan.'"],["'.$nutYeuCauThang.'"]';
  //$initKeyboard     =     '["'.$nutDanhSach.'"],["'.$nutYeuCauTuan.'"]';

  // Lay mang tu Google
  $userInfo               =     getDataUser('user', '1NgZq41xShwrIkxDxX5XpWlI7QL0D8npnfN7slj_gIK0'); // Array User From Google

  $currentUser            =     getUserInfo($chatId, $userInfo);// Array Current User 483198952 - thay bang $chatID khi test xong

  $userInline             =     convertUserData($currentUser);

  // Tao nut yeu cau rút coin theo tuan
  $keyboardRequest        =     getRequestButton($currentUser, $chatId, 'check_tuan'); 

  // Tao nut yeu cau rút coin theo thang
  $keyboardRequestMonth   =     getRequestButton($currentUser, $chatId, 'check_thang'); 

// Kiem Tra User
$query            =   $update['callback_query'];
$queryid          =   $query['id'];
$queryUserId      =   $query['from']['id'];
$queryUsername    =   $query['from']['username'];
$queryData        =   $query['data'];
$querymsgId       =   $query['message']['message_id'];
$querymsgText     =   $query['message']['text'];

$arrayCurrentPlan    =   getCurrentPlan();
foreach($arrayCurrentPlan as $k => $v) {
  // Truy xuat thong tin Plan
  if($queryData   ==   "print_$v") {
    $result   =   getResultPlan($v, $queryUserId);
    answerQuery($queryid, $result);
    exit();
  }

  // Tao nut yeu cau tai rut tuan
  if($queryData   ==   "request_$v") {
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $date = new DateTime();
    if($date->format('D') === 'Fri')  {
      answerQuery($queryid, "Hôm nay là ngày chia lãi, bạn vui lòng yêu cầu vào ngày khác.");
    } else {
      $answerButton   =   createRequestCoin($v, 'nut_tuan');
      editMessageText($queryUserId, $querymsgId, "Vui lòng chọn yêu cầu cho plan ". strtoupper($v) ." của bạn", $answerButton);
    }
  }

  // Tao nut yeu cau tai rut
  if($queryData   ==   "request_month_$v") {
    $answerMonthButton   =   createRequestCoin($v, 'nut_thang');
    editMessageText($queryUserId, $querymsgId, "Vui lòng chọn yêu cầu cho plan ". strtoupper($v) ." của bạn", $answerMonthButton);
  }

  // Update cho nut rút lãi tuần
  if($queryData == $v.'_yes') {
    $result   =   updateRequest($queryUserId, $v, "có", "rut_tuan");
    if($result == true) {
      answerQuery($queryid, "Cập nhật thành công");
    } else {
      answerQuery($queryid, "Lỗi ! Vui lòng thử lại");
    }
    exit();
  }

  if($queryData == $v.'_no') {
    $result   =   updateRequest($queryUserId, $v, "không", "rut_tuan");
    if($result == true) {
      answerQuery($queryid, "Cập nhật thành công");
    } else {
      answerQuery($queryid, "Lỗi ! Vui lòng thử lại");
    }
    exit();
  }
  // End rut lãi tuần

  // Update cho nut rút lãi tháng
  if($queryData == $v.'_month_lai') {
    $result   =   updateRequest($queryUserId, $v, "Rút Lãi", "rut_thang");
    if($result == true) {
      answerQuery($queryid, "Cập nhật thành công");
    } else {
      answerQuery($queryid, "Lỗi ! Vui lòng thử lại");
    }
    exit();
  }

  if($queryData == $v.'_month_goc') {
    $result   =   updateRequest($queryUserId, $v, "Rút Gốc", "rut_thang");
    if($result == true) {
      answerQuery($queryid, "Cập nhật thành công");
    } else {
      answerQuery($queryid, "Lỗi ! Vui lòng thử lại");
    }
    exit();
  }

  if($queryData == $v.'_month_huy') {
    $result   =   updateRequest($queryUserId, $v, "Chưa có yêu cầu", "rut_thang");
    if($result == true) {
      answerQuery($queryid, "Cập nhật thành công");
    } else {
      answerQuery($queryid, "Lỗi ! Vui lòng thử lại");
    }
    exit();
  }
  // End rut lãi tháng

}

// Nút Quay lại - yêu cầu rút tuần
if($queryData   ==   "answer_back") {
  $currentUser      =     getUserInfo($queryUserId, $userInfo);
  $keyboardRequest  =     getRequestButton($currentUser, $queryUserId, 'check_tuan');
  editMessageText($queryUserId, $querymsgId, "Chọn Plan bạn muốn rút Coin", $keyboardRequest);
  exit();
}

// Nút Quay Lại - yêu cầu rút tháng
if($queryData   ==   "answer_month_back") {
  $currentUser      =     getUserInfo($queryUserId, $userInfo);
  $keyboardRequestMonth  =     getRequestButton($currentUser, $queryUserId, 'check_thang');
  editMessageText($queryUserId, $querymsgId, "Chọn Plan bạn muốn rút Coin \n(rút lãi hoặc gốc theo tháng)", $keyboardRequestMonth);
  exit();
}

$logged     =   'no';
foreach($currentUser as $key => $value) {
  if(is_numeric($key)) {
    continue;
  } else {
    if($value == $chatId) {
      $logged   =   'yes';
    }
  }
}

if(!empty($currentUser) && $logged == 'yes') {

  switch ($text) {
      case '/start':
          keyboard($chatId, "Xin chào $firstName $lastName" , $initKeyboard, "physical");
        break;
      case $nutDanhSach:
        if(!empty($userInline)) {
          keyboard($chatId, "Danh sách plan bạn đang tham gia", $userInline, 'inline');
          keyboard($chatId, "Mọi thắc mắc xin liên hệ team" , $initKeyboard, "physical");
        } else {
          sendMessage($chatId, "Bạn chưa tham gia plan nào");
        }
        break;
      case $nutYeuCauTuan:
        if(!empty($keyboardRequest)) {
          keyboard($chatId, "Chọn Plan bạn muốn rút Coin", $keyboardRequest, 'inline');
          keyboard($chatId, "Mọi thắc mắc xin liên hệ team" , $initKeyboard, "physical");
        } else {
          sendMessage($chatId, "Bạn chưa tham gia plan nào");
        }
        break;
      case $nutYeuCauThang:
        if(!empty($keyboardRequest)) {
          keyboard($chatId, "Chọn Plan bạn muốn rút Coin \n(rút lãi hoặc gốc theo tháng)", $keyboardRequestMonth, 'inline');
          keyboard($chatId, "Mọi thắc mắc xin liên hệ team" , $initKeyboard, "physical");
        } else {
          sendMessage($chatId, "Bạn chưa tham gia plan nào");
        }
        break;
      default:
        break;
    }
} else {
    processLogin($update, $initKeyboard);
}
?>