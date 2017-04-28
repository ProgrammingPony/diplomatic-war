<?php
define("cVERIFICATION_CODE_EXPIRY", 7200);
define("cMAXIMUM_PASSWORD_LENGTH", 26);
define("cFAIL_LOGIN_MESSAGE", "The provided combination is invalid.");
define("cPASS_LOGIN_MESSAGE", "Login is successful... you will be redirected.");
define("cMAXIMUM_VERIFICATION_ATTEMPTS", 3);
define("cVERIFICATION_CODE_LENGTH", 40);
define('cPASSWORD_RETRIEVAL_CODE_LENGTH', 20);
define('cPASSWORD_RETRIEVAL_SALT_LENGTH', 32);
define('cPASSWORD_RETRIEVAL_CODE_EXPIRY', 7200);
define('cPASSWORD_SALT_LENGTH', 32);

/*
Adds a new user and returns the associated user id. First and last name is automatically captialized before
sending to database.
Returns user_id
Requires: database_functions.php security_functions.php openssl
*/
function umf_add_new_user ($f_name, $l_name, $birth_date, $display_name, $email, $password) {
	//Ensure names in uppercase
	$f_name = strtoupper($f_name);;
	$l_name = strtoupper($l_name);

	//Create salt for password
	$strength = false; //Strength of salt	
	while (!$strength)
		$salt = openssl_random_pseudo_bytes(cPASSWORD_SALT_LENGTH, $strength);	
	$pass_hash = secf_password_hash($password, $salt);
	
	//Add user information to user table and password in one transaction
	$con = dbf_user_connect();
	$con->autocommit(FALSE);
	$stmt = $con->prepare('INSERT INTO users (user_type, first_name, last_name, birth_date, display_name, email, status)
		VALUES (2, ?, ?, ?, ?, ?, 1);');
	$stmt->bind_param('sssss', $f_name, $l_name, $birth_date, $display_name, $email);
	$stmt->execute();
	$stmt->close();
	
	$stmt = $con->prepare('INSERT INTO passwords (salt, password_hashed) 
		VALUES(?, ?);');
	$stmt->bind_param('ss', $salt, $pass_hash);	
	$stmt->execute();
	$stmt->close();
	
	if (!$con->commit()) {
		error_log('umf_add_new_user: insert transaction failed');
		$con->rollback();
	}	
	
	//Setup login history
	$con->autocommit(TRUE);
	$user_id = umf_get_user_id ("email", $email);
	$stmt = $con->prepare('INSERT INTO login_history (user_id)
		VALUES (?);');
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->close();
	
	$con->close();
	return $user_id;
}

/*
Checks if an entered password for a user is correct or not. Assumes that verification was done on input
Returns true if correct, false otherwise
Requires: database_functions.php security_functions.php
*/
function umf_check_credentials($user_id, $password) {

	//Retrieve information from database
	$con = dbf_user_connect();
	$stmt = $con->prepare("SELECT salt,password_hashed FROM passwords WHERE user_id=?;");	
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->bind_result($salt, $pass_hash);
	$stmt->fetch();
	
	//When response isn't empty
	if (isset($salt) || isset($pass_hash)) {		
		//When credentials are not correct
		if (strcmp($pass_hash, secf_password_hash($password, $salt))) {
			$return = false;
		}
		//When they do match
		else {
			$return = true;
		}
	//When the response is empty
	} else {
		error_log("umf_correct_credentials: The user id does not have any corresponding data.");
		$return = false;
	}
	
	$stmt->close();	
	$con->close();	
	return $return;
}

/*
Returns true if duplicate, false if it isn't
Requires: database_functions.php
*/
function umf_display_name_duplicate($display_name) {
	$return = false; //Assume it is not duplicated
	$con = dbf_user_connect();
	$stmt = $con->prepare("SELECT display_name FROM users WHERE display_name=?;");
	$stmt->bind_param('s', $display_name);
	$stmt->execute();
	$stmt->bind_result($res);
	$stmt->close();
	$con->close();
	
	//When it exists in the database
	if ($res)
		$return = true;
		
	return $return;
}

/*
Returns true if duplicate, false if it isnt
Requires: database_functions.php
*/
function umf_email_duplicate($email) {
	$con = dbf_user_connect();
	$stmt = $con->prepare("SELECT email FROM users WHERE email=?;");
	$stmt->bind_param('s', $email);
	$stmt->execute();
	$stmt->bind_result($res);
	$stmt->fetch();
	$stmt->close();
	
	if ($res)
		$return = true;
	else
		$return = false;
	
	$con->close();
	return $return;
}

/*
Returns email associated with user id
Requires: database_functions.php
*/
function umf_get_email ($user_id) {
	$con = dbf_user_connect();
	$stmt = $con->prepare("SELECT email FROM users WHERE user_id=?;");
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->bind_result($email);
	$stmt->fetch();
	$stmt->close();	
	return $email;
}

/*
Returns user type, status, display name
Requires: database_functions.php
*/
function umf_get_user_info($user_id) {
	$con = dbf_user_connect();
	$stmt = $con->prepare("SELECT user_type,display_name,status FROM users WHERE user_id=?;");
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->bind_result($u_type, $d_name, $status);
	$stmt->fetch();
	$stmt->close();
	
	$return['user_type'] = $u_type;
	$return['display_name'] = $d_name;
	$return['status'] = $status;
	return $return;
}

/*
NOTE THIS FUNCTION NEEDS A CHANGE, SHOULDN'T BE ALLOWED TO GRAB WITH SESSION_ID WITHOUT WARNING
paramaters( "email"|"display_name"|"session_id" , string corresponding to param 1)
returns
1. user_id associated with the "email"|"display_name"|"session_id" given in parameter 2
2. 0 if it was unable to retrieve user id
Requires: database_functions.php
*/
function umf_get_user_id ($type, $value) {
	$con = dbf_user_connect();
	
	switch($type) {
	case "email":
		$stmt = $con->prepare("SELECT user_id FROM users WHERE email=?;");
		$stmt->bind_param('s', $value);
		break;
	case "display_name":
		$stmt = $con->prepare("SELECT user_id FROM users WHERE display_name=?;");
		$stmt->bind_param('s', $value);
		break;
	case "session_id":
		$stmt = $con->prepare("SELECT user_id FROM sessions WHERE session_id=?;");
		$stmt->bind_param('s', $value);
		break;
	default:
		break;
	}
	
	$stmt->execute();
	$stmt->bind_result($user_id);
	$stmt->fetch();
	$stmt->close();
	
	//When it exists in database
	if (isset($user_id))
		$return = intval($user_id);
	//When no response, doesn't exist
	else
		$return = 0;
		
	$con->close();
	return $return;
}

function umf_get_status_string($status_num) {
	switch($status_num) {
	case 1:
		return 'Not Verified';
		break;
	case 2:
		return 'Verified';
		break;
	case 3:
		return 'Banned';
		break;
	case 4:
		return 'Suspended';
		break;
	default:
		error_log("umf_get_status_string: value $status_num is invalid input");
		return '';
		break;
	}
}

/*
Returns display name associated with given user id
Requires: database_functions.php
*/
function umf_get_display_name ($user_id) {
	$con = dbf_user_connect();
	$stmt = $con->prepare("SELECT display_name FROM users WHERE user_id=?");
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->bind_result($d_name);
	$stmt->fetch();
	$stmt->close();
	$con->close();
	return $d_name;
}

/*
Updates the time in the database when a user last logged in
Requires: database_functions.php client_functions.php
*/
function umf_update_last_login ($user_id) {
	$client_ip = clif_get_client_ip();
	$date = date('Y-m-d H:i:s');

	//Retrieve previous login information
	$con = dbf_user_connect();
	
	$stmt = $con->prepare('SELECT latest_login_ip,latest_login_time FROM login_history
		WHERE user_id=?');
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->bind_result($prev_ip, $prev_time);
	$stmt->fetch();
	$stmt->close();
	
	//Update previous login (if it exists)
	if (!empty($prev_time)) {
		$stmt = $con->prepare("UPDATE login_history SET prev_login_ip=?,prev_login_time=?
			WHERE user_id=?;");
		$stmt->bind_param('ssi', $prev_ip, $prev_time, $user_id);
		$stmt->execute();
		$stmt->close();	
	}
	
	//Update latest login
	$stmt3 = $con->prepare("UPDATE login_history SET latest_login_ip=?,latest_login_time=?
		WHERE user_id=?;");
	$stmt3->bind_param('ssi', $client_ip, $date, $user_id);
	$stmt3->execute();
	$stmt3->close();
		
	$con->close();
}

/*
Creates verification code, stores it in database, delivers email to user.
Requires: database_functions.php
*/
function umf_prepare_verification($user_id, $email) {
	umf_remove_verification_code($user_id); //Remove old code if it exists	

	$ver_code = dcf_generate_string (cVERIFICATION_CODE_LENGTH); //Generate new verification
	$expiry = date("mdyHis", time()+cVERIFICATION_CODE_EXPIRY);
	
	//Put into database
	$con = dbf_user_connect();
	$stmt = $con->prepare("INSERT INTO verifications (user_id, ver_code, expiry)
		VALUES (?, ?, ?);");
	$stmt->bind_param('iss', $user_id, $ver_code, $expiry);
	$stmt->execute();
	$stmt->close();
	$con->close();
	
	//Send mail to user
	$message = 'Hello, this is the verification email to complete your registration. Please click on the following link or paste it into your browser to complete your registration, the verification link expires in two hours. http://diplomatic-war.omarabdelbari.com/verify.php?ver_code=' . $ver_code . '
Also you may manually enter your verification code at http://diplomatic-war.omarabdelbari.com/verify.php, Your code is: ' . $ver_code . '.

NOTE: YOU MUST BE LOGGED IN IN ORDER FOR THE VERIFICATION LINK TO WORK.

Has your verification link expired? No worries! Just try to login on at http://diplomatic-war.omarabdelbari.com and we will send you another link.

If you did not register at Diplomatic War, don\'t worry, you can ignore this email.';

	$mail = new PHPMailer();

	$mail->IsSMTP();
	$mail->Host = 'p3plcpnl0425.prod.phx3.secureserver.net';
	$mail->SMTPAuth = true;
	$mail->Username = "dolphinlover1";
	$mail->Password = "135531Ab";

	$mail->From = 'noreply@diplomatic-war.com';
	$mail->FromName = "Diplomatic War";
	$mail->AddAddress($email);

	$mail->WordWrap = 70;
	$mail->IsHTML(true);

	$mail->Subject = 'Verification Code';
	$mail->Body    = $message;
	$mail->AltBody = $message;

	if(!$mail->Send())
		error_log('verification code failed to sendMailer Error: ' . $mail->ErrorInfo);
}

/*
Removes verification code from database
Requires: database_functions.php
*/
function umf_remove_verification_code($user_id) {
	$con = dbf_user_connect();
	$stmt = $con->prepare("DELETE FROM verifications WHERE user_id=?;");
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->close();
	$con->close();
}

/*
Check if a verification code has expired, removes it if it has
Returns true if verification code is expired or doesnt exist, false if it hasn't expired
Requires: database_functions.php, data_conversion_functions.php
*/
function umf_verification_expired($user_id) {
	$return = false; //Assume in most cases it will not have expired
	$con = dbf_user_connect();
	$stmt = $con->prepare("SELECT expiry FROM verifications WHERE user_id=?;");
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->bind_result($expiry);
	$stmt->fetch();
	$stmt->close();
	
	//If there is an expiry listed in database
	if ($expiry) {			
		//When it has expired
		if ((time() - dcf_strtounixepoch ($expiry)) > 0) {
			$return = true;
			umf_remove_verification_code($user_id);
		}
	//When there isnt an expiry listed in the database
	} else
		$return = true;
		
	$con->close();
	return $return;
}

/*
Checks if given verification by a user is correct and not expired. If verification is successful, changes 
status to verified , removes verification code from the database as well
Returns
0 if verification successful
1 if verification failed
2 if verification code expired
3 no verification code found
4 maximum attempts allowed used, new one sent
Requires: database_function.php, data_conversion_functions.php
*/
function umf_verify_user($user_id, $c_ver_code) {
	$con = dbf_user_connect();
	$stmt = $con->prepare("SELECT ver_code,expiry,ver_attempts FROM verifications WHERE user_id=?;");
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->bind_result($s_ver_code, $expiry, $attempts);
	$stmt->fetch();
	$stmt->close();
	
	$email = umf_get_email($user_id);
	
	//If it exists in database
	if ($s_ver_code) {
		$expiry = dcf_strtounixepoch($expiry);
		
		//Check if it expired
		if ((time() - $expiry) > 0) {
			$return = 2;
			umf_remove_verification_code($user_id); //Remove from database
			umf_prepare_verification($user_id, $email);
		} else {			
			//When it matches the one in the database, update their status, delete code
			if ($s_ver_code == $c_ver_code) {
				$stmt = $con->prepare("UPDATE users SET status=2 WHERE user_id=?;");
				$return = 0;
				$stmt->bind_param('i', $user_id);
				$stmt->execute();
				$stmt->close();
				umf_remove_verification_code($user_id);
			//When it doesn't match the one in the database, increment attempts, and remove if more than allowed attempt
			} else {
				$attempts++;
				
				//Check attempts, remove if necessary, update if necessary into database
				if ($attempts >= cMAXIMUM_VERIFICATION_ATTEMPTS) {
					$return = 4;
					umf_remove_verification_code($user_id);
					umf_prepare_verification($user_id, $email);
				} else {
					$return = 1;
					$stmt = $con->prepare("UPDATE verifications SET ver_attempts=? WHERE user_id=?;");
					$stmt->bind_param('ii', $attempts, $user_id);
					$stmt->execute();
					$stmt->close();
				}
			}
		}
	//When no verification code is found...
	} else {
		$return = 3;
		umf_prepare_verification($user_id, $email);
	}
	
	$con->close();
	return $return;
}

/*
Requires database_functions.php class.phpmailer.php
*/
function umf_prepare_password_retrieval($user_id) {
	//Generate information	
	$code = dcf_generate_string(cPASSWORD_RETRIEVAL_CODE_LENGTH);
	$salt = openssl_random_pseudo_bytes(cPASSWORD_RETRIEVAL_SALT_LENGTH);
	$ret_hash = secf_password_reset_hash($code, $salt);
	$expiry = date("mdyHis", time()+cPASSWORD_RETRIEVAL_CODE_EXPIRY);
	$session_id = session_id();

	if (!$user_id)
		error_log('umf_prepare_password_retrieval: user_id is 0 or empty');
	
	//Store in database
	$con = dbf_user_connect();
	$stmt = $con->prepare('INSERT INTO password_retrieval (user_id, salt, retrieval_hash, expiry, session_id)
		VALUES (?, ?, ?, ?, ?);');
	$stmt->bind_param('issss', $user_id, $salt, $ret_hash, $expiry, $session_id);
	$stmt->execute();
	$stmt->close();
	$con->close();
	
	//Mail user
	$email = umf_get_email($user_id);
	$message = 'Hello, a request was made to reset the password at diplomatic-war associated with this account.
The code is ' . $code;
//'. You can click on the following link as well https://diplomatic-war.com/support/retrieve-password.php?code=' . $code . '
//NOTE: YOU MUST ENTER THIS CODE IN THE SESSION IN WHICH YOU REQUESTED IT FROM OR IT WILL NOT WORK.';

	$mail = new PHPMailer();

	$mail->IsSMTP();
	$mail->Host = 'p3plcpnl0425.prod.phx3.secureserver.net';
	$mail->SMTPAuth = true;
	$mail->Username = "dolphinlover1";
	$mail->Password = "135531Ab";

	$mail->From = 'noreply@diplomatic-war.com';
	$mail->FromName = "Diplomatic War";
	$mail->AddAddress($email);

	$mail->WordWrap = 70;
	$mail->IsHTML(true);

	$mail->Subject = 'Password Reset Code';
	$mail->Body    = $message;
	$mail->AltBody = $message;

	if(!$mail->Send())
		error_log("umf_prepare_password_retrieval: user_id=$user_id $mail->ErrorInfo");
}

/*
Delete stored database information for password reset
requires: database_functions.php
*/
function umf_remove_password_retrieval($user_id) {
	$con = dbf_user_connect();
	$stmt = $con->prepare('DELETE FROM password_retrieval WHERE user_id=?;');
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->close();
	$con->close();
}

/*
Delete stored database information for password reset
Return true if deleted password retrieval, false otherwise
requires: database_functions.php
*/
function umf_update_password_retrieval_attempts($user_id) {
	$con = dbf_user_connect();
	$stmt = $con->prepare('SELECT attempts FROM password_retrieval WHERE user_id=?;');
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->bind_result($attempts);
	$stmt->fetch();
	$stmt->close();
	$con->close();

	$attempts++;
	
	if ($attempts > 3) {
		umf_remove_password_retrieval($user_id);
		return true;
	} else {
		$session_id = session_id();
		$con = dbf_user_connect();
		$stmt = $con->prepare('UPDATE password_retrieval SET attempts=? WHERE user_id=?;');
		$stmt->bind_param('is', $attempts, $user_id);
		$stmt->execute();
		$stmt->close();
		$con->close();
		return false;
	}
}
?>
