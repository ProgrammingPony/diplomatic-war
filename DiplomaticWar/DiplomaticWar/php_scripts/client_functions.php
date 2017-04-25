<?php
// Function to get the client ip address
// Reference: http://techtalk.virendrachandak.com/getting-real-client-ip-address-in-php-2/
function clif_get_client_ip() {
    $ipaddress = '';

    else if($_SERVER['REMOTE_ADDR'])*/
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
}

//Reference(modified): http://php.net/manual/en/function.get-browser.php#101125
function clif_get_browser() {
	$u_agent = $_SERVER['HTTP_USER_AGENT'];

	if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) { 
		return 'Internet Explorer';
	} elseif(preg_match('/Firefox/i',$u_agent)) { 
		return 'Mozilla Firefox';
	} elseif(preg_match('/Chrome/i',$u_agent)) { 
		return 'Google Chrome';
	} elseif(preg_match('/Safari/i',$u_agent)) { 
		return 'Apple Safari';
	} elseif(preg_match('/Opera/i',$u_agent)) { 
		return 'Opera';
	} elseif(preg_match('/Netscape/i',$u_agent)) { 
		return 'Netscape';
	} else {
		return 'Other';
	}
}

//Reference(modified): http://php.net/manual/en/function.get-browser.php#101125
function clif_get_os() {
	$platform = 'Unknown';
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	
	if (preg_match('/linux/i', $u_agent)) {
		$platform = 'linux';
	} elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		$platform = 'macintosh';
	} elseif (preg_match('/windows|win32/i', $u_agent)) {
		$platform = 'windows';
	} else {
		$platform = 'other';
	}
}
?>