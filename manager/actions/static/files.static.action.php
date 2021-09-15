<?php 
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");

// settings
$excludes = array(".", "..", "cgi-bin", "manager");
$editablefiles = array(".txt", ".php", ".html", ".htm", ".xml", ".js", ".css", ".etoCache", $friendly_url_suffix);
$inlineviewablefiles = array(".txt", ".php", ".html", ".htm", ".xml", ".js", ".css", ".etoCache", $friendly_url_suffix);
$viewablefiles = array(".jpg", ".gif", ".png", ".ico");
$uploadablefiles = split(",", $upload_files);
$count = count($uploadablefiles);
for($i=0; $i<$count; $i++) {
	$uploadablefiles[$i] = ".".$uploadablefiles[$i]; // add a dot :)
}
// end settings

function ufilesize($size) {
	$a = array("B", "KB", "MB", "GB", "TB", "PB");
	$pos = 0;
	while ($size >= 1024) {
		   $size /= 1024;
		   $pos++;
	}
	return round($size,2)." ".$a[$pos];
}

function removeLastPath($string) {
   $pos = false;
   $search = "/";
   if (is_int(strpos($string, $search))) {
	   $endPos = strlen($string);
	   while ($endPos > 0) {
		   $endPos = $endPos - 1;
		   $pos = strpos($string, $search, $endPos);
		   if (is_int($pos)) {
			   break;
		   }
	   } 
   }
   if (is_int($pos)) {
	   $len = strlen($search);
	   return substr($string, 0, $pos);
   }
	return $string;
}

function getExtension($string) {
   $pos = false;
   $search = ".";
   if (is_int(strpos($string, $search))) {
	   $endPos = strlen($string);
	   while ($endPos > 0) {
		   $endPos = $endPos - 1;
		   $pos = strpos($string, $search, $endPos);
		   if (is_int($pos)) {
			   break;
		   }
	   } 
   }
   if (is_int($pos)) {
	   $len = strlen($search);
	   return substr($string, $pos);
   }
	return $string;
}

function fsize($file) {
       $a = array("B", "KB", "MB", "GB", "TB", "PB");
       $pos = 0;
       $size = filesize($file);
       while ($size >= 1024) {
               $size /= 1024;
               $pos++;
       }
       return round($size,2)." ".$a[$pos];
} 

// get the current work directory
if($_REQUEST['path']!="") {
	$startpath = is_dir($_REQUEST['path']) ? $_REQUEST['path'] : removeLastPath($_REQUEST['path']) ;
} else {
	$startpath = $filemanager_path;
}

$len = strlen($filemanager_path);
?>
<div class="subTitle">
	<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $site_name ;?> - <?php echo $_lang['files_title']; ?></span>
</div>

<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['files_files']; ?></div><div class="sectionBody">

<script type="text/javascript">
function viewfile(url) {
	document.getElementById('imageviewer').style.border="1px solid #000080";
	document.getElementById('imageviewer').src=url;
}

