<?php 
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");
if($_SESSION['permissions']['settings']!=1 && $_REQUEST['a']==17) {	$e->setError(3);
	$e->dumpError();	
}

// check to see the edit settings page isn't locked
$sql = "SELECT internalKey, username FROM $dbase.".$table_prefix."active_users WHERE $dbase.".$table_prefix."active_users.action=17";
$rs = mysql_query($sql);
$limit = mysql_num_rows($rs);
if($limit>1) {
	for ($i=0;$i<$limit;$i++) {
		$lock = mysql_fetch_assoc($rs);
		if($lock['internalKey']!=$_SESSION['internalKey']) {		
			$msg = $lock['username']." is currently editing these settings. Please wait until the other user has finished and try again.";
			$e->setError(5, $msg);
			$e->dumpError();
		}
	}
} 
// end check for lock

$displayStyle = $_SESSION['browser']=='mz' ? "table-row" : "block" ;
?>

<script type="text/javascript">
function checkIM() {
	im_on = document.settings.im_plugin[0].checked; // check if im_plugin is on
	if(im_on==true) {
		showHide(/imRow/, 1);
	}
}

function showHide(what, onoff){

	var all = document.getElementsByTagName( "*" );
	var l = all.length;
	var buttonRe = what;
	var id, el, stylevar;
	
	if(onoff==1) {
		stylevar = "<?php echo $displayStyle; ?>";
	} else {
		stylevar = "none";
	}
	
	for ( var i = 0; i < l; i++ ) {
		el = all[i]
		id = el.id;
		if ( id == "" ) continue;
		if (buttonRe.test(id)) {
			el.style.display = stylevar;
		}
	}
}
</script>
<div class="subTitle"> 
	<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['settings_title']; ?></span>

	<table cellpadding="0" cellspacing="0">
		<tr>
			<td id="Button1" onaction="documentDirty=false; document.settings.submit();"><img src="media/images/icons/save.gif" align="absmiddle"> <?php echo $_lang['save']; ?></td>
				<script>createButton(document.getElementById("Button1"));</script>
			<td id="Button5" onaction="document.location.href='index.php?a=2';"><img src="media/images/icons/cancel.gif" align="absmiddle"> <?php echo $_lang['cancel']; ?></td>
				<script>createButton(document.getElementById("Button5"));</script>
		</tr>
	</table>
</div>	
<div style="margin: 0px 10px 0px 20px"> 
  <form name="settings" action="index.php?a=30" method="post"> 
    <input onChange="documentDirty=true;" type="hidden" name="settings_version" value="<?php echo $version; ?>"> 
    <!-- this field is used to check site settings have been entered/ updated after install or upgrade --> 
    <?php if(!isset($settings_version) || $settings_version!=$version) { ?> 
    <div class='sectionBody' style='margin-left: 0px; margin-right: 0px;'><?php echo $_lang['settings_after_install']; ?></div> 
    <?php } ?> 
    <link type="text/css" rel="stylesheet" href="media/style/tabs.css" /> 
    <script type="text/javascript" src="media/script/tabpane.js"></script> 
    <div class="tab-pane" id="settingsPane"> 
      <script type="text/javascript">
		tpSettings = new WebFXTabPane( document.getElementById( "settingsPane" ) );
	</script> 
      <div class="tab-page" id="tabPage2"> 
        <h2 class="tab"><?php echo $_lang["settings_site"] ?></h2> 
        <script type="text/javascript">tpSettings.addTabPage( document.getElementById( "tabPage2" ) );</script> 
        <table border="0" cellspacing="0" cellpadding="3"> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["serveroffset_title"] ?></b></td> 
            <td> <select name="server_offset_time" size="1" class="inputBox"> 
                <?php 
			for($i=-24; $i<25; $i++) {
				$seconds = $i*60*60;
				$selectedtext = $seconds==$server_offset_time ? "selected='selected'" : "" ;
			?> 
                <option value="<?php echo $seconds; ?>" <?php echo $selectedtext; ?>><?php echo $i; ?></option> 
                <?php
			}
			?> 
              </select> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php printf($_lang["serveroffset_message"], strftime('%H:%M:%S', time()), strftime('%H:%M:%S', time()+$server_offset_time)); ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'>&nbsp;</div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["server_protocol_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="server_protocol" value="http" <?php echo ($server_protocol=='http' || !isset($server_protocol))? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["server_protocol_http"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="server_protocol" value="https" <?php echo $server_protocol=='https' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["server_protocol_https"]?> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["server_protocol_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr>
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["language_title"]?></b></td> 
            <td> <select name="manager_language" size="1" class="inputBox" onChange="documentDirty=true;"> 
                <?php
	$dir = dir("includes/lang");

	while ($file = $dir->read()) {
		if(strpos($file, ".inc.php")>0) {
			$endpos = strpos ($file, ".");
			$languagename = substr($file, 0, $endpos);
			$selectedtext = $languagename==$manager_language ? "selected='selected'" : "" ;
?> 
                <option value="<?php echo $languagename; ?>" <?php echo $selectedtext; ?>><?php echo ucwords(str_replace("_", " ", $languagename)); ?></option> 
                <?php					
		}
	}
	$dir->close();
