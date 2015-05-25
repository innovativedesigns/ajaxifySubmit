<?php

$response = array();
$response['status'] = 'OK';
$response['messages'] = array();

if ( isset($_POST) && $_POST ) {

	// attempt to prevent spam by checking whether the 'confirmation' field has been filled
	// (spam bots will often fill all fields with rubbish values, so if we get a value here then assume it's spam)
	// (to use this, include a text input field with the name 'confirmation' in the contact form,
	//  and hide it using css.  don't use a hidden input field, as bots can get past those easier)
	if ( isset($_POST['confirmation']) ) {
		if ( $_POST['confirmation'] !== "" ) {
			// there is a value in the confirmation field, so this is spam
			$response['status'] = 'FAIL';
			$response['messages'] = array('Spam detected');
		}
	}

	$name = ( isset($_POST['contact_name']) ? trim($_POST['contact_name']) : "" );
	$email = ( isset($_POST['contact_email']) ? trim($_POST['contact_email']) : "" );
	$phone = ( isset($_POST['contact_phone']) ? trim($_POST['contact_phone']) : "" );
	$location = ( isset($_POST['contact_location']) ? trim($_POST['contact_location']) : "" );
	$msg = ( isset($_POST['contact_message']) ? trim($_POST['contact_message']) : "" );

	$dest_email = FILL_ME_IN;

	if ( $name === "" ) {
		array_push($response['messages'], "Name");
		$response['status'] = "FAIL";
	}
	if ( $email === ""  && $phone === "" ) {
		array_push($response['messages'], "One of Email or Phone #");
		$response['status'] = "FAIL";
	}
	if ( $msg === "" ) {
		array_push($response['messages'], "Message");
		$response['status'] = "FAIL";
	}

	if ( $response['status'] === "OK" ) {
		$contact = "$email, $phone";

		mail($dest_email, "Web contact from $name for ".$_SERVER['HTTP_HOST'],
			"Name: $name\r\n".
			"Contact Info: $contact\r\n\r\n".
			$msg,
			"From: $email\r\nReply-To: $email\r\nX-Mailer: PHP/".phpversion());
	}

} else {
	$response['status'] = 'FAIL';
}

echo json_encode($response);
