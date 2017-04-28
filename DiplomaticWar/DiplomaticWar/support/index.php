<?php
require 'template1_imports.php';
require "$root/template1_header_operations.php";
require "$root/libraries/recaptchalib.php";
?>

<!DOCTYPE HTML>
<HTML>

<HEAD>
<meta charset="UTF-8">
<link rel="shortcut icon" href="http://diplomatic-war.omarabdelbari.com/favicon.ico" type="image/x-icon"/>
<title>SUPPORT - DIPLOMATIC WAR</title>

<STYLE>
BODY {
}

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
	<p>This is the temporary look for the support page.</p>
	<a href="http://diplomatic-war.omarabdelbari.com/support/retrieve-password.php">Retrieve Password</a>
	<br>
	<a href="http://diplomatic-war.omarabdelbari.com/support/faq.php">Frequently Asked Questions (FAQ)</a>
</div></div>
</BODY>

</HTML>
