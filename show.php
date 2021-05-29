<?php
ini_set('display_errors', 'on');
session_start();

$apiKey = $_SESSION['key'];
$server = $_SESSION['server'];
echo $apiKey . '<br>';
echo $server . '<br>';




require_once('vendor/autoload.php');
$mailchimp = new MailchimpMarketing\ApiClient();
$mailchimp->setConfig([
  'apiKey' => $apiKey,
  'server' => $server,
]);

function clean_operation(){
	global $mailchimp;
	$response = $mailchimp->batches->list();
	$arr = $response->batches;	

	foreach ($arr as $value) {

		$response = $mailchimp->batches->deleteRequest($value->id);
		print_r($response);
	}
}

// $response = $mailchimp->batches->list();
//  print_r($response);


$batch_id = $_SESSION['batch_id'];

try { 
	$response = $mailchimp->batches->status($batch_id);
	echo json_encode($response);
	}	catch (\MailchimpMarketing\ApiException $e) {
	    echo json_encode($e->getMessage());
	}
	



$response = $mailchimp->batches->list();
print_r($response);
// clean_operation();