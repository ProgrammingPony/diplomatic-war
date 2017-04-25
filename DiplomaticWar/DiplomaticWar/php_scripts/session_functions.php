<?php 
define("cSESSION_ID_LENGTH",30);
define("cSESSION_TIMEOUT_LENGTH",21600); //6 Hours

/*
Checks a user's session and ensures it is valid and not expired. If it is not valid or expired,
a new session is made for them.
NO INPUT
NO RETURN VALUE
Requires: database_functions.php
*/
function sesf_assert() {
	$session_id = session_id();	
	
	$con = dbf_user_connect();
	$stmt = $con->prepare('SELECT salt,session_hash FROM sessions WHERE session_id=?');
	$stmt->bind_param('s', $session_id);
	$stmt->execute();
	$stmt->bind_result($salt, $ses_hash);
	$stmt->fetch();
	
	$does_not_belong = strcmp($ses_hash, secf_session_hash(session_id(), $salt));

	$stmt->close();
	$con->close();
	
	//When the session doesn't belongs to the user
	if ($does_not_belong) {	
		sesf_create_session(null);
	//When session does belong to the user
	} else {
		//Ensure not expired, if so end it and make a new guest session
		if ( sesf_expired(session_id())) {
			sesf_create_session(null);
		}
	}
}

/*
Creates new session and stores it into database, updates last login
param (integer user id)
NOTE: use 0 as input if user is guest

return true if successful, false if failure
Requires: database_functions.php, client_functions.php user_management_functions.php session_functions.php
*/
function sesf_create_session ($user_id) {
	session_regenerate_id(true);
	session_unset();
	$session_unique = sesf_is_session_unique(session_id());
	
	//Create unique session id
	while(!$session_unique) {
		session_regenerate_id(false);
		$session_unique = sesf_is_session_unique(session_id()); //Check if session is unique
	}
	
	$session_id = session_id();
	
	//Create server salt
	$strong = false; // Strength of salt
	while (!$strong) { $salt = openssl_random_pseudo_bytes(32, $strong); }

	$ses_hash = secf_session_hash($session_id, $salt);
	$date = date("mdyHis", time()+cSESSION_TIMEOUT_LENGTH);
	
	//Add to database
	$con = dbf_user_connect();
	$stmt = $con->prepare("INSERT INTO sessions (session_id, user_id, salt, session_hash, expiry)
		VALUES (?, ?, ?, ?, ?);");
	$stmt->bind_param('sisss', $session_id, $user_id, $salt, $ses_hash, $date);
	$stmt->execute();
	$stmt->close();
	$con->close();
	
	umf_update_last_login($user_id);//Update last login information for user
}

/*
param (string session_id)
returns false if session_id exists, true otherwise
Requires: database_functions.php
*/
function sesf_is_session_unique($session_id) {
	$con = dbf_user_connect ();
	$stmt = $con->prepare("SELECT session_id FROM sessions WHERE session_id=?;");
	$stmt->bind_param('s', $session_id);
	$stmt->execute();
	$stmt->bind_result($db_res);
	$stmt->fetch();

	//When the session id does not exist in the database
	if ($db_res) {
		$return = true;
		$stmt->close();
	//When the session id is in the database
	} else {
		//Delete it if its expired, making it available for use
		if ( sesf_expired($session_id) )
			$return = true;
		//Means it is still in use so we cant use it
		else
			$return = false;
	}
	
	$con->close();
	return $return;
}

/*
Checks expiry of a session id, removes if expired
returns true if it expired, false if it is active
Requires: database_functions.php
*/
function sesf_expired($session_id) {
	$con = dbf_user_connect ();	
	$stmt = $con->prepare("SELECT expiry FROM sessions WHERE session_id=?;");
	$stmt->bind_param('s', $session_id);
	$stmt->execute();
	$stmt->bind_result($expiry);
	$stmt->fetch();
	$stmt->close();

	//If expiry exists in database
	if ($expiry) {
		$expiry = dcf_strtounixepoch ($expiry);
		//If it expired
		if ( ( time() - $expiry ) > 0 ) {
			$stmt= $con->prepare("DELETE FROM sessions WHERE session_id=?;");
			$stmt->bind_param('s', $session_id);
			$stmt->execute();
			$stmt->close();
			$return = true;
		//If it didnt expire
		} else
			$return = false;	
	//If expiry doesn't exist in database then we indicate its expired
	} else {
		$return = true;
	}
	
	$con->close();
	return $return;
}

/*
Ends session
No parameters
No Output
Requires: database_functions.php
*/
function sesf_end_session($session_id) {
	$con = dbf_user_connect();
	$stmt= $con->prepare("DELETE FROM sessions WHERE session_id=?;");
	$stmt->bind_param('s', $session_id);
	$stmt->execute();
	$stmt->close();
	$con->close();
}

/*
Redirects user if they are already logged in
*/
function sesf_redirect_if_logged_in($user_id) {
	if ($user_id) {
		header("Location: https://diplomatic-war.com/index.php");
		exit;
	}
}
?>