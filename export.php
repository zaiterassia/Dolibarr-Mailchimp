<?php
ini_set('display_errors', 'on');
session_start();
require_once('vendor/autoload.php');
include("connexion_bdd.php");


mysqli_set_charset($con, "utf8"); 
$req = "SELECT * FROM llx_societe";
$resultat = mysqli_query($con, $req);

$apiKey = mysqli_real_escape_string($con,$_POST['mailchimpApiKey']);
$server = $server = explode("-", $apiKey)[1];
$list_id = mysqli_real_escape_string($con,$_POST['mailchimpListeId']);

if(strlen($apiKey)<1){
    $_SESSION["error"]=1;
    $_SESSION["msg"]="mailchimp key est requise";
    mysqli_close($con);
    echo json_encode(0);
 }
elseif(strlen($list_id)<1){
    $_SESSION["error"]=1;
    $_SESSION["msg"]="l'identifiant de l'audiance est requise";
    mysqli_close($con);
    echo json_encode(0);
 }
elseif(strlen($apiKey)<20){
    $_SESSION["error"]=1;
    $_SESSION["msg"]="veuillez saisir une clé mailchimp valide";
    mysqli_close($con);
    echo json_encode(0);
}
else{

	$_SESSION['key'] = $apiKey;
	$_SESSION['server'] = $server;
	$_SESSION['list'] = $list_id;	

	$mailchimp = new MailchimpMarketing\ApiClient();
	$mailchimp->setConfig([
	  'apiKey' => $apiKey,
	  'server' => $server,
	]);	

	function export_contact(){
		global $resultat, $mailchimp, $list_id, $batch_id;
		$operations = [];
		while($data= mysqli_fetch_array($resultat)) {
		    $operation = [
		        'method' => 'POST',
		        'path' => "/lists/$list_id/members",
		        'operation_id' => $data['rowid'],
		        'body' => json_encode([
		            'email_address' => $data['email'],
		            'status' => 'subscribed',
		            'merge_fields'  => array(
	                 'FNAME'     => $data['nom'],
	                 'LNAME'     => ' ',
	                 'ADDRESS'   => $data['address'] . ' ' . $data['zip'] . ' ' . $data['town'] ,
	                 'PHONE'    => $data['phone']
	             	)
	  
		        ])
		    ];
		    array_push($operations, $operation);
		}		

		try {
		    $response = $mailchimp->batches->start(["operations" => $operations]);
		    $_SESSION['batch_id'] = $response->id;
		   	return $response;
			
		} catch (\MailchimpMarketing\ApiException $e) {
		    return $e->getMessage();
		}
	}	

	echo json_encode(export_contact());
}