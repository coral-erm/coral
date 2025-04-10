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

/* CORAL setup */
include_once 'directory.php';

$util = new Utility();



if (isset($_GET['service'])){
	$service = $_GET['service'];
}else{
	$service = $util->getCORALURL();
}

$errorMessage = '';
$message='';
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


$moduleTitle = _('Authentication');
include_once '../templates/header.php';

?>
<form name="loginForm" method="post" action="index.php?service=<?php echo htmlentities($service); ?>">

	<div id="login-form" class="card" role="main">
		<div class="card-header">
	        <div id="img-title"><img src="images/authtitle.png" alt="" /></div>
	        <h1 class="fw-normal"><?php echo _("eRM Authentication"); ?></h1>
    	</div>

		<div class="card-body" id="main-content">
			<?php if ($message) { ?>
				<p class='warning center'><?php echo $message; ?></p>
			<?php } ?>
			<?php if ($errorMessage) { ?>
				<p class='error center'><?php echo $errorMessage; ?></p>
			<?php } ?>
			<div class="form-grid">
				<label for='loginID'><?php echo _("Login ID:")?></label>
				<input type='text' id='loginID' name='loginID' value="<?php echo $inputLoginID; ?>" autocomplete="username" />

				<label for='password'><?php echo _("Password:")?></label>
				<input type='password' id='password' name='password' value='' autocomplete="current-password" />

				<p class="checkbox center">
					<input type='checkbox' id='remember' name='remember' value='Y' <?php echo $rememberChecked; ?> />
					<label for='remember'><?php echo _("Remember my login ID")?></label>
				</p>
			</div>
			
			<!-- this sits outside the form grid -->
			<p class="center">
			  <label for="lang"><?php echo _("Change language:");?></label>
				<?php $lang_name->getLanguageSelector(); ?>
			</p>
			<p class="center">
				<input type="submit" value="<?php echo _('Login')?>" id="loginbutton" class="loginButton" />
			</p>
		</div>

		<div class="card-footer">
			<p><a href='admin.php' title="<?php echo _("Admin page")?>"><?php echo _("Admin page")?></a></p>
		</div>
	</div>
	
</form>
<script type="text/javascript" src="js/index.js"></script>
</body>
</html>
