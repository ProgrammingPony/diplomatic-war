<?php 
echo '
</HEAD>

<BODY>

	<div id="main-container">
		<div id="left-container" class="container">
			<!--LOGO HOLDER-->
			<div id="logo-container" class="left-container-parent">
				<a href="https://diplomatic-war.com/index.php"><img id="logo" alt="Logo" class="width-wide-image" src="Graphics/logo.jpg"></a>
			</div>';

if ($user_id) {
	//Retrieve Last Login Information
	$con = dbf_user_connect();
	$stmt = $con->prepare("SELECT prev_login_ip,prev_login_time FROM login_history WHERE user_id=?");
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->bind_result($last_login_ip, $last_login_time);
	$stmt->fetch();
	$stmt->close();
	$con->close();
	
	$user_status_str = umf_get_status_string($user_status);
	
	echo "
			<div id='login-information-holder' class='left-container-parent'>
				<div id='login-information-body'>
					<span class='login-label'>Name </span><span class='login-value'>$display_name</span>
					<br>
					<span class='login-label'>Status </span><span class='login-value'>$user_status_str</span>
					<br>
					<span class='login-label'>Previous Login</span>
					<br>
					<span class='login-label'>IP</span><span class='login-value'>$last_login_ip</span>
					<br>
					<span class='login-label'>Time</span><span class='login-value'>$last_login_time</span>
					<br>
					<a href='https://diplomatic-war.com/logout.php'>Logout</a>
				</div>
			</div>";
} else {
	echo '	<!--LOGIN HOLDER-->
			<div id="login-icon-container" class="left-container-parent">
				<a href="https://diplomatic-war.com/login.php"><img id="login-image" alt="Login Icon" class="width-wide-image" src="Graphics/LoginIcon.png"></a>
			</div>
			
			<!--REGISTER HOLDER-->
			<div class="left-container-parent">
				<a href="https://diplomatic-war.com/register.php"><img id="register-image" alt="Register Button" class="width-wide-image" src="Graphics/RegisterIcon.jpg"></a>
			</div>';
}

echo	'
			<!--LINK LIST-->
			<div id="left-container-linklist" class="left-container-parent">
				<a href="https://diplomatic-war.com/tour.php">The Tour</a>
				<a href="https://game-info.diplomatic-war.com">Game Manual</a>
				<a href="https://diplomatic-war.com/download.php">Download</a>
			</div>
		</div>
		
		<div id="right-container" class="container">

			<div id="top-slide-container">
				<div id="top-slide-body">
					<!--<div id="top-slide-navbar">-->
						<img id="top-slide-tab-0" alt="top-slide-tab-0" src="Graphics/SlideTabActive.png" style="clear:left;" class="top-slide-navbutton-active">
						<img id="top-slide-tab-1" alt="top-slide-tab-1" src="Graphics/SlideTabInactive.png" class="top-slide-navbutton-inactive">
						<img id="top-slide-tab-2" alt="top-slide-tab-2" src="Graphics/SlideTabInactive.png" class="top-slide-navbutton-inactive">
						<img id="top-slide-tab-3" alt="top-slide-tab-3" src="Graphics/SlideTabInactive.png" style="clear:right;" class="top-slide-navbutton-inactive">
					<!--</div>-->
					
					<div id="top-slide-imageholder">
						<a href="#"><img id="top-slide-image" alt="top-slide-image" src="Graphics/banners/banner-test.jpg" class="top-slide-image"></a>
					</div>
				</div>
			</div>

			<div id="body-box-background">
				<div id="body-box">';
?>