<?php

//AUTHENTICATION FUNCTIONS
/*
input(int, string)
output false if password is not correct, true if it is.
requires: database_functions.php
*/
function secf_password_check($user_id, $password) {
	$correct = false;
	$con = dbf_user_connect();
	$stmt = $con->prepare("SELECT password_hashed,salt FROM passwords WHERE user_id=?;");
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->bind_result($pass_hash, $salt);
	$stmt->fetch();
	$stmt->close();
	$con->close();
	
	if (strcmp($pass_hash, secf_password_hash($password, $salt)))
		$correct = true;

	return $correct;
}

//HASHES
/*
Hash used for passwords
*/
function secf_password_hash($password, $salt) {
	return hash('sha512', $password . $salt);
}

/*
Hash used in password retrieval
Requires client_functions.php
*/
function secf_password_reset_hash($code, $salt) {
	return hash('sha512', $code . substr(clif_get_browser(), 1, 3) . substr(clif_get_client_ip(), 2, 4) . session_id() . substr(clif_get_os(), 0, 4) . $salt);
}

/*
Requires client_functions.php
*/
function secf_session_hash($session_id, $server_salt) {
	return hash('sha512', substr(clif_get_os(), -4) . $session_id . substr(clif_get_browser(), 2) . substr(clif_get_client_ip(), 3) . $server_salt);
}
?>