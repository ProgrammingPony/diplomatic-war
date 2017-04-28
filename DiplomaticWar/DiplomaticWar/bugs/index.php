<?php
define('cMAX_FILE_SIZE', 5242880);
//Reference: http://stackoverflow.com/questions/4369/how-to-include-php-files-that-require-an-absolute-path
require 'template1_imports.php';
require "$root/template1_header_operations.php";
require "$root/libraries/recaptchalib.php";

$email_error = $title_error = $categ_error = $desc_error = $attach_error = $recaptcha_error = '';
$b_email = $title = $categ = $desc = $recaptcha = '';
$submit_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$is_valid = true;
	
	$b_email = $_POST['bug_email'];
	$title = $_POST['bug_title'];
	$categ = $_POST['bug_category'];
	$desc = $_POST['bug_description'];
	
	if (isset($_FILES['file']['size'])) {
		$allowed_exts = array('gif', 'jpeg', 'jpg', 'png');
		$file_ext = explode('.', $_FILES['file']['name']);
		$allowed_file_types = array('image/gif', 'image/jpeg', 'image/jpg', 'image/png');
		$file_ext = end($file_ext);
	}
	
	//Recaptcha validation
	$privatekey = '6Lde-fUSAAAAAAfvCM3Zqjqfy-K-_oc-87YFjHHZ';
	$resp = recaptcha_check_answer ($privatekey,
		$_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"],
		$_POST["recaptcha_response_field"]);
			
	if ($resp->is_valid) {
		//Email
		if (empty($b_email)) {
			$email_error = "Email is required";
			$is_valid = false;
		} else if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $b_email)) {
			$email_error = "Invalid format for email.";
			$is_valid = false;
		}
		
		//Title
		if (empty($title)) {
			$title_error = "Title is required";
			$is_valid = false;
		}
		
		//Category
		if (empty($categ)) {
			$categ_error = "Category is required";
			$is_valid = false;
		}
		
		//Description
		if (empty($desc)) {
			$desc_error = "Description is required";
			$is_valid = false;
		}

		//Validate file
		if (isset($_FILES['file']['size'])) {
			if ($_FILES['file']['size'] > cMAX_FILE_SIZE) {
				$is_valid = false;
				$attach_error = 'The file size is too big';
			} else if (!in_array($_FILES['file']['type'], $allowed_file_types)) {
				$is_valid = false;
				$attach_error = 'Invalid file type';		
			} else if (!in_array($file_ext, $allowed_exts)) {
				$is_valid = false;
				$attach_error = 'Invalid extension';
			}
		}
	
		//When no validation errors
		if ($is_valid) {
			//Reference: http://www.inmotionhosting.com/support/email/send-email-from-a-page/using-phpmailer-to-send-mail-through-php
			require("http://diplomatic-war.omarabdelbari.com/libraries/PHPMailer_5.2.0/class.phpmailer.php");

			$mail = new PHPMailer();

			$mail->IsSMTP();
			$mail->Host = 'p3plcpnl0425.prod.phx3.secureserver.net';
			$mail->SMTPAuth = true;
			$mail->Username = "dolphinlover1";
			$mail->Password = "135531Ab";

			$mail->From = 'bugs@diplomatic-war.com';
			$mail->FromName = "Bugs";
			$mail->AddAddress('bugs@diplomatic-war.com', 'Bugs');
			$mail->AddReplyTo($b_email);

			$mail->WordWrap = 70;
			if (isset($_FILES['file']['size'])) {
				$mail->AddAttachment($_FILES['file']['tmp_name'], 'attachment.' . $file_ext);
			}
			$mail->IsHTML(true);

			$mail->Subject = 'Submitted Bug';
			$mail->Body    = "$title<br>$categ<br>$desc<br>";
			$mail->AltBody = "$title ~~ $categ ~~ $desc";

			if($mail->Send())
				$submit_msg = 'Your report has been sent';
			else
				$submit_msg = 'index.php, email could not be sent. Mailer Error: ' . $mail->ErrorInfo;

			$b_email = $title = $desc = '';
		}
	} else {
		$recaptcha_error = 'Invalid entry in the recaptcha field';
		$is_valid = false;
	}
}

?>

<!DOCTYPE HTML>
<HTML>

<HEAD>
<meta charset="UTF-8">
<link rel="shortcut icon" href="http://diplomatic-war.omarabdelbari.com/favicon.ico" type="image/x-icon"/>
<title>BUGS - DIPLOMATIC WAR</title>

<STYLE>
BODY {
	background-image: url("http://diplomatic-war.omarabdelbari.com/Graphics/textures/Wood_texture_by_shadowh3.jpg");
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
	width:1000px;
	padding:2%;
	background-color:#c6a486;
	color:#330000;
	border-radius:7px;
}
</STYLE>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</HEAD>

<BODY>

<div id="container">
	<p>This is the temporary look for the bug reporting centre.</p>
	<p>For now we will accept emails from non-members in case there is a problem with registration, 
	we will make it only available to members in the future.</p>

	<form method="post" action="<?php echo htmlspecialchars('http://diplomatic-war.omarabdelbari.com/bugs/index.php')?>" enctype="multipart/form-data">
		<p class="form-error">* marks required fields</p>
		<span id="submit-message" class="form-error"><?php echo $submit_msg; ?></span>
		<br>
		
		<label for="bug_email">Email <span id="email_error" class="form-error">* <?php echo $email_error;?></span></label>
		<input id="bug_email" name="bug_email" maxlength="255" type="text" placeholder="Enter a valid email address. This will be how we respond to you." value="<?php echo $b_email;?>">
		<br>

		<label for="bug_title">Title <span id="title_error" class="form-error">* <?php echo $title_error;?></span></label>
		<input id="bug_title" name="bug_title" maxlength="20" type="text" placeholder="A brief way of describing the bug." value="<?php echo $title;?>">
		<br>
		
		<label for="category_div">Category <span id="category_error" class="form-error">* <?php echo $categ_error;?></span></label>
		<div id="category_div">
			<select id="bug_category" name="bug_category">
				<option id="category_sel" value="">Please Select a Category</option>
				<option value="Registration">Registration</option>
				<option value="Verification">Verification</option>
				<option value="Webpage">Webpage (display, broken links, etc.)</option>
				<option value="Game Support">Game Support</option>
				<option value="Purchases">Purchases</option>
				<option value="Other">Other</option>
			</select>
		</div>
		<br>

		<label for="bug_description" class="select-label">Description <span id="description_error" class="form-error">* <?php echo $desc_error;?></span></label>
		<textarea id="bug_description" name="bug_description" maxlength="250" rows="12" cols="50" placeholder="Things to help us fix the bug. Describe the actions that led up to the bug what the unexpected/unwanted result was."><?php echo $desc; ?></textarea> 
		<br>
		
		<span>Attach an image showing the bug if available (.png, .jpg/.jpeg, .gif accepted) Maximum Size: <?php echo cMAX_FILE_SIZE/1048576;?>MB</span>
		<label for="file"></label>
		<input id="file" type="file" name="file">
		<br>
		<span id="file_error" class="form-error"><?php echo $attach_error;?></span>
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
		
		<input id="submit" type="submit" name="submit" value="Submit Bug">
	</form>
</div>

<SCRIPT>
//Remove placeholder value from category after user selects a value
$("#bug_category").change(function(){
	$("#bug_category").children("#category_sel").remove();
});
</SCRIPT>
</BODY>

</HTML>
