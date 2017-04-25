<?php
/*
Takes as input a post request with a user name and password.
If successful returns a session hash encrypted, otherwise returns empty string
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	require ('imports.php');

	$password = $_POST['password'];
	$email = $_POST['email'];

	$isValid = true;

	//Validate other fields
	if (empty($email)) {
		$isValid = false;
	} else if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
		$isValid = false;
	}

	if (empty($password)) {
		$isValid = false;
	} else if (!preg_match("/^[a-zA-Z0-9]*$/", $password)) {
		$isValid = false;
	}
		
	if ($isValid) {
		$user_id = umf_get_user_id("email", $email);
		
		//Authenticate Credentials				
		//When user id has a value
		if (isset($user_id)) {
			//When the credential provided were correct
			if (umf_check_credentials($user_id, $password)) {
				$submit_message = cPASS_LOGIN_MESSAGE;
				umf_update_last_login ($user_id);
				
				//Create new session
				sesf_end_session(session_id());						
				sesf_create_session ($user_id);
				echo '|SESSIONID|' . session_id();
				
			//When the credentials provided were not
			} else {
				echo '|MESSAGE|' . cFAIL_LOGIN_MESSAGE;
			}
		} else {
			echo '|MESSAGE|' . cFAIL_LOGIN_MESSAGE;
		}
	}
}
?>