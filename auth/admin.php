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

if (isset($_SESSION['loginID'])){
	$loginID=$_SESSION['loginID'];
    $user = new User(new NamedArguments(array('primaryKey' => $loginID)));
}

if (isset($user) && ($user->isAdmin) && ($user->getOpenSession())){


$moduleTitle = _('Authentication');
include_once '../templates/header.php';
?>
<main id="main-content">
    <article>
		<h2 class='headerText'><?php echo _("Users")?></h2>
		<p>* <?php echo _("Login ID must match the login ID set up in the modules")?></p>

		<div id='div_users'>
			<img src='images/circle.gif'><?php echo _("Processing...")?>
		</div>
		<p>
			<label for="lang"><?php echo _("Change language:");?></label>
			<?php $lang_name->getLanguageSelector(); ?>
		</p>
		<p><a href='index.php' id='login-link'><?php echo _("Login page")?></a></p>
    </article>
</main>

<script type="text/javascript" src="js/admin.js"></script>
<?php include '../templates/footer.php'; ?>
</body>
</html>


<?php

}else{

	if (isset($user) && $user->getOpenSession()){
		header('Location: index.php?service=admin.php&invalid');
        exit; //PREVENT SECURITY HOLE
	}else{
		header('Location: index.php?service=admin.php&admin');
        exit; //PREVENT SECURITY HOLE
	}
}

?>
