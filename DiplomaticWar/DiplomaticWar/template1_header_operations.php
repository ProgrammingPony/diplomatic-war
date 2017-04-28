<?php
//Resume session, or make a new one if not existant/expired
sesf_assert();

//Retrieve logged in user information, or set values to null if guest
$user_id = umf_get_user_id('session_id', session_id());

if ($user_id) {
	$user_arr = umf_get_user_info($user_id);
	$user_status = $user_arr['status'];
	
	//If user still needs to be verified
	if ( ($user_status == 1) && (strpos($_SERVER['REQUEST_URI'], 'verify.php') === false) && (strpos($_SERVER['REQUEST_URI'], 'bugs/') !== false)) {
		header('Location: http://diplomatic-war.omarabdelbari.com/verify.php' );
		exit();
	}
	
	$display_name = $user_arr['display_name'];	
	$user_type = $user_arr['user_type'];
} else {
	$user_status = '';
	$display_name = '';
	$user_type = '';
}
?>
