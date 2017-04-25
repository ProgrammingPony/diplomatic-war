<?php
echo '
<!DOCTYPE HTML>
<HTML>

<HEAD>
<!--General-->
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<meta charset="utf-8"/>
<meta name="robots" content="noindex, nofollow">
<meta name="author" content="Omar Abdel Bari">

<!--General Stylesheet-->
<style>
IMG {
	border-width:0;
}

#main-container {
	margin:2% auto;
	width:1000px;
}

A {
	text-decoration:none;
	display:block;
}

.container {
	margin-top:2vh;
}

/*TOP LINKS*/
#top-container {
	background-color:#251000;
	text-align:center;
	width:100%;
	padding:0.5vw 0;
	clear:both;
	float:left;
}

#top-container a {
	display:inline-block;
	color:#ffa35f;
	padding:0 1.5vw;
}

#top-container a:hover {
	color:#ffffff;
}

/*LEFT CONTAINER*/
#left-container {
	float:left;
	clear:left;
	width:25%;
	text-align;center;
}

/*To Centre Images*/
.left-container-parent {
	width:100%;
	text-align:center;
	padding:0;
	display:inline-block;
	margin-top:2vh;
}

/*Constrain Image dimensions*/
#left-container img {
	max-width:250px;
	height:auto;
}

#logo {
	box-shadow:1vh 1vh 0.5vh #666666;
}

#login-icon-container {
}

#login-image {
	border-radius:7px;
	border-style:none;
	box-shadow:1vh 1vh 0.5vh #666666;
}

#register-image {
	border-radius:7px;
	border-style:none;
	box-shadow:1vh 1vh 0.5vh #666666;
}

/*LOGIN INFORMATION CONTAINER*/
#login-information-holder {
	width:96%;
	padding:1vh 2%;
	background-image: url("Graphics/textures/Wood_texture_by_shadowh3.jpg");
	border-radius:7px;
	border-style:none;
	box-shadow:1vh 1vh 0.5vh #666666;
}

#login-information-body {
	border-radius:7px;
	border-style:none;
	width:80%;
	padding:0.5vh 5%;
	margin:0 auto;
	text-align:left;
	background-color:#c6a486;
}

#login-information-body SPAN {
	display:inline-block;
}

.login-label {
	clear:left;
	float:left;
	font-weight:700;
	text-transform:uppercase;
}

.login-value {
	clear:right;
	float:left;
	margin-left:2%;
}

#login-information-body a {
	color:#550000;
	text-transform:uppercase;
}

#login-information-body a:hover {
	color:#ff0000;
}

/*LEFT CONTAINER LINKS*/
#left-container-linklist {
	width:100%;
	background-image: url("Graphics/textures/Wood_texture_by_shadowh3.jpg");
	text-align:center;
	padding:1vh 0;
	box-shadow:1vh 1vh 0.5vh #666666;
	border-radius:7px;
	border-style:none;
	text-transform:uppercase;
}

#left-container-linklist a {
	width:90%;
	margin:0.5vh 0;
	padding-top:1vh;
	padding-bottom:1vh;
	background-image: url("Graphics/textures/Bg_Texture___wood_by_nortago.jpg");
	display:inline-block;
	text-decoration:none;
	border-radius:7px;
	border-style:none;
}

#left-container-linklist a:link {
	color:#ff3300;
	font-weight:500;
	text-shadow:0px 0px #ff9966;
}

#left-container-linklist a:visited {
	color:#ff3300;
	font-weight:500;
	text-shadow:0px 0px #ff9966;
}

#left-container-linklist a:hover {
	color:#aa0000 !important;
	font-weight:700;
	text-shadow:1px 1px #ff0000;
}

#left-container-linklist a:active {
	color:#aa0000 !important;
	font-weight:700;
	text-shadow:1px 1px #ff0000;
}

#startcom-icon {
	box-shadow:1vh 1vh 0.5vh #666666;
}

#right-container {
	float:right;
	clear:right;
	width:66%;
	margin-right:2vw;
}

