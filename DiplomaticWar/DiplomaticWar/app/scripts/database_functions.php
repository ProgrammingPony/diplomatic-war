<?php
/*
Connect to diplomatic war game database
Requires manual close using mysqli_close;
Returns object (non-zero) if successful and 0 otherwise.
*/
function dbf_dw_connect() {
	$con = new mysqli("localhost", "root", "", "diplomatic_war");
	if ($con->connect_error) {
		error_log('Failed to connect to users Database. No.' . $con->errno . ' '. $con->error);
		return 0;
	} else {
		//mysqli_ssl_set($con,"key.pem","cert.pem","cacert.pem",NULL,NULL);
		return $con;
	} 
}

/*Connect to user database
Returns object (non-zero) if successful and 0 otherwise.
Requires manual close using mysqli_close;
*/
function dbf_user_connect() {
	$con = new mysqli("localhost", "root", "", "users");
	if ($con->connect_error) {
		error_log('Failed to connect to users Database. No.' . $con->errno . ' '. mysqli_connect_error());
		return 0;
	} else {
		//mysqli_ssl_set($con,"key.pem","cert.pem","cacert.pem",NULL,NULL);
		return $con;
	} 
}
?>