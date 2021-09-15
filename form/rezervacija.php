<?


$Refresh = $_REQUEST['Refresh'];
$EMAIL[0] = $_REQUEST['email'];
$EMAIL[1] = $_REQUEST['Email'];
$EMAIL[2] = $_REQUEST['E_mail'];
$EMAIL[3] = $_REQUEST['e_mail'];
$EMAIL[4] = $_REQUEST['email_address'];
$EMAIL[5] = $_REQUEST['Email_Address'];
$EMAIL[6] = $_REQUEST['Email_address'];


# MAIN SETTINGS
$Action = "E";
$Require = "ime,telefon,email";
$Confirm = "";
$Format = "imeA";
$Preview = "1";
$TimeZone = "";

# DISPLAY SETTINGS
$MessageTemplate = "";

$BorderColor = "";
$BgColor = "";
$FontFamily = "verdana";
$FontSize = "10px";
$FontColor = "blue";

# EMAIL SETTINGS
$SMTPServer = "";
$Recipient = "info@buonarte.com";
$CC = "";
$Subject = "Rezervacija preko interneta";
$TextEmails = "";
$HtmlEmails = "1";
$EmailTemplate = "EmailTemplate.html";

$AttachmentFields = "";
$AllowedExt = "";
$MaxFileSize = "";


# MYSQL DATABASE SETTINGS
$SQL_UserName = "";
$SQL_Password = "";
$SQL_Database = "";
$SQL_Table = "";
$SQL_ENV = "";

# CSV FILE SETTING
$CSVFile = "";

# FLAT-FILE SETTING
$FlatFile = "";

# POST-SUBMISSION SETTINGS
$ThankYou = "";
$Redirect = "hvala.php";
$AutoRespond = "1";
$AutoFromName = "IL Paradiso";
$AutoSubject = "Zahvaljujemo na rezervaciji";
$AutoTemplate = "";
$AutoContent = "Poštovani $Name,<br>Primili smo Vašu rezervaciju, kontaktiracemo vas uskoro.<br>Hvala.";

# STATISTICS SETTINGS
$UseStats = "";
$FormName = "";

# SECURITY SETTINGS
$FloodControl = "";
$Interval = "";
$Banned = "";

#======================================================


/*if ($FormRecipient) $Recipient = $FormRecipient;*/

#===========================================================
# RETRIEVE ENVIRONMENTAL VARIABLES
#===========================================================
header("Cache-control: public");
$IPAddress = $_SERVER['REMOTE_ADDR'];
$MkTime = mktime();
$Time = date('M j, Y  @ g:i:s a',mktime(date('H')+$TimeZone));
$Browser = $_SERVER['HTTP_USER_AGENT'];


#===========================================================
# STYLE AND HTML HEADER/FOOTER
#===========================================================
if (!$FontFamily)
  $FontFamily = 'arial'; // Default
  
if (!$FontSize)
  $FontSize = '10pt'; // Default

if (!$FontColor)
  $FontColor = '#000000'; // Default

if ($BorderColor and $BgColor)
{
 $msg['style'] = '<div style="margin-left:24px;padding:10,10,10,10;border:solid '.$BorderColor.' 1px;background-color:'.$BgColor.';font-family:'.$FontFamily.';font-size:'.$FontSize.';color:'.$FontColor.';">';
 $msg['tplstyle'] = '<div style="padding:10,10,10,10;border:solid '.$BorderColor.' 1px;background-color:'.$BgColor.';font-family:'.$FontFamily.';font-size:'.$FontSize.';color:'.$FontColor.';">';
}
else
{
 $msg['style'] = '<div style="margin-left:24px;font-family:'.$FontFamily.';font-size:'.$FontSize.';color:'.$FontColor.';">';
 $msg['tplstyle'] = '<div style="font-family:'.$FontFamily.';font-size:'.$FontSize.';color:'.$FontColor.';">';
}
if ($MessageTemplate and file_exists($MessageTemplate))
{
  $handle = @fopen($MessageTemplate,'r');
  $MsgTPL = @fread($handle,filesize($MessageTemplate));
  @fclose($handle);
  
  $MsgTPL = explode("[MMEX]",$MsgTPL);
  $msg['header'] = $MsgTPL[0].$msg['tplstyle'];
  $msg['footer'] = "</div>" . $MsgTPL[1];
}
else
{
  $msg['header'] = $msg['style'];
  $msg['footer'] = "</div><br><br>";
}