/*Announcement Slides*/
#top-slide-container {
	width:100%;
	height:auto;
	border-radius:7px;
	border-style:none;
	clear:both;
	float:left;
	background-image: url("Graphics/textures/Wood_texture_by_shadowh3.jpg");
	padding:1vh 1%;
	box-shadow: 1vw 1vh 0.5vh #666666;
	margin-top:2vh;
}

#top-slide-body {
	width:98%;
	height:auto;
	margin:0 auto;
	border-radius:7px;
	border-style:none;
	text-align:center;
}

#top-slide-navbar {
	width:100%;
	clear:both;
	float:left;
	text-align:center;
	border-radius:7px 7px 0 0;
	border-style:none;
}

.top-slide-navbutton-active {
	max-height:35px;
	width:auto;
	margin:0 2%;
	float:left;
}

.top-slide-navbutton-inactive {
	max-height:35px;
	width:auto;
	margin:0 2%;
	float:left;
}

.top-slide-image {
	width:630px;/*.66*.96*1000px*/
	max-height:200px;
	border-radius:7px;
	border-style:none;
	margin:1vh auto;
}

#top-slide-imageholder {
	width:98%;
	border-radius:7px 7px;
	border-style:none solid solid solid;
	background-color:#ffd8b5;
	border-color:#c68144;
	clear:both;
	float:left;
	margin:0 auto;
}

/*EDITABLE BODY*/
#body-box-background {
	width:100%;
	background-image: url("Graphics/textures/Wood_texture_by_shadowh3.jpg");
	border-radius:7px;
	border-style:none;
	box-shadow:1vw 1vh 0.5vh #666666;
	margin-top:2vh;
	padding:1vh 1%;
	clear:both;
	float:left;
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

/*BODY BOX FONTS*/
#body-box p {
	text-shadow:0px 0px #000000;
}

#body-box ul {
	padding-left:1vw;
	list-style-type:none;
	text-shadow:0px 0px #000000;
}

#body-box ol {
	padding-left:2.1vw;
	text-shadow:0px 0px #000000;
}

#body-box li {
}

/*Title*/
#body-box h1 {
	font-size:28px;
	display:block;
	margin:1vh 0 0.8vh 0;
	text-align:center;
	text-shadow:0px 0px #000000;
}

/*Subtitle*/
#body-box h2 {
	color:#550000;
	font-size:24px;
	display:block;
	margin:0.1vh 0 0.8vh 0;
	text-align:center;
	text-shadow:0px 0px #000000;
}

/*Heading Lvl 1*/
#body-box h3 {
	font-size:20px;
	margin:0.6vh 0 0.8vh 0;
	text-shadow:0px 0px #000000;
}

/*Heading Lvl 2*/
#body-box h4 {
	color:#550000;
	font-size:16px;
	text-shadow:0px 0px #000000;
	margin:0.6vh 0 0.8vh 0;
}

/*Heading Lvl 3*/
#body-box h5 {
	color:#770000;
	text-decoration:underline;
	font-size:16px;
	text-shadow:0px 0px #000000;
	margin:0.6vh 0 0.8vh 0;
}

/*Links*/
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

/*BOTTOM LINKS*/
#bottom-container {
	background-color:#251000;
	text-align:center;
	width:100%;
	color:#ffa35f;
	float:left;
}

#bottom-container a {
	display:inline-block;
	color:#ffa35f;
	padding:0 1.5vw;
	margin:1vh 0;
}

#bottom-container a:hover {
	color:#ffffff;
}

.form-error {
	color:#0000ff;
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
	border-color:#ffa35f;
	/*background-color:#251000;*/
	color:#ffa35f;
	background-image: url("Graphics/textures/Bg_Texture___wood_by_nortago.jpg");
}

LABEL {
	margin-top:1%;
	display:inline-block;
	font-size:2vh;
	clear:left;
	float:left;
	margin-left:2%;
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

</style>

<!--Mobile Redirect-->
<script> //if (screen.width < 800) {window.location="";} </script>

<!--Imported Scripts-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
'; ?>