function confirmDelete(url) {
	if(confirm("<?php echo $_lang['confirm_delete_file'] ?>")) {
		document.location.href=url;
	}
}
</script>
<?php
if(isset($_FILES['userfile']['tmp_name'])) {
		// this seems to be an upload action.
printf($_lang['files_uploading'], $_FILES['userfile']['name'], substr($startpath, $len, strlen($startpath)));
echo $_FILES['userfile']['error']==0 ? $_lang['files_file_type'].$_FILES['userfile']['type'].", ".fsize($_FILES['userfile']['tmp_name'])."<br />" : "" ;

	$userfile = $_FILES['userfile']['tmp_name'];

	if (is_uploaded_file($userfile)) {
	  // file is uploaded file, process it!
		if(!in_array(getExtension($_FILES['userfile']['name']), $uploadablefiles)) {
			echo "<br /><span class='warning'>".$_lang['files_filetype_notok']."</span><br />";
		} else {
			if(@move_uploaded_file($_FILES['userfile']['tmp_name'], $_POST['path']."/".$_FILES['userfile']['name'])) {
					echo "<br /><span class='success'>".$_lang['files_upload_ok']."</span><br />";				
			} else {
				echo "<br /><span class='warning'>".$_lang['files_upload_copy_failed']."</span> Possible permission problems - the directory you want to upload to needs to be set to 0777 permissions.<br />";
			}
		}
	}else{
		echo "<br /><span class='warning'><b>".$_lang['files_upload_error'].":</b> ";
	  switch($_FILES['userfile']['error']){
	   case 0: //no error; possible file attack!
		 echo $_lang['files_upload_error0'];
		 break;
	   case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
		 echo $_lang['files_upload_error1'];
		 break;
	   case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
		 echo $_lang['files_upload_error2'];
		 break;
	   case 3: //uploaded file was only partially uploaded
		 echo $_lang['files_upload_error3'];
		 break;
	   case 4: //no file was uploaded
		 echo $_lang['files_upload_error4'];
		 break;
	   default: //a default error, just in case!  :)
		 echo $_lang['files_upload_error5'];
		 break;
		}
		echo "</span><br />";
	}
	echo "<hr/>";
}

if($_POST['mode']=="save") {
	echo $_lang['editing_file'];
	$filename = $_POST['path'];
	$content = $_POST['content'];
	if (!$handle = fopen($filename, 'w')) {
		 echo "Cannot open file ($filename)";
		 exit;
	}
	
	// Write $content to our opened file.
	if (fwrite($handle, $content) === FALSE) {
	   echo "<span class='warning'><b>".$_lang['file_not_saved']."</b></span><br /><br />";
	} else {
	   echo "<span class='success'><b>".$_lang['file_saved']."</b></span><br /><br />";
	   $_REQUEST['mode'] = "edit";
	}
	fclose($handle);
}


if($_REQUEST['mode']=="delete") {
	printf($_lang['deleting_file'], str_replace('\\', '/', $_REQUEST['path']));
	$file = $_REQUEST['path'];
	if (!@unlink($file)) {
	   echo "<span class='warning'><b>".$_lang['file_not_deleted']."</b></span><br /><br />";
	} else {
	   echo "<span class='success'><b>".$_lang['file_deleted']."</b></span><br /><br />";
	}	
}


echo $_lang['files_dir_listing']; ?><b><?php echo substr($startpath, $len, strlen($startpath))=="" ? "/" : substr($startpath, $len, strlen($startpath)) ; ?></b><br /><br />
<?php
// check to see user isn't trying to move below the document_root
if(substr(strtolower($startpath), 0, $len)!=strtolower($filemanager_path)) {
?>
<?php echo $_lang['files_access_denied']; ?>
</div>

<?php
	exit;
}

$uponelevel = removeLastPath($startpath);

if($startpath==$filemanager_path) {
	echo "<img src='media/images/tree/deletedfolder.gif' border=0 align='absmiddle'><span style='color:#bbb;cursor:default;'> <b>".$_lang['files_top_level']."</b></span><br />\n";
} else {
	echo "<img src='media/images/tree/folder.gif' border=0 align='absmiddle'> <a href='index.php?a=31&mode=drill&path=".$_SERVER['DOCUMENT_ROOT']."'><b>".$_lang['files_top_level']."</b></a><br />\n";
}

if($startpath==$filemanager_path) {
	echo "<img src='media/images/tree/deletedfolder.gif' border=0 align='absmiddle'><span style='color:#bbb;cursor:default;'> <b>".$_lang['files_up_level']."</b></span><br />\n";
} else {
	echo "<a href='index.php?a=31&mode=drill&path=$uponelevel'><img src='media/images/tree/folder.gif' border=0 align='absmiddle'> <b>".$_lang['files_up_level']."</b></a><br />\n";
}
echo "<br />";

$filesize = 0;
$files = 0;
$folders = 0;
$dirs_array = array();
$files_array = array();


