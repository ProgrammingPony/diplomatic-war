<?php
require ('template1_imports.php');
sesf_end_session(session_id());
sesf_create_session(null);
header("Location: http://diplomatic-war.com/index.php");
exit;
?>