?> 
              </select> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["language_message"]?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["charset_title"]?></b></td> 
            <td> <select name="etomite_charset" size="1" class="inputBox" onChange="documentDirty=true;"> 
                <?php
	include "charsets.php";
?> 
              </select> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["charset_message"]?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["sitename_title"] ?></b></td> 
            <td ><input onChange="documentDirty=true;" type='text' maxlength='50' style="width: 200px;" name="site_name" value="<?php echo isset($site_name) ? $site_name : "My Etomite Site" ; ?>"></td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["sitename_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["sitestart_title"] ?></b></td> 
            <td ><input onChange="documentDirty=true;" type='text' maxlength='10' size='5' name="site_start" value="<?php echo isset($site_start) ? $site_start : 1 ; ?>"></td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["sitestart_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["errorpage_title"] ?></b></td> 
            <td ><input onChange="documentDirty=true;" type='text' maxlength='10' size='5' name="error_page" value="<?php echo isset($error_page) ? $error_page : 1 ; ?>"></td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["errorpage_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["sitestatus_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="site_status" value="1" <?php echo ($site_status=='1' || !isset($site_status)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["online"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="site_status" value="0" <?php echo $site_status=='0' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["offline"]?> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["sitestatus_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["siteunavailable_title"] ?></b></td> 
            <td> <textarea name="site_unavailable_message" style="width:100%; height: 120px;"><?php echo isset($site_unavailable_message) ? $site_unavailable_message : "The site is currently unavailable" ; ?></textarea> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["siteunavailable_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["track_visitors_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="track_visitors" value="1" <?php echo ($track_visitors=='1' || !isset($track_visitors)) ? 'checked="checked"' : "" ; ?> onclick='showHide(/logRow/, 1);'> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="track_visitors" value="0" <?php echo $track_visitors=='0' ? 'checked="checked"' : "" ; ?> onclick='showHide(/logRow/, 0);'> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["track_visitors_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr id='logRow1' class='row1' style="display: <?php echo $track_visitors==1 ? $displayStyle : 'none' ; ?>"> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["resolve_hostnames_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="resolve_hostnames" value="1" <?php echo ($resolve_hostnames=='1' || !isset($resolve_hostnames)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="resolve_hostnames" value="0" <?php echo $resolve_hostnames=='0' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr id='logRow2' class='row1' style="display: <?php echo $track_visitors==1 ? $displayStyle : 'none' ; ?>"> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["resolve_hostnames_message"] ?></td> 
          </tr> 
          <tr id='logRow3' style="display: <?php echo $track_visitors==1 ? $displayStyle : 'none' ; ?>"> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["top_howmany_title"] ?></b></td> 
            <td><input onChange="documentDirty=true;" type='text' maxlength='50' size="5" name="top_howmany" value="<?php echo isset($top_howmany) ? $top_howmany : 10 ; ?>"></td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["top_howmany_message"] ?></td> 
          </tr>
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["defaulttemplate_title"] ?></b></td> 
            <td>
			<?php
				$sql = "select templatename, id from $dbase.".$table_prefix."site_templates"; 
				$rs = mysql_query($sql); 
			?>
			  <select name="default_template" class="inputBox" onChange='documentDirty=true;' style="width:150px">
				<?php
				while ($row = mysql_fetch_assoc($rs)) {
						$selectedtext = $row['id']==$default_template ? "selected='selected'" : "" ;	
				?>
					<option value="<?php echo $row['id']; ?>" <?php echo $selectedtext; ?>><?php echo $row['templatename']; ?></option>
				<?php					
				}
				?>		
 			 </select>
			</td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["defaulttemplate_message"] ?></td> 
          </tr> 		  
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr>
          <tr> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["defaultpublish_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="publish_default" value="1" <?php echo $publish_default=='1' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="publish_default" value="0" <?php echo ($publish_default=='0' || !isset($publish_default)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["defaultpublish_message"] ?></td> 
          </tr> 	

          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["defaultcache_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="cache_default" value="1" <?php echo $cache_default=='1' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="cache_default" value="0" <?php echo ($cache_default=='0' || !isset($cache_default)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["defaultcache_message"] ?></td> 
          </tr> 		
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr>
          <tr> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["defaultsearch_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="search_default" value="1" <?php echo $search_default=='1' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="search_default" value="0" <?php echo ($search_default=='0' || !isset($search_default)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["defaultsearch_message"] ?></td> 
          </tr> 		
        </table> 
      </div> 
      <div class="tab-page" id="tabPage3"> 
        <h2 class="tab"><?php echo $_lang["settings_furls"] ?></h2> 
        <script type="text/javascript">tpSettings.addTabPage( document.getElementById( "tabPage3" ) );</script> 
        <table border="0" cellspacing="0" cellpadding="3"> 
          <tr> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["friendlyurls_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="friendly_urls" value="1" <?php echo $friendly_urls=='1' ? 'checked="checked"' : "" ; ?> onclick='showHide(/furlRow/, 1);'> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="friendly_urls" value="0" <?php echo ($friendly_urls=='0' || !isset($friendly_urls)) ? 'checked="checked"' : "" ; ?> onclick='showHide(/furlRow/, 0);'> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["friendlyurls_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr id='furlRow1' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>"> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["friendlyurlsprefix_title"] ?></b></td> 
            <td><input onChange="documentDirty=true;" type='text' maxlength='50' style="width: 200px;" name="friendly_url_prefix" value="<?php echo isset($friendly_url_prefix) ? $friendly_url_prefix : "p" ; ?>"></td> 
          </tr> 
          <tr id='furlRow2' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>"> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["friendlyurlsprefix_message"] ?></td> 
          </tr> 
          <tr id='furlRow3' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>"> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr id='furlRow4' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>"> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["friendlyurlsuffix_title"] ?></b></td> 
            <td><input onChange="documentDirty=true;" type='text' maxlength='50' style="width: 200px;" name="friendly_url_suffix" value="<?php echo isset($friendly_url_suffix) ? $friendly_url_suffix : ".html" ; ?>"></td> 
          </tr> 
          <tr id='furlRow5' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>"> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["friendlyurlsuffix_message"] ?></td> 
          </tr> 
          <tr id='furlRow6' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>"> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr id='furlRow7' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>"> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["friendly_alias_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="friendly_alias_urls" value="1" <?php echo $friendly_alias_urls=='1' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="friendly_alias_urls" value="0" <?php echo ($friendly_alias_urls=='0' || !isset($friendly_alias_urls)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr id='furlRow8' class='row1' style="display: <?php echo $friendly_urls==1 ? $displayStyle : 'none' ; ?>"> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["friendly_alias_message"] ?></td> 
          </tr> 
        </table> 
      </div> 
      <div class="tab-page" id="tabPage4"> 
        <h2 class="tab"><?php echo $_lang["settings_users"] ?></h2> 
        <script type="text/javascript">tpSettings.addTabPage( document.getElementById( "tabPage4" ) );</script> 
        <table border="0" cellspacing="0" cellpadding="3"> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["udperms_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="use_udperms" value="1" <?php echo $use_udperms=='1' ? 'checked="checked"' : "" ; ?> onclick='showHide(/udPerms/, 1);'> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="use_udperms" value="0" <?php echo ($use_udperms=='0' || !isset($use_udperms)) ? 'checked="checked"' : "" ; ?> onclick='showHide(/udPerms/, 0);'> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["udperms_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr>
          <tr id='udPermsRow1' class='row1' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>"> 
            <td nowrap class="warning"><b><?php echo $_lang["udperms_allowroot_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="udperms_allowroot" value="1" <?php echo $udperms_allowroot=='1' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="udperms_allowroot" value="0" <?php echo ($udperms_allowroot=='0' || !isset($udperms_allowroot)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr id='udPermsRow2' class='row1' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>"> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["udperms_allowroot_message"] ?></td> 
          </tr> 
          <tr id='udPermsRow3' style="display: <?php echo $use_udperms==1 ? $displayStyle : 'none' ; ?>"> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["captcha_title"] ?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="use_captcha" value="1" <?php echo $use_captcha=='1' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="use_captcha" value="0" <?php echo ($use_captcha=='0' || !isset($use_captcha)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["captcha_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["captcha_words_title"] ?></b></td> 
            <td><input name="captcha_words" style="width:400px" value="<?php echo isset($captcha_words) ? $captcha_words : "Alex,BitCode,Chunk,Design,Etomite,FinalFantasy,Gerry,Holiday,Join(),Kakogenic,Lightning,Maaike,Marit,Niche,Oscilloscope,Phusion,Query,Retail,Snippet,Template,USSEnterprise,Verily,Website,Ypsilon,Zebra" ; ?>" /></td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["captcha_words_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning" valign="top"><b><?php echo $_lang["signupemail_title"] ?></b></td> 
            <td> <textarea name="signupemail_message" style="width:100%; height: 120px;"><?php echo isset($signupemail_message) ? $signupemail_message : "Hi! \n\nHere are your login details for Etomite:\n\nUsername: %s\nPassword: %s\n\nOnce you log into Etomite, you can change your password.\n\nRegards,\nThe Management" ; ?></textarea> </td> 
          </tr>
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["signupemail_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["emailsender_title"] ?></b></td> 
            <td ><input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 200px;" name="emailsender" value="<?php echo isset($emailsender) ? $emailsender : "you@yourdomain.com" ; ?>"></td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["emailsender_message"] ?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["emailsubject_title"] ?></b></td> 
            <td ><input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 200px;" name="emailsubject" value="<?php echo isset($emailsubject) ? $emailsubject : "Your Etomite login details" ; ?>"></td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["emailsubject_message"] ?></td> 
          </tr>
        </table> 
      </div> 
      <div class="tab-page" id="tabPage5"> 
        <h2 class="tab"><?php echo $_lang["settings_ui"] ?></h2> 
        <script type="text/javascript">tpSettings.addTabPage( document.getElementById( "tabPage5" ) );</script> 
        <table border="0" cellspacing="0" cellpadding="3"> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["nologentries_title"]?></b></td> 
            <td><input onChange="documentDirty=true;" type='text' maxlength='50' size="5" name="number_of_logs" value="<?php echo isset($number_of_logs) ? $number_of_logs : 100 ; ?>"></td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["nologentries_message"]?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["nomessages_title"]?></b></td> 
            <td><input onChange="documentDirty=true;" type='text' maxlength='50' size="5" name="number_of_messages" value="<?php echo isset($number_of_messages) ? $number_of_messages : 30 ; ?>"></td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["nomessages_message"]?></td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["treetype_message"]?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["use_editor_title"]?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="use_editor" value="1" <?php echo ($use_editor=='1' || !isset($use_editor)) ? 'checked="checked"' : "" ; ?> onclick="checkIM(); showHide(/editorRow/, 1);"> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="use_editor" value="0" <?php echo $use_editor=='0' ? 'checked="checked"' : "" ; ?> onclick="showHide(/imRow/, 0); showHide(/editorRow/, 0);"> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["use_editor_message"]?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["which_editor_title"]?></b></td> 
            <td> 
				<select name="which_editor">
					<option value="1" <?php echo !isset($which_editor) || $which_editor==1 ? "selected='selected'" : "" ;?>>TinyMCE</option>
					<option value="2" <?php echo $which_editor==2 ? "selected='selected'" : "" ;?>>HTMLArea</option>
				</select>
			</td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["which_editor_message"]?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr id='editorRow10' class='row1' style="display: <?php echo $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td nowrap class="warning"><b><?php echo $_lang["use_strict_editor_title"]?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="strict_editor" value="1" <?php echo ($strict_editor=='1' || !isset($strict_editor)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="strict_editor" value="0" <?php echo $strict_editor=='0' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 		  
          <tr id='editorRow11' class='row1' style="display: <?php echo $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["use_strict_editor_message"]?></td> 
          </tr> 
          <tr id='editorRow12' class='row1' style="display: <?php echo $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr id='editorRow1' class='row1' style="display: <?php echo $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td nowrap class="warning"><b><?php echo $_lang["im_plugin_title"]?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="im_plugin" value="1" <?php echo ($im_plugin=='1' || !isset($im_plugin)) ? 'checked="checked"' : "" ; ?> onclick="showHide(/imRow/, 1);"> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="im_plugin" value="0" <?php echo $im_plugin=='0' ? 'checked="checked"' : "" ; ?> onclick="showHide(/imRow/, 0);"> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr id='editorRow2' class='row1' style="display: <?php echo $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["im_plugin_message"]?></td> 
          </tr> 
          <tr id='editorRow3' style="display: <?php echo $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr id='imRow1' class='row3' style="display: <?php echo $im_plugin==1 && $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td nowrap class="warning"><b><?php echo $_lang["im_plugin_base_dir_title"]?></b></td> 
            <td> <?php
function getImageBaseDir() {
	return str_replace("/manager/index.php", "/assets/images/", $_SERVER["PATH_TRANSLATED"]);
}
?> 
              <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="im_plugin_base_dir" value="<?php echo isset($im_plugin_base_dir) ? $im_plugin_base_dir : getImageBaseDir() ; ?>"> 
              <br /> </td> 
          </tr> 
          <tr id='imRow2' class='row3' style="display: <?php echo $im_plugin==1 && $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["im_plugin_base_dir_message"]?></td> 
          </tr> 
          <tr id='imRow3' style="display: <?php echo $im_plugin==1 && $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr id='imRow4' class='row3' style="display: <?php echo $im_plugin==1 && $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td nowrap class="warning"><b><?php echo $_lang["im_plugin_base_url_title"]?></b></td> 
            <td> <?php
function getImageBaseUrl() {
	return 'http://'.$_SERVER['SERVER_NAME'].str_replace("/manager/index.php", "/assets/images/", $_SERVER["PHP_SELF"]);
}
?> 
              <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="im_plugin_base_url" value="<?php echo isset($im_plugin_base_url) ? $im_plugin_base_url : getImageBaseUrl() ; ?>"> 
              <br /> </td> 
          </tr> 
          <tr id='imRow5' class='row3' style="display: <?php echo $im_plugin==1 && $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["im_plugin_base_url_message"]?></td> 
          </tr> 
          <tr id='imRow6' style="display: <?php echo $im_plugin==1 && $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr id='editorRow4' class='row1' style="display: <?php echo $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td nowrap class="warning"><b><?php echo $_lang["cm_plugin_title"]?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="cm_plugin" value="1" <?php echo $cm_plugin=='1' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="cm_plugin" value="0" <?php echo ($cm_plugin=='0' || !isset($cm_plugin)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr id='editorRow5' class='row1' style="display: <?php echo $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["cm_plugin_message"]?></td> 
          </tr> 
          <tr id='editorRow6' style="display: <?php echo $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr id='editorRow7' class='row1' style="display: <?php echo $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td nowrap class="warning"><b><?php echo $_lang["to_plugin_title"]?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="to_plugin" value="1" <?php echo $to_plugin=='1' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="to_plugin" value="0" <?php echo ($to_plugin=='0' || !isset($to_plugin)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr id='editorRow8' class='row1' style="display: <?php echo $use_editor==1 ? $displayStyle : 'none' ; ?>"> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["to_plugin_message"]?></td> 
          </tr>
		  <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["tiny_css_path_title"]?></b></td> 
            <td><input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="tiny_css_path" value="<?php echo isset($tiny_css_path) ? $tiny_css_path : "" ; ?>"> 
			</td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["tiny_css_path_message"]?></td> 
          </tr> 
		  <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["tiny_css_selectors_title"]?></b></td> 
            <td><input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="tiny_css_selectors" value="<?php echo isset($tiny_css_selectors) ? $tiny_css_selectors : "" ; ?>"> 
			</td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["tiny_css_selectors_message"]?></td> 
          </tr> 
        </table> 
      </div> 
      <div class="tab-page" id="tabPage7"> 
        <h2 class="tab"><?php echo $_lang["settings_misc"] ?></h2> 
        <script type="text/javascript">tpSettings.addTabPage( document.getElementById( "tabPage7" ) );</script> 
        <table border="0" cellspacing="0" cellpadding="3"> 
		  <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["settings_strip_image_paths_title"]?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="strip_image_paths" value="1" <?php echo $strip_image_paths=='1' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["yes"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="strip_image_paths" value="0" <?php echo ($strip_image_paths=='0' || !isset($strip_image_paths)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["no"]?> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["settings_strip_image_paths_message"]?></td> 
          </tr>
		  <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["filemanager_path_title"]?></b></td> 
            <td> <?php
function getEtomiteRoot() {
	return str_replace("/manager/index.php", "", $_SERVER["PATH_TRANSLATED"]);
}
?> 
              <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="filemanager_path" value="<?php echo isset($filemanager_path) ? $filemanager_path : getEtomiteRoot() ; ?>"> 
              <br /> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["filemanager_path_message"]?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["uploadable_files_title"]?></b></td> 
            <td>
              <input onChange="documentDirty=true;" type='text' maxlength='255' style="width: 300px;" name="upload_files" value="<?php echo isset($upload_files) ? $upload_files : "jpg,gif,png,ico,txt,php,html,htm,xml,js,css,cache,zip,gz,rar,z,tgz,tar,htaccess,bmp,mp3,wav,au,wmv,avi,mpg,mpeg,pdf,psd" ; ?>"> 
            </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["uploadable_files_message"]?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr>
          <?php if($_SESSION['browser']=='ie') { ?> 
          <tr> 
            <td nowrap class="warning"><b><?php echo $_lang["layout_title"]?></b></td> 
            <td> <input onChange="documentDirty=true;" type="radio" name="manager_layout" value="1" <?php echo ($manager_layout=='1' || !isset($manager_layout)) ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["layout_settings_1"]?><br /> 
              <input onChange="documentDirty=true;" type="radio" name="manager_layout" value="0" <?php echo $manager_layout=='0' ? 'checked="checked"' : "" ; ?>> 
              <?php echo $_lang["layout_settings_2"]?><br /> </td> 
          </tr> 
          <tr> 
            <td width="200">&nbsp;</td> 
            <td class='comment'><?php echo $_lang["layout_message"]?></td> 
          </tr> 
          <tr> 
            <td colspan="2"><div class='split'></div></td> 
          </tr>  
          <?php } else { ?> 
          <input onChange="documentDirty=true;" type="hidden" name="manager_layout" value="1"> 
          <?php } ?> 
        </table> 
      </div> 
    </div> 
  </form> 
</div> 