function ls ($curpath) {
	global $excludes, $editablefiles, $inlineviewablefiles, $viewablefiles, $folders, $files, $filesizes, $len, $dirs_array, $files_array;
	$dircounter = 0;
	$filecounter = 0;

	$dir = dir($curpath);

	// first, get info
	while ($file = $dir->read()) {
		if(!in_array($file, $excludes)) {
			$newpath = $curpath."/".$file;
			if(is_dir($newpath)) {
				$dirs_array[$dircounter]['dir'] = $newpath;
				$dirs_array[$dircounter]['stats'] = lstat($newpath);
				$dirs_array[$dircounter]['text'] = "<img src='media/images/tree/folder.gif' border=0 align='absmiddle'> <a href='index.php?a=31&mode=drill&path=$newpath'><b style=' font-size: 11px;'>$file</b></a>";

				// increment the counter
				$dircounter++;
			}  else {
				$type=getExtension($newpath);
				$files_array[$filecounter]['file'] = $newpath;
				$files_array[$filecounter]['stats'] = lstat($newpath);
				$files_array[$filecounter]['text'] = "<img src='media/images/tree/page.gif' border=0 align='absmiddle'> $file";
				$files_array[$filecounter]['view'] = (in_array($type, $viewablefiles)) ? "<span style='cursor:pointer; width:20px;' onClick='viewfile(\"".substr($newpath, $len, strlen($newpath))."\");'><img src='media/images/icons/context_view.gif' border=0 align='absmiddle'></span>" : "<span class='disabledImage'><img src='media/images/icons/context_view.gif' border=0 align='absmiddle'></span>" ;
				$files_array[$filecounter]['view'] = (in_array($type, $inlineviewablefiles)) ? "<span style='width:20px;'><a href='index.php?a=31&mode=view&path=$newpath'><img src='media/images/icons/context_view.gif' border=0 align='absmiddle'></a></span>" : $files_array[$filecounter]['view'] ;
				$files_array[$filecounter]['edit'] = (in_array($type, $editablefiles) && is_writable($curpath) && is_writable($newpath)) ? "<span style='width:20px;'><a href='index.php?a=31&mode=edit&path=$newpath'><img src='media/images/icons/save.gif' border=0 align='absmiddle'></a></span>" : "<span class='disabledImage'><img src='media/images/icons/save.gif' border=0 align='absmiddle'></span>" ;
				$files_array[$filecounter]['delete'] = is_writable($curpath) && is_writable($newpath) ? "<span style='width:20px;'><a href='javascript:confirmDelete(\"".addslashes("index.php?a=31&mode=delete&path=$newpath")."\");'><img src='media/images/icons/delete.gif' border=0 align='absmiddle'></a></span>" : "<span class='disabledImage'><img src='media/images/icons/delete.gif' border=0 align='absmiddle'></span>" ;

				// increment the counter
				$filecounter++;
			}
		}
	}
	$dir->close();

	// dump array entries for directories
	$folders = count($dirs_array);
	for($i=0; $i<$folders; $i++) {
		$filesizes += $dirs_array[$i]['stats']['7'];
		echo "<div style='position: relative; float: left; width: 300px; font-size: 11px;'>".$dirs_array[$i]['text']."</div>";
		echo "<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'>".strftime('%d-%m-%y, %H:%M:%S', $dirs_array[$i]['stats']['9'])."</div>";
		echo "<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'>".ufilesize($dirs_array[$i]['stats']['7'])."</div>";		
		echo "<br clear='all' />\n";
	}

	// dump array entries for files
	$files = count($files_array);
	for($i=0; $i<$files; $i++) {
		$filesizes += $files_array[$i]['stats']['7'];
		echo "<div style='position: relative; float: left; width: 300px; font-size: 11px;'>".$files_array[$i]['text']."</div>";
		echo "<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'>".strftime('%d-%m-%y, %H:%M:%S', $files_array[$i]['stats']['9'])."</div>";
		echo "<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'>".ufilesize($files_array[$i]['stats']['7'])."</div>";		
		echo "<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'>";
		echo $files_array[$i]['view'];
		echo $files_array[$i]['edit'];
		echo $files_array[$i]['delete'];
		echo "</div>";
		
		echo "<br clear='all' />\n";
	}

	
	return;
}
echo "\n\n\n\n\n\n\n";
?>
<div style='position: relative; float: left; width: 300px; font-size: 11px;'><b><?php echo $_lang['files_filename']; ?></b></div>
<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'><b><?php echo $_lang['files_modified']; ?></b></div>
<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'><b><?php echo $_lang['files_filesize']; ?></b></div>
<div style='position: relative; float: left; width: 120px; text-align:right; font-size: 11px;'><b><?php echo $_lang['files_fileoptions']; ?></b></div>
<br />
<?php
ls($startpath); 
echo "\n\n\n\n\n\n\n";
if($folders==0 && $files==0) {
	echo "<img src='media/images/tree/deletedfolder.gif' border=0 align='absmiddle'><span style='color:#888;cursor:default;'> This directory is empty.</span><br />\n";
}