#===========================================================
# ERROR MESSAGES
#===========================================================
$msg['banned'] = "<b>Vasa IP adresa je blokirana.</b>";
$msg['nosubmit'] = "<b>Vasa rezervacija ne moze biti dostavljena.</b>";
$msg['recent'] = "<b>Ponovljeno upisivanje. Zbog sigurnosti ponovni upisi su ograniceni na svakih $Interval minuta.</b>";
$msg['norecipient'] = "<b>Ovaj eMail ne mozemo dostaviti. Niste napisali primaoca.</b>";
$msg['required'] = "<b>Sljedeca polja su neophodna za slanje rezervacije.<br> Molimo kliknite BACK i ponovo popunite sva polja.</b>";
$msg['confirmed'] = "<b>Sljedeca polja moraju biti jednaka:</b>";
$msg['formatted'] = "<b>Sljedeca polja nisu upisana u pravilnom formatu.<br> Molimo kliknite BACK i pravilno popunite sva polja. :</b>";

#===========================================================
# CHECK FLOOD CONTROL (PRE-SUBMISSION)
#===========================================================
if ($FloodControl == "1" and file_exists('data/flood.log'))
{
  $Seconds = $Interval * 60;

  $handle = @fopen('data/flood.log','r');
  @flock($handle, LOCK_SH);
  $LogData = @fread($handle,filesize('flood.dat'));
  $LogData = explode("#",$LogData);
  for ($x=0;$x<count($LogData);$x++)
  {
  $line = explode("|",$LogData[$x]);
   if ($IPAddress == $line[0])
   {
     if (($MkTime - $line[1]) < $Seconds)
  	  exit ($msg['header'] . $msg['recent'] . $msg['footer']);
   }
  }
  @flock($handle,LOCK_UN);
  @fclose($handle);
}

#===========================================================
# SAVE INFORMATION TO STATS LOG
#===========================================================
if ($UseStats == "1")
{
  $StatInfo = "$FormName|$MkTime|$Time|$IPAddress";
  $handle = @fopen('data/stats.log','a');
  @flock($handle,LOCK_EX);
  @fwrite($handle,$StatInfo."\n");
  @flock($handle,LOCK_UN);
  @fclose($handle);
}

#===========================================================
# BANNED IP ADDRESSES
#===========================================================
  if(stristr($Banned,$IPAddress))
    exit ($msg['header'] . $msg['banned'] . $msg['footer']);

#===========================================================
# ADJUST A FEW VARIABLES
#===========================================================
$Alpha = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$a = rand(0,25);
$b = rand(0,25);
$c = $Alpha[$a];
$d = $Alpha[$b];
$e = rand(10000,99999);
$f = rand(100,999);
$ID = $c.$e.$d.$f;

for ($x=0;$x<count($EMAIL);$x++){
 if ($EMAIL[$x]) $SendFrom = $EMAIL[$x];}
if (!$SendFrom)
 $SendFrom = $Recipient;

$_EMAIL = $_POST;
$_HTML = $_POST;
$_SQL = $_POST;
$_CSV = $_POST;
$_FILE = $_POST;
$_AUTO = $_POST;

$AttachmentField = explode(",",$AttachmentFields);

$x=0;
foreach ($AttachmentField as $ThisAttachment)
{
 $ATTACH[$x][0] = $_FILES[$ThisAttachment]['name'];
 $ATTACH[$x][1] = $_FILES[$ThisAttachment]['tmp_name'];
 $x++;
}

if ($SMTPServer)
{
 ini_set('SMTP',$SMTPServer);
 ini_set('sendfrom_mail',$Recipient);
}

#===========================================================
# CHECK REQUIRED FIELDS
#===========================================================
if ($Require)
{
  $Required = explode(",",$Require);
  for($i=0;$i<count($Required);$i++)
  {
	$Field = $Required[$i];
	if(!$_POST[$Field])
	{
	 $Field = str_replace("_"," ",$Field);
	 $Field = ucfirst($Field);
	 $EmptyFields .= "<li>$Field</li>";
	 if ($Preview == "1")
	  $RedFields .= "$Field,";
	}
  }
  if ($EmptyFields)
   $Errors = $msg['required'] . "<ul type=\"square\">" . $EmptyFields . "</ul><br>";
}

