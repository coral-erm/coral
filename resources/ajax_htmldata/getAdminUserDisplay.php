<?php
		$instanceArray = array();
		$user = new User();
		$tempArray = array();

		foreach ($user->allAsArray() as $tempArray) {

			$privilege = new Privilege(new NamedArguments(array('primaryKey' => $tempArray['privilegeID'])));

			$tempArray['priv'] = $privilege->shortName;

			array_push($instanceArray, $tempArray);
		}



		if (count($instanceArray) > 0){
			?>
			<div class="adminHeader">
			<div class="adminRightHeader"><?php echo _("Users");?></div>
			<div class="addElement" style="margin-right: 4px"><?php echo "<a href='javascript:void(0);' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm&loginID=\",300,400)' class='thickbox' id='addUser'><img id='addUserGroup' src='images/plus.gif' title='" . _("add") . "' />";?></a></div>
			</div>
			<table class='linedDataTable' style='width:570px;margin-bottom:5px;'>
				<tr>
				<th><?php echo _("Login ID");?></td>
				<th><?php echo _("First Name");?></td>
				<th><?php echo _("Last Name");?></td>
				<th><?php echo _("Privilege");?></td>
				<th><?php echo _("View Accounts");?></td>
				<th><?php echo _("Email Address");?></td>
				<th>&nbsp;</td>
				<th>&nbsp;</td>
				</tr>
				<?php

				foreach($instanceArray as $instance) {
					if ($instance['accountTabIndicator'] == '1') {
						$accountTab = 'Y';
					}else{
						$accountTab = 'N';
					}

					echo "<tr>";
					echo "<td>" . $instance['loginID'] . "</td>";
					echo "<td>" . $instance['firstName'] . "</td>";
					echo "<td>" . $instance['lastName'] . "</td>";
					echo "<td>" . $instance['priv'] . "</td>";
					echo "<td>" . $accountTab . "</td>";
					echo "<td>" . $instance['emailAddress'] . "</td>";
					echo "<td><a href='javascript:void(0);' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm&loginID=" . $instance['loginID'] . "\",300,400)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit user")."'></a></td>";
					echo "<td><a href='javascript:deleteUser(\"" . $instance['loginID'] . "\")'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
			</table>

			<?php

		}else{
			echo _("(none found)")."<br /><a href='javascript:void(0);' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm&loginID=\", 300,400)' class='thickbox' id='addUser'>"._("add new user")."</a>";
		}

?>