echo "<br />";
echo "<div style='position: relative; float: left; width: 140px;'>".$_lang['files_directories'].":</div><b>$folders</b><br />";
echo "<div style='position: relative; float: left; width: 140px;'>".$_lang['files_files'].":</div><b>$files</b><br />";
echo "<div style='position: relative; float: left; width: 140px;'>".$_lang['files_data'].":</div><b>".ufilesize($filesizes)."</b><br />";
?>
<span style='position: relative; float: left; width: 140px;'><?php echo $_lang['files_dirwritable']; ?></span><b><?php echo is_writable($startpath)==1 ? "Yes." : "No."; ?></b><br />
<div align="center">
<img src="media/images/_tx_.gif" id='imageviewer'>
</div>
<hr>
<?php
if (((@ini_set("file_uploads", 1) === true) || get_cfg_var("file_uploads") == 1) && is_writable($startpath)) {
	@ini_set("upload_max_filesize", $uploadmaxsize);
?>
<form enctype="multipart/form-data" action="index.php?a=31" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="1048576"><input type="hidden" name="path" value="<?php echo $startpath; ?>">
	<b><?php echo $_lang['files_uploadfile']; ?></b><br />
	<span style="width:300px;"><?php echo $_lang['files_uploadfile_msg']; ?></span><input name="userfile" type="file" style="height: 19px;"><input type="submit" value="<?php echo $_lang['files_uploadfile']; ?>">
</form>
<?php
} else {
	echo $_lang['files_upload_inhibited_msg'];
}
?>


</div>

<?php 

if($_REQUEST['mode']=="edit" || $_REQUEST['mode']=="view") {
?>
<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_REQUEST['mode']=="edit" ? $_lang['files_editfile'] : $_lang['files_viewfile'] ; ?></div><div class="sectionBody">
<?php
$filename=$_REQUEST['path'];
$handle = @fopen($filename, "r");
if(!$handle) {
	echo 'Error opening file for reading.';
	exit;
} else {
	while (!feof($handle)) {
		$buffer .= fgets($handle, 4096);
	}
	fclose ($handle);
}

?>
<form action="index.php" method="post" name="editFile">
<input type="hidden" name="a" value="31" />
<input type="hidden" name="mode" value="save" />
<input type="hidden" name="path" value="<?php echo $_REQUEST['path']; ?>" />
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><textarea style="width:100%; height:370px;" name="content"><?php echo htmlentities($buffer); ?></textarea></td>
  </tr>
</table>
</form>
<?php

if($_REQUEST['mode']=="edit") {
?>
<p />
<table cellpadding="0" cellspacing="0">
	<td id="Button1" onaction="document.editFile.submit();"><img src="media/images/icons/save.gif" align="absmiddle"> <?php echo $_lang["save"]; ?></td>
		<script>createButton(document.getElementById("Button1"));</script>
	<td id="Button2" onaction="document.location.href='index.php?a=31';"><img src="media/images/icons/cancel.gif" align="absmiddle"> <?php echo $_lang["cancel"]; ?></td>
		<script>createButton(document.getElementById("Button2"));</script>
</table>
<?php } ?>
</div>
<?php
}
?>