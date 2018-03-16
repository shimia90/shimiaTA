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

function editMessageText($chatId, $message_id, $newText) {
	$url      =   $GLOBALS[website]. "/editMessageText?chat_id=$chatId&message_id=$message_id&text=".urlencode($newText);
	file_get_contents($url);
  }

function checkUserInfo($arrayUser, $username) {
	$username 	=	'';
	$tenDK 		=	'';
	$coinMining =	'';
	$coinInvest = 	'';
	$share 		=	'';
	$facebook 	=	'';
	$wallet 	=	'';	
	$result 	=	'';
	foreach($arrayUser as $key => $value) {
		if(trim($username) == trim($value[0])) {
			$username 			=	$value[0];
			$tenDK 				=	$value[1];
			$coinMining 		=	$value[2];
			$coinInvest 		=	$value[3];
			$share		 		=	$value[4];
			$facebook		 	=	$value[5];
			$wallet		 		=	$value[6];
			$result 	=	"Thông tin user:</br />$username:$username</br />Tên Đăng Ký: $tenDK<br />Số Coin Đào: $coinMining";
		} else {
			$result = 'Khong tim thay user';
		}
	}
	return $result;
}

/**
*
* Lay Du Lieu Tu Google Doc
*/
function getDataFromGoogle($tenPlan, $tenSheet) {
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

	  // Loai bo nhung mang khong can thiet
	  for($i = 0 ; $i < 10; $i++) {
			unset($arrayData[$i]);
	   }

	$arrayData 	=	array_values($arrayData);

	return $arrayData;
}

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
	$count 				=	0;
	$result 			=	'';
	foreach($arrayCurrentUser as $key => $value) {
		
		if($value == '' || is_numeric($key)) {
			continue;
		} else {
			$arrayKeyboard[][] 	 = array(
				"text" => strtoupper($key),
				"callback_data" => "print_$key"
			);
			$count++;
		}
	}

	$result 	=	json_encode($arrayKeyboard);
	$result 	=	substr($result, '1');
	$result 	=	substr($result, '0', '-1');
	return $result;
}

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
			$result 	      =	"Thông tin plan ".(strtoupper($key))." của bạn:\nTên Đăng Ký: ".ucwords($tenDK)."\nSố Coin Đào PoS: ".$soCoinDao."\nCổ Phần: ".$coPhan."\nLãi Tuần: ".$laituan;
		}
	}
	return $result;
}