#===========================================================
# CHECK FIELDS FOR CONFIRMATION                             
#===========================================================
if ($Confirm)
{
  $Confirmed = explode(",",$Confirm);
  for($i=0;$i<count($Confirmed);$i+=2)
  {
	$Field1 = $Confirmed[$i];
	$Field2 = $Confirmed[$i+1];
	$FieldA = $_POST[$Field1];
	$FieldB = $_POST[$Field2];
	if ($FieldA != $FieldB)
	{
	  $Field1 = str_replace("_"," ",$Field1);
	  $Field1 = ucfirst($Field1);
	  $Field2 = str_replace("_"," ",$Field2);
	  $Field2 = ucfirst($Field2);
	  $WrongFields .= "<li><b>$Field1</b> ne odgovara <b>$Field2</b></li>";
	  if ($Preview == "1")
	    $RedFields .= "$Field1,$Field2,";
    }
  }
  if ($WrongFields and !$Errors)
   $Errors = $msg['confirmed'] . "<ul type=\"square\">" . $WrongFields . "</ul><br>";
  else if ($WrongFields and $Errors)
   $Errors .= "<br>" . $msg['confirmed'] . "<ul type=\"square\">" . $WrongFields . "</ul><br>";
}

#===========================================================
# CHECK FORMATTED FIELDS
#===========================================================
if ($Format)
{
  $Formatted = explode(",",$Format);
  for ($x=0;$x<count($Formatted);$x++)
  {
    $Format = substr($Formatted[$x],-1);
	$Field = substr($Formatted[$x],0,-1);

	if ($Format == "<") // MINIMUM CHAR FORMAT
	{
	  $Length = substr($Field,-2);
      $Field = substr($Field,0,-2);
	  if (substr($Length,0,1) == "0")
	   $Length = substr($Length,-1);
	  if ($_POST[$Field])
	  {
        if(strlen($_POST[$Field]) < $Length)
	    {
	      $Field = str_replace("_"," ",$Field);
		  $Field = ucfirst($Field);
	      $IncorrectFields .= "<li><b>$Field</b>&nbsp;potrebno upisati najmanje $Length karaktera.</li>";
	      if ($Preview == "1")
	       $RedFields .= "$Field,";
		}
	  }
	}

	if ($Format == ">") // MAXIMUM CHAR FORMAT
	{
	  $Length = substr($Field,-2);
      $Field = substr($Field,0,-2);
	  if (substr($Length,0,1) == "0")
	   $Length = substr($Length,-1);
	  if ($_POST[$Field])
	  {
        if(strlen($_POST[$Field]) > $Length)
	    {
	      $Field = str_replace("_"," ",$Field);
		  $Field = ucfirst($Field);
	      $IncorrectFields .= "<li><b>$Field</b>&nbsp;Prelazi maksimalan broj $Length karaktera.</li>";
	      if ($Preview == "1")
	       $RedFields .= "$Field,";
		}
	  }
	}

	if ($Format == "@" and $_POST[$Field]) // EMAIL FORMAT
	{
	  if(!eregi('^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$',$_POST[$Field]))
	  {
	   $Field = str_replace("_"," ",$Field);
	   $Field = ucfirst($Field);
	   $IncorrectFields .= "<li><b>$Field</b>&nbsp;potreban pravilan email format.</li>";
	   if ($Preview == "1")
	    $Redfields .= "$Field,";
	  }
	  $Host = explode("@",$_POST[$Field]);
	  if (!checkdnsrr($Host[1].'.', 'MX'))
	  {
	   $Field = str_replace("_"," ",$Field);
	   $Field = ucfirst($Field);
	   $IncorrectFields .= "<li><b>$Field</b>&nbsp;potrebna ispravna domena u email-e.</li>";
	   if ($Preview == "1")
	    $Redfields .= "$Field,";
	  }
	}

	if ($Format == "#" and $_POST[$Field]) // PHONE NUMBER FORMAT
	{
	  if (!eregi('([0-9]{3})-([0-9]{3})-([0-9]{4})', $_POST[$Field]) and !eregi('([0-9]{3})\.([0-9]{3})\.([0-9]{4})', $_POST[$Field]) and !eregi('([0-9]{3}) ([0-9]{3}) ([0-9]{4})', $_POST[$Field]) and !eregi('(\([0-9]{3}\))-([0-9]{3})-([0-9]{4})', $_POST[$Field]) and !eregi('(\([0-9]{3}\))\.([0-9]{3})\.([0-9]{4})', $_POST[$Field]) and !eregi('(\([0-9]{3}\)) ([0-9]{3}) ([0-9]{4})', $_POST[$Field]) and !eregi('(\([0-9]{3}\)) ([0-9]{3})-([0-9]{4})', $_POST[$Field]) and !eregi('(\([0-9]{3}\)) ([0-9]{3})\.([0-9]{4})', $_POST[$Field]))
	  {
	   $Field = str_replace("_"," ",$Field);
	   $Field = ucfirst($Field);
	   $IncorrectFields .= "<li><b>$Field</b>&nbsp;potreban pravilan format broja telefona.</li>";
	   if ($Preview == "1")
	    $RedFields .= "$Field,";
	  }
	}

	if ($Format == "Z" and $_POST[$Field]) // ZIP CODE FORMAT
	{
	  if (!eregi('([0-9]{5})', $_POST[$Field]))
	  {
	   $Field = str_replace("_"," ",$Field);
	   $Field = ucfirst($Field);
	   $IncorrectFields .= "<li><b>$Field</b>&nbsp;potrebam pravilan format.</li>";
	   if ($Preview == "1")
	    $RedFields .= "$Field,";
	  }
	}

	if ($Format == "S" and $_POST[$Field]) // STATE FORMAT
	{
	  if (!eregi('([a-zA-Z]{2})', $_POST[$Field]))
	  {
	   $Field = str_replace("_"," ",$Field);
	   $Field = ucfirst($Field);
	   $IncorrectFields .= "<li><b>$Field</b>&nbsp;potrebam pravilan format.</li>";
	   if ($Preview == "1")
	    $RedFields .= "$Field,";
	  }
	}

	if ($Format == "N" and $_POST[$Field] > "") // NUMERIC ONLY FORMAT
	{
	  if (eregi('([a-zA-Z_\-])', $_POST[$Field]))
	  {
	   $Field = str_replace("_"," ",$Field);
	   $Field = ucfirst($Field);
	   $IncorrectFields .= "<li><b>$Field</b>&nbsp;potrebni samo brojevi.</li>";
	   if ($Preview == "1")
	    $RedFields .= "$Field,";
	  }
	}

	if ($Format == "A" and $_POST[$Field]) // ALPHA ONLY FORMAT
	{
	  if (eregi('([0-9])', $_POST[$Field]))
	  {
	   $Field = str_replace("_"," ",$Field);
	   $Field = ucfirst($Field);
	   $IncorrectFields .= "<li><b>$Field</b>&nbsp;potrebna samo slova.</li>";
	   if ($Preview == "1")
	    $RedFields .= "$Field,";
	  }
	}
  }
  if ($IncorrectFields and !$Errors)
   $Errors = $msg['formatted'] . "<ul type=\"square\">" . $IncorrectFields . "</ul><br>";
  else if ($IncorrectFields and $Errors)
   $Errors .= "<br>" . $msg['formatted'] . "<ul type=\"square\">" . $IncorrectFields . "</ul><br>";
}

