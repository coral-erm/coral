<?php

		$workflow = new Workflow();
		$workflowArray = $workflow->allAsArray();

		$userGroup = new UserGroup();
		$userGroupArray = $userGroup->allAsArray();
		?>

		<div class='adminHeader header'>
			<h3 class='adminRightHeader'>
				<?php
					echo _("Workflow Setup");
					//user groups are required to set workflows up so display this message if there arent any
					?>
			</h3>
				<span class='addElement'>
					<?php
					if (count($userGroupArray) >0){
						echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminWorkflowForm&workflowID=\",528,750)'  class='thickbox'><img id='addWorflowSetup' src='images/plus.gif' title='"._("add workflow")."' /></a>";


					}else{
						echo "<i>"._("You must set up at least one user group before you can add workflows")."</i>";
					}
					?>
				</span>
				
			</div>
			<?php
		if (is_array($workflowArray) && count($workflowArray) > 0) {
			?>
			<table class='linedDataTable table-striped table-border'>
			<thead>
				<tr>
				<th scope="col"><?php echo _("Acquisition Type");?></th>
				<th scope="col"><?php echo _("Resource Format");?></th>
				<th scope="col"><?php echo _("Resource Type");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				</tr>
			</thead>
			<tbody>
				<?php

				foreach($workflowArray as $wf) {

					if (($wf['resourceFormatIDValue'] != '') && ($wf['resourceFormatIDValue'] != '0')){
                        $resourceFormat = new ResourceFormat(new NamedArguments(array('primaryKey' => $wf['resourceFormatIDValue'])));
                        $rfName = $resourceFormat->shortName;
                    } else {
                        $rfName = 'any';
                    }

					if (($wf['acquisitionTypeIDValue'] != '') && ($wf['acquisitionTypeIDValue'] != '0')){
                        $acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $wf['acquisitionTypeIDValue'])));
                        $atName = $acquisitionType->shortName;
                    } else {
                        $atName = 'any';
                    }
					if (($wf['resourceTypeIDValue'] != '') && ($wf['resourceTypeIDValue'] != '0')){
						$resourceType = new ResourceType(new NamedArguments(array('primaryKey' => $wf['resourceTypeIDValue'])));
						$rtName = $resourceType->shortName;
					}else{
						$rtName = 'any';
					}

					echo "<tr>";
					echo "<th scope='row'>" . $atName . "</th>";
					echo "<td>" . $rfName . "</td>";
					echo "<td>" . $rtName . "</td>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminWorkflowForm&workflowID=" . $wf['workflowID'] . "\",528,750)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a>";
					echo "<a href='javascript:duplicateWorkflow(" . $wf['workflowID'] . ")'><img src='images/notes.gif' alt='"._("duplicate")."' title='"._("duplicate")."'></a>";
					echo "<a href='javascript:deleteWorkflow(\"Workflow\", " . $wf['workflowID'] . ");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
			</tbody>
			</table>
			<?php

		}else{
			echo "<p>". _("(none found)")."</p>";
		}



		?>

		<div class='adminHeader header'>
			<h3 class='adminRightHeader'><?php echo _("User Group Setup"); ?></h3>
				<span class='addElement'>
					<?php
					echo "<a href='javascript:void(0)'  onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUserGroupForm&userGroupID=\",400,305)' class='thickbox'><img id='addUserGroup' src='images/plus.gif' title='"._("add user group")."' /></a>";
					?>
				</span>
		</div>
			<?php
		if (is_array($userGroupArray) && count($userGroupArray) > 0) {
			?>
			<table class='linedDataTable table-border table-striped'>
			<thead>
				<tr>
				<th scope="col"><?php echo _("Group Name");?></th>
				<th scope="col"><?php echo _("Email Address");?></th>
				<th scope="col"><?php echo _("Users");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				</tr>
			</thead>
			<tbody>
				<?php

				foreach($userGroupArray as $ug) {
					$userGroup = new UserGroup(new NamedArguments(array('primaryKey' => $ug['userGroupID'])));
					echo "<tr>";
					echo "<th scope='row'>" . $userGroup->groupName . "</th>";
					echo "<td>" . $userGroup->emailAddress . "</td>";
					echo "<td>";
					foreach ($userGroup->getUsers() as $groupUser){
						echo $groupUser->getDisplayName . "<br />";
					}
					echo "</td>";
					// echo "<td><a href='ajax_forms.php?action=getAdminUserGroupForm&userGroupID=" . $userGroup->userGroupID . "&height=400&width=305&modal=true' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a></td>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUserGroupForm&userGroupID=" . $userGroup->userGroupID . "\",500,405)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a>";
					echo "<a href='javascript:deleteWorkflow(\"UserGroup\", " . $userGroup->userGroupID . ");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
			</tbody>
			</table>
			<?php

		}else{
			echo "<p>". _("(none found)")."</p>";
		}




?>
