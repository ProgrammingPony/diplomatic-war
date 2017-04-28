<?php
require ('template1_imports.php');
require ('template1_header_operations.php');

if (!empty($user_status)) {
	switch($user_status) {
	case 1: case 4: case 3://This may change later for banned and suspended users
		header("Location: http://diplomatic-war.omarabdelbari.com/index.php");
		exit;
		break;
	case 2:
		header("Location: http://diplomatic-war.omarabdelbari.com/verify.php");
		exit;
	default:
		error_log('login.php : invalid user_status when determining header');
		break;
	}
}

$email_error = $password_error = $recaptcha_error = "";	
$submit_message = "";

if (!isset($_SESSION["login_attempts"]))
	$_SESSION["login_attempts"] = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$_SESSION["login_attempts"]++; //Update number of attempts
	
	//Only do login check when attempts less than 10
	if ($_SESSION["login_attempts"] < 10) {
		$validation_success = true; //Will be turned false when any error is spotted
		$email = $_POST["login_email"];
		$password = $_POST["login_password"];
	
		//Only do recaptcha validation after 3 attempts
		if ($_SESSION["login_attempts"] > 3) {
			require_once('libraries/recaptchalib.php');
			$privatekey = "6LdWFPQSAAAAAA3OGlakOS0MzH5uBBV6dcgveamm";
			$resp = recaptcha_check_answer ($privatekey,
				$_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"]);
			
			if (!$resp->is_valid) {
				$recaptcha_error = "The user input in the recaptcha field is not valid.";
				$validation_success = false;
			}
		}	
		
		//Validate other fields
		if (empty($email)) {
			$email_error = "Email is required";
			$validation_success = false;
		} else if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
			$email_error = "Invalid format for email.";	
			$validation_success = false;
		}
		
		if (empty($password)) {
			$password_error = "Password is required";
			$password = "";
			$validation_success = false;
		} else if (!preg_match("/^[a-zA-Z0-9]*$/", $password)) {
			$password_error = "Only letters and numbers are permitted for the password";
			$password = "";
			$validation_success = false;
		}
			
		if ($validation_success) {
			$l_user_id = umf_get_user_id("email", $email);
			
			//Authenticate Credentials				
			//When user id has a value
			if (isset($l_user_id)) {
				//When the credential provided were correct
				if (umf_check_credentials($l_user_id, $password)) {
					$submit_message = cPASS_LOGIN_MESSAGE;
					umf_update_last_login ($l_user_id);
					
					//Create new session
					sesf_end_session(session_id());						
					sesf_create_session ($l_user_id);

					header("Location: http://diplomatic-war.com/index.php");
					exit;
				//When the credentials provided were not
				} else {
					$submit_message = cFAIL_LOGIN_MESSAGE;
				}
			} else {
				$submit_message = cFAIL_LOGIN_MESSAGE;
			}
		}
	}		
}

require('template1_part1.php');
?>

<title>LOGIN - DIPLOMATIC WAR</title>
<?php require('template1_part2.php'); ?>
<h1>Login</h1>

<?php 
if ($_SESSION["login_attempts"] < 10) {
	echo '<form method="post" action="' . htmlspecialchars('http://diplomatic-war.com/login.php') . '">
	<p class="form-error">' . $submit_message . '</p>
	<br>';
	
	if ($_SESSION["login_attempts"] > 2) {
		echo '<p class="form-error">You have submitted ' . $_SESSION["login_attempts"] . ' invalid login attempts during this session 
			Please complete the recaptcha challenge in order to continue. Once you reach 10 attempts you will no longer
			be able to make any attempts until an hour has elapsed since the session started.</p>';
	}
	
	echo '
		<label for="login_email">E-mail <span id="email_error" class="form-error">' . $email_error . '</span></label>
		<input id="login_email" name="login_email" maxlength="255" type="text">
		<br>
		
		<label for="login_password">Password <span id="password_error" class="form-error">' . $password_error . '</span></label>
		<input id="login_password" name="login_password" maxlength="30" type="password">
		<br>';
		
	if ($_SESSION["login_attempts"] > 2) {
		echo '<div id="recaptcha_challenge_image-holder">';
		require_once('libraries/recaptchalib.php');
		$public_key = "6LdWFPQSAAAAAJCSb4UZR7tZp0JwKDwC88W11dxA";
		echo recaptcha_get_html($public_key);
		echo '</div>
		<br>
		<span class="form-error">'. $recaptcha_error .'</span> 	
		<br>';
	}		

	echo '<input id="submit" type="submit" name="submit" value="Login">
		<br>
		</form>';
} else {
	echo '<p>You have reached the maximum number of login attempts for your current session, please try again later.</p>';
}
?>

<p>Forgot your credentials? Click <a href="http://support.diplomatic-war.com/retrieve-password.php">here</a>.</p>

<?php require('template1_part3.php');?>