#=======================================
# CHECK ATTACHMENT EXTENSION
#=======================================
if ($AllowedExt and ($ATTACH[0][0] or $ATTACH[1][0] or $ATTACH[2][0]))
{
   for ($x=0;$x<count($ATTACH);$x++)
   {
    $MaxSize = $MaxFileSize * 1000;   
    if (filesize($ATTACH[$x][1]) > $MaxSize)
    {
     $Errors .= "<br><b><i>".$ATTACH[$x][0]."</i> has exceeded the allowed file size of $MaxFileSize KB.</b><br>";
    }
    $i = strrpos($ATTACH[$x][0],".");
    $l = strlen($ATTACH[$x][0]) - $i;
    $Ext = substr($ATTACH[$x][0],$i+1,$l);
    $Ext = strtolower($Ext);
    if ($Ext)
	{
     if (!strstr($AllowedExt,$Ext))
     {
	  $Errors .= "<br><b><i>.$Ext</i> is an invalid extension. The following extensions are allowed:</b><ul type=\"square\">";
	  $Allowed = explode(",",$AllowedExt);
	  for($x=0;$x<count($Allowed);$x++)
	  {
	   $Errors .= "<li>.".$Allowed[$x]."</li>";
	  }
	  $Errors .= "</ul><br>";
	 }
    }
   }
}

