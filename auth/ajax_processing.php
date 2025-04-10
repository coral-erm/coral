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
}

$user = new User(new NamedArguments(array('primaryKey' => $loginID)));


if (($user->isAdmin) && ($user->getOpenSession())){

	switch ($_GET['action']) {
		case 'submitUser':
			$util = new Utility();

			//if this is an existing user
			if ((isset($_POST['editLoginID'])) && ($_POST['editLoginID'] != '')){
				$sUser = new User(new NamedArguments(array('primaryKey' => $_POST['editLoginID'])));
			}else{
				//set up new user
				$sUser = new User();
				$sUser->loginID = $_POST['loginID'];
			}

			//only update it if it was sent
			if (isset($_POST['password']) && ($_POST['password'] != '')){
				$prefix = $util->randomString(45);
				$sUser->password 		= $util->hashString('sha512', $prefix . $_POST['password']);
				$sUser->passwordPrefix	= $prefix;
			}

			if ($_POST['adminInd'] == "1" || $_POST['adminInd'] == "Y"){
				$sUser->adminInd 		= "Y";
			}else{
				$sUser->adminInd 		= "N";
			}

			try {
				$sUser->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}

			break;

		case 'deleteUser':
			$loginID = $_GET['loginID'];
			$dUser = new User(new NamedArguments(array('primaryKey' => $loginID)));

			try {
				$dUser->delete();
				echo "<span class='success'>";
				echo _("User successfully deleted.");
				echo "</span>";
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}

			break;





		default:
				if (empty($action))
            return;
		   printf(_("Action %s not set up!"), $action);
		   break;

	}
}

?>
