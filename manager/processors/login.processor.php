<?php
// set the include_once path
if(version_compare(phpversion(), "4.3.0")>=0) {
	set_include_path("../includes/"); // include path the new way
} else {
	ini_set("include_path", "../includes/"); // include path the old way
}

define("IN_ETOMITE_SYSTEM", "true"); 	// we use this to make sure files are accessed through
									// the manager instead of seperately.
// include the database configuration file
include_once "config.inc.php";

// connect to the database
if(@!$etomiteDBConn = mysql_connect($database_server, $database_user, $database_password)) {
	die("Failed to create the database connection!");
} else {
	mysql_select_db($dbase);
}

// get the settings from the database
include_once "settings.inc.php";

// include version info
include_once "version.inc.php";

// include the logger
include_once "log.class.inc.php";

// include the crypto thing
include_once "crypt.class.inc.php";


session_start();

// include_once the error handler
include_once "error.class.inc.php";
$e = new errorHandler;



$username = htmlspecialchars($_POST['username']);
$givenPassword = htmlspecialchars($_POST['password']);
$captcha_code = $_POST['captcha_code'];

$sql = "SELECT $dbase.".$table_prefix."manager_users.*, $dbase.".$table_prefix."user_attributes.* FROM $dbase.".$table_prefix."manager_users, $dbase.".$table_prefix."user_attributes WHERE $dbase.".$table_prefix."manager_users.username REGEXP BINARY '^".$username."$' and $dbase.".$table_prefix."user_attributes.internalKey=$dbase.".$table_prefix."manager_users.id;";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);

if($limit==0 || $limit>1) {
		$e->setError(900);
		$e->dumpError();
}	

$row = mysql_fetch_assoc($rs);
	
$internalKey 			= $row['internalKey'];
$dbasePassword 			= $row['password'];
$failedlogins 			= $row['failedlogincount'];
$blocked 				= $row['blocked'];
$blockeddate			= $row['blockeduntil'];
$registeredsessionid	= $row['sessionid'];
$role					= $row['role'];
$lastlogin				= $row['lastlogin'];
$nrlogins				= $row['logincount'];
$fullname				= $row['fullname'];
$sessionRegistered 		= checkSession();
$email 					= $row['email'];

if($failedlogins>=3 && $blockeddate>time()) {	// blocked due to number of login errors.
		session_destroy();
		session_unset();
		$e->setError(902);
		$e->dumpError();
}

if($failedlogins>=3 && $blockeddate<time()) {	// blocked due to number of login errors, but get to try again
	$sql = "UDPATE $dbase.".$table_prefix."user_attributes SET failedlogincount='0', blockeduntil='".(time()-1)."' where internalKey=$internalKey";
	$rs = mysql_query($sql);
}

if($blocked=="1") { // this user has been blocked by an admin, so no way he's loggin in!
	session_destroy();
	session_unset();
	$e->setError(903);
	$e->dumpError();
}

if($blockeddate>time()) { // this user has a block date in the future. Shouldn't really occur, unless someones been editing the database.
	session_destroy();
	session_unset();
	$e->setError(904);
	$e->dumpError();
}

if($dbasePassword != md5($givenPassword)) {
		$e->setError(901);
		$newloginerror = 1;
}

if($use_captcha==1) {
	if($_SESSION['veriword']!=$captcha_code) {
		$e->setError(905);
		$newloginerror = 1;
	}
}

if($newloginerror==1) {
	$failedlogins += $newloginerror;
	if($failedlogins>=3) { //increment the failed login counter, and block!
		$sql = "update $dbase.".$table_prefix."user_attributes SET failedlogincount='$failedlogins', blockeduntil='".(time()+(1*60*60))."' where internalKey=$internalKey";
		$rs = mysql_query($sql);
	} else { //increment the failed login counter
		$sql = "update $dbase.".$table_prefix."user_attributes SET failedlogincount='$failedlogins' where internalKey=$internalKey";
		$rs = mysql_query($sql);
	}
	session_destroy();
	session_unset();
	$e->dumpError();
}

$currentsessionid = session_id();

if(!isset($_SESSION['validated'])) {
	$sql = "update $dbase.".$table_prefix."user_attributes SET failedlogincount=0, logincount=logincount+1, lastlogin=thislogin, thislogin=".time().", sessionid='$currentsessionid' where internalKey=$internalKey";
	$rs = mysql_query($sql);
}

// get permissions
$_SESSION['shortname']=$username; $_SESSION['fullname']=$fullname;$_SESSION['email']=$email;$_SESSION['validated']=1;$_SESSION['internalKey']=$internalKey;$_SESSION['valid']=base64_encode($givenPassword);$_SESSION['user']=base64_encode($username);$_SESSION['failedlogins']=$failedlogins;$_SESSION['lastlogin']=$lastlogin;$_SESSION['sessionRegistered']=$sessionRegistered;$_SESSION['role']=$role;$_SESSION['lastlogin']=$lastlogin;$_SESSION['nrlogins']=$nrlogins;
$sql="SELECT * FROM $dbase.".$table_prefix."user_roles where id=".$role.";";
$rs = mysql_query($sql); 
$row = mysql_fetch_assoc($rs);
$_SESSION['permissions'] = $row;

if($_POST['rememberme']==1) {
	$username = $_POST['username'];
	$thepasswd = "cryptocipher";
	$rc4 = new rc4crypt;
	$thestring = $rc4->endecrypt($thepasswd,$username);
	setcookie("af8d399ecd929cde", $thestring,time()+604800, "/", "", 0);
} else {
	setcookie("af8d399ecd929cde", "",time()-604800, "/", "", 0);
}

$log = new logHandler;
$log->initAndWriteLog("Logged in", $_SESSION['internalKey'], $_SESSION['shortname'], "58", "-", "Etomite");

header("Location: ../");
?>