#===========================================================
# PREVIEW
#===========================================================
if ($Preview == "1" and !$Refresh)
{
 $HTML['preview'] .= "
 <b><u>Vaša rezervacija</u></b><br><br>
 <form method=\"post\" action=\"".$PHP_SELF."\">
  <input type=\"hidden\" name=\"Refresh\" value=\"Y\">
  <input type=\"hidden\" name=\"Settings\" value=\"".$Settings."\">
  <table style=\"font-family:$FontFamily;font-size:$FontSize;\" cellspacing=1 cellpadding=2>
  ";

$RedFields = explode(",",$RedFields);
$flag = 0;

while (list ($key, $val) = each ($_POST))
{
 if ($key != "Settings" and $key != "Refresh")
 {
  $val = stripslashes($val);
  $val = str_replace('"','&#34;',$val);
  $HTML['form'] .= "<input type=\"hidden\" name=\"".$key."\" value=\"".$val."\">";

  $key = ucfirst($key);
  $key = str_replace("_"," ",$key);

  for ($x=0;$x<count($RedFields)-1;$x++)
  {
   if ($key == $RedFields[$x])
   {
    $HTML['form'] .= "<tr><td style=\"color:red\"><b>$key</b>:</td><td>$val</td></tr>";
	$Flag = 1;
   }
  }
  if ($Flag != 1)
    $HTML['form'] .= "<tr><td><b>$key</b>:</td><td>$val</td></tr>";
  $Flag = 0;
 }
}
  $HTML['form'] .= "<tr><td height=16 colspan=2></td></tr>";
  if (!$Errors)
   $HTML['form'] .= "<tr><td><input type=\"submit\" value=\"Potvrdi\" style=\"font-family:$font_family;font-size:$font_size;border: solid #AAAAAA 1px; cursor:hand\"></td></tr>";
  $HTML['form'] .= "</form></table>";
}

if ($Errors and $Preview != "1")
 exit ($msg['header'].$Errors.$msg['footer']);
else if ($Errors and $Preview == "1" and !$Refresh)
 exit ($msg['header'].$Errors."<br>".$HTML['preview'].$HTML['form'].$msg['footer']);
else if ($Preview == "1" and !$Refresh)
 exit ($msg['header'].$HTML['preview'].$HTML['form'].$msg['footer']);


#===========================================================
# CHOOSE ACTION
#===========================================================
if (!$Action)
 exit($msg['header'] . $msg['nosubmit'] . $msg['footer']);
 
