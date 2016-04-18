<?php
//this script runs entire installation process in 5 steps

//take "step" variable to determine which step the current is
$step = $_POST['step'];


//perform field validation(steps 3-5) and database connection tests (steps 3 and 4) and send back to previous step if not working
$errorMessage = array();
if ($step == "3"){
	//first, validate all fields are filled in
	$database_host = trim($_POST['database_host']);
	$database_username = trim($_POST['database_username']);
	$database_password = trim($_POST['database_password']);
	$database_name = trim($_POST['database_name']);
	$usage_database_name = trim($_POST['usage_database_name']);
	$base_url = trim($_POST['base_url']);

	if (!$database_host) $errorMessage[] = 'Host name is required';
	if (!$database_name) $errorMessage[] = 'Database name is required';
	if (!$database_username) $errorMessage[] = 'User name is required';
	if (!$database_password) $errorMessage[] = 'Password is required';
	if (!$usage_database_name) $errorMessage[] = 'Usage Module Database Name is required';



	//only continue to checking DB connections if there were no errors this far
	if (count($errorMessage) > 0){
		$step="2";
	}else{

		//first check connecting to host
		$link = @mysql_connect("$database_host", "$database_username", "$database_password");
		if (!$link) {
			$errorMessage[] = "Could not connect to the server '" . $database_host . "'<br />MySQL Error: " . mysql_error();
		}else{

			//next check that the database exists
			$dbcheck = @mysql_select_db("$database_name");
			if (!$dbcheck) {
				$errorMessage[] = "Unable to access the database '" . $database_name . "'.  Please verify it has been created.<br />MySQL Error: " . mysql_error();
			}else{
				//passed db host, name check, can open/run file now
				//make sure SQL file exists
				$test_sql_file = "test_create.sql";
				$sql_file = "create_tables_data.sql";

			    if (!file_exists($test_sql_file)) {
			    	$errorMessage[] = "Could not open sql file: " . $test_sql_file . ".  If this file does not exist you must download new install files.";
			    }else{
					//run the file - checking for errors at each SQL execution
					$f = fopen($test_sql_file,"r");
					$sqlFile = fread($f,filesize($test_sql_file));
					$sqlArray = explode(";",$sqlFile);

					//Process the sql file by statements
					foreach ($sqlArray as $stmt) {
					   if (strlen(trim($stmt))>3){

							$result = mysql_query($stmt);
							if (!$result){
								$errorMessage[] = mysql_error() . "<br /><br />For statement: " . $stmt;
								 break;
							}
					    }
					}
				}

				//once this check has passed we can run the entire ddl/dml script
				if (count($errorMessage) == 0){
					if (!file_exists($sql_file)) {
						$errorMessage[] = "Could not open sql file: " . $sql_file . ".  If this file does not exist you must download new install files.";
					}else{
						//run the file - checking for errors at each SQL execution
						$f = fopen($sql_file,"r");
						$sqlFile = fread($f,filesize($sql_file));
						$sqlArray = explode(';',$sqlFile);

						//Process the sql file by statements
						foreach ($sqlArray as $stmt) {
						   if (strlen(trim($stmt))>3){
                               
								$result = mysql_query($stmt);
								if (!$result){
									$errorMessage[] = mysql_error() . "<br /><br />For statement: " . $stmt;
									 break;
								}
							}
						}

					}
				}
                
                //next check the usage database exists
				$dbcheck = @mysql_select_db("$usage_database_name");
				if (!$dbcheck) {
					$errorMessage[] = "Unable to access the usage database '" . $usage_database_name . "'.  Please verify it has been created.<br />MySQL Error: " . mysql_error();
				}else{

					//passed db host, name check, test that user can select from License database
					$result = mysql_query("SELECT outlierID FROM " . $usage_database_name . ".Outlier WHERE outlierLevel = '1';");
					if (!$result){
						$errorMessage[] = "Unable to select from the Outlier table in database '" . $usage_database_name . "' with user '" . $database_username . "'.  Please complete the Usage install and verify the database has been set up.  Error: " . mysql_error();
					}
				}
                
			}
		}

	}

	if (count($errorMessage) > 0){
		$step="2";
	}

}else if ($step == "4"){

	//first, validate all fields are filled in
	$database_host = trim($_POST['database_host']);
	$database_username = trim($_POST['database_username']);
	$database_password = trim($_POST['database_password']);
	$database_name = trim($_POST['database_name']);
	$usage_database_name = trim($_POST['usage_database_name']);
	$base_url = trim($_POST['base_url']);

	if (!$database_username) $errorMessage[] = 'User name is required';
	if (!$database_password) $errorMessage[] = 'Password is required';

	//only continue to checking DB connections if there were no errors this far
	if (count($errorMessage) > 0){
		$step="3";
	}else{

		//first check connecting to host
		$link = @mysql_connect("$database_host", "$database_username", "$database_password");
		if (!$link) {
			$errorMessage[] = "Could not connect to the server '" . $database_host . "'<br />MySQL Error: " . mysql_error();
		}else{

			//next check that the database exists
			$dbcheck = @mysql_select_db("$database_name");
			if (!$dbcheck) {
				$errorMessage[] = "Unable to access the database '" . $database_name . "'.  Please verify it has been created.<br />MySQL Error: " . mysql_error();
			}else{
				//passed db host, name check, test that user can select from Reports database
				$result = mysql_query("SELECT reportID FROM " . $database_name . ".Report WHERE reportName like '%Usage%';");
				if (!$result){
					$errorMessage[] = "Unable to select from the Report table in database '" . $database_name . "' with user '" . $database_username . "'.  Error: " . mysql_error();
				}else{
					while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
						$reportID = $row[0];
					}

					//everything checked out, we can write to the configuration file now
					//write the config file
					$configFile = "../admin/configuration.ini";
					$fh = fopen($configFile, 'w');

					if (!$fh){
						$errorMessage[] = "Could not open file " . $configFile . ".  Please verify it's existence.";
					}else{

						$iniData = array();
						$iniData[] = "[settings]";
						$iniData[] = "baseURL=\"" . $base_url . "\"";


						$iniData[] = "\n[database]";
						$iniData[] = "type = \"mysql\"";
						$iniData[] = "host = \"" . $database_host . "\"";
						$iniData[] = "name = \"" . $database_name . "\"";
						$iniData[] = "usageDatabase = \"" . $usage_database_name . "\"";
						$iniData[] = "username = \"" . $database_username . "\"";
						$iniData[] = "password = \"" . $database_password . "\"";


						fwrite($fh, implode("\n",$iniData));
						fclose($fh);
					}

				}

			}
		}

	}

	if (count($errorMessage) > 0){
		$step="3";
	}


}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CORAL Reports Installation</title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>
<center>
<table style='width:700px;'>
<tr>
<td style='vertical-align:top;'>
<div style="text-align:left;">


