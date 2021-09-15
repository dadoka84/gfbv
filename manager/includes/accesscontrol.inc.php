<?php
session_start();
if(!isset($_SESSION['validated'])){
	include_once("browsercheck.inc.php");

	if(isset($manager_language)) {
	include_once "lang/".$manager_language.".inc.php";
	} else {
		include_once "lang/english.inc.php";
	}
	include_once ("crypt.class.inc.php");
	if(isset($_COOKIE['af8d399ecd929cde'])) {
		$cookieSet = 1;
		$username = $_COOKIE['af8d399ecd929cde'];	//af8d399ecd929cde
	}
	$thepasswd = "cryptocipher";
	$rc4 = new rc4crypt;
	$thestring = $rc4->endecrypt($thepasswd,$username,'de');
	$uid = $thestring;
	?>
<html>
<head>
<title>Etomite</title>
<meta http-equiv="content-type" content="text/html; charset=<?php echo $etomite_charset; ?>" />
<meta name="robots" content="noindex, nofollow" />
<link type="text/css" rel="StyleSheet" href="media/style/style.css" />
<link rel="stylesheet" type="text/css" href="media/style/coolButtons2.css" />
<script type="text/javascript" language="JavaScript1.5" src="media/script/ieemu.js"></script>
<script type="text/javascript" src="media/script/cb2.js"></script>
<style>
.loginTbl {
	background-color:			White; 
	border:						1px solid #003399; 
	background-image: 			url('media/images/bg/section.jpg'); 
	background-position: 		top right; 
	background-repeat: 			no-repeat; 
	padding: 					20px; 
	text-align: 				justify;
}
</style>
<script language="JavaScript">
	function checkRemember () {
		if(document.loginfrm.rememberme.value==1) {
			document.loginfrm.rememberme.value=0;	
		} else {
			document.loginfrm.rememberme.value=1;
		}
	}
	
	if (top.frames.length!=0) {
		top.location=self.document.location;
	}
	
	function enter(nextfield) {
		if(window.event && window.event.keyCode == 13) {
			  nextfield.focus();
			  return false; 
		} else {
			  return true;
		}
	}
</script>
</head>
<body>
<form method="post" name="loginfrm" action="processors/login.processor.php" style="margin: 0px; padding: 0px;"> 
<input type="hidden" value="<?php echo isset($cookieSet) ? 1 : 0; ?>" name="rememberme"> 
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center">
	<!-- intro text, logo and login box -->
		<table border="0" width="600" cellspacing="0" cellpadding="10" class="loginTbl">
		  <tr>
			<td><img src='media/images/misc/logo.gif' alt='<?php echo $_lang["etomite_slogan"]; ?>'></td>
			<td><?php echo $_lang["login_message"]; echo $use_captcha==1 ? "<p />".$_lang["login_captcha_message"] : "" ; ?></td>
		  </tr>
		  <tr>
		  	<td colspan="2" align="center">
				<table border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<?php if($use_captcha==1) { ?>
					<td>
						<a href="<?php echo $_SERVER['PHP_SELF'];?>"><img src="includes/veriword.php" width="148" height="80" alt="<?php echo $_lang["login_captcha_message"]; ?>" style="border: 1px solid #003399"></a>
					</td>
					<td>&nbsp;&nbsp;&nbsp;</td>	
					<?php } ?>
					<td>
						<table border="0" cellspacing="0" cellpadding="0">
						  <tr>
							<td><b><?php echo $_lang["username"]; ?>:</b></td>
							<td><input type="text" name="username" tabindex="1" onkeypress="return enter(document.loginfrm.password);" size="8" style="width: 150px;" value="<?php echo $uid ?>" /></td>
						  </tr>
						  <tr>
							<td><b><?php echo $_lang["password"]; ?>:</b></td>
							<td><input type="password" name="password" tabindex="2" onkeypress="return enter(<?php echo $use_captcha==1 ? "document.loginfrm.captcha_code" : "document.getElementById('Button1')" ;?>);" style="width: 150px;" value="" /></td>
						  </tr>
						  <?php if($use_captcha==1) { ?>
						  <tr>
							<td><b><?php echo $_lang["captcha_code"]; ?>:</b></td>
							<td><input type="text" name="captcha_code" tabindex="3" style="width: 150px;" onkeypress="return enter(document.getElementById('Button1'));" value="" /></td>
						  </tr>
						  <?php } ?>
						  <tr>
							<td><label for="thing" style="cursor:pointer"><?php echo $_lang["remember_username"]; ?>:&nbsp; </label></td>
							<td>
								<table width="100%"  border="0" cellspacing="0" cellpadding="0">
								  <tr>
									<td valign="top"><input type="checkbox" id="thing" name="thing" tabindex="4" SIZE="1" value="" <?php echo isset($cookieSet) ? "checked" : ""; ?> onClick="checkRemember()"></td>
									<td align="right">						
											<div tabindex="5" style="width:60px; text-align: center;border:1px solid black;border-left-color:ButtonHighlight;
											border-right-color:ButtonShadow;border-top-color:ButtonHighlight;border-bottom-color:ButtonShadow;padding:3px 4px 3px 4px;" id="Button1" onaction="document.loginfrm.submit();">
											<img src="media/images/icons/save.gif" align="absmiddle"> <?php echo $_lang["login_button"]; ?></div>
											<script>createButton(document.getElementById("Button1"));</script>
									</td>
								  </tr>
								</table>
							</td>
						  </tr>
						</table>
					  </td>
				  </tr>
				</table>
			</td>
		  </tr>
		</table>
		<br />
		<table border="0" width="600" cellspacing="0" cellpadding="10" class="loginTbl">
		  <tr>
			<td>			  <br />
				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<td><input type="checkbox" id="licenseOK" name="licenseOK" checked='checked' tabindex="6" /></td>
					<td><label for='licenseOK'><i>"I agree to use only my own password to access these resources."</i></label></td>
				  </tr>
			</table>			</td>
		  </tr>
		</table>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>				
  </tr>
</table>
</form>
<script type="text/javascript">
<?php echo !empty($uid) ? "document.loginfrm.password.focus();" : "document.loginfrm.username.focus();" ?>
</script>
</body>
</html>
	<?php
	exit;
}
if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");else if(getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");else if(getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");else $ip = "UNKNOWN";$_SESSION['ip'] = $ip;
$itemid = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'NULL' ;$lasthittime = time();$a = isset($_REQUEST['a']) ? $_REQUEST['a'] : "" ;
if($a!=1) {
	$sql = "REPLACE INTO $dbase.".$table_prefix."active_users(internalKey, username, lasthit, action, id, ip) values(".$_SESSION['internalKey'].", '".$_SESSION['shortname']."', '".$lasthittime."', '".$a."', '".$itemid."', '$ip')";
	if(!$rs = mysql_query($sql)) {
		echo "error replacing into active users! SQL: ".$sql;
		exit;
	}
}
?>