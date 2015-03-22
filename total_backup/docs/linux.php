<?php
//------------------------------------------//
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>ITS Backup schema</title>
	<link rel="stylesheet" href="css/ITS_versions.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/docs.css">
	<link rel="stylesheet" href="css/print/ITS_print.css" media="print">
	<style>
	body { margin: 5% 0 }
	.ITS_version td { text-align:left; }
	.at { color:#666 }
    .f { color:#009; }
	</style>
</head>
<body>
	<center>
	        <h3 class="DATA">LINUX COMMANDS</h3>
  <table class="ITS_version" summary="ITS versions">
  	    <!--------------------------------------------------------------------->
			<tr><th>Command</th><th>Description</th></tr>
		  <!--------------------------------------------------------------------->
	    <tr><td>ssh -X yourADusername@itsdev5.vip.gatech.edu</td><td>connect to the server</td></tr>
<tr><td>who</td><td>who is logged in</td></tr>
<tr><td>tree -a	</td><td>directory structure</td></tr>
<tr><td>gnome-panel	</td><td>server GUI via X-windows</td></tr>
<tr><td>gnome-system-monitor</td><td>	resource usage GUI</td></tr>
<tr><td>system-config-services	</td><td>start/restart/stop server services</td></tr>
<tr><td>/etc/init.d/httpd start	</td><td>start apache with ssl support,password: csip</td></tr>
<tr><td>mysqldump --single-transaction --skip-add-locks dbname tbname -u root -p > file.sql</td><td>backup database "dbname" to a sql file</td></tr>
<tr><td>sudo rsync -vaz /home/ITSdrive/ /media/ITSbackup/BACKUP/ITSdrive</td><td>backup files from source to destination folder</td></tr>
<tr><td>rsync -a -e ssh source/ username@remotemachine.com:/path/to/destination/</td><td>backup files from source to server destination</td></tr>
<tr><td>grep -iRn "searchpattern" *</td><td>recursivley search for pattern in files</td></tr>
<tr><td>perl -p -i -e 's/oldstring/newstring/g' `grep -ril searchpattern *`</td><td>recursivley search pattern and replace in files</td></tr>
		  <!--------------------------------------------------------------------->		  
  </table>
 </center>
</body>
</html>
