<?php

/*
**************************************************************************************************************************
** CORAL Authentication Module v. 1.0
**
** Copyright (c) 2011 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/


session_start();

include_once 'directory.php';
$util = new Utility();



if (isset($_GET['service'])){
	$service = $_GET['service'];
}else{
	$service = $util->getCORALURL();
}

$errorMessage = '';
$message='&nbsp;';
$inputLoginID='';
$rememberChecked='';


if(isset($_SESSION['loginID'])){

	$loginID=$_SESSION['loginID'];

	$user = new User(new NamedArguments(array('primaryKey' => $loginID)));

}


//user is trying to log out
if(array_key_exists('logout', $_GET)){


	$user->processLogout();

	$message = _('You are successfully logged out of the system.');

	$user = new User();

	//get login, if set
	$inputLoginID = $user->getRememberLogin();

	if ($inputLoginID){
		$rememberChecked = 'checked';
	}

//the user is trying to log in
}else if (isset($_POST['loginID']) && isset($_POST['password'])){

	$loginID = $_POST['loginID'];
	$password = $_POST['password'];

	$user = new User(new NamedArguments(array('primaryKey' => $loginID)));

	//set login remember cookie if it was checked
	if (isset($_POST['remember'])){
		$user->setRememberLogin();
		$rememberChecked = 'checked';

	}else{
		$user->unsetRememberLogin();
	}


	//perform  login checks
	if ($user->loginID == ''){
		$errorMessage = _("Invalid login ID.  Please try again.");

	//perform login, if failed issue message
	}else{
		if(!$user->processLogin($password)){
			$errorMessage = _("Invalid password.  Please try again.");
			$inputLoginID = $loginID;
		}else{

			//login succeeded, perform redirect
			header('Location: ' . $service) ;
			exit; //PREVENT SECURITY HOLE

		}
	}



//user is already logged in
}else if(isset($_SESSION['loginID'])){

	if ($user->getOpenSession()){
			$message = _("You are already logged in as ") . $loginID . ".<br />" . _("You may log in as another user below,")." <a href='" . $service . "'>"._("return")."</a> "._("or")." <a href='?logout'>". _("logout")."</a>.";
	}

	$inputLoginID = $user->getRememberLogin();

	if ($inputLoginID){
		$rememberChecked = 'checked';
	}


//user comes in new
}else{
	$user = new User();

	//get login, if set
	$inputLoginID = $user->getRememberLogin();

	if ($inputLoginID){
		$rememberChecked = 'checked';
	}

	$message = _("Please enter login credentials to sign in.");

}


//user was just timed out
if(array_key_exists('timeout', $_GET)){

	$errorMessage = _("Your session has timed out.");
	$message = "";

}


//user does not have permissions to enter the module
if(array_key_exists('invalid', $_GET)){

	$errorMessage = _("You do not have permission to enter.")."<br />"._("Please contact an administrator.");
	$message = "";

}



//user needs to access admin page
if(array_key_exists('admin', $_GET)){

	$errorMessage = _("You must log in before accessing the admin page.");
	$message = "";

}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CORAL Authentication</title>
<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
<link rel="SHORTCUT ICON" href="images/favicon.ico" />
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href='https://fonts.googleapis.com/css?family=Open+Sans:100,400,300,600,700' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="js/plugins/jquery.js"></script>
<script type="text/javascript" src="js/plugins/thickbox.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/plugins/Gettext.js"></script>
<?php
    // Add translation for the JavaScript files
    global $http_lang;
    $str = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
    $default_l = $lang_name->getLanguage($str);
    if($default_l==null || empty($default_l)){$default_l=$str;}
    if(isset($_COOKIE["lang"])){
        if($_COOKIE["lang"]==$http_lang && $_COOKIE["lang"] != "en_US"){
            echo "<link rel='gettext' type='application/x-po' href='./locale/".$http_lang."/LC_MESSAGES/messages.po' />";
        }
    }else if($default_l==$http_lang && $default_l != "en_US"){
            echo "<link rel='gettext' type='application/x-po' href='./locale/".$http_lang."/LC_MESSAGES/messages.po' />";
    }
?>
</head>
<body>
<noscript><font face="arial"><?php echo _("JavaScript must be enabled in order for you to use CORAL. However, it seems JavaScript is either disabled or not supported by your browser. To use CORAL, enable JavaScript by changing your browser options, then")." <a href=''>"._("try again")."</a>."?></font></noscript>

<center>
<form name="loginForm" method="post" action="index.php?service=<?php echo htmlentities($service); ?>">

	<br />

	<div style="width:418px;" id="login-form">
		<div id="title-div">
	        <div id="img-title"><img src="images/authtitle.png" /></div>
	        <div id="text-title"><?php echo _("eRM Authentication"); ?></div>
	        <div class="clear"></div>
    	</div>

		<div id="login-content">
			<label style='width:100%;font-weight:normal;'><span class='smallerText'><?php echo $message; ?></span><span class='smallDarkRedText'><?php echo $errorMessage; ?></span></label><br />
			<label for='loginID'><?php echo _("Login ID:")?></label>
			<input type='text' id='loginID' name='loginID' value="<?php echo $inputLoginID; ?>" />
			<br />
			<label for='password'><?php echo _("Password:")?></label>
			<input type='password' id='password' name='password' value='' />
			<br />
	<!-- 		<label for='remember'>&nbsp;</label> -->
			<input type='checkbox' id='remember' name='remember' value='Y' style='margin:1px 5px 0px 0px; padding:0px;' <?php echo $rememberChecked; ?> /><span class='smallText'><?php echo _("Remember my login ID")?></span>

			<br />
			<input type="submit" value="<?php echo _('Login')?>" id="loginbutton" class="loginButton" style='margin-top:17px;' />
		</div>

	</div>
	<div class='boxRight'>
		<p class="fontText"><?php echo _("Change language:");?></p>
		<select name="lang" id="lang" class="dropDownLang">
			<?php
            // Get all translations on the 'locale' folder
            $route='locale';
            $lang[]="en_US"; // add default language
            if (is_dir($route)) { 
                if ($dh = opendir($route)) { 
                    while (($file = readdir($dh)) !== false) {
                        if (is_dir("$route/$file") && $file!="." && $file!=".."){
                            $lang[]=$file;
                        } 
                    } 
                    closedir($dh); 
                } 
            }else {
                echo "<br>"._("Invalid translation route!"); 
            }
            // Get language of navigator
            $defLang = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
            
            // Show an ordered list
            sort($lang); 
            for($i=0; $i<count($lang); $i++){
                if(isset($_COOKIE["lang"])){
                    if($_COOKIE["lang"]==$lang[$i]){
                        echo "<option value='".$lang[$i]."' selected='selected'>".$lang_name->getNameLang($lang[$i])."</option>";
                    }else{
                        echo "<option value='".$lang[$i]."'>".$lang_name->getNameLang($lang[$i])."</option>";
                    }
                }else{
                    if($defLang==substr($lang[$i],0,5)){
                        echo "<option value='".$lang[$i]."' selected='selected'>".$lang_name->getNameLang($lang[$i])."</option>";
                    }else{
                        echo "<option value='".$lang[$i]."'>".$lang_name->getNameLang($lang[$i])."</option>";
                    }
                }
            }
			?>
		</select>
	</div>
	<div class='smallerText' style='text-align:center; margin-top:13px;'><a href='admin.php' title="<?php echo _("Admin page")?>"><?php echo _("Admin page")?></a></div>

</form>


<br />
<br />


</center>
<br />
<br />
    <script>
        /*
         * Functions to change the language with the dropdown
         */
        $("#lang").change(function() {
            setLanguage($("#lang").val());
            location.reload();
        });
        // Create a cookie with the code of language
        function setLanguage(lang) {
			var wl = window.location, now = new Date(), time = now.getTime();
            var cookievalid=2592000000; // 30 days (1000*60*60*24*30)
            time += cookievalid;
			now.setTime(time);
			document.cookie ='lang='+lang+';path=/'+';domain='+wl.host+';expires='+now;
	    }
    </script>
<script type="text/javascript">
//give focus to login form
document.getElementById('loginID').focus();
</script>
<script type="text/javascript" src="js/index.js"></script>

</body>
</html>
