<?php
//===== eAthena Script =======================================
//= Automated SVN updates
//===== By: ==================================================
//= Brian
//===== Current Version: =====================================
//= 1.0
//===== Compatible With: =====================================
//= eAthena SVN (SQL only)
//===== Description: =========================================
//= Automatically 'svn update', recompile, and restart server.
//===== Additional Comments: =================================
//= 1.0 First Version. 
//= 
/*============================================================

CREATE TABLE IF NOT EXISTS `svn_log` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `check_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `server_rev` smallint(6) unsigned NOT NULL default '0',
  `eathena_rev` smallint(6) unsigned NOT NULL default '0',
  `restart` tinyint(4) NOT NULL default '0',
  `message` varchar(150) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

//==========================================================*/

$local_path = '/home/eathena/trunk/';           // your eAthena server directory (including trailing slash)
$output_dir = '/home/eathena/update_log/';      // where to save the .log files (including trailing slash)
$eathena_url = 'http://eathena-project.googlecode.com/svn/'; // URL for checking eAthena HEAD revision
$host = 'localhost';    // MySQL host
$user = 'ragnarok';     // MySQL user
$pass = '';             // MySQL password
$log_db = 'log';        // ragnarok log database

//============================================================


// Get the SVN revision of a local directory
function local_svn_revision($entriesFile = null) {
	if (file_exists($entriesFile) && is_readable($entriesFile)) {
		$fp  = fopen($entriesFile, 'r');
		$arr = explode("\n", fread($fp, 256));
		
		if (isset($arr[3]) && ctype_digit($rev=trim($arr[3]))) {
			fclose($fp);
			return (int)$rev;
		} else {
			return null;
		}
	}
}
// Get the Head revision of a remote SVN repository
function remote_svn_revision($url = null) {
	$head = file($url);
	$head = explode('- Revision ', $head[2], 2);
	$head = explode(':', $head[1], 2);
	return (int)$head[0];
}

$server_rev = local_svn_revision($local_path . '.svn/entries');
$eathena_rev = remote_svn_revision($eathena_url);

if ($server_rev < $eathena_rev) {
	// attempt to 'svn update'			save the ouput in a log file
	`svn update --non-interactive $local_path > $output_dir$server_rev-$eathena_rev.log`;
	// separator
	`echo '=================================================================' >> $output_dir$server_rev-$eathena_rev.log`;
	`echo '=================================================================' >> $output_dir$server_rev-$eathena_rev.log`;
	
	// scan the log output to see what happened
	if (`cat $output_dir$server_rev-$eathena_rev.log | grep 'Updated to revision '` != null) {
		$sql_files = 'A    ' . $local_path . 'sql-files';
		if (`cat $output_dir$server_rev-$eathena_rev.log | grep 'C    /'` != null) {
			$mes = "[Error]: CONFLICTS found!";
			$restart = -1;
		} else if (`cat $output_dir$server_rev-$eathena_rev.log | grep '$sql_files'` != null) {
			$mes = "[Error]: SQL tables need to be updated!";
			$restart = -1;
		} else if (`cat $output_dir$server_rev-$eathena_rev.log | grep 'G    /'` != null) {
			$mes = "svn update successful (files merged)";
			$restart = 1;
			// update MOTD
			$cmd = 'echo "-----------------------------------------------------" >> '.$local_path.'conf/motd.txt';	`$cmd`;
			$cmd = 'echo "SERVER RESTART at 4:45 am for updates." >> '.$local_path.'conf/motd.txt';	`$cmd`;
		} else {
			$mes = "svn update successful";
			$restart = 1;
			// update MOTD
			$cmd = 'echo "-----------------------------------------------------" >> '.$local_path.'conf/motd.txt';	`$cmd`;
			$cmd = 'echo "SERVER RESTART at 4:45 am for updates." >> '.$local_path.'conf/motd.txt';	`$cmd`;
		}
	} else if (`cat $output_dir$server_rev-$eathena_rev.log | grep 'At revision '` != null) {
		$mes = "No changes";
		$restart = 0;
	} else {
		$mes = "[Error]: svn update FAILED!";
		$restart = 0;
	}
	
} else if ($server_rev == $eathena_rev) {
	$mes = "Already at HEAD revision";
	$restart = 0;
} else {
	$mes = "[Error]: svn update FAILED!";
	$restart = 0;
}

$cxn = mysqli_connect($host,$user,$pass,$log_db);
$query = sprintf("INSERT INTO svn_log (check_time,server_rev,eathena_rev,restart,message) VALUES (NOW(),'%d','%d','%d','%s')", $server_rev,$eathena_rev,$restart,$mes);
$result = mysqli_query($cxn,$query);

?>
