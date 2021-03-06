<?php
/*
Connect to diplomatic war game database
Requires manual close using mysqli_close;
Returns object (non-zero) if successful and 0 otherwise.
*/
function dbf_dw_connect() {
	$con = new mysqli("localhost", "diplomaticuser", "yolo#horses123", "diplomatic_war");
	if ($con->connect_error) {
		die('Failed to connect to users Database. No.' . $con->errno . ' '. $con->error);
		error_log('Failed to connect to users Database. No.' . $con->errno . ' '. $con->error);
		return 0;
	} else if (!$con) {
		die("Connection error: " . mysqli_connect_error());
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
	$con = new mysqli("localhost", "diplomaticuser", "yolo#horses123", "diplomatic_war");
	if ($con->connect_error) {
                die('Failed to connect to users Database. No.' . $con->errno . ' '. $con->error);
                error_log('Failed to connect to users Database. No.' . $con->errno . ' '. $con->error);
                return 0;
        } else if (!$con) {
                die("Connection error: " . mysqli_connect_error());
        }  else {
		//mysqli_ssl_set($con,"key.pem","cert.pem","cacert.pem",NULL,NULL);
		return $con;
	} 
}
?>
