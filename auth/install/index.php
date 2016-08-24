<?php
require_once("classes/DBService.php");
require_once("classes/Postdata.php");
require_once("classes/Util.php");

$db = new DBService;
$database = new Postdata;
$ldap = new Postdata;
$general = new Postdata;
$admin = new Postdata;

//this script runs entire installation process in 5 steps

//take "step" variable to determine which step the current is
$general->acquire('step','0');


//perform field validation(steps 3-5) and database connection tests (steps 3 and 4) and send back to previous step if not working
$errorMessage = array();
if ($general->step == "3"){
	//first, validate all fields are filled in

	if (!$database->acquire('dbhost'))
		$errorMessage[] = 'Host name is required';

	if (!$database->acquire('dbname'))
		$errorMessage[] = 'Database name is required';

	if (!$database->acquire('dbuser'))
		$errorMessage[] = 'User name is required';

	if (!$database->acquire('dbpass'))
		$errorMessage[] = 'Password is required';
	
	//only continue to checking DB connections if there were no errors this far
	if (count($errorMessage) > 0){
		$general->step="2";
	}else{

		//first check connecting to host
		if ($errmsg = $db->connect("$database->dbhost", "$database->dbuser", "$database->dbpass")) {
			$errorMessage[] = $errmsg;
		}else{

			//next check that the database exists
			if ($errmsg = $db->selectDB("$database->dbname")) {
				$errorMessage[] = $errmsg;
			}else{

				//make sure the tables don't already exist - otherwise this script will overwrite all of the data!
				$query = "SELECT count(*) count FROM information_schema.`TABLES` WHERE table_schema = '$database->dbname' AND table_name='User' and table_rows > 0";

				//if User table exists, error out
				if (!$row = $db->query($query)->fetch_array()){
					$errorMessage[] = "Please verify your database user has access to select from the information_schema MySQL metadata database.";
				} else if ($row['count'] > 0 ){
					$errorMessage[] = "The Authentication tables already exist.  If you intend to upgrade, please run upgrade.php instead.  If you would like to perform a fresh install you will need to manually drop all of the Authentication tables in this schema first.";
				} else {

					//passed db host, name check, can open/run file now
					//make sure SQL file exists
					$test_sql_file = "test_create.sql";
					$sql_file = "create_tables_data.sql";

					if (!file_exists($test_sql_file)) {
						$errorMessage[] = "Could not open sql file: $test_sql_file.  If this file does not exist you must download new install files.";
					}else{
						//run the file - checking for errors at each SQL execution
						$f = fopen($test_sql_file,"r");
						$sqlFile = fread($f,filesize($test_sql_file));
						$sqlArray = explode(";",$sqlFile);

						//Process the sql file by statements
						foreach ($sqlArray as $stmt) {
							if (strlen(trim($stmt))>3 && !$db->query($stmt)){
								$errorMessage[] = $db->error()."<br /><br />For statement: ".$stmt;
								 break;
							}
						}

					}


					//once this check has passed we can run the entire ddl/dml script
					if (count($errorMessage) == 0){
						if (!file_exists($sql_file)) {
							$errorMessage[] = "Could not open sql file: $sql_file.  If this file does not exist you must download new install files.";
						} else {
							//run the file - checking for errors at each SQL execution
							$f = fopen($sql_file,"r");
							$sqlFile = fread($f,filesize($sql_file));
							$sqlArray = explode(';',$sqlFile);

							//Process the sql file by statements
							foreach ($sqlArray as $stmt) {
								if (strlen(trim($stmt))>3 && !$db->query($stmt)){
									$errorMessage[] = $db->error()."<br /><br />For statement: $stmt";
									 break;
								}
							}

						}
					}
				}
			}
		}

	}

	if (count($errorMessage) > 0){
		$general->step="2";
	}

}else if ($general->step == "4"){

	//first, validate all fields are filled in

	$database->acquire('dbhost');

	$database->acquire('dbname');

	if (!$database->acquire('dbuser'))
		$errorMessage[] = 'User name is required';

	if (!$database->acquire('dbpass'))
		$errorMessage[] = 'Password is required';

	if (!$admin->acquire('coral_username'))
		$errorMessage[] = 'CORAL Admin Username is required';
	if (!$admin->acquire('coral_password'))
		$errorMessage[] = 'CORAL Admin Password is required';

	$passwordPrefix = Util::randomString(45);
	$password 		= Util::hashString('sha512', $passwordPrefix . $admin->coral_password);
	$create_admin_query = "INSERT INTO `User` VALUES ('$admin->coral_username','$password','$passwordPrefix','Y')";

	if (!$general->acquire('session_timeout'))
		$errorMessage[] = 'Session timeout is required';

	$ldap->acquire('ldap_enabled',false,true);
	$ldap->acquire('ldap_host');
	$ldap->acquire('ldap_port');
	$ldap->acquire('ldap_search_key');
	$ldap->acquire('ldap_base_dn');
	$ldap->acquire('ldap_bind_account');
	$ldap->acquire('ldap_bind_password');

    if ($ldap->ldap_enabled) {
        if (!$ldap->ldap_host)       $errorMessage[] = "LDAP Host is required for LDAP";
        if (!$ldap->ldap_search_key) $errorMessage[] = "LDAP Search Key is required for LDAP";
        if (!$ldap->ldap_base_dn)    $errorMessage[] = "LDAP Base DN is required for LDAP";
    }

	//only continue to checking DB connections if there were no errors this far
	if (count($errorMessage) > 0){
		$general->step="3";

	//first check connecting to host
	} else if ($errmsg = $db->connect("$database->dbhost", "$database->dbuser", "$database->dbpass")) {
		$errorMessage[] = $errmsg;

	//next check that the database exists
	} else if ($errmsg = $db->selectDB("$database->dbname")) {
		$errorMessage[] = $errmsg;
	} else if (!$db->query($create_admin_query)) {
		$errorMessage[] = "Failed to create CORAL Admin";
	//passed db host, name check, test that user can select from Auth database
	} else if (!$db->query("SELECT loginID FROM ".$database->dbname.".User WHERE loginID like '%".$admin->coral_username."%';")){
		$errorMessage[] = "Unable to select from the User table in database '$database->dbname' with user '$database->dbuser'.  Error: ".$db->error();
	}

	//only continue if there were no errors this far
	if (count($errorMessage) > 0){
		$general->step="3";

	//write the config file
	} else {
		$configFile = "../admin/configuration.ini";
		$fh = fopen($configFile, 'w');

		if (!$fh){
			$errorMessage[] = "Could not open file $configFile.  Please verify you can write to the /admin/ directory.";
		}else{

			$iniData = array();
			$iniData[] = "[settings]";
			$iniData[] = "timeout=".$general->session_timeout;

			$iniData[] = "\n[database]";
			$iniData[] = "type = \"mysql\"";
			$iniData[] = "host = \"$database->dbhost\"";
			$iniData[] = "name = \"$database->dbname\"";
			$iniData[] = "username = \"$database->dbuser\"";
			$iniData[] = "password = \"$database->dbpass\"";

            $iniData[] = "\n[ldap]";
			$iniData[] = "ldap_enabled = \"".($ldap->ldap_enabled?'Y':'N')."\"";
			$iniData[] = "host = \"$ldap->ldap_host\"";
			$iniData[] = "port = \"$ldap->ldap_port\"";
			$iniData[] = "search_key = \"$ldap->ldap_search_key\"";
			$iniData[] = "base_dn = \"$ldap->ldap_base_dn\"";
			$iniData[] = "bindAccount = \"$ldap->ldap_bind_account\"";
			$iniData[] = "bindPass = \"$ldap->ldap_bind_password\"";

			fwrite($fh, implode("\n",$iniData));
			fclose($fh);
		}


	}

	if (count($errorMessage) > 0){
		$general->step="3";
	}

}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CORAL Installation</title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<script src="../../js/plugins/jquery-1.11.3.js"></script>
<script src="js/index.js"></script>
</head>
<body>
<center>
<table style='width:700px;'>
<tr>
<td style='vertical-align:top;'>
<div style="text-align:left;">


