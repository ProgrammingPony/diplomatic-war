<?php
session_start();
$root = '/var/www/diplomatic-war/DiplomaticWar/DiplomaticWar';
$sub_root = '/var/www/diplomatic-war/DiplomaticWar/DiplomaticWar/support';

$root_script = $root . '/php_scripts';
//Redirect to HTTPS if they try http
#if ( !isset($_SERVER['HTTPS']) || (strtolower($_SERVER['HTTPS']) !== 'on') ) {
#	header('Location: https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
#	exit();
#}
date_default_timezone_set("America/Toronto");

require ("$root_script/database_functions.php");
require ("$root_script/session_functions.php");
require ("$root_script/user_management_functions.php");
require ("$root_script/data_functions.php");
require ("$root_script/security_functions.php");
require ("$root_script/client_functions.php");
?>
