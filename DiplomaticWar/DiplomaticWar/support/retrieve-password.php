<?php
//Reference: http://stackoverflow.com/questions/4369/how-to-include-php-files-that-require-an-absolute-path
require 'template1_imports.php';
require "$root/template1_header_operations.php";
require "$root/libraries/recaptchalib.php";

//1 for the request form, 2 for the code form, 3 for the new password form
if (!isset($_SESSION['retrieve_password_stage']))
	$_SESSION['retrieve_password_stage'] = 1;

$email_error = $code_error = $password_error = $password2_error = '';
$submit_msg = '';
$session_id = session_id(); //For bind_param database queries

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$is_valid = true; //If fields have the right format and characters
	
	if ($_SESSION['retrieve_password_stage'] === 1) {
		$f_email = $_POST['r_email'];
		
		if (empty($f_email)) {
			$is_valid = false;
			$email_error = "Email is required";		
		} else if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$f_email)) {
			$is_valid = false;
			$email_error = "Invalid format for email.";
		//Ensure that email is registered
		} else {
			$con = dbf_user_connect();
			$stmt = $con->prepare('SELECT user_id FROM users WHERE email=?;');
			$stmt->bind_param('s', $f_email);
			$stmt->execute();
			$stmt->bind_result($r_user_id);
			$stmt->fetch();
			$stmt->close();
			
			//We don't tell them if it doesn't exist to make things harder for hackers
			if (empty($r_user_id))
				$is_valid = false;
			
			$con->close();
		}
			
		if ($is_valid) {
			require("$root/libraries/PHPMailer_5.2.0/class.phpmailer.php");
			
			umf_remove_password_retrieval($r_user_id); //Delete any active requests, if any
			sesf_end_session(session_id());
			sesf_create_session(null);		
			umf_prepare_password_retrieval($r_user_id);
			
			$_SESSION['retrieve_password_stage'] = 2; //Indicate now they can move to next form
			$f_email = '';
		}
	} else if ($_SESSION['retrieve_password_stage'] === 3) {
		$password = $_POST["r_password"];
		$password2 = $_POST["r_password2"];	
		
		if (empty($password)) {
			$password_error = "Password is required";
			$password = "";
			$is_valid = false;
		} else if (!preg_match("/^[a-zA-Z0-9]*$/",$password)) {
			$password_error = "Only letters and numbers are permitted for the password";
			$password = "";
			$is_valid = false;
		}
		
		if (empty($password2)) {
			$password2_error = "Please Confirm your Password";
			$password2 = "";
			$is_valid = false;
		} else if (strcmp($password, $password2) != 0) {
			$password2_error = "There is a mismatch with the passwords";
			$password2 = '';
			$is_valid = false;
		}
		
		if ($is_valid) {
			//First ensure they didnt hack the client side, check db
			$salt = openssl_random_pseudo_bytes(cPASSWORD_SALT_LENGTH);
			$password_hash = secf_password_hash($password, $salt);
			
			$con = dbf_user_connect();
			$stmt = $con->prepare('SELECT user_id,submitted FROM password_retrieval WHERE session_id=?;');
			$stmt->bind_param('s', $session_id);
			$stmt->execute();
			$stmt->bind_result($r_user_id, $submitted);
			$stmt->fetch();
			$stmt->close();
			
			//When indeed they can change their password
			if ($submitted) {
				$stmt = $con->prepare('UPDATE passwords SET password_hashed=?,salt=? WHERE user_id=?;');
				$stmt->bind_param('ssi', $password_hash, $salt, $r_user_id);
				$stmt->execute();
				$stmt->close();
				
				$_SESSION['retrieve_password_stage'] = 1;
			}
			
			$con->close();
			
			$password = $password2 = '';
		}
	}
} else if ( ($_SERVER["REQUEST_METHOD"] === "GET") && ($_SESSION['retrieve_password_stage'] ===2) ) {
	$code = $_GET['r_code'];

	//Fetch information
	$con = dbf_user_connect();
	$stmt = $con->prepare('SELECT user_id,attempts,expiry,retrieval_hash,salt FROM password_retrieval WHERE session_id=?;');
	$stmt->bind_param('s', $session_id);
	$stmt->execute();
	$stmt->bind_result($r_user_id, $attempts, $expiry, $retrieval_hash, $salt);
	$stmt->fetch();
	$stmt->close();
	$con->close();


	//Invalid session for the code or does not exist
	if (empty($r_user_id)) {
		$session_id = session_id();
		$submit_msg = 'Invalid session. This may also be possible if you changed internet networks or cleared your cache.';
		$_SESSION['retrieve_password_stage'] = 1; //Make first form appear
	
	//When there is a row in the database
	} else {
		$is_correct = false; //When the code entered by the user matches their session
		$expiry = dcf_strtounixepoch ($expiry);
		$attempts++;
		
		//Check if expired, if so delete
		if ((time() - $expiry) > 0) {
			$submit_msg = 'The code has expired. You can make another reset request in order to retrieve a new code.';
			umf_remove_password_retrieval($r_user_id);
		}
		
		//Check if it matches with database value
		$try_retrieval_hash = secf_password_reset_hash($code, $salt);
		
		//When they are different
		if (strcmp($try_retrieval_hash, $retrieval_hash)) {
			$submit_msg = 'The code does not match the one provided';
			
			//Ensure they dont do too many attempts
			$removed = umf_update_password_retrieval_attempts($r_user_id);
			if ($removed) {
				$submit_msg = 'You have made three unsuccessful attempts entering the code. You make another reset request to retrieve a new code.';
				sesf_end_session(session_id());
				sesf_create_session(null);
				$_SESSION['retrieve_password_stage'] = 1; //Make first form appear
			} else {
				$submit_msg = 'The code you have entered is invalid, please try again.';
			}
		//When they are the same
		} else {		
			$con = dbf_user_connect();
			$stmt = $con->prepare('UPDATE password_retrieval SET submitted=1 WHERE session_id=?;');
			$stmt->bind_param('s', $session_id);
			$stmt->execute();
			$stmt->close();
			$con->close();
			
			$_SESSION['retrieve_password_stage'] = 3; //Make new password form appear
		}
	}
}
?>

