<?php

// first, set some settings, and do some stuff >.<
$mtime = microtime(); $mtime = explode(" ",$mtime); $mtime = $mtime[1] + $mtime[0]; $tstart = $mtime;
@ini_set('session.use_trans_sid', false);
@ini_set("url_rewriter.tags","");
header('P3P: CP="NOI NID ADMa OUR IND UNI COM NAV"'); // header for weird cookie stuff. Blame IE.
ob_start();
error_reporting(E_ALL);
session_start();



/***************************************************************************
 Class Name: etomite
 Function: This class contains the main parsing functions
/***************************************************************************/

class etomite {
	var $db, $rs, $result, $sql, $table_prefix, $config, $debug,
		$documentIdentifier, $documentMethod, $documentGenerated, $documentContent, $tstart,
		$snippetParsePasses, $documentObject, $templateObject, $snippetObjects,
		$stopOnNotice, $executedQueries, $queryTime, $currentSnippet, $documentName,
		$aliases, $visitor, $entrypage, $documentListing, $dumpSnippets, $chunkCache, 
		$snippetCache, $contentTypes, $dumpSQL, $queryCode;
	
	function etomite() {
		$this->dbConfig['host'] = $GLOBALS['database_server'];
		$this->dbConfig['dbase'] = $GLOBALS['dbase'];
		$this->dbConfig['user'] = $GLOBALS['database_user'];
		$this->dbConfig['pass'] = $GLOBALS['database_password'];
		$this->dbConfig['table_prefix'] = $GLOBALS['table_prefix'];
	}

	function checkCookie() {
		if(isset($_COOKIE['etomiteLoggingCookie'])) {
			$this->visitor = $_COOKIE['etomiteLoggingCookie'];
			if(isset($_SESSION['_logging_first_hit'])) {
				$this->entrypage = 0;
			} else {
				$this->entrypage = 1;
				$_SESSION['_logging_first_hit'] = 1;
			}
		} else {
			if (function_exists('posix_getpid')) {
			  $visitor = crc32(microtime().posix_getpid());
			} else {
			  $visitor = crc32(microtime().session_id());
			}
			$this->visitor = $visitor;
			$this->entrypage = 1;
			setcookie('etomiteLoggingCookie', $visitor, time()+(365*24*60*60), '/', '');
		}
	}


	function getMicroTime() { 
	   list($usec, $sec) = explode(" ", microtime()); 
	   return ((float)$usec + (float)$sec); 
	}

	function sendRedirect($url, $count_attempts=0, $type='') {
		if(empty($url)) {
			return false;
		} else {
			if($count_attempts==1) {
			// append the redirect count string to the url
				$currentNumberOfRedirects = isset($_REQUEST['err']) ? $_REQUEST['err'] : 0 ;
				if($currentNumberOfRedirects>3) {
					$this->messageQuit("Redirection attempt failed - please ensure the document you're trying to redirect to exists. Redirection URL: <i>$url</i>");
				} else {
					$currentNumberOfRedirects += 1;
					if(strpos($url, "?")>0) {
						$url .= "&err=$currentNumberOfRedirects";
					} else {
						$url .= "?err=$currentNumberOfRedirects";
					}
				}
			}
			if($type==REDIRECT_REFRESH) {
				$header = "Refresh: 0;URL=".$url;
			} elseif($type==REDIRECT_META) {
				$header = "<META HTTP-EQUIV=Refresh CONTENT='0; URL=".$url."' />";
				echo $header;
				exit;
			} elseif($type==REDIRECT_HEADER || empty($type)) {
				$header = "Location: $url";
			}
			header($header);
			$this->postProcess();
		}
	}
	
	function dbConnect() {
	// function to connect to the database
		$tstart = $this->getMicroTime(); 
		if(@!$this->rs = mysql_connect($this->dbConfig['host'], $this->dbConfig['user'], $this->dbConfig['pass'])) {
			$this->messageQuit("Failed to create the database connection!");
		} else {
			mysql_select_db($this->dbConfig['dbase']);
			$tend = $this->getMicroTime(); 
			$totaltime = $tend-$tstart;
			if($this->dumpSQL) {
				$this->queryCode .= "<fieldset style='text-align:left'><legend>Database connection</legend>".sprintf("Database connection was created in %2.4f s", $totaltime)."</fieldset><br />";
			}
			$this->queryTime = $this->queryTime+$totaltime;
		}
	}
	
	function dbQuery($query) {
	// function to query the database
		// check the connection and create it if necessary
		if(empty($this->rs)) {
			$this->dbConnect();
		}
		$tstart = $this->getMicroTime(); 
		if(@!$result = mysql_query($query, $this->rs)) {
			$this->messageQuit("Execution of a query to the database failed", $query);
		} else {
			$tend = $this->getMicroTime(); 
			$totaltime = $tend-$tstart;
			$this->queryTime = $this->queryTime+$totaltime;
			if($this->dumpSQL) {
				$this->queryCode .= "<fieldset style='text-align:left'><legend>Query ".($this->executedQueries+1)." - ".sprintf("%2.4f s", $totaltime)."</legend>".$query."</fieldset><br />";
			}
			$this->executedQueries = $this->executedQueries+1;
			return $result;
		}
	}
	
	function recordCount($rs) {
	// function to count the number of rows in a record set
		return mysql_num_rows($rs);
	}
	
	function fetchRow($rs, $mode='assoc') {
		if($mode=='assoc') {
			return mysql_fetch_assoc($rs);
		} elseif($mode=='num') {
			return mysql_fetch_row($rs);
		} elseif($mode=='both') {
			return mysql_fetch_array($rs, MYSQL_BOTH);		
		} else {
			$this->messageQuit("Unknown get type ($mode) specified for fetchRow - must be empty, 'assoc', 'num' or 'both'.");
		}
	}
	
	function affectedRows($rs) {
		return mysql_affected_rows($rs);
	}
	
	function insertId($rs) {
		return mysql_insert_id($rs);
	}
	
	function dbClose() {
	// function to close a database connection
		mysql_close($this->rs);
	}
     