#########################################################################
# EMAIL FUNCTIONS                                                       #
#########################################################################
if ($Action == "E" or $Action == "EC" or $Action == "ED" or $Action == "EF"){

while (list ($key, $val) = each ($_EMAIL))
{
 if ($key != "Settings" and $key != "Refresh")
 {
  $val = stripslashes($val);
  $val = str_replace('&#34;','"',$val);
  $val = strip_tags($val);
  $key = ucfirst($key);
  $key = str_replace("_"," ",$key);
  $EMAIL['text'] .= "$key:  $val\r\n";
  $val = nl2br($val);
  $EMAIL['form'] .= "<b>$key</b>:&nbsp;&nbsp;$val<br>";
  $EMAIL['html'] .= "<tr><td width=120 valign=top><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$key</font></td><td width=380 valign=top><font size=\"2\" face=\"Verdana, Arial, Helvetica, sans-serif\">$val</font></td></tr>";
 }
}

if (!$Recipient)
  exit ($msg['header'] . $msg['norecipient'] . $msg['footer']);

if (!$Subject)
   $Subject = 'Form Submission';

#===========================================================
# Email Templates
#===========================================================
$EMAIL['textemail'] = "
Dear Recipient,\r\n
This is a notice that a new submission has been generated by Mail Manage EX on $Time.\r\n\r\n
***Form Results***\r\n
".$EMAIL['text']."
User's Browser: $Browser\r\n
User's IP Address: $IPAddress\r\n\r\n
- Mail Manage EX Notifier -
";

if ($EmailTemplate)
{
 $handle = @fopen($EmailTemplate,'r');
 $EMAIL['htmlemail'] = @fread($handle,filesize($EmailTemplate));
 @fclose($handle);
 if (!strstr($EMAIL['htmlemail'],'[ALL]'))
 {
  while(list($key,$val) = each($_HTML))
  {
   $insert = "[".$key."]";
   $val = stripslashes($val);
   $val = strip_tags($val);
   $val = nl2br($val);
   $EMAIL['htmlemail'] = str_replace($insert,$val,$EMAIL['htmlemail']);
  }
 }
 else
  $EMAIL['htmlemail'] = str_replace('[ALL]',$EMAIL['form'],$EMAIL['htmlemail']);
  
 $EMAIL['htmlemail'] = str_replace("[Received]",$Time,$EMAIL['htmlemail']);
 $EMAIL['htmlemail'] = str_replace("[IPAddress]",$IPAddress,$EMAIL['htmlemail']);
 $EMAIL['htmlemail'] = str_replace("[Browser]",$Browser,$EMAIL['htmlemail']);
 $EMAIL['htmlemail'] = str_replace("[ID]",$ID,$EMAIL['htmlemail']);
 $EMAIL['htmlemail'] = str_replace("[Attach1]",$ATTACH[0][0],$EMAIL['htmlemail']);
 $EMAIL['htmlemail'] = str_replace("[Attach2]",$ATTACH[1][0],$EMAIL['htmlemail']);
 $EMAIL['htmlemail'] = str_replace("[Attach3]",$ATTACH[2][0],$EMAIL['htmlemail']);
}
else
{
$EMAIL['htmlemail'] = '
<html>
<head></head>
<xbody>
<table border="0" cellspacing="0" cellpadding="0" width="600">
  <tr>
    <td width="217" align="left">
     <a target="_blank"  href="http://www.arecaweb.com/php/mmex/mmex.html"><img src="http://www.arecaweb.com/php/mmex/i/logo_mmex.jpg" border="0"></a></td>
    <td width="383" align="right" valign="bottom">
     <font color="#30A11E" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Submission: '.$ID.'</strong></font></td>
      </tr>
</table>

<table bgcolor="#F1F1F1" border="0" cellspacing="0" cellpadding="5" width="600">
  <tr>
    <td align="left" valign="middle">
     <strong><font color="#202020" size="1" face="Verdana, Arial, Helvetica, sans-serif">THANK YOU FOR CHOOSING MAIL MANAGE EX!</font></strong></td>
      </tr>
</table><br>

<table bgcolor="#F9F9F9" border="0" cellspacing="0" cellpadding="5" width="600">
  <tr>
    <td width="380" align="center" valign="middle">
     <img src="#"  width="250" height="1" border="0"></td>
    <td width="220" align="left" valign="top">
     <font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">
<br>
<strong>'.$Time.'<br>
<font color="#30A11E">IP:&nbsp;'.$IPAddress.'</font></font></strong><br>
<font color="#000000" size="1" face="Verdana, Arial, Helvetica, sans-serif">'.$Browser.'</font></td>
      </tr>
</table>

<table bgcolor="#F9F9F9" border="0" cellspacing="0" cellpadding="5" width="600">
  <tr>
    <td>
     <font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">
Dear Admin,<br><br>
This is a notice that a new submission has been generated by Mail Manage EX.<br><br>
***Form Results***<br><br>
<table bgcolor="#F9F9F9" border="0" cellspacing="1" cellpadding="0" width="500">
'.$EMAIL['html'].'
</table><br><br>

<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
Please let us know if you have any questions!<br><br>
Sincerely,</font><br>
<font color="#30A11E"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Mail Manage EX Notifier</font></strong></font><br></td>
      </tr>
</table><br>

<table bgcolor="#F1F1F1" border="0" cellspacing="0" cellpadding="5" width="600">
  <tr>
    <td align="left" valign="middle">
     <strong><font color="#202020" size="1" face="Verdana, Arial, Helvetica, sans-serif">Copyright &copy; 2003-2004, Gregg Kenneth Jewell.</font></strong></td>
      </tr>
</table>
<font face="Verdana, Arial, Helvetica, sans-serif" size=1>&nbsp;Support MMEX by making a <a href="https://www.paypal.com/xclick/business=greggjewell%40yahoo.com&item_name=Mail+Manage+EX&no_note=1&tax=0&currency_code=USD&lc=US" style="color:blue">donation</a> today!!</font>
</xbody>
</html>';
}
#===========================================================
# Put Header Information in Email
#===========================================================
$rand = md5(time());
$MimeBoundary = "MMEX". $rand;

$Headers = "From: $SendFrom\n";
$Headers .= "Reply-to: $SendFrom\n";
$Headers .= "Return-Path: $SendFrom\n";
if ($CC)
  $Headers .= "cc: $CC\n";
$Headers .= "X-Mailer: My PHP Mailer\n";
$Headers .= "MIME-Version: 1.0\n";
if ($AttachmentFields and ($ATTACH[0][0] or $ATTACH[1][0] or $ATTACH[2][0]))
  $Headers .= "Content-Type: multipart/mixed; charset=\"iso-8859-1\"; boundary=\"$MimeBoundary\";\n\n";
else
  $Headers .= "Content-Type: multipart/alternative; charset=\"iso-8859-1\"; boundary=\"$MimeBoundary\";\n\n";
$Headers .= "This is a multi-part message in MIME format.\n\n";

if ($TextEmails == "1")
{
 $Content .= "--$MimeBoundary\n";
 $Content .= "Content-Type: text/plain; charset=\"iso-8859-1\"\n";
 $Content .= "Content-Transfer-Encoding: 8bit\n\n";
 $Content .= $EMAIL['textemail']."\n\n";
}
if ($HtmlEmails == "1")
{
 $Content .= "--$MimeBoundary\n";
 $Content .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
 $Content .= "Content-Transfer-Encoding: 8bit\n\n";
 $Content .= $EMAIL['htmlemail']."\n\n";
}
if ($AttachmentFields)
{
   for ($x=0;$x<count($ATTACH);$x++)
   {
        $TmpFile = "data/".$ATTACH[$x][0];
        @copy($ATTACH[$x][1],"$TmpFile");
		$fa = @fopen ($TmpFile,'r');
        $FileContent = @fread($fa,filesize($TmpFile));
        @fclose($fa);
        $FileContent = chunk_split(base64_encode($FileContent));
        
		$Content .= "--$MimeBoundary\n";
        $Content .= "Content-Type: application/octetstream;\n name=\"".$ATTACH[$x][0]."\"\n";
        $Content .= "Content-Transfer-Encoding: base64\n";
        $Content .= "Content-Disposition: attachment;\n filename=\"".$ATTACH[$x][0]."\"\n\n";
		$Content .= $FileContent."\n";
		@unlink($TmpFile);
   }
}
$Content .= "--$MimeBoundary--\n\n\n";


#===========================================================
# Send It Off
#===========================================================
mail($Recipient, $Subject, $Content, $Headers);


} // END OF ACTION