<!DOCTYPE HTML>
<HTML>

<HEAD>
<meta charset="UTF-8">
<link rel="shortcut icon" href="http://diplomatic-war.omarabdelbari.com/favicon.ico" type="image/x-icon"/>
<title>SUPPORT - DIPLOMATIC WAR</title>

<STYLE>
A {
	text-decoration:none;
	display:block;
}

SELECT {
	border-style:solid;
	border-radius:7px;
	border-color:#230e00;
	border-width:2px;
	width:75%;
	font-size:1.7vh;
	text-align:center;
	padding:1% 1%;
	display:inline-block;
	float:left;
	background-color:#ffc9b6;
	color:#230e00;
}

INPUT, TEXTAREA {
	border-color:#230e00;
	font-size:1.7vh;
	padding:2% 2%;
	border-radius:15px;
	margin-bottom:2%;
	background-color:#ffc9b6;
	border-style:solid;
}

INPUT[type=text], TEXTAREA {
	border-style:solid;
	width:96%;
	color:#230e00;
}

INPUT[type=file] {
	border-style:none;
	background-color:#c6a486;
}

TEXTAREA {
	border-width:2px;
	resize:none;
}

TEXTAREA:focus, INPUT:focus, SELECT:focus {
	outline:0;
}

INPUT[type=password] {
	width:96%;
	color:#230e00;
}

INPUT[type=submit] {
	cursor:pointer;
	border-radius:7px;
	/*background-color:#251000;*/
	background-image: url("http://diplomatic-war.omarabdelbari.com/Graphics/textures/Bg_Texture___wood_by_nortago.jpg");
	border-color:#ffa35f;
	color:#ffa35f;
}

LABEL {
	margin-top:1%;
	display:inline-block;
	font-size:2vh;
	clear:left;
	float:left;
	margin-left:2%;
}

