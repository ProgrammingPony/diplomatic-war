<?php 
require ('template1_imports.php');
require ('template1_header_operations.php');
require('libraries/recaptchalib.php');
sesf_redirect_if_logged_in($user_id);

$firstName_error = $lastName_error = $email_error = $password2_error = $password_error = $recaptcha_error = $birthDate_error = $displayName_error = "";
$validation_success = -1; //-1 =unset, >0 =not successful 0 =successful
$f_name = $l_name = $email = $display_name = ""; 

if (($_SERVER["REQUEST_METHOD"] == "POST")) {
	
	$f_name = $_POST["register_firstName"];
	$l_name = $_POST["register_lastName"];
	$email = $_POST["register_email"];
	$display_name = $_POST["register_displayName"];
	$birth_y = intval($_POST["register_birthY"]);
	$birth_m = intval($_POST["register_birthM"]);
	$birth_d = intval($_POST["register_birthD"]);					
	$password = $_POST["register_password"];
	$password2 = $_POST["register_password2"];	
		
	//Prevent error from arrising if no input in recaptcha field
	if (!isset($_POST["recaptcha_response_field"])) {
		$recaptcha_error = "Please complete the recaptcha challenge";
		$resp->is_valid = false;
	} else {
		$privatekey = '6Lde-fUSAAAAAAfvCM3Zqjqfy-K-_oc-87YFjHHZ';
		$resp = recaptcha_check_answer ($privatekey,
			$_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"],
			$_POST["recaptcha_response_field"]);
	}
	
	if (!$resp->is_valid) {
		$recaptcha_error = "The user input in the recaptcha field is either empty or not valid.";
	} else {
		$validation_success = 0;

		if (empty($f_name)) {
			$firstName_error = "First name is required";
			$validation_success = 1;
		} else if (!preg_match("/^[a-zA-Z -]*$/",$f_name)) {
			$firstName_error = "Only a-z, A-Z, spaces, hyphens are accepted characters for this field";
			$validation_success = 1;				
		}
		
		if (empty($l_name)) {
			$lastName_error = "Last name is required";
			$validation_success = 1;
		} else if (!preg_match("/^[a-zA-Z -]*$/",$l_name)) {
			$lastName_error = "Only a-z, A-Z, spaces, hyphens are accepted characters for this field";
			$validation_success = 1;
		}
		
		if (empty($email)) {
			$email_error = "Email is required";
			$validation_success = 1;
		} else if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
			$email_error = "Invalid format for email.";
			$validation_success = 1;			
		//Ensure that email is not already in database
		} else {
			if (umf_email_duplicate($email)) {				
				$email_error = $email . " is already in use. Try another email.";
				$validation_success = 1;
			}
		}
		
		if (empty($display_name)) {
			$displayName_error = "Display name is required";
			$validation_success = false;
		} else if (!preg_match("/^[a-zA-Z0-9]*$/",$display_name)) {
			$displayName_error = "Only a-z, A-Z, and 0-9 are accepted characters for this field";
			$validation_success = false;			
		//Ensure display name is not already in use
		} else {			
			if (umf_display_name_duplicate($display_name)) {
				$displayName_error = $display_name . " is already in use. Try another display name.";
				$validation_success = 1;
			}
		}
		
		if (empty($birth_y) || ($birth_y == 0)) {
			$birthDate_error = "Please Enter a Year";
			$validation_success = 1;
		}
		if (empty($birth_m) || ($birth_m == 0)) {
			if ($birthDate_error == "") {
				$birthDate_error = "Please Enter a Month.";
			} else {
				$birthDate_error = $birthDate_error . ", Month";
			}
			$validation_success = 1;
		}
		if (empty($birth_d) || ($birth_d ==0)) {
			if ($birthDate_error == "") {
				$birthDate_error = "Please Enter a Date";
			} else {
				$birthDate_error = $birthDate_error . ", Date";
			}
			$validation_success = 1;
		}
		
		if (empty($password)) {
			$password_error = "Password is required";
			$password = "";
			$validation_success = 1;
		} else if (!preg_match("/^[a-zA-Z0-9]*$/",$password)) {
			$password_error = "Only letters and numbers are permitted for the password";
			$password = "";
			$validation_success = 1;
		}
		
		if (empty($password2)) {
			$password2_error = "Please Confirm your Password";
			$password2 = "";
			$validation_success = 1;
		} else if (strcmp($password, $password2) != 0) {
			$password2_error = "There is a mismatch with the passwords";
			$password2 = "";
			$validation_success = 1;
		}
		
		//When validation is successful setup new user
		if (!$validation_success) {
			require('libraries/PHPMailer_5.2.0/class.phpmailer.php');
		
			$user_id = umf_add_new_user ($f_name, $l_name, $_POST["register_birthM"] . $_POST["register_birthD"] . $_POST["register_birthY"], $display_name, $email, $password);
			
			if (!$user_id)
				error_log("@Register Page: user_id is 0");
			
			sesf_end_session(session_id()); //Remove old guest session
			sesf_create_session ($user_id);	//Login user		
			umf_prepare_verification($user_id, $email); //Setup verification process for user
			umf_update_last_login ($user_id);
			
			header("Location: https://diplomatic-war.com/verify.php");
			exit;
		}
	}
}
	