<?php if($general->step=='0'){ ?>

	<h3>Welcome to a new CORAL Auth installation!</h3>
	This installation will:
	<ul>
		<li>Check that you are running PHP 5</li>
		<li>Connect to MySQL and create the CORAL Auth tables</li>
		<li>Test the database connection the CORAL Auth application will use </li>
		<li>Set up the config file with settings you choose</li>
	</ul>

	<br />
	To get started you should:
	<ul>
		<li>Create a MySQL Schema created for CORAL Auth Module - recommended name is coral_auth_prod.  Each CORAL module has separate user permissions and requires a separate schema.</li>
		<li>Know your host, username and password for MySQL with permissions to create tables</li>
		<li>It is recommended for security to have a different username and password for CORAL with only select, insert, update and delete privileges to CORAL schemas</li>
		<li>Verify that your /admin/ directory is writable by server during the installation process (chmod 777).  After installation you should chmod it back.</li>
	</ul>


	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
	<input type='hidden' name='step' value='1'>
	<input type="submit" value="Continue" name="submit">
	</form>


<?php
//first step - check system info and verify php 5
} else if ($general->step == '1') {
	ob_start();
    phpinfo(-1);
    $phpinfo = array('phpinfo' => array());
    if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
    foreach($matches as $match){
        if(strlen($match[1]))
            $phpinfo[$match[1]] = array();
        else if(isset($match[3]))
            $phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
        else
            $phpinfo[end(array_keys($phpinfo))][] = $match[2];
    }




    ?>

	<h3>Getting system info and verifying php version</h3>
	<ul>
	<li>System: <?php echo $phpinfo['phpinfo']['System'];?></li>
    <li>PHP version: <?php echo phpversion();?></li>
    <li>Server API: <?php echo $phpinfo['phpinfo']['Server API'];?></li>
	</ul>

	<br />

	<?php


	if (phpversion() >= 5){
	?>
		<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
		<input type='hidden' name='step' value='2'>
		<input type="submit" value="Continue" name="submit">
		</form>
	<?php
	}else{
		echo "<span style='font-size=115%;color:red;'>PHP 5 is not installed on this server!  Installation will not continue.</font>";
	}

//second step - ask for DB info to run DDL
} else if ($general->step == '2') {

	if (!isset($database->dbhost)) $database->dbhost='localhost';
	if (!isset($database->dbname)) $database->dbname='coral_auth_prod';
    if (!isset($database->dbuser)) $database->dbuser = "";
    if (!isset($database->dbpass)) $database->dbpass = "";
	?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		<h3>MySQL info with permissions to create tables</h3>
		<?php
			if (count($errorMessage) > 0){
				echo "<span style='color:red'><b>The following errors occurred:</b><br /><ul>";
				foreach ($errorMessage as $err)
					echo "<li>" . $err . "</li>";
				echo "</ul></span>";
			}

		?>
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<?php $data=array(
				array('text','Database Host','dbhost',$database->dbhost),
				array('text','Database Schema Name','dbname',$database->dbname),
				array('text','Database Username','dbuser',$database->dbuser),
				array('password','Database Password',"dbpass",$database->dbpass)
			);
			foreach ($data as $vals) { ?>
			<tr>
				<td>&nbsp;<?php echo $vals[1]?></td>
				<td>
					<input type="<?php echo $vals[0]?>" name="<?php echo $vals[2]?>" size="30" value='<?php echo $vals[3]?>'>
				</td>
			</tr>
			<?php } ?>
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
} else if ($general->step == '3') {

	if (!isset($general->session_timeout))
		$general->session_timeout='3600';

	if (!isset($admin->coral_username))
		$admin->coral_username = 'coral';

	if (!isset($admin->coral_password))
		$admin->coral_password = 'admin';

	$ldap->acquire('ldap_enabled',false,true);
	$ldap->acquire('ldap_host');
	$ldap->acquire('ldap_port');
	$ldap->acquire('ldap_search_key');
	$ldap->acquire('ldap_base_dn');
	$ldap->acquire('ldap_bind_account');
	$ldap->acquire('ldap_bind_password');

	?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		<h3>MySQL user for CORAL web application - with select, insert, update, delete privileges to CORAL schemas</h3>
		*It's recommended but not required that this user is different than the one used on the prior step
		<?php if (count($errorMessage) > 0){ ?>
			<br /><span style='color:red'><b>The following errors occurred:</b><br /><ul>
			<li><?php echo implode("</li>\n<li>",$errorMessage)?></li>
			</ul></span>
		<?php } ?>
		<input type="hidden" name="dbhost" value='<?php echo $database->dbhost?>'>
		<input type="hidden" name="dbname" value="<?php echo $database->dbname?>">

		<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<?php $data=array(
				array('text','Database Username','dbuser',$database->dbuser),
				array('password','Database Password',"dbpass",$database->dbpass)
			);
			foreach ($data as $vals) {?>
			<tr>
				<td>&nbsp;<?php echo $vals[1]?></td>
				<td>
					<input type='<?php echo $vals[0]?>' name='<?php echo $vals[2]?>' size="30" value="<?php echo $vals[3]?>">
				</td>
			</tr>
			<?php } ?>

            <tr>
				<td colspan=2>&nbsp;</td>
			</tr>
		</table>
		<h3>Admin Account</h3>
*The default admin username is preset here. It's recommended to change it here. If using LDAP, enter LDAP credentials for admin account
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<?php $data=array(
				array('text','CORAL - Admin Username','coral_username',$admin->coral_username),
				array('password','CORAL - Admin Password','coral_password',$admin->coral_password),
				array("text",'Session Timeout - in seconds',"session_timeout",$general->session_timeout)
			);
			foreach ($data as $vals) {?>
			<tr>
				<td>&nbsp;<?php echo $vals[1]?></td>
				<td>
					<input type='<?php echo $vals[0]?>' name='<?php echo $vals[2]?>' size="30" value="<?php echo $vals[3]?>">
				</td>
			</tr>
			<?php } ?>

		</table>
		<h3>LDAP Settings</h3>
		<table>
			<tr>
				<td>&nbsp;Enable LDAP</td>
				<td>
					<input type="checkbox" id="ldap_enabled" name="ldap_enabled" size="30" <?php echo $ldap->ldap_enabled?'checked="true"':''?> onclick="ShowLDAP()">
				</td>
			</tr>

			<?php $data=array(
				array('text',    'LDAP Host',         "ldap_host",         $ldap->ldap_host),
				array("text",    'LDAP Port',         "ldap_port",         $ldap->ldap_port),
				array("text",    'LDAP Search Key',   "ldap_search_key",   $ldap->ldap_search_key),
				array("text",    'LDAP Base DN',      "ldap_base_dn",      $ldap->ldap_base_dn),
				array("text",    'LDAP Bind Account', "ldap_bind_account", $ldap->ldap_bind_account),
				array("password",'LDAP Bind Password',"ldap_bind_password",$ldap->ldap_bind_password)
			);
			foreach ($data as $vals) {?>
			<tr>
                <td>&nbsp;<?php echo $vals[1]?></td>
                <td>
                    <input type="<?php echo $vals[0]?>" name="<?php echo $vals[2]?>" class="ldap" size="30" value="<?php echo $vals[3]?>" <?php echo $ldap->ldap_enabled?'':'disabled="disabled"'?>>
                </td>
            </tr>
			<?php } ?>

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
    <script>
    ShowLDAP();
    </script>
<?php
//fourth step - ask for other settings in configuration.ini
} else if ($general->step == '4') {

?>
	<h3>CORAL Authentication installation is now complete!</h3>
	It is recommended you now:
	<ul>
		<li>Set up your .htaccess file</li>
		<li>Remove the /install/ directory for security purposes</li>
		<li>Set up your users on the <a href='../admin.php'>admin screen</a>.  You may log in initially with coral/admin.</li>
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
