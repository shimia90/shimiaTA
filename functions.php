<?php

/**
Gui thong bao
**/
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

/*function editMessageText($chatId, $message_id, $newText) {
		$url      =   $GLOBALS[website]. "/editMessageText?chat_id=$chatId&message_id=$message_id&text=".urlencode($newText);
	file_get_contents($url);
}*/

function editMessageText($chatId, $message_id, $newText, $replyMarkup) {
		$url      =   $GLOBALS[website]. "/editMessageText?chat_id=$chatId&message_id=$message_id&text=".urlencode($newText);
		if(!empty($replyMarkup)) {
			$url 	.=		'&reply_markup={"inline_keyboard":['.$replyMarkup.'],"resize_keyboard":true}';
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

    $arrayData 	=	array();

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

	  $client = new Google_Client();
	  $client->useApplicationDefaultCredentials();
	  $client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);
	  $service = new Google_Service_Sheets($client);
	  $range 	=	$service->spreadsheets->get($spreadsheet_id);
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

    $arrayData 	=	array();

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

		$arrayCurrentUser 	=	array();
		$arrayCurrentPlan 	= 	array();

		for($i = 0; $i < 4; $i++) {
			unset($arrayUserInfo[0][$i]);
		}
		$arrayCurrentPlan 	=	$arrayUserInfo[0];
		
		foreach($arrayUserInfo as $key => $value) {
			if(in_array($username, $value) && $username != '') {
				$arrayCurrentUser 	=	$arrayUserInfo[$key];
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
	$arrayKeyboard 		=	array();
	$result 			=	'';
	foreach($arrayCurrentUser as $key => $value) {
		
		if($value == '' || is_numeric($key)) {
			continue;
		} else {
			$arrayKeyboard[][] 	 = array(
				"text" => strtoupper($key),
				"callback_data" => "print_$key"
			);
		}
	}

	$result 	=	json_encode($arrayKeyboard);
	$result 	=	substr($result, '1');
	$result 	=	substr($result, '0', '-1');
	return $result;
}

// Tra ve thong tin user khi tim thay
function getResultPlan($tenPlan, $userId) {
	$result 	=	'';
	$tenDK 		=	'';
	$soCoinDao  = 	'';	
	$coPhan 	=	'';
	$laiTuan 	=	'';
	$userPlanDetail 	=	getDataUser($tenPlan, '1m_zf3zUJa4iHemxzDSHPJ9KHhN0868ShNoeqc7tQ-kQ');
	foreach($userPlanDetail as $k => $v) {
		if(in_array($userId, $v)) {
			$tenDK 		  =	$userPlanDetail[$k][2];
			$soCoinDao 	=	$userPlanDetail[$k][3];
			$coPhan 	  =	$userPlanDetail[$k][5];
			$laituan 	  =	end($userPlanDetail[$k]);
			$result 	      =	"Thông tin plan ".(strtoupper($tenPlan))." của bạn:\nTên Đăng Ký: ".ucwords($tenDK)."\nSố Coin Đào PoS: ".$soCoinDao."\nCổ Phần: ".$coPhan."\nLãi Tuần: ".$laituan;
		}
	}
	return $result;
}

//Kiem Tra Trang Thai Tái hay Rút
function checkPlanStatus($userId, $tenPlan) {

	require 'vendor/autoload.php';

	$result 		=	'';

	$service_account_file = 'client_services.json';

    //$spreadsheet_id = '1m_zf3zUJa4iHemxzDSHPJ9KHhN0868ShNoeqc7tQ-kQ';
    $spreadsheet_id = '1NgZq41xShwrIkxDxX5XpWlI7QL0D8npnfN7slj_gIK0';

    //$spreadsheet_range = 'Buzz kì 6';
    $spreadsheet_range = trim($tenPlan);

    $arrayData 	=	array();

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
  			$result 	=	$value[8];
  			break;
  		}
  	}

	return $result;
}

/**
*	Tao Nut Cac Plan Hien Co Cua User
**/
function getRequestButton($currentUser = array(), $userId) {
	$arrayUserPlan	= 	array();
	$arrayResult 	= 	array();
	$result 		=	'';
	$tenPlan 		=	'';

	if(!empty($currentUser)) {
		foreach($currentUser as $key => $value) {
			if(is_numeric($key) || empty($value)) {
				continue;
			} else {
				$arrayUserPlan[] 	=	ucfirst($key);
			}
		} // foreach

		foreach($arrayUserPlan as $k => $v) {
			$plans 		=	strtolower(trim($v));
			$status 	=	ucfirst(checkPlanStatus($userId, $plans));
			$arrayResult[][] 	 = array(
				"text" 			=> 		$v. " - Trạng Thái: " .$status . " Tái",
				"callback_data" => 		"request_$plans"
			);
		}
	}

	$result 	=	json_encode($arrayResult);
	$result 	=	substr($result, '1');
	$result 	=	substr($result, '0', '-1');

	return $result;
}

function createRequestCoin($tenPlan) {

	$result 	=	'[{"text":"Có","callback_data":"'.$tenPlan.'_yes"}, {"text":"Không","callback_data":"'.$tenPlan.'_no"}],[{"text":"Quay Lại","callback_data":"answer_back"}]';

	return $result;

}