require('template1_part1.php');
?>
<title>REGISTER - DIPLOMATIC WAR</title>

<?php require('template1_part2.php'); ?>

<H1>Register</H1>

<form method="post" action="<?php echo htmlspecialchars('https://diplomatic-war.com/register.php');?>">
	<label for="register_firstName">First Name <span id="firstName_error" class="form-error">* <?php echo $firstName_error;?></span></label>
	<input id="register_firstName" name="register_firstName" type="text" maxlength="70" placeholder="Maximum Length:70. Accepted Characters:a-z, A-Z." value="<?php echo $f_name;?>">
	<br>
	
	<label for="register_lastName">Last Name <span id="lastName_error" class="form-error">* <?php echo $lastName_error;?></span></label>
	<input id="register_lastName" name="register_lastName" type="text" maxlength="70" placeholder="Maximum Length:70. Accepted Characters:a-z, A-Z." value="<?php echo $l_name;?>">
	<br>
	
	<label for="register_email">Email <span id="email_error" class="form-error">* <?php echo $email_error;?></span></label>
	<input id="register_email" name="register_email" maxlength="255" type="text" placeholder="Enter a valid email address, it will be used for login and recovery." value="<?php echo $email;?>">
	<br>
	
	<label for="register_displayName">Display Name <span id="displayName_error" class="form-error">* <?php echo $displayName_error;?></span></label>
	<input id="register_displayName" name="register_displayName" type="text" maxlength="16" placeholder="Maximum Length:16. Accepted Characters:0-9, a-z, A-Z." value="<?php echo $display_name;?>">
	
	<label for="register_birth">Time of Birth: <span id="birthDate_error" class="form-error">* <?php echo $birthDate_error;?></span></label>
	<div id="register_birth">
		<label for="register_birthY" class="select-label">Year</label>
		<select id="register_birthY" name="register_birthY" >
			<option id="birthY_def" value="">Please Select a Year</option>
		</select>
		
		<label for="register_birthM" class="select-label">Month</label>
		<select id="register_birthM" name="register_birthM">
			<option id="birthM_def" value="">Please Select a Month</option>
			<option value="01">January</option>
			<option value="02">February</option>
			<option value="03">March</option>
			<option value="04">April</option>
			<option value="05">May</option>
			<option value="06">June</option>
			<option value="07">July</option>
			<option value="08">August</option>
			<option value="09">September</option>
			<option value="10">October</option>
			<option value="11">November</option>
			<option value="12">December</option>
		</select>
		
		<label for="register_birthD" class="select-label">Date</label>
		<select id="register_birthD" name="register_birthD">
			<option value="">Please select a month first to view options</option>
		</select>
	</div>
	<br>
	
	<label for="register_password">Password <span id="password_error" class="form-error">* <?php echo $password_error;?></span></label>
	<input id="register_password" name="register_password" type="password" maxlength="26" placeholder="Maximum Length:26. Accepted Characters:0-9, a-z, A-Z.">
	<br>
	
	<label for="register_password2">Confirm Password <span id="password2_error" class="form-error">* <?php echo $password2_error;?></span></label>
	<input id="register_password2" name="register_password2" type="password" maxlength="26" placeholder="Confirm your password">
	<br>
	
	<div id="recaptcha_challenge_image-holder">
<?php
$public_key = '6Lde-fUSAAAAAJ1Eoe1y7D65eqrwXcg0KNsaiOyT';
echo recaptcha_get_html($public_key);
?>
	</div>
	<br>
	<span class="form-error"><?php echo $recaptcha_error; ?></span>
	<br>

	<input id="submit" type="submit" name="submit" value="Register">
</form>

<script>
	//Reference:http://stackoverflow.com/questions/5620996/is-there-a-jquery-dropdown-year-selector
	//Year Selector
	for (i = new Date().getFullYear(); i > 1900; i--)
	{
		$('#register_birthY').append($('<option />').val(i).html(i));
	}
	
	//Day Selector (does not account for leap years; gives february 29 every year)
	//TODO: BUG WITH DISABLING DATE, ENABLING IT IS NOT WORKING INSIDE FUNCTION
	//$('#register_birthD').prop('disabled', true); //false activator in change function
	
	
	function updateDateSelector() {
		var selected_month = $('#register_birthM').val();
		
		$('#register_birthD').empty();
		
		switch(selected_month) {
			case "01": case "03": case "05": case "07": case "08": case "10": case "12":
				appendDates(31);
				break;
			case "04": case "06": case "09": case "11":
				appendDates(30);
				break;
			case "02":
				appendDates(29);
				break;
		}
	}
	
	function appendDates (max_date) {
		for (var i=1; i<=max_date; i++) {
			if (i < 10)
				$('#register_birthD').append($('<option />').val("0" + i).html(i));
			else
				$('#register_birthD').append($('<option />').val(i).html(i));
		}
	}
	
	//Remove defaults from Birth Date fields when changed
	$("#register_birthM").change(function(){
		//$('#register_birthD').prop('disabled', false);
		$("#register_birthM").children("#birthM_def").remove();
		updateDateSelector();
	});
	
	$("#register_birthY").change(function(){
		
		$("#register_birthM").children("#birthY_def").remove();
	});
	
</script>
<?php require('template1_part3.php'); ?>