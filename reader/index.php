<?php

/**
 * @file
 *
 * Reads data from a Google Spreadsheet that needs authentication.
 */

require 'vendor/autoload.php';

/**
 * Set here the full path to the private key .json file obtained when you
 * created the service account. Notice that this path must be readable by
 * this script.
 */
$service_account_file = 'client_services.json';

/**
 * This is the long string that identifies the spreadsheet. Pick it up from
 * the spreadsheet's URL and paste it below.
 */
//$spreadsheet_id = '1m_zf3zUJa4iHemxzDSHPJ9KHhN0868ShNoeqc7tQ-kQ'; // sheet plan
//$spreadsheet_id = '1NgZq41xShwrIkxDxX5XpWlI7QL0D8npnfN7slj_gIK0'; // sheet user

$spreadsheet_id = '1FMqeGWQUg-rb2IH9Z9qZy2o516HjC7EKPK8W1_FMoXc'; // Sheet Test

/**
 * This is the range that you want to extract out of the spreadsheet. It uses
 * A1 notation. For example, if you want a whole sheet of the spreadsheet, then
 * set here the sheet name.
 *
 * @see https://developers.google.com/sheets/api/guides/concepts#a1_notation
 */
$spreadsheet_range = 'plan4';

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);
$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope(Google_Service_Sheets::SPREADSHEETS);
$service = new Google_Service_Sheets($client);

$result = $service->spreadsheets_values->get($spreadsheet_id, $spreadsheet_range);
//var_dump($result->getValues());

// Get Range Of Sheet
$range 	=	$service->spreadsheets->get($spreadsheet_id);
foreach($range->getSheets() as $s) {
	$sheets[] 		= 	$s['properties']['title'];
	$sheets_id[] 	=	$s['properties']['sheetId'];
}

/*
	sheet_metadata = service.spreadsheets().get(spreadsheetId=spreadsheet_id).execute()
	sheets = sheet_metadata.get('sheets', '')
	title = sheets[0].get("properties", {}).get("title", "Sheet1")
	sheet_id = sheets[0].get("properties", {}).get("sheetId", 0)
*/

/*echo '<pre>';
print_r($sheets_id);
echo '</pre>';*/
// End

/*$arrayData = $result->getValues(); // Mang du lieu

// Writing 
// Get our spreadsheet
$values = [
    [
        'a','b','c','d'
    ],
    // Additional rows ...
];
$valueInputOption = 'USER_ENTERED';
$body = new Google_Service_Sheets_ValueRange([
  'values' => $values
]);
$params = [
  'valueInputOption' => $valueInputOption
];
$result = $service->spreadsheets_values->append($spreadsheet_id, $spreadsheet_range,
    $body, $params);
printf("%d cells appended.", $result->getUpdates()->getUpdatedCells());*/
/*---------------------*/

/*--------- Testing Update --------*/




//$spreadsheet_range1=$spreadsheet_range.'!f4';
// Update the spreadsheet
//$service->spreadsheets_values->update($spreadsheet_id, $spreadsheet_range1, $valueRange, $conf);
/*echo '<pre>';
print_r($spreadsheetList);
echo '</pre>';*/


//===========================================================================

  // You need to specify the values you insert
	$valueRange= new Google_Service_Sheets_ValueRange($client);
	$valueRange->setValues(["values" => ["15%"]]); // Add two values

	// Then you need to add some configuration
	$conf = ["valueInputOption" => "RAW"];

  $result = $service->spreadsheets_values->get($spreadsheet_id, $spreadsheet_range);
  $arrayData  = $result->getValues();

  putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

  putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $service_account_file);

  $arrayData = $result->getValues(); // Mang du lieu

  foreach($arrayData as $key => $value) {
    if(in_array('Phuc Loc', $value)) {
        $spreadsheet_range1	=$spreadsheet_range.'!e'.($key+1);
        $service->spreadsheets_values->update($spreadsheet_id, $spreadsheet_range1, $valueRange, $conf);
        break;
    }

   }

   echo '<pre>';
   print_r($arrayData);
   echo '</pre>';
