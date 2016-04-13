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


include_once 'directory.php';


switch ($_GET['action']) {


	case 'getUsers':
		$userObj = new User();
		$usersArray = $userObj->allAsArray();


		if (count($usersArray) > 0){
			?>
			<table class='linedDataTable' style='width:327px;'>
				<tr>
				<th>Login ID</th>
				<th>Admin?</th>
				<th style='width:20px;'>&nbsp;</th>
				<th style='width:20px;'>&nbsp;</th>
				</tr>
				<?php

				foreach($usersArray as $userArray) {
					if ($userArray['adminInd'] =='Y' || $userArray['adminInd'] == '1'){
						$isAdmin='Y';
					}else{
						$isAdmin='N';
					}

					echo "<tr>";
					echo "<td>" . $userArray['loginID'] . "</td>";
					echo "<td>" . $isAdmin . "</td>";
					echo "<td><a href='ajax_forms.php?action=getAdminUserUpdateForm&loginID=" . $userArray['loginID'] . "&height=230&width=315&modal=true' class='thickbox'><img src='images/edit.gif' alt='"._("edit password or admin status")."' title='"._("edit password or admin status")."'></a></td>";
					echo "<td><a href='javascript:void(0);' class='deleteUser' id='" . $userArray['loginID'] . "'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
			</table>
			<a href='ajax_forms.php?action=getAdminUserUpdateForm&loginID=&height=215&width=315&modal=true' class='thickbox' id='addUser'><?php echo _("add new user")?></a>
			<?php

		}else{
			echo "(none found)<br /><a href='ajax_forms.php?action=getUserUpdateForm&loginID=&height=275&width=315&modal=true' class='thickbox' id='addUser'>"._("add new user")."</a>";
		}

		break;






	default:
       echo _("Action ") . $action . _(" not set up!");
       break;


}


?>

