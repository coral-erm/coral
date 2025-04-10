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


		if (is_array($usersArray) && count($usersArray) > 0) {
			?>
			<table class='table-border table-striped'>
				<thead>
					<tr>
						<th scope="col"><?php echo('Login ID'); ?></th>
						<th scope="col"><?php echo('Admin?'); ?></th>
						<th class="actions"><?php echo('Actions'); ?></th>
					</tr>
				</thead>
				<?php

				foreach($usersArray as $userArray) {
					if ($userArray['adminInd'] =='Y' || $userArray['adminInd'] == '1'){
						$isAdmin='Y';
					}else{
						$isAdmin='N';
					}

					echo "<tr>";
					echo "<th scope='row'>" . $userArray['loginID'] . "</th>";
					echo "<td>" . $isAdmin . "</td>";
					echo "<td class='actions'><button onclick='myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm&loginID=" . $userArray['loginID'] . "&height=230&width=315&modal=true\",400,350)' class='thickbox'><img src='images/edit.gif' alt='"._("edit password or admin status")."' title='"._("edit password or admin status")."'></button>";
					echo "<button type='button' class='btn deleteUser' id='" . $userArray['loginID'] . "'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></button></td>";
					echo "</tr>";
				}

				?>
			</table>
			<button type="button" class="btn" onclick='myDialog("ajax_forms.php?action=getAdminUserUpdateForm&loginID=&height=215&width=315&modal=true",400,350)' class='thickbox' id='addUser'><?php echo _("add new user")?></button>
			<?php

		}else{
			echo "(none found)<br /><button type='button' class='btn' onclick='myDialog(\"ajax_forms.php?action=getUserUpdateForm&loginID=&height=275&width=315&modal=true\",300,350)' class='thickbox' id='addUser'>"._("add new user")."</button>";
		}

		break;






	default:
			if (empty($action))
        return;
       printf(_("Action %s not set up!"), $action);
       break;


}


?>

