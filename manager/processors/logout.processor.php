<?php
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
session_destroy();
$sessionID = md5(date('d-m-Y H:i:s'));
session_id($sessionID);
session_start();
session_destroy();
header("Location: ./");
?>