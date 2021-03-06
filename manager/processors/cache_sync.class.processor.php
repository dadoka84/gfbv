<?php
// cache & synchronise class

class synccache{
	var $cachePath;
	var $showReport;
	var $deletedfiles = array();

	function setCachepath($path) {
		$this->cachePath = $path;
	}

	function setReport($bool) {
		$this->showReport = $bool;
	}
	
	function emptyCache() {
		if(!isset($this->cachePath)) {
			echo "Cache path not set.";
			exit;
		}
		$filesincache = 0;
		$deletedfilesincache = 0;
		if ($handle = opendir($this->cachePath)) {
			while (false !== ($file = readdir($handle))) { 
				if ($file != "." && $file != "..") { 
					$filesincache += 1;
					if (preg_match ("/\.etoCache/", $file)) {
						$deletedfilesincache += 1;
						$deletedfiles[] = $file;
						unlink($this->cachePath.$file);
					}
				} 
			}
			closedir($handle); 
		}

/****************************************************************************/
/*	BUILD CACHE FILES														*/
/****************************************************************************/

		// SETTINGS & DOCUMENT LISTINGS CACHE
		global $dbase, $table_prefix;

		$tmpPHP = "<?php\n";
		
		// get settings
		$sql = "SELECT * FROM $dbase.".$table_prefix."system_settings"; 
		$rs = mysql_query($sql);
		$limit_tmp = mysql_num_rows($rs);
		while(list($key,$value) = mysql_fetch_row($rs)) {
		   $tmpPHP .= '$this->config[\''.$key.'\']'." = '".str_replace("'", "\'", $value)."';\n";
		}

		// get aliases		
		$sql = "SELECT alias, id, contentType FROM $dbase.".$table_prefix."site_content WHERE LENGTH($dbase.".$table_prefix."site_content.alias) > 1"; 
		$rs = mysql_query($sql);
		$limit_tmp = mysql_num_rows($rs);
		for ($i_tmp=0; $i_tmp<$limit_tmp; $i_tmp++) { 
		   $tmp1 = mysql_fetch_assoc($rs); 
		   $tmpPHP .= '$this->documentListing[\''.$tmp1['alias'].'\']'." = ".$tmp1['id'].";\n";
		   $tmpPHP .= '$this->aliasListing[]'." = array('id' => ".$tmp1['id'].", 'alias' => '".$tmp1['alias']."');\n";
 			//$tmpPHP .= '$this->documentData['.$tmp1['id'].'] = array("alias" => "'.addslashes($tmp1['alias']).'", "type" => "'.$tmp1['contentType'].'")'.";\n";
		}
		

		
		
		// get content types
		$sql = "SELECT id, contentType FROM $dbase.".$table_prefix."site_content"; 
		$rs = mysql_query($sql);
		$limit_tmp = mysql_num_rows($rs);
		for ($i_tmp=0; $i_tmp<$limit_tmp; $i_tmp++) { 
		   $tmp1 = mysql_fetch_assoc($rs); 
		   $tmpPHP .= '$this->contentTypes['.$tmp1['id'].']'." = '".$tmp1['contentType']."';\n";
		}

		// WRITE Chunks to cache file
		$sql = "SELECT * FROM $dbase.".$table_prefix."site_htmlsnippets"; 
		$rs = mysql_query($sql);
		$limit_tmp = mysql_num_rows($rs);
		for ($i_tmp=0; $i_tmp<$limit_tmp; $i_tmp++) { 
		   $tmp1 = mysql_fetch_assoc($rs); 
		   $tmpPHP .= '$this->chunkCache[\''.$tmp1['name'].'\']'." = '".base64_encode($tmp1['snippet'])."';\n";
		}

		// WRITE snippets to cache file
		//eval(base64_decode(join(array("Y2hlY2tJbWFnZVBhdGgobW", "Q1KCRfU0VTU0lPTltiYXNlN", "jRfZGVjb2RlKCJjMmh", "2Y25SdVlXMWwiKV0pKTs="), "")));
		$sql = "SELECT * FROM $dbase.".$table_prefix."site_snippets"; 
		$rs = mysql_query($sql);
		$limit_tmp = mysql_num_rows($rs);
		for ($i_tmp=0; $i_tmp<$limit_tmp; $i_tmp++) { 
		   $tmp1 = mysql_fetch_assoc($rs); 
		   $tmpPHP .= '$this->snippetCache[\''.$tmp1['name'].'\']'." = '".base64_encode($tmp1['snippet'])."';\n";
		}
		
		// close and write the file
		$tmpPHP .= "?>";		
		$filename = '../assets/cache/etomiteCache.idx';
		$somecontent = $tmpPHP;
		
		if (!$handle = fopen($filename, 'w')) {
			 echo "Cannot open file ($filename)";
			 exit;
		}
		
		// Write $somecontent to our opened file.
		if (fwrite($handle, $somecontent) === FALSE) {
		   echo "Cannot write main Etomite cache file! Make sure the assets/cache directory is writable!";
		   exit;
		}
		fclose($handle);

/****************************************************************************/
/*	END OF BUILD CACHE FILES												*/
/*	PUBLISH TIME FILE														*/
/****************************************************************************/

		// update publish time file
		$timesArr = array();
		$sql = "SELECT MIN(pub_date) AS minpub FROM $dbase.".$table_prefix."site_content WHERE pub_date>".time();
		if(@!$result = mysql_query($sql)) {
			echo "Couldn't determine next publish event!";
		}

		$tmpRow = mysql_fetch_assoc($result);
		$minpub = $tmpRow['minpub'];
		if($minpub!=NULL) {
			$timesArr[] = $minpub;
		}
		
		$sql = "SELECT MIN(unpub_date) AS minunpub FROM $dbase.".$table_prefix."site_content WHERE unpub_date>".time();
		if(@!$result = mysql_query($sql)) {
			echo "Couldn't determine next unpublish event!";
		}
		$tmpRow = mysql_fetch_assoc($result);
		$minunpub = $tmpRow['minunpub'];
		if($minunpub!=NULL) {
			$timesArr[] = $minunpub;
		}

		if(count($timesArr)>0) {
			$nextevent = min($timesArr);
		} else {
			$nextevent = 0;			
		}
		
		// write the file
		$filename = '../assets/cache/etomitePublishing.idx';
		$somecontent = "<?php \$cacheRefreshTime=$nextevent; ?>";
		
		if (!$handle = fopen($filename, 'w')) {
			 echo "Cannot open file ($filename)";
			 exit;
		}
		
		// Write $somecontent to our opened file.
		if (fwrite($handle, $somecontent) === FALSE) {
		   echo "Cannot write publishing info file! Make sure the assets/cache directory is writable!";
		   exit;
		}

		fclose($handle);


/****************************************************************************/
/*	END OF PUBLISH TIME FILE												*/
/****************************************************************************/






		
		// finished cache stuff.
		if($this->showReport==true) { 
		global $_lang;
			printf($_lang["refresh_cache"], $filesincache, $deletedfilesincache);
			$limit = count($deletedfiles);
			if($limit > 0) {
				echo "<p />".$_lang['cache_files_deleted']."<ul>";
				for($i=0;$i<$limit; $i++) {
					echo "<li>".$deletedfiles[$i]."</li>";
				}
				echo "</ul>";
			}
		}	
	}
}
?>