	function getSettings() {
		if(file_exists("assets/cache/etomiteCache.idx")) {
			include_once "assets/cache/etomiteCache.idx";
		} else {
			$result = $this->dbQuery("SELECT setting_name, setting_value FROM ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."system_settings");
			while ($row = $this->fetchRow($result, 'both')) {
				$this->config[$row[0]] = $row[1];
			}			
		}
	} 
	
	function getDocumentMethod() {
	// function to test the query and find the retrieval method
		if(isset($_REQUEST['q'])) {
			return "alias";
		} elseif(isset($_REQUEST['id'])) {
			return "id";
		} else {
			return "none";
		}
	}

	function getDocumentIdentifier($method) {
	// function to test the query and find the retrieval method
		switch($method) {
			case "alias" :
				return $_REQUEST['q'];
				break;
			case "id" :
				return $_REQUEST['id'];
				break;
			case "none" :
				return $this->config['site_start'];
				break;
			default :
				return $this->config['site_start'];
		}			
	}

	function checkSession() {
		if(isset($_SESSION['validated'])) {
			return true;
		} else  {
			return false;
		}
	}

	function checkPreview() {
		if($this->checkSession()==true) {
			if(isset($_REQUEST['z']) && $_REQUEST['z']=='manprev') {
				return true;
			} else {
				return false;
			}
		} else  {
			return false;
		}
	}

	function checkSiteStatus() {
		$siteStatus = $this->config['site_status'];
		if($siteStatus==1) {
			return true;
		} elseif($siteStatus==0 && $this->checkSession()) {
			return true;
		} else {
			return false;
		}
	}

	function cleanDocumentIdentifier($qOrig) {
		$q = str_replace($this->config['friendly_url_prefix'], "", $qOrig);
		$q = str_replace($this->config['friendly_url_suffix'], "", $q);
		if(strpos($q, "/")>0) {
			$q = substr($q, 0, strpos($q, "/"));
		}
		if(is_numeric($q)) { // we got an ID returned
			$this->documentMethod = 'id';
			return $q;
		} else { // we didn't get an ID back, so instead we assume it's an alias
			if($this->config['friendly_alias_urls']==1) {
				$q = str_replace($this->config['friendly_url_prefix'], "", $qOrig);
				$q = str_replace($this->config['friendly_url_suffix'], "", $q);
				if(strpos($q, "/")>0) {
					$q = substr($q, 0, strpos($q, "/"));
				}
			} else {
				$q = $qOrig;
			}
			$this->documentMethod = 'alias';
			return $q;
		}
	}

	function checkCache($id) {
		$cacheFile = "assets/cache/docid_".$id.".etoCache";
		if(file_exists($cacheFile)) {
			$this->documentGenerated=0;
			return join("",file($cacheFile));
		} else {
			$this->documentGenerated=1;
			return "";		
		}
	}

	function addNotice($content, $type="text/html") {

		$notice	  = "\n \n\n".
					"\t \n\n";
		if($type=="text/html") {		
			$notice .=	" \n\n";
		} else {
			$notice .=	"\t \n\n".
						"-->";
		}
		// insert the message into the document
		if(strpos($content, "</body>")>0) {
			$content = str_replace("</body>", $notice."</body>", $content);
		} elseif(strpos($content, "</BODY>")>0) {
			$content = str_replace("</body>", $notice."</BODY>", $content);		
		} else {
			$content .= $notice;
		}
		return $content;
	}
	
	function outputContent() {

		$output = $this->documentContent;
		
		// check for non-cached snippet output
		if(strpos($output, '[!')>-1) {
			$output = str_replace('[!', '[[', $output);
			$output = str_replace('!]', ']]', $output);
			
			$this->nonCachedSnippetParsePasses = empty($this->nonCachedSnippetParsePasses) ? 1 : $this->nonCachedSnippetParsePasses ;
			for($i=0; $i<$this->nonCachedSnippetParsePasses; $i++) {
				if($this->dumpSnippets==1) {
					echo "<fieldset style='text-align: left'><legend>NONCACHED PARSE PASS ".($i+1)."</legend>The following snipppets (if any) were parsed during this pass.<div style='width:100%' align='center'>";
				}
				// replace settings referenced in document
				$output = $this->mergeSettingsContent($output);
				// replace HTMLSnippets in document
				$output = $this->mergeHTMLSnippetsContent($output);				
				// find and merge snippets
				$output = $this->evalSnippets($output);
				if($this->dumpSnippets==1) {
					echo "</div></fieldset><br />";
				}
			}
		}		
		
		$output = $this->rewriteUrls($output);
		
		$totalTime = ($this->getMicroTime() - $this->tstart);
		$queryTime = $this->queryTime;
		$phpTime = $totalTime-$queryTime;

		$queryTime = sprintf("%2.4f s", $queryTime);
		$totalTime = sprintf("%2.4f s", $totalTime);
		$phpTime = sprintf("%2.4f s", $phpTime);
		$source = $this->documentGenerated==1 ? "database" : "cache";
		$queries = isset($this->executedQueries) ? $this->executedQueries : 0 ;
		
		// send out content-type headers
		$type = !empty($this->contentTypes[$this->documentIdentifier]) ? $this->contentTypes[$this->documentIdentifier] : "text/html";
		$header = 'Content-Type: '.$type.'; charset='.$this->config['etomite_charset'];
		header($header);
		
		$documentOutput = $this->addNotice($output, $type);
		if($this->dumpSQL) {
			$documentOutput .= $this->queryCode;
		}
		$documentOutput = str_replace("[^q^]", $queries, $documentOutput);
		$documentOutput = str_replace("[^qt^]", $queryTime, $documentOutput);
		$documentOutput = str_replace("[^p^]", $phpTime, $documentOutput);
		$documentOutput = str_replace("[^t^]", $totalTime, $documentOutput);
		$documentOutput = str_replace("[^s^]", $source, $documentOutput);
		
		echo $documentOutput;
		ob_end_flush();
	}


	function checkPublishStatus(){
		include "assets/cache/etomitePublishing.idx";
		$timeNow = time()+$this->config['server_offset_time'];
		if($cacheRefreshTime<=$timeNow && $cacheRefreshTime!=0) {
			// now, check for documents that need publishing
			$sql = "UPDATE ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content SET published=1 WHERE ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content.pub_date < $timeNow AND ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content.pub_date!=0";
			if(@!$result = $this->dbQuery($sql)) {
				$this->messageQuit("Execution of a query to the database failed", $sql);
			}
			
			// now, check for documents that need un-publishing
			$sql = "UPDATE ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content SET published=0 WHERE ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content.unpub_date < $timeNow AND ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content.unpub_date!=0";
			if(@!$result = $this->dbQuery($sql)) {
				$this->messageQuit("Execution of a query to the database failed", $sql);
			}
				
			// clear the cache
			$basepath=dirname(__FILE__);
			if ($handle = opendir($basepath."/assets/cache")) {
				$filesincache = 0;
				$deletedfilesincache = 0;
				while (false !== ($file = readdir($handle))) { 
					if ($file != "." && $file != "..") { 
						$filesincache += 1;
						if (preg_match ("/\.etoCache/", $file)) {
							$deletedfilesincache += 1;
							while(!unlink($basepath."/assets/cache/".$file));
						}
					} 
				}
				closedir($handle); 
			}

			// update publish time file
			$timesArr = array();
			$sql = "SELECT MIN(pub_date) AS minpub FROM ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content WHERE pub_date>$timeNow";
			if(@!$result = $this->dbQuery($sql)) {
				$this->messageQuit("Failed to find publishing timestamps", $sql);
			}
			$tmpRow = $this->fetchRow($result);
			$minpub = $tmpRow['minpub'];
			if($minpub!=NULL) {
				$timesArr[] = $minpub;
			}
			
			$sql = "SELECT MIN(unpub_date) AS minunpub FROM ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content WHERE unpub_date>$timeNow";
			if(@!$result = $this->dbQuery($sql)) {
				$this->messageQuit("Failed to find publishing timestamps", $sql);
			}
			$tmpRow = $this->fetchRow($result);
			$minunpub = $tmpRow['minunpub'];
			if($minunpub!=NULL) {
				$timesArr[] = $minunpub;
			}

			if(count($timesArr)>0) {
				$nextevent = min($timesArr);
			} else {
				$nextevent = 0;			
			}
			
			$basepath=dirname(__FILE__);
			$fp = @fopen($basepath."/assets/cache/etomitePublishing.idx","wb");
			if($fp) {
				@flock($fp, LOCK_EX);
				$len = strlen($data);
				@fwrite($fp, "<?php \$cacheRefreshTime=$nextevent; ?>", $len);
				@flock($fp, LOCK_UN);
				@fclose($fp);
			}
		}
	}

	function postProcess() {

		// if the current document was generated, cache it!
		if($this->documentGenerated==1 && $this->documentObject['cacheable']==1 && $this->documentObject['type']=='document') {
			$basepath=dirname(__FILE__);
			if($fp = @fopen($basepath."/assets/cache/docid_".$this->documentIdentifier.".etoCache","w")){
				fputs($fp,$this->documentContent);
				fclose($fp);
			}
		}

		if($this->config['track_visitors']==1 && !isset($_REQUEST['z'])) {
			$this->log();
		}
		// end post processing
	}
	
	function mergeDocumentContent($template) {
		foreach ($this->documentObject as $key => $value) {
			$template = str_replace("[*".$key."*]", stripslashes($value), $template);
		}
		return $template;
	}
	
	function mergeSettingsContent($template) {
		preg_match_all('~\[\((.*?)\)\]~', $template, $matches);
		$settingsCount = count($matches[1]);
		for($i=0; $i<$settingsCount; $i++) {
			$replace[$i] = $this->config[$matches[1][$i]];
		}
		$template = str_replace($matches[0], $replace, $template);
		return $template;
	}

	function mergeHTMLSnippetsContent($content) {
		preg_match_all('~{{(.*?)}}~', $content, $matches);
		$settingsCount = count($matches[1]);
		for($i=0; $i<$settingsCount; $i++) {
			if(isset($this->chunkCache[$matches[1][$i]])) {
				$replace[$i] = base64_decode($this->chunkCache[$matches[1][$i]]);
			} else {
				$sql = "SELECT * FROM ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_htmlsnippets WHERE ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_htmlsnippets.name='".$matches[1][$i]."';";
				$result = $this->dbQuery($sql);
				$limit=$this->recordCount($result);
				if($limit<1) {
					$this->chunkCache[$matches[1][$i]] = "";
					$replace[$i] = "";
				} else {
					$row=$this->fetchRow($result);
					$this->chunkCache[$matches[1][$i]] = $row['snippet'];
					$replace[$i] = $row['snippet'];
				}
			}
		}
		$content = str_replace($matches[0], $replace, $content);
		return $content;
	}

	function evalSnippet($snippet, $params) {
		$etomite = &$this;
		if(is_array($params)) {
			extract($params, EXTR_SKIP);
		}
		$snip = eval(base64_decode($snippet));
		return $snip;
	}
	
	function evalSnippets($documentSource) {
		preg_match_all('~\[\[(.*?)\]\]~', $documentSource, $matches);
		
		$etomite = &$this;

		$matchCount=count($matches[1]);
		for($i=0; $i<$matchCount; $i++) {
			$spos = strpos($matches[1][$i], '?', 0);
			if($spos!==false) {
				$params = substr($matches[1][$i], $spos, strlen($matches[1][$i]));
			} else {
				$params = '';
			}
			$matches[1][$i] = str_replace($params, '', $matches[1][$i]);
			$snippetParams[$i] = $params;
		}
		$nrSnippetsToGet = count($matches[1]);
		for($i=0;$i<$nrSnippetsToGet;$i++) {
			if(isset($this->snippetCache[$matches[1][$i]])) {			
				$snippets[$i]['name'] = $matches[1][$i];
				$snippets[$i]['snippet'] = $this->snippetCache[$matches[1][$i]];
			} else {
				$sql = "SELECT * FROM ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_snippets WHERE ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_snippets.name='".$matches[1][$i]."';";
				$result = $this->dbQuery($sql);
				if($this->recordCount($result)==1) {
					$row = $this->fetchRow($result);
					$snippets[$i]['name'] = $row['name'];
					$snippets[$i]['snippet'] = base64_encode($row['snippet']);					
					$this->snippetCache = $snippets[$i];
				} else {
					$snippets[$i]['name'] = $matches[1][$i];
					$snippets[$i]['snippet'] = base64_encode("return false;");
					$this->snippetCache = $snippets[$i];
				}
			}
		}
		
		for($i=0; $i<$nrSnippetsToGet; $i++) {
			$parameter = array();
			$snippetName = $this->currentSnippet = $snippets[$i]['name'];
			$currentSnippetParams = $snippetParams[$i];
			if(!empty($currentSnippetParams)) {
				$tempSnippetParams = str_replace("?", "", $currentSnippetParams);
				$splitter = strpos($tempSnippetParams, "&amp;")>0 ? "&amp;" : "&";
				$tempSnippetParams = split($splitter, $tempSnippetParams);
				for($x=0; $x<count($tempSnippetParams); $x++) {
					$parameterTemp = explode("=", $tempSnippetParams[$x]);
					$parameter[$parameterTemp[0]] = $parameterTemp[1];
				}
			}
			$executedSnippets[$i] = $this->evalSnippet($snippets[$i]['snippet'], $parameter);
			if($this->dumpSnippets==1) {
				echo "<fieldset><legend><b>$snippetName</b></legend><textarea style='width:60%; height:200px'>".htmlentities($executedSnippets[$i])."</textarea></fieldset><br />";
			}
			$documentSource = str_replace("[[".$snippetName.$currentSnippetParams."]]", $executedSnippets[$i], $documentSource);
		}
		return $documentSource;
	}

	function rewriteUrls($documentSource) {
		// rewrite the urls
		// based on code by daseymour ;) 
		if($this->config['friendly_urls']==1) {		
			$aliases = array(); 
			$limit_tmp = count($this->aliasListing);
			for ($i_tmp=0; $i_tmp<$limit_tmp; $i_tmp++) { 
			   $aliases[$this->aliasListing[$i_tmp]['id']] = $this->aliasListing[$i_tmp]['alias']; 
			} 
			$this->aliases = $aliases;
			
			// write the function for the preg_replace_callback. Probably not the best way of doing this,
			// but otherwise it braks on some people's installs...
			$func = '				
			$aliases=unserialize("'.addslashes(serialize($this->aliases)).'");
			if (isset($aliases[$m[1]])) { 
				if('.$this->config["friendly_alias_urls"].'==1) {
					return "'.$this->config["friendly_url_prefix"].'".$aliases[$m[1]]."'.$this->config["friendly_url_suffix"].'"; 
				} else { 
					return $aliases[$m[1]]; 
				} 
			} else { 
				return "'.$this->config["friendly_url_prefix"].'".$m[1]."'.$this->config["friendly_url_suffix"].'"; 
			}';
			
			$in = '!\[\~(.*?)\~\]!is'; 
			$documentSource = preg_replace_callback($in, create_function('$m', $func), $documentSource); 
		} else {
			$in = '!\[\~(.*?)\~\]!is'; 
			$out = "index.php?id=".'\1';
			$documentSource = preg_replace($in, $out, $documentSource);   
		}
		return $documentSource;
	}
	
	function executeParser() {
		//error_reporting(0);
		set_error_handler(array(&$this,"phpError"));
		
		// get the settings
		if(empty($this->config)) {
			$this->getSettings();
		}
		if(!$this->checkSiteStatus()) {
			$this->documentContent = $this->config['site_unavailable_message'];
			$this->outputContent();
			exit; // stop processing here, as the site's offline
		}
		
		// make sure the cache doesn't need updating
		$this->checkPublishStatus();
		
		// check the logging cookie
		if($this->config['track_visitors']==1 && !isset($_REQUEST['z'])) {
			$this->checkCookie();
		}
		
		
		// find out which document we need to display
		$this->documentMethod = $this->getDocumentMethod();
		$this->documentIdentifier = $this->getDocumentIdentifier($this->documentMethod);
		if($this->documentMethod=="none"){
			$this->documentMethod = "id"; // now we know the site_start, change the none method to id
		}
		if($this->documentMethod=="alias"){
			$this->documentIdentifier = $this->cleanDocumentIdentifier($this->documentIdentifier);
		}
		
		if($this->documentMethod=="alias"){

			$this->documentIdentifier = $this->documentListing[$this->documentIdentifier];
			$this->documentMethod = 'id';

		}
		// we now know the method and identifier, let's check the cache
		$this->documentContent = $this->checkCache($this->documentIdentifier);
		if($this->documentContent!="") {
			// do nothing?
		} else {
			$sql = "SELECT * FROM ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content WHERE ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content.".$this->documentMethod." = '".$this->documentIdentifier."';";
			$result = $this->dbQuery($sql);
			$rowCount = $this->recordCount($result);
			if($rowCount<1) {
				$this->sendRedirect($this->makeUrl($this->config['error_page']), 1); // no match found, send the visitor to the error_page
				exit;
			}
			if($rowCount>1) {
				$this->messageQuit("More than one result returned when attempting to translate `alias` to `id` - there are multiple documents using the same alias"); // no match found, send the visitor to the error_page
			}
			# this is now the document :) #
			$this->documentObject = $this->fetchRow($result);

			// write the documentName to the object
			$this->documentName = $this->documentObject['pagetitle'];

			// validation routines
			if($this->documentObject['deleted']==1) {
				$this->sendRedirect($this->makeUrl($this->config['error_page']), 1);
			}
													//  && !$this->checkPreview()
			if($this->documentObject['published']==0) {
				//echo "this document is not published";
				//exit;
				$this->sendRedirect($this->makeUrl($this->config['error_page']), 1);
			}
			
			// check whether it's a reference
			if($this->documentObject['type']=="reference") {
				$this->sendRedirect($this->documentObject['content']);
			}
			
						
			// get the template and start parsing!
			$sql = "SELECT * FROM ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_templates WHERE ".$this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_templates.id = '".$this->documentObject['template']."';";
			$result = $this->dbQuery($sql);
			$rowCount = $this->recordCount($result);
			if($rowCount!=1) {
				$this->messageQuit("Incorrect number of templates returned from database", $sql);
			}
			$row = $this->fetchRow($result);
			$documentSource = $row['content'];
			
			// get snippets and parse them the required number of times
			$this->snippetParsePasses = empty($this->snippetParsePasses) ? 3 : $this->snippetParsePasses ;
			for($i=0; $i<$this->snippetParsePasses; $i++) {
				if($this->dumpSnippets==1) {
					echo "<fieldset><legend><b style='color: #821517;'>PARSE PASS ".($i+1)."</b></legend>The following snipppets (if any) were parsed during this pass.<div style='width:100%' align='center'>";
				}
				// combine template and content
				$documentSource = $this->mergeDocumentContent($documentSource);
				// replace settings referenced in document
				$documentSource = $this->mergeSettingsContent($documentSource);
				// replace HTMLSnippets in document
				$documentSource = $this->mergeHTMLSnippetsContent($documentSource);				
				// find and merge snippets
				$documentSource = $this->evalSnippets($documentSource);
				if($this->dumpSnippets==1) {
					echo "</div></fieldset><br />";
				}
			}
			$this->documentContent = $documentSource;
		}	
		register_shutdown_function(array(&$this,"postProcess")); // tell PHP to call postProcess when it shuts down
		$this->outputContent();
		//$this->postProcess();
	}

/***************************************************************************************/
/* Etomite API functions																/
/***************************************************************************************/
	
	
	function getAllChildren($id=0, $sort='menuindex', $dir='ASC', $fields='id, pagetitle, description, parent, alias') {
		$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content";
		$sql = "SELECT $fields FROM $tbl WHERE $tbl.parent=$id ORDER BY $sort $dir;";
		$result = $this->dbQuery($sql);
		$resourceArray = array();
		for($i=0;$i<@$this->recordCount($result);$i++)  {
			array_push($resourceArray,@$this->fetchRow($result));
		}
		return $resourceArray;
	}
	
	function getActiveChildren($id=0, $sort='menuindex', $dir='ASC', $fields='id, pagetitle, description, parent, alias') {
		$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content";
		$sql = "SELECT $fields FROM $tbl WHERE $tbl.parent=$id AND $tbl.published=1 AND $tbl.deleted=0 ORDER BY $sort $dir;";
		$result = $this->dbQuery($sql);
		$resourceArray = array();
		for($i=0;$i<@$this->recordCount($result);$i++)  {
			array_push($resourceArray,@$this->fetchRow($result));
		}
		return $resourceArray;
	}

	function getDocuments($ids=array(), $published=1, $deleted=0, $fields="*", $where='', $sort="menuindex", $dir="ASC") {
		if(count($ids)==0) {
			return false;
		} else {
			$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content";
			$sql = "SELECT $fields FROM $tbl WHERE $tbl.id IN (".join($ids, ",").") AND $tbl.published=$published AND $tbl.deleted=$deleted $where ORDER BY $sort $dir;";
			$result = $this->dbQuery($sql);
			$resourceArray = array();
			for($i=0;$i<@$this->recordCount($result);$i++)  {
				array_push($resourceArray,@$this->fetchRow($result));
			}
			return $resourceArray;
		}
	}

	function getDocument($id=0, $fields="*") {
		if($id==0) {
			return false;
		} else {
			$tmpArr[] = $id;
			$docs = $this->getDocuments($tmpArr, 1, 0, $fields);
			if($docs!=false) {
				return $docs[0];
			} else {
				return false;
			}
		}
	}
	
	function getPageInfo($pageid=-1, $active=1, $fields='id, pagetitle, description, alias') { 
		if($pageid==0) { 
			return false; 
		} else { 
			$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content"; 
			$activeSql = $active==1 ? "AND $tbl.published=1 AND $tbl.deleted=0" : "" ; 
			$sql = "SELECT $fields FROM $tbl WHERE $tbl.id=$pageid $activeSql"; 
			$result = $this->dbQuery($sql); 
			$pageInfo = @$this->fetchRow($result); 
			return $pageInfo; 
		} 
	}
	
	function getParent($pid=-1, $active=1, $fields='id, pagetitle, description, alias, parent') {
		if($pid==-1) {
			$pid = $this->documentObject['parent'];
		}
		if($pid==0) {
			return false;
		} else {
			$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."site_content";
			$activeSql = $active==1 ? "AND $tbl.published=1 AND $tbl.deleted=0" : "" ;
			$sql = "SELECT $fields FROM $tbl WHERE $tbl.id=$pid $activeSql";
			$result = $this->dbQuery($sql);
			$parent = @$this->fetchRow($result);
			return $parent;
		}
	}

	function getSnippetName() {
		return $this->currentSnippet;
	}

	function clearCache() {
		$basepath=dirname(__FILE__);
		if (@$handle = opendir($basepath."/assets/cache")) {
			$filesincache = 0;
			$deletedfilesincache = 0;
			while (false !== ($file = readdir($handle))) { 
				if ($file != "." && $file != "..") { 
					$filesincache += 1;
					if (preg_match ("/\.etoCache/", $file)) {
						$deletedfilesincache += 1;
						unlink($_SERVER['DOCUMENT_ROOT']."/assets/cache/".$file);
					}
				} 
			}
			closedir($handle); 
			return true;
		} else {
			return false;
		}
	}

	function makeUrl($id, $alias='', $args='') {
		if(!is_numeric($id)) {
			$this->messageQuit("`$id` is not numeric and may not be passed to makeUrl()");
		}
		if($this->config['friendly_urls']==1 && $alias!='') {
			return $alias.$args;		
		} elseif($this->config['friendly_urls']==1 && $alias=='') {
			return $this->config['friendly_url_prefix'].$id.$this->config['friendly_url_suffix'].$args;
		} else {
			return "index.php?id=$id$args";
		}
	}
	
	function getConfig($name='') {
		if(!empty($this->config[$name])) {
			return $this->config[$name];
		} else {
			return false;
		}
	}
	
	function getVersionData() {
		include "manager/includes/version.inc.php";
		$version = array();
		$version['code_name'] = $code_name;
		$version['version'] = $small_version;
		$version['patch_level'] = $patch_level;
		$version['full_appname'] = $full_appname;
		return $version;
	}
	
	function makeList($array, $ulroot='root', $ulprefix='sub_', $type='', $ordered=false, $tablevel=0) {
		// first find out whether the value passed is an array
		if(!is_array($array)) {
			return "<ul><li>Bad list</li></ul>";
		}
		if(!empty($type)) {
			$typestr = " style='list-style-type: $type'";
		} else {
			$typestr = "";		
		}
		$tabs = "";
		for($i=0; $i<$tablevel; $i++) {
			$tabs .= "\t";
		}
		$listhtml = $ordered==true ? $tabs."<ol class='$ulroot'$typestr>\n" : $tabs."<ul class='$ulroot'$typestr>\n" ;
		foreach($array as $key=>$value) {
			if(is_array($value)) {
				$listhtml .= $tabs."\t<li>".$key."\n".$this->makeList($value, $ulprefix.$ulroot, $ulprefix, $type, $ordered, $tablevel+2).$tabs."\t</li>\n";
			} else {
				$listhtml .= $tabs."\t<li>".$value."</li>\n";
			}
		}
		$listhtml .= $ordered==true ? $tabs."</ol>\n" : $tabs."</ul>\n" ;
		return $listhtml;
	}

	function userLoggedIn() {
		$userdetails = array();
		if(isset($_SESSION['validated'])) {
			$userdetails['loggedIn']=true;
			$userdetails['id']=$_SESSION['internalKey'];
			$userdetails['username']=$_SESSION['shortname'];
			return $userdetails;
		} else {
			return false;
		}		
	}
	
	function getKeywords($id=0) {
		if($id==0) {
			$id=$this->documentObject['id'];
		}
		$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix'];
		$sql = "SELECT keywords.keyword FROM ".$tbl."site_keywords AS keywords INNER JOIN ".$tbl."keyword_xref AS xref ON keywords.id=xref.keyword_id WHERE xref.content_id = $id";
		$result = $this->dbQuery($sql);
		$limit = $this->recordCount($result);
		$keywords = array();
		if($limit > 0) 	{
			for($i=0;$i<$limit;$i++) {
				$row = $this->fetchRow($result);
				$keywords[] = $row['keyword'];
			}
		}
		return $keywords;
	}

	function runSnippet($snippetName, $params=array()) {
		$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix'];
		$sql = "SELECT snippet FROM ".$tbl."site_snippets WHERE name = '$snippetName'";
		$result = $this->dbQuery($sql);
		$limit = $this->recordCount($result);
		if($limit!=1) 	{
			return false;
		} else {
			$row = $this->fetchRow($result);
			return $this->evalSnippet($row['snippet'], $params);
		}		
	}
	
	function getChunk($chunkName) {
		return base64_decode($this->chunkCache[$chunkName]);
	}

	function putChunk($chunkName) { // alias name >.<
		return $this->getChunk($chunkName);
	}

	function parseChunk($chunkName, $chunkArr, $prefix="{", $suffix="}") {
		if(!is_array($chunkArr)) {
			return false;
		}
		$chunk = $this->getChunk($chunkName);
		foreach($chunkArr as $key => $value) {
			$chunk = str_replace($prefix.$key.$suffix, $value, $chunk);
		}
		return $chunk;
	}

	function getUserData() {
		include_once "manager/includes/etomiteExtenders/getUserData.extender.php";
		return $tmpArray;
	}
	
	function getSiteStats() {
		$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_totals";
		$sql = "SELECT * FROM $tbl";
		$result = $this->dbQuery($sql);		
		$tmpRow = $this->fetchRow($result);
		return $tmpRow;
	}


/***************************************************************************************/
/* End of Etomite API functions																/
/***************************************************************************************/

	function phpError($nr, $text, $file, $line) {
		if($nr==8 && $this->stopOnNotice==false) {
			return true;
		}
		if (is_readable($file)) {
			$source = file($file);
			$source = htmlspecialchars($source[$line-1]);
		} else { 
			$source = "";
		}  //Error $nr in $file at $line: <div><code>$source</code></div>
		$this->messageQuit("PHP Parse Error", '', true, $nr, $file, $source, $text, $line);
	}

	function messageQuit($msg='unspecified error', $query='', $is_error=true, $nr='', $file='', $source='', $text='', $line='') {
				$parsedMessageString = "
		<html><head><title>Etomite ".$GLOBALS['version']." &raquo; ".$GLOBALS['code_name']."</title> 
		<style>TD, BODY { font-size: 11px; font-family:verdana; }</style>
		<script type='text/javascript'>
			function copyToClip() 
			{
				holdtext.innerText = sqlHolder.innerText;
				Copied = holdtext.createTextRange();
				Copied.execCommand('Copy');
			}
		</script>
		</head><body>
		";
		if($is_error) {
			$parsedMessageString .= "<h3 style='color:red'>&laquo; Etomite Parse Error &raquo;</h3>
			<table border='0' cellpadding='1' cellspacing='0'>
			<tr><td colspan='3'>Etomite encountered the following error while attempting to parse the requested resource:</td></tr>
			<tr><td colspan='3'><b style='color:red;'>&laquo; $msg &raquo;</b></td></tr>";
		} else {
			$parsedMessageString .= "<h3 style='color:#003399'>&laquo; Etomite Debug/ stop message &raquo;</h3>
			<table border='0' cellpadding='1' cellspacing='0'>
			<tr><td colspan='3'>The Etomite parser recieved the following debug/ stop message:</td></tr>
			<tr><td colspan='3'><b style='color:#003399;'>&laquo; $msg &raquo;</b></td></tr>";
		}
		
		if(!empty($query)) {
			$parsedMessageString .= "<tr><td colspan='3'><b style='color:#999;font-size: 9px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SQL:&nbsp;<span id='sqlHolder'>$query</span></b>
			<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:copyToClip();' style='color:#821517;font-size: 9px; text-decoration: none'>[Copy SQL to ClipBoard]</a><textarea id='holdtext' style='display:none;'></textarea></td></tr>";
		}
		
		if($text!='') {
		
			$errortype = array (
				E_ERROR          => "Error",
				E_WARNING        => "Warning",
				E_PARSE          => "Parsing Error",
				E_NOTICE          => "Notice",
				E_CORE_ERROR      => "Core Error",
				E_CORE_WARNING    => "Core Warning",
				E_COMPILE_ERROR  => "Compile Error",
				E_COMPILE_WARNING => "Compile Warning",
				E_USER_ERROR      => "User Error",
				E_USER_WARNING    => "User Warning",
				E_USER_NOTICE    => "User Notice",
			);
		   
			$parsedMessageString .= "<tr><td>&nbsp;</td></tr><tr><td colspan='3'><b>PHP error debug</b></td></tr>";
			
			$parsedMessageString .= "<tr><td valign='top'>&nbsp;&nbsp;Error: </td>";
			$parsedMessageString .= "<td colspan='2'>$text</td><td>&nbsp;</td>";
			$parsedMessageString .= "</tr>";

			$parsedMessageString .= "<tr><td valign='top'>&nbsp;&nbsp;Error type/ Nr.: </td>";
			$parsedMessageString .= "<td colspan='2'>".$errortype[$nr]." - $nr</b></td><td>&nbsp;</td>";
			$parsedMessageString .= "</tr>";
			
			$parsedMessageString .= "<tr><td>&nbsp;&nbsp;File: </td>";
			$parsedMessageString .= "<td colspan='2'>$file</td><td>&nbsp;</td>";
			$parsedMessageString .= "</tr>";
	
			$parsedMessageString .= "<tr><td>&nbsp;&nbsp;Line: </td>";
			$parsedMessageString .= "<td colspan='2'>$line</td><td>&nbsp;</td>";
			$parsedMessageString .= "</tr>";
			if($source!='') {
				$parsedMessageString .= "<tr><td valign='top'>&nbsp;&nbsp;Line $line source: </td>";
				$parsedMessageString .= "<td colspan='2'>$source</td><td>&nbsp;</td>";
				$parsedMessageString .= "</tr>";
			}
		}

		$parsedMessageString .= "<tr><td>&nbsp;</td></tr><tr><td colspan='3'><b>Parser timing</b></td></tr>";

		$parsedMessageString .= "<tr><td>&nbsp;&nbsp;MySQL: </td>";
		$parsedMessageString .= "<td><i>[^qt^] s</i></td><td>(<i>[^q^] Requests</i>)</td>";
		$parsedMessageString .= "</tr>";
		
		$parsedMessageString .= "<tr><td>&nbsp;&nbsp;PHP: </td>";
		$parsedMessageString .= "<td><i>[^p^] s</i></td><td>&nbsp;</td>";
		$parsedMessageString .= "</tr>";

		$parsedMessageString .= "<tr><td>&nbsp;&nbsp;Total: </td>";
		$parsedMessageString .= "<td><i>[^t^] s</i></td><td>&nbsp;</td>";
		$parsedMessageString .= "</tr>";

		$parsedMessageString .= "</table>";
		$parsedMessageString .= "</body></html>";
	
		$this->documentContent = $parsedMessageString;
		$this->outputContent();
		
		exit;
	}


	// Parsing functions used in this class are based on/ inspired by code by Sebastian Bergmann.
	// The regular expressions used in this class are taken from the ModLogAn (http://jan.kneschke.de/projects/modlogan/) project.
	function log() {
				$user_agents = array();
		$user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \(compatible; iCab ([^;]); ([^;]); [NUI]; ([^;])\)#', 'string' => 'iCab $1');
		$user_agents[] = array('pattern' => '#^Opera/(\d+\.\d+) \(([^;]+); [^)]+\)#', 'string' => 'Opera $1');
		$user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \(compatible; MSIE [^;]+; ([^)]+)\) Opera (\d+\.\d+)#', 'string' => 'Opera $2');
		$user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \(([^;]+); [^)]+\) Opera (\d+\.\d+)#', 'string' => 'Opera $2');
		$user_agents[] = array('pattern' => '#^Mozilla/[1-9]\.0 ?\(compatible; MSIE ([1-9]\.[0-9b]+);(?: ?[^;]+;)*? (Mac_[^;)]+|Windows [^;)]+)(?:; [^;]+)*\)#', 'string' => 'Internet Explorer $1');
		$user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; ([^;]+); [^;]+; Galeon\) Gecko/\d{8}$#', 'string' => 'Galeon');
		$user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; Galeon; [^;]+; ([^;)]+)\)$#', 'string' => 'Galeon $1');
		$user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ Galeon/([0-9.]+) \(([^;)]+)\) Gecko/\d{8}$#', 'string' => 'Galeon $1');
		$user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; ([^;]+); [^;]+; rv:[^;]+(?:; [^;]+)*\) Gecko/\d{8} ([a-zA-Z ]+/[0-9.b]+)#', 'string' => '$2');
		$user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; ([^;]+); [^;]+; rv:([^;]+)(?:; [^;]+)*\) Gecko/\d{8}$#', 'string' => 'Mozilla $2');
		$user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; ([^;]+); [^;]+; (m\d+)(?:; [^;]+)*\) Gecko/\d{8}$#', 'string' => 'Mozilla $2');
		$user_agents[] = array('pattern' => '#^Mozilla/\d+\.\d+ \([^;]+; [NIU]; ([^;]+)(?:; [^;]+)*\) Mozilla/(.+)$#', 'string' => 'Mozilla $2');
		$user_agents[] = array('pattern' => '#^Mozilla/4\.(\d+)[^(]+\(X11; [NIU] ?; ([^;]+)(?:; [^;]+)*\)#', 'string' => 'Netscape 4.$1');
		$user_agents[] = array('pattern' => '#^Mozilla/4\.(\d+)[^(]+\((OS/2|Linux|Macintosh|Win[^;]*)[;,] [NUI] ?[^)]*\)#', 'string' => 'Netscape 4.$1');
		$user_agents[] = array('pattern' => '#^Mozilla/3\.(\d+)\S*[^(]+\(X11; [NIU] ?; ([^;]+)(?:; [^;)]+)*\)#', 'string' => 'Netscape 3.$1');
		$user_agents[] = array('pattern' => '#^Mozilla/3\.(\d+)\S*[^(]+\(([^;]+); [NIU] ?(?:; [^;)]+)*\)#', 'string' => 'Netscape 3.$1');
		$user_agents[] = array('pattern' => '#^Mozilla/2\.(\d+)\S*[^(]+\(([^;]+); [NIU] ?(?:; [^;)]+)*\)#', 'string' => 'Netscape 2.$1');
		$user_agents[] = array('pattern' => '#^Mozilla \(X11; [NIU] ?; ([^;)]+)\)#', 'string' => 'Netscape');
		$user_agents[] = array('pattern' => '#^Mozilla/3.0 \(compatible; StarOffice/(\d+)\.\d+; ([^)]+)\)$#', 'string' => 'StarOffice $1');
		$user_agents[] = array('pattern' => '#^ELinks \((.+); (.+); .+\)$#', 'string' => 'ELinks $1');
		$user_agents[] = array('pattern' => '#^Mozilla/3\.0 \(compatible; NetPositive/([0-9.]+); BeOS\)$#', 'string' => 'NetPositive $1');
		$user_agents[] = array('pattern' => '#^Konqueror/(\S+)$#', 'string' => 'Konqueror $1');
		$user_agents[] = array('pattern' => '#^Mozilla/5\.0 \(compatible; Konqueror/([^;]); ([^)]+)\).*$#', 'string' => 'Konqueror $1');
		$user_agents[] = array('pattern' => '#^Lynx/(\S+)#', 'string' => 'Lynx/$1');
		$user_agents[] = array('pattern' => '#^Mozilla/4.0 WebTV/(\d+\.\d+) \(compatible; MSIE 4.0\)$#', 'string' => 'WebTV $1');
		$user_agents[] = array('pattern' => '#^Mozilla/4.0 \(compatible; MSIE 5.0; (Win98 A); (ATHMWWW1.1); MSOCD;\)$#', 'string' => '$2');
		$user_agents[] = array('pattern' => '#^(RMA/1.0) \(compatible; RealMedia\)$#', 'string' => '$1');
		$user_agents[] = array('pattern' => '#^antibot\D+([0-9.]+)/(\S+)#', 'string' => 'antibot $1');
		$user_agents[] = array('pattern' => '#^Mozilla/[1-9]\.\d+ \(compatible; ([^;]+); ([^)]+)\)$#', 'string' => '$1');
		$user_agents[] = array('pattern' => '#^Mozilla/([1-9]\.\d+)#', 'string' => 'compatible Mozilla/$1');
		$user_agents[] = array('pattern' => '#^([^;]+)$#', 'string' => '$1');	
		$GLOBALS['user_agents'] = $user_agents;
		
		$operating_systems = array();
		$operating_systems[] = array('pattern' => '#Win.*NT 5.0#', 'string' => 'Windows 2000');
		$operating_systems[] = array('pattern' => '#Win.*NT 5.1#', 'string' => 'Windows XP');
		$operating_systems[] = array('pattern' => '#Win.*(XP|2000|ME|NT|9.?)#', 'string' => 'Windows $1');
		$operating_systems[] = array('pattern' => '#Windows .*(3\.11|NT)#', 'string' => 'Windows $1');
		$operating_systems[] = array('pattern' => '#Win32#', 'string' => 'Windows [unknown version)');
		$operating_systems[] = array('pattern' => '#Linux 2\.(.?)\.#', 'string' => 'Linux 2.$1.x');
		$operating_systems[] = array('pattern' => '#Linux#', 'string' => 'Linux (unknown version)');
		$operating_systems[] = array('pattern' => '#FreeBSD .*-CURRENT$#', 'string' => 'FreeBSD Current');
		$operating_systems[] = array('pattern' => '#FreeBSD (.?)\.#', 'string' => 'FreeBSD $1.x');
		$operating_systems[] = array('pattern' => '#NetBSD 1\.(.?)\.#', 'string' => 'NetBSD 1.$1.x');
		$operating_systems[] = array('pattern' => '#(Free|Net|Open)BSD#', 'string' => '$1BSD [unknown version]');
		$operating_systems[] = array('pattern' => '#HP-UX B\.(10|11)\.#', 'string' => 'HP-UX B.$1.xP');
		$operating_systems[] = array('pattern' => '#IRIX(64)? 6\.#', 'string' => 'IRIX 6.x');
		$operating_systems[] = array('pattern' => '#SunOS 4\.1#', 'string' => 'SunOS 4.1.x');
		$operating_systems[] = array('pattern' => '#SunOS 5\.([4-6])#', 'string' => 'Solaris 2.$1.x');
		$operating_systems[] = array('pattern' => '#SunOS 5\.([78])#', 'string' => 'Solaris $1.x');
		$operating_systems[] = array('pattern' => '#Mac_PowerPC#', 'string' => 'Mac OS [PowerPC]');
		$operating_systems[] = array('pattern' => '#Mac#', 'string' => 'Mac OS');
		$operating_systems[] = array('pattern' => '#X11#', 'string' => 'UNIX [unknown version]');
		$operating_systems[] = array('pattern' => '#Unix#', 'string' => 'UNIX [unknown version]');
		$operating_systems[] = array('pattern' => '#BeOS#', 'string' => 'BeOS [unknown version]');
		$operating_systems[] = array('pattern' => '#QNX#', 'string' => 'QNX [unknown version]');
		$GLOBALS['operating_systems'] = $operating_systems;

		// fix for stupid browser shells sending lots of requests
		if(strpos($_SERVER['HTTP_USER_AGENT'], "http://www.avantbrowser.com") > -1) {
			exit;
		}

		if(strpos($_SERVER['HTTP_USER_AGENT'], "WebDAV") > -1) {
			exit;
		}
			
		//work out browser and operating system
		$user_agent = $this->useragent($_SERVER['HTTP_USER_AGENT']);
		$os = crc32($user_agent['operating_system']);
		$ua = crc32($user_agent['user_agent']);
		
		//work out access time data
		$accesstime = getdate(); 
		$hour = $accesstime['hours']; 
		$weekday = $accesstime['wday']; 

		// work out the host
		if (isset($_SERVER['REMOTE_ADDR'])) {
			$hostname = $_SERVER['REMOTE_ADDR'];
			if (isset($_SERVER['REMOTE_HOST'])) {
				$hostname = $_SERVER['REMOTE_HOST'];
			} else {
				if ($this->config['resolve_hostnames']==1) {
					$hostname = gethostbyaddr($hostname); // should be an IP address
				}
			}
		} else {
			$hostname = 'Unknown';
		}		
		$host = crc32($hostname);

		// work out the referer
		$referer = urldecode($_SERVER['HTTP_REFERER']);
		if(empty($referer)) {
			$referer = "Unknown";
		} else {
			$pieces = parse_url($referer);
		    $referer = $pieces[scheme]."://".$pieces[host].$pieces[path];
		}
		if(strpos($referer, $_SERVER['SERVER_NAME'])>0) {
			$referer = "Internal";		
		}
		$ref = crc32($referer);

		if($this->documentIdentifier==0) {
			$docid=$this->config['error_page'];
		} else {
			$docid=$this->documentIdentifier;
		}

		if($docid==$this->config['error_page']) {
			exit; //stop logging 404's
		}
		
		// log the access hit
		$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_access";
		$sql = "INSERT INTO $tbl(visitor, document, timestamp, hour, weekday, referer, entry) VALUES('".$this->visitor."', '".$docid."', '".(time()+$this->config['server_offset_time'])."', '".$hour."', '".$weekday."', '".$ref."', '".$this->entrypage."')";
		$result = $this->dbQuery($sql);
		
		// check if the visitor exists in the database
		if(!isset($_SESSION['visitorLogged'])) {
			$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_visitors";
			$sql = "SELECT COUNT(*) FROM $tbl WHERE id='".$this->visitor."'";
			$result = $this->dbQuery($sql);
			$tmp = $this->fetchRow($result);
			$_SESSION['visitorLogged'] = $tmp['COUNT(*)'];
		} else {
			$_SESSION['visitorLogged'] = 1;
		}
		
		// log the visitor		
		if($_SESSION['visitorLogged']==0) {		
			$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_visitors";
			$sql = "INSERT INTO $tbl(id, os_id, ua_id, host_id) VALUES('".$this->visitor."', '".crc32($user_agent['operating_system'])."', '".$ua."', '".$host."')";
			$result = $this->dbQuery($sql);
			$_SESSION['visitorLogged'] = 1;
		}

		// check if the user_agent exists in the database
		if(!isset($_SESSION['userAgentLogged'])) {
			$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_user_agents";
			$sql = "SELECT COUNT(*) FROM $tbl WHERE id='".$ua."'";
			$result = $this->dbQuery($sql);
			$tmp = $this->fetchRow($result);
			$_SESSION['userAgentLogged'] = $tmp['COUNT(*)'];
		} else {
			$_SESSION['userAgentLogged'] = 1;
		}
		
		// log the user_agent		
		if($_SESSION['userAgentLogged']==0) {		
			$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_user_agents";
			$sql = "INSERT INTO $tbl(id, data) VALUES('".$ua."', '".$user_agent['user_agent']."')";
			$result = $this->dbQuery($sql);
			$_SESSION['userAgentLogged'] = 1;
		}

		// check if the os exists in the database
		if(!isset($_SESSION['operatingSystemLogged'])) {
			$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_operating_systems";
			$sql = "SELECT COUNT(*) FROM $tbl WHERE id='".$os."'";
			$result = $this->dbQuery($sql);
			$tmp = $this->fetchRow($result);
			$_SESSION['operatingSystemLogged'] = $tmp['COUNT(*)'];
		} else {
			$_SESSION['operatingSystemLogged'] = 1;
		}
		
		// log the os		
		if($_SESSION['operatingSystemLogged']==0) {		
			$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_operating_systems";
			$sql = "INSERT INTO $tbl(id, data) VALUES('".$os."', '".$user_agent['operating_system']."')";
			$result = $this->dbQuery($sql);
			$_SESSION['operatingSystemLogged'] = 1;
		}

		// check if the hostname exists in the database
		if(!isset($_SESSION['hostNameLogged'])) {
			$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_hosts";
			$sql = "SELECT COUNT(*) FROM $tbl WHERE id='".$host."'";
			$result = $this->dbQuery($sql);
			$tmp = $this->fetchRow($result);
			$_SESSION['hostNameLogged'] = $tmp['COUNT(*)'];
		} else {
			$_SESSION['hostNameLogged'] = 1;
		}
		
		// log the hostname		
		if($_SESSION['hostNameLogged']==0) {		
			$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_hosts";
			$sql = "INSERT INTO $tbl(id, data) VALUES('".$host."', '".$hostname."')";
			$result = $this->dbQuery($sql);
			$_SESSION['hostNameLogged'] = 1;
		}

		// log the referrer
		$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_referers";
		$sql = "REPLACE INTO $tbl(id, data) VALUES('".$ref."', '".$referer."')";
		$result = $this->dbQuery($sql);
		
		/*************************************************************************************/
		// update the logging cache
		$tbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_totals";
		$realMonth = strftime("%m");
		$realToday = strftime("%Y-%m-%d");

		// find out if we're on a new day
		$sql = "SELECT today, month FROM $tbl LIMIT 1";
		$result = $this->dbQuery($sql);
		$rowCount = $this->recordCount($result);
		if($rowCount<1) {
			$sql = "INSERT $tbl(today, month) VALUES('$realToday', '$realMonth')";
			$tmpresult = $this->dbQuery($sql);
			$sql = "SELECT today, month FROM $tbl LIMIT 1";
			$result = $this->dbQuery($sql);
		}
		$tmpRow = $this->fetchRow($result);
		$dbMonth = $tmpRow['month'];
		$dbToday = $tmpRow['today'];
		
		if($dbToday!=$realToday) {
			$sql = "UPDATE $tbl SET today='$realToday', piDay=0, viDay=0, visDay=0";
			$result = $this->dbQuery($sql);		
		}
		
		if($dbMonth!=$realMonth) {
			$sql = "UPDATE $tbl SET month='$realMonth', piMonth=0, viMonth=0, visMonth=0";
			$result = $this->dbQuery($sql);		
		}		
		
		// update the table for page impressions
		$sql = "UPDATE $tbl SET piDay=piDay+1, piMonth=piMonth+1, piAll=piAll+1";
		$result = $this->dbQuery($sql);		
		
		// update the table for visits
		if($this->entrypage==1) {
			$sql = "UPDATE $tbl SET viDay=viDay+1, viMonth=viMonth+1, viAll=viAll+1";
			$result = $this->dbQuery($sql);				
		}
		
		// get visitor counts from the logging tables
		$day      = date('j');
		$month    = date('n');
		$year     = date('Y');
		
		$monthStart = mktime(0,   0,  0, $month, 1, $year);
		$dayStart = mktime(0,   0,  0, $month, $day, $year);
		$dayEnd   = mktime(23, 59, 59, $month, $day, $year);
		
		$tmptbl = $this->dbConfig['dbase'].".".$this->dbConfig['table_prefix']."log_access";
		
		$sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tmptbl WHERE timestamp > '".$dayStart."' AND timestamp < '".$dayEnd."'";
		$rs = $this->dbQuery($sql);
		$tmp = $this->fetchRow($rs);
		$visDay = $tmp['COUNT(DISTINCT(visitor))'];

		$sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tmptbl WHERE timestamp > '".$monthStart."' AND timestamp < '".$dayEnd."'";
		$rs = $this->dbQuery($sql);
		$tmp = $this->fetchRow($rs);
		$visMonth = $tmp['COUNT(DISTINCT(visitor))'];

		$sql = "SELECT COUNT(DISTINCT(visitor)) FROM $tmptbl";
		$rs = $this->dbQuery($sql);
		$tmp = $this->fetchRow($rs);
		$visAll = $tmp['COUNT(DISTINCT(visitor))'];

		// update the table for visitors
		$sql = "UPDATE $tbl SET visDay=$visDay, visMonth=$visMonth, visAll=$visAll";
		$result = $this->dbQuery($sql);		
		/*************************************************************************************/
	
	}
	
	
	function match($elements, $rules) {
		if (!is_array($elements)) {
			$noMatch  = $elements;
			$elements = array($elements);
		} else {
			$noMatch = 'Not identified';
		}
		foreach ($rules as $rule) {
			if (!isset($result)) {
				foreach ($elements as $element) {
					$element = trim($element);
					$pattern = trim($rule['pattern']);
					if (preg_match($pattern, $element, $tmp)) {
						$result = str_replace(array('$1', '$2', '$3'), array(isset($tmp[1]) ? $tmp[1] : '', isset($tmp[2]) ? $tmp[2] : '', isset($tmp[3]) ? $tmp[3] : '' ), trim($rule['string']));
						break;
					}
				}
			} else {
				break;
			}
		}
		return isset($result) ? $result : $noMatch;
	}

	function userAgent($string) {
		if (preg_match('#\((.*?)\)#', $string, $tmp)) {
			$elements   = explode(';', $tmp[1]);
			$elements[] = $string;
		} else {
			$elements = array($string);
		}
		if ($elements[0] != 'compatible') {
			$elements[] = substr($string, 0, strpos($string, '('));
		}
		$result['operating_system'] = $this->match($elements,$GLOBALS['operating_systems']);
		$result['user_agent'] = $this->match($elements,$GLOBALS['user_agents']);
		return $result;
	}

// End of etomite class.

}



/***************************************************************************
 Filename: index.php
 Function: This file loads and executes the parser. 
/***************************************************************************/

// get the required includes
define("IN_ETOMITE_PARSER", "true");

// set these values here for a small speed increase! :)
$database_type = "";
$database_server = "";
$database_user = "";
$database_password = "";
$dbase = "";
$table_prefix = "";		

if($database_user=="") {
	include "manager/includes/config.inc.php";
}

// initiate a new document parser
$etomite = new etomite;

// set some options
$etomite->snippetParsePasses = 3;
$etomite->nonCachedSnippetParsePasses = 2;
$etomite->dumpSQL = false;
$etomite->dumpSnippets = false;
// feed the parser the execution start time
$etomite->tstart = $tstart;
// execute the parser
$etomite->executeParser();
?>