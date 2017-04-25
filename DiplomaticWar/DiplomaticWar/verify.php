<?php
require ('template1_imports.php');
require ('template1_header_operations.php');

//Redirect to main page if they are already verified
if ( !empty($user_status) && ($user_status == 2) ) {
	error_log("leaving verify to index");
	header("Location: https://diplomatic-war.com/index.php");
	exit;
}

//verify user if they used get or post request and they are logged in
if ( isset($user_id) && isset($_GET['ver_code']) ) {
		$ver_err = umf_verify_user($user_id, $_GET['ver_code']);
}

//When the verification was successful, redirect to succes page
if (isset($ver_err) && (!$ver_err)) {
	header('Location: http://diplomatic-war.com/register-success.php');
	exit;
}

require('template1_part1.php');
?>

<title>VERIFICATION - DIPLOMATIC WAR</title>
	
<?php
require('template1_part2.php');

//When they are logged into the account
if ($user_id) {
	//When the process of verification was run
	if (isset($ver_err)) {
		switch($ver_err) {
		case 0:
			error_log("ver_err is read as 0 when that case should have been handled before header sent out.");
			break;
		case 1:
			echo '<span class="form-error">The verification code you entered does not match the one provided to you.
			Please try again.</span>';
			break;
		case 2:
			echo '<span class="form-error">Your verification code has expired, a new one has already been sent.</span>';
			break;
		case 3:
			echo '<span class="form-error">No verification code associated with your account was found, another verification 
			code was automatically sent to your registered email.</span>';
			break;
		case 4:
			echo '<span class="form-error">The maximum attempts of the active verification code have been exceeded, a new
			verification code was sent.</span>';
			break;
		default:
			error_log("Received invalid error code from umf_verify_user() in verify.php");
			break;
		}
	//When the process of verification was not run
	} else {
		echo '<p>A verification string was sent to the email you provided to us during your registration. Please 
			enter this code in the field below. After ' . cMAXIMUM_VERIFICATION_ATTEMPTS . ' failed attempts, the verification code is deleted and a new one is sent automatically.
			Refresh this page in order to receive a new one. If an account remains unverified for ' . (cVERIFICATION_CODE_EXPIRY/3600 ) .
			' hours it is subject to removal, after which the email may be used for registering again. You may need to check your spam folder!</p>';
	}

	echo '<form method="get" action="' . htmlspecialchars('https://diplomatic-war.com/verify.php') . '">
			<label for="ver_code">Verification Code: </label>
			<input id="ver_code" name="ver_code" type="text" maxlength="50" placeholder="Please Enter the Verification Code Here">
			<br>
			
			<input id="submit" type="submit" name="submit" value="Verify">
		</form>';
//For a guest session
} else {
	echo '<p>You must be logged in order to verify your account.</p>';

}

require('template1_part3.php');
?>