.form-error {
	color:#0000ff;
}

.select-label {
	width:20%;
}

#recaptcha_challenge_image-holder {
	width:310px;
	text-align:center;
	margin-left:auto;
	margin-right:auto;
}

#category_div {
	width:100%;
}

#bug_category {
	width:96%;
}

#container {
	margin:2% auto;
	width:950px;
	padding:1%;
	color:#330000;
	border-radius:7px;
	background-image: url("http://diplomatic-war.omarabdelbari.com/Graphics/textures/Wood_texture_by_shadowh3.jpg");
}

#body-box {
	margin:0 auto;
	width:94%;
	border-style:none;
	border-radius:7px;
	background-color:#c6a486;
	padding:2%;
	color:#330000;
}

#body-box a {
	display:inline;
	font-weight:500;
}

#body-box a:link {
	color:#aa0000;
	text-shadow:0px 0px #000000;
}

#body-box a:visited {
	color:#aa0000;
	text-shadow:0px 0px #000000;
}

#body-box a:hover {
	color:#ff0000;
	text-shadow:0px 0px #000000;
}

#body-box h1 {
	font-size:28px;
	text-align:center;
	display:block;
	margin:0 auto;
}
</STYLE>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</HEAD>

<BODY>

<div id="container"><div id="body-box">
	<H1>Retrieve Password</H1>
	<p>This is the temporary look for the support page.</p>
	<span id="submit-message" class="form-error"><?php echo $submit_msg; ?></span>
<?php
if ($_SESSION['retrieve_password_stage'] === 1) {
	echo '<form method="post" action="' . htmlspecialchars('http://diplomatic-war.omarabdelbari.com/support/retrieve-password.php') . '">
		<p>Please enter the email address associated with the account with which you wish to retrieve a password.
		An email will be sent to this email address with instructions to reset your password.</p>
		
		<label for="r_email">Email <span id="email_error" class="form-error">' . $email_error . '</span></label>
		<input id="r_email" name="r_email" maxlength="255" type="text" placeholder="Enter the email associated with your account">
		<br>
		
		<input id="submit" type="submit" name="submit" value="Reset Password">
	</form>';
} else if ($_SESSION['retrieve_password_stage'] === 2) {
	echo '<p>If the email you entered was registered, an email was sent to that email with instructions to reset your password
	It may take a few minutes for the email to arrive.</p>
	<form method="get" action="' . htmlspecialchars('http://diplomatic-war.omarabdelbari.com/support/retrieve-password.php') . '">
		<label for="r_code">Code <span id="code_error" class="form-error">' . $code_error . '</span></label>
		<input id="r_code" name="r_code" maxlength="' . cPASSWORD_RETRIEVAL_CODE_LENGTH . '" type="text" placeholder="Enter the code provided in the email we sent you">
		<br>
		
		<input id="submit" type="submit" name="submit" value="Submit Code">
	</form>';
} else if ($_SESSION['retrieve_password_stage'] === 3) {
	echo '<p>The code you entered is valid. Please enter your new password.</p>
	<form method="post" action="' . htmlspecialchars('http://diplomatic-war.omarabdelbari.com/support/retrieve-password.php') . '">
		<label for="r_password">Password <span id="password_error" class="form-error">' . $password_error . '</span></label>
		<input id="r_password" name="r_password" type="password" maxlength="' . cMAXIMUM_PASSWORD_LENGTH . '" placeholder="Maximum Length:26. Accepted Characters:0-9, a-z, A-Z.">
		<br>
		
		<label for="r_password2">Confirm Password <span id="password2_error" class="form-error">' . $password2_error . '</span></label>
		<input id="r_password2" name="r_password2" type="password" maxlength="' . cMAXIMUM_PASSWORD_LENGTH . '" placeholder="Confirm your password">
		<br>
		
		<input id="submit" type="submit" name="submit" value="Apply New Password">
	</form>';
}
?>
</div></div>
</BODY>

</HTML>
