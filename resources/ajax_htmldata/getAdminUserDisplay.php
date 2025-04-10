<?php
		$instanceArray = array();
		$user = new User();
		$tempArray = array();

		foreach ($user->allAsArray() as $tempArray) {

			$privilege = new Privilege(new NamedArguments(array('primaryKey' => $tempArray['privilegeID'])));

			$tempArray['priv'] = $privilege->shortName;

			array_push($instanceArray, $tempArray);
		}



		if (is_array($instanceArray) && count($instanceArray) > 0) {
			?>
			<div class="adminHeader header">
				<h3 class="adminRightHeader"><?php echo _("Users");?></h3>
				<span class="addElement"><?php echo "<a href='javascript:void(0);' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm&loginID=\",300,400)' class='thickbox' id='addUser'><img id='addUserGroup' src='images/plus.gif' title='" . _("add") . "' />";?></a></div>
			</div>
			<table class='table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("Login ID");?></td>
				<th scope="col"><?php echo _("First Name");?></td>
				<th scope="col"><?php echo _("Last Name");?></td>
				<th scope="col"><?php echo _("Privilege");?></td>
				<th scope="col"><?php echo _("View Accounts");?></td>
				<th scope="col"><?php echo _("Email Address");?></td>
				<th scope="col"><span class="visually-hidden"><?php echo _("Edit");?></span></td>
				<th scope="col"><span class="visually-hidden"><?php echo _("Delete");?></span></td>
				</tr>
			</thead>
			<tbody>
				<?php

				foreach($instanceArray as $instance) {
					if ($instance['accountTabIndicator'] == '1') {
						$accountTab = 'Y';
					}else{
						$accountTab = 'N';
					}

					echo "<tr>";
					echo "<th scope='row'>" . $instance['loginID'] . "</th>";
					echo "<td>" . $instance['firstName'] . "</td>";
					echo "<td>" . $instance['lastName'] . "</td>";
					echo "<td>" . $instance['priv'] . "</td>";
					echo "<td>" . $accountTab . "</td>";
					echo "<td class='url'>" . $instance['emailAddress'] . "</td>";
					echo "<td class='actions'><a href='javascript:void(0);' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm&loginID=" . $instance['loginID'] . "\",300,400)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit user")."'></a></td>";
					echo "<td class='actions'><a href='javascript:deleteUser(\"" . $instance['loginID'] . "\")'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
				</tbody>
			</table>

			<?php

		}else{
			echo _("(none found)")."<br /><a href='javascript:void(0);' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm&loginID=\", 300,400)' class='thickbox' id='addUser'>"._("add new user")."</a>";
		}

?>