#########################################################################
# CSV-FILE FUNCTIONS                                                    #
#########################################################################
if ($Action == "C" or $Action == "EC" or $Action == "CD"){

$Time = date('M j Y  @ g:i:s a',mktime(date('H')+$TimeZone));

while (list ($key, $val) = each ($_CSV))
{
 if ($key != "Settings" and $key != "Refresh")
 {
  $val = stripslashes($val);
  $val = str_replace('&#34;','"',$val);
  $val = strip_tags($val);
  $val = str_replace(",","¸",$val);
  $val = str_replace("\r\n"," ",$val);
  $val = str_replace("\n"," ",$val);  
  $CSVData .= "$val,";
 }
}
$CSVData .= "$Time,$IPAddress,$Browser";

$handle = @fopen($CSVFile,'a');
@flock($handle,LOCK_EX);
@fwrite($handle,$CSVData."\n");
@flock($handle,LOCK_UN);
@fclose($handle);

} // END OF ACTION

#########################################################################
# FLAT-FILE FUNCTIONS                                                   #
#########################################################################
if ($Action == "F" or $Action == "EF" or $Action == "DF"){

while (list ($key, $val) = each ($_FILE))
{
 if ($key != "Settings" and $key != "Refresh")
 {
  $val = stripslashes($val);
  $val = str_replace('&#34;','"',$val);
  $val = strip_tags($val);
  $key = ucfirst($key);
  $key = str_replace("_"," ",$key);
  $FileData .= "$key: $val\r\n";
 }
}

$FileData .= "Received: " . $Time . "\r\nIP: " . $IPAddress . "\r\n" . $FileData . "\r\n========================================\r\n\r\n";

$handle = @fopen($FlatFile,'a');
@flock($handle,LOCK_EX);
@fwrite($handle,$FileData);
@flock($handle,LOCK_UN);
@fclose($handle);

} // END OF ACTION