<?php if(!$step){ ?>

	<h3>Welcome to a new CORAL Usage Reporting installation!</h3>
	This installation will:
	<ul>
		<li>Check that you are running PHP 5</li>
		<li>Connect to MySQL and create the CORAL Usage Reporting tables</li>
		<li>Test the database connection the CORAL Usage Reporting application will use </li>
		<li>Set up the config file with settings you choose</li>
	</ul>

	<br />
	To get started you should have:
	<ul>
		<li>MySQL Schema created for CORAL Usage Reporting Module - recommended name is coral_reporting_prod.  Each CORAL module has separate user permissions and requires a separate schema.</li>
		<li>It is required that you install the CORAL Usage Module first.  Also have the schema name for the CORAL Usage Database.  For more information about inter-operability refer to the user guide or technical documentation.</li>
		<li>Verify the reports/admin/ directory has write permissions for this script to write the configuration file.</li>
		<li>If you would like the link resolver URL to appear for each title on the reports you will need your resolver's base URL</li>
		<li>Host, username and password for MySQL with permissions to create tables</li>
		<li>It is recommended for security to have a different username and password for CORAL with only select, insert, update and delete privileges to CORAL schemas</li>
	</ul>


	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type='hidden' name='step' value='1'>
	<input type="submit" value="Continue" name="submit">
	</form>


<?php
//first step - check system info and verify php 5
} else if ($step == '1') {
	ob_start();
    phpinfo(-1);
    $phpinfo = array('phpinfo' => array());
    if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
    foreach($matches as $match){
        if(strlen($match[1]))
            $phpinfo[$match[1]] = array();
        elseif(isset($match[3]))
            $phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
        else
            $phpinfo[end(array_keys($phpinfo))][] = $match[2];
    }




    ?>

	<h3>Getting system info and verifying php version</h3>
	<ul>
	<li>System: <?php echo $phpinfo['phpinfo']['System']; ?></li>
    <li>PHP version: <?php echo phpversion(); ?></li>
    <li>Server API: <?php echo $phpinfo['phpinfo']['Server API'];?></li>
	</ul>

	<br />

	<?php


	if (phpversion() >= 5){
	?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type='hidden' name='step' value='2'>
		<input type="submit" value="Continue" name="submit">
		</form>
	<?php
	}else{
		echo "<span style='font-size=115%;color:red;'>PHP 5 is not installed on this server!  Installation will not continue.</font>";
	}

//second step - ask for DB info to run DDL
} else if ($step == '2') {

	if (!$database_host) $database_host='localhost';
	if (!$database_name) $database_name='coral_reporting_prod';
	if (!$usage_database_name) $usage_database_name='coral_usage_prod';
	?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<h3>MySQL info with permissions to create tables and Link Resolver info</h3>
		<?php
			if (count($errorMessage) > 0){
				echo "<span style='color:red'><b>The following errors occurred:</b><br /><ul>";
				foreach ($errorMessage as $err)
					echo "<li>" . $err . "</li>";
				echo "</ul></span>";
			}
		?>
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr>
			<tr>
				<td>&nbsp;Database Host</td>
				<td>
					<input type="text" name="database_host" value='<?php echo $database_host; ?>' style="width:250px;">
				</td>
			</tr>
			<tr>
				<td>&nbsp;Database Schema Name</td>
				<td>
					<input type="text" name="database_name" style="width:250px;" value="<?php echo $database_name; ?>">
				</td>
			</tr>
			<tr>
				<td>&nbsp;Database Username</td>
				<td>
					<input type="text" name="database_username" style="width:250px;" value="<?php echo $database_username; ?>">
				</td>
			</tr>
			<tr>
				<td>&nbsp;Database Password</td>
				<td>
					<input type="password" name="database_password" style="width:250px;" value="<?php echo $database_password; ?>">
				</td>
			</tr>

			<tr>
				<td>&nbsp;Usage Module Schema Name</td>
				<td>
					<input type="text" name="usage_database_name" style="width:250px;" value="<?php echo $usage_database_name; ?>">
				</td>
			</tr>

			<tr>
				<td>&nbsp;Link Resolver Base URL (optional)</td>
				<td>
					<textarea id="base_url" name="base_url" style="width:250px;" rows="3"><?php echo $base_url; ?></textarea>
				</td>
			</tr>

			<tr>
				<td colspan=2>&nbsp;</td>
			</tr>
			<tr>
				<td align='left'>&nbsp;</td>
				<td align='left'>
				<input type='hidden' name='step' value='3'>
				<input type="submit" value="Continue" name="submit">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="Cancel" onclick="document.location.href='index.php'">
				</td>
			</tr>

		</table>
		</form>
<?php
//third step - ask for DB info to log in from CORAL
} else if ($step == '3') {
	?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<h3>MySQL user for CORAL web application - with select, insert, update, delete privileges to CORAL schemas</h3>
		*It's recommended but not required that this user is different than the one used on the prior step
		<?php
			if (count($errorMessage) > 0){
				echo "<br /><span style='color:red'><b>The following errors occurred:</b><br /><ul>";
				foreach ($errorMessage as $err)
					echo "<li>" . $err . "</li>";
				echo "</ul></span>";
			}
		?>
		<input type="hidden" name="database_host" value='<?php echo $database_host; ?>'>
		<input type="hidden" name="database_name" value="<?php echo $database_name; ?>">
		<input type="hidden" name="usage_database_name" value="<?php echo $usage_database_name; ?>">
		<input type="hidden" name="resolver" value="<?php echo $resolver; ?>">
		<input type="hidden" name="base_url" value="<?php echo $base_url; ?>">
		<input type="hidden" name="sid" value="<?php echo $sid?>">
		<input type="hidden" name="client_identifier" value="<?php echo $client_identifier; ?>">

		<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr>
			<tr>
				<td>&nbsp;Database Username</td>
				<td>
					<input type="text" name="database_username" size="30" value="<?php echo $database_username; ?>">
				</td>
			</tr>
			<tr>
				<td>&nbsp;Database Password</td>
				<td>
					<input type="password" name="database_password" size="30" value="<?php echo $database_password; ?>">
				</td>
			</tr>

			<tr>
				<td colspan=2>&nbsp;</td>
			</tr>
			<tr>
				<td align='left'>&nbsp;</td>
				<td align='left'>
				<input type='hidden' name='step' value='4'>
				<input type="submit" value="Continue" name="submit">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="Cancel" onclick="document.location.href='index.php'">
				</td>
			</tr>

		</table>
		</form>
<?php
}else if ($step == '4'){ ?>
	<h3>CORAL Usage Reporting installation is now complete!</h3>
	It is recommended you now:
	<ul>
		<li>Set up your .htaccess file</li>
		<li>Remove the /install/ directory for security purposes</li>
		<li>As advanced notice, reports will not show any data until data is in the Usage Module and Providers/Publishers are set in the Report Options of the Usage Module.  Refer to the Usage Statistics User Manual for more information about this.</li>
	</ul>

<?php
}
?>

</td>
</tr>
</table>
<br />
</center>


</body>
</html>