#########################################################################
# DATABASE FUNCTIONS                                                    #
#########################################################################
if ($Action == "D" or $Action == "ED" or $Action == "CD" or $Action == "DF"){

$Connect = @mysql_connect("localhost", "$SQL_UserName", "$SQL_Password");
@mysql_select_db("$SQL_Database",$Connect);

while (list ($key, $val) = each ($_SQL))
{
 if ($key != "Settings" and $key != "Refresh")
 {
	$val = stripslashes($val);
	$val = str_replace('&#34;','"',$val);
    $val = strip_tags($val);
	$Columns .= $key . ',';
    $Values .= "'" . $val . "',";
 }
}
if ($SQL_ENV == "1")
{
 $Columns .= "Received,IPAddress,Browser";
 $Values .= "'" . $Time . "','" . $IPAddress ."','" . $Browser . "'";
}
else
{
 $Columns = substr($Columns,0,-1);
 $Values = substr($Values,0,-1);
}

@mysql_query("INSERT INTO $SQL_Table ($Columns) VALUES ($Values)");
@mysql_close($Connect);

} // END OF ACTION

#########################################################################
# POST SUBMIT FUNCTIONS                                                 #
#########################################################################

#===========================================================
# SAVE FLOOD CONTROL DATA (POST-SUBMISSION)
#===========================================================
if ($FloodControl == "1")
{
$LogData = $IPAddress . "|" . $MkTime . "#";

  $handle = @fopen('data/flood.log','a');
  @flock($handle,LOCK_EX);
  @fwrite($handle,$LogData);
  @flock($handle,LOCK_UN);
  @fclose($handle);
}

#===========================================================
# Auto Respond Information
#===========================================================
if ($AutoRespond == "1")
{
  if (!$AutoSubject)
    $AutoSubject = 'Thank you for your submission';
  if ($AutoTemplate and file_exists($AutoTemplate))
  {
    $handle = @fopen($AutoTemplate,'r');
    $AutoContent = @fread($handle,filesize($AutoTemplate));
    @fclose($handle);
	while(list($key,$val) = each($_AUTO))
    {
     $insert = "[".$key."]";
     $val = stripslashes($val);
     $val = strip_tags($val);
     $val = nl2br($val);
     $AutoContent = str_replace($insert,$val,$AutoContent);
	 $AutoContent = str_replace("[Received]",$Time,$AutoContent);
     $AutoContent = str_replace("[ID]",$ID,$AutoContent);
     $AutoContent = str_replace("[Attach1]",$ATTACH[0][0],$AutoContent);
     $AutoContent = str_replace("[Attach2]",$ATTACH[1][0],$AutoContent);
     $AutoContent = str_replace("[Attach3]",$ATTACH[2][0],$AutoContent);
    }
  }
  if (!$AutoContent)
	$AutoContent = "Thank you for your submmission. We will contact you shortly.";
  if ($AutoFromName)
   $AutoHeaders = "From: \"$AutoFromName\" <$Recipient>\n";
  else
   $AutoHeaders = "From: $Recipient\n";
  $AutoHeaders .= "MIME-Version: 1.0\nContent-Type: text/html; charset=ISO-8859-1\nContent-Transfer-Encoding: 8bit\n\n";
    @mail($SendFrom,$AutoSubject,$AutoContent,$AutoHeaders);
}

#===========================================================
# REDIRECT OR THANK YOU
#===========================================================
if (!$Redirect)
{
  if (!$ThankYou)
    $ThankYou = 'Thank you for your submission.';
  exit ($msg['header'] . "$ThankYou". $msg['footer']);
}
else
  echo "<html><head><META http-equiv=\"refresh\" content=\"0;URL=$Redirect\"></head><body></body></html>";
?>