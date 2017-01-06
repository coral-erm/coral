<?php
		$resourceID = $_GET['resourceID'];
		$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
		$status = new Status();

		$completeStatusID = $status->getIDFromName('complete');
		$archiveStatusID = $status->getIDFromName('archive');

		$resourceSteps = $resource->getResourceSteps();

		if (count($resourceSteps) == "0"){
			if (($resource->statusID != $completeStatusID) && ($resource->statusID != $archiveStatusID)){
				echo "<i>"._("No workflow steps have been set up for this resource's combination of Acquisition Type and Resource Format.")."<br />"._("If you think this is in error, please contact your workflow administrator.")."</i>";
			}else{
				echo "<i>"._("Not entered into workflow.")."</i>";
			}
		}else{
			?>
			<table class='linedDataTable' style='width:100%;margin-bottom:5px;'>
				<tr>
				<th style='background-color:#dad8d8;width:350px;'><?php echo _("Step");?></th>
					<th style='background-color:#dad8d8;'>&nbsp;</th>
				<th style='background-color:#dad8d8;width:150px;'><?php echo _("Group");?></th>
				<th style='background-color:#dad8d8;width:120px;'><?php echo _("Start Date");?></th>
				<th style='background-color:#dad8d8;width:250px;'><?php echo _("Complete");?></th>
					<th style='background-color:#dad8d8;'><?php echo _("Delete");?></th>
				</tr>
			<?php
			$openStep=0;
            $archivingDate = 'init';
            $archivedWorkflow = false;
			foreach($resourceSteps as $resourceStep){

				$userGroup = new UserGroup(new NamedArguments(array('primaryKey' => $resourceStep->userGroupID)));
				$eUser = new User(new NamedArguments(array('primaryKey' => $resourceStep->endLoginID)));

				$classAdd = "style='background-color: white;'";
				//make the row gray if it is complete or not started
				if ((($resourceStep->stepEndDate) && ($resourceStep->stepEndDate != "0000-00-00")) || (!$resourceStep->stepStartDate) || ($resource->statusID == $archiveStatusID) || ($resource->statusID == $completeStatusID)){
					$classAdd = "class='complete'";
				}

                $stepClass = $resourceStep->archivingDate ? " class='archivedWorkflow' style='display:none'"  : '';
				?>
				<tr<?php echo $stepClass; ?>>
                <?php
                if ($archivingDate != $resourceStep->archivingDate) {
                    $archivingDate = $resourceStep->archivingDate;
                    $stepIndication = $resourceStep->archivingDate ? _("Workflow archived on") . " $archivingDate" : _("Current workflow");
                    if ($resourceStep->archivingDate && $archivedWorkflow == false) {
                        $archivedWorkflow = true; 
                        echo "<td colspan='6'><em><strong>Archived Workflows</strong></em></td></tr><tr$stepClass>";
                    }

                    echo "<td colspan='6'><em><strong>$stepIndication</strong></em></td></tr><tr$stepClass>";
                }
                ?> 

				<td <?php echo $classAdd; ?> ><?php echo $resourceStep->stepName; ?></td>
				<td <?php echo $classAdd; ?> ><?php if (is_null_date($resourceStep->stepEndDate)){
						echo '<a href="ajax_forms.php?action=getResourceStepForm&amp;resourceStepID='.$resourceStep->resourceStepID.'&amp;height=250&amp;width=750&amp;modal=true" class="thickbox"><img src="images/edit.gif" alt="edit" title="edit"></a>';
					} ?></td>
				<td <?php echo $classAdd; ?> ><?php echo $userGroup->groupName; ?></td>
				<td <?php echo $classAdd; ?> ><?php if ($resourceStep->stepStartDate) { echo format_date($resourceStep->stepStartDate); } ?></td>
				<td <?php echo $classAdd; ?> >
				<?php
					if ($resourceStep->stepEndDate) {
						if (($eUser->firstName) || ($eUser->lastName)){
							echo format_date($resourceStep->stepEndDate) . _(" by ") . $eUser->firstName . " " . $eUser->lastName;
						}else{
							echo format_date($resourceStep->stepEndDate) . _(" by ") . $resourceStep->endLoginID;
						}
					}else{
						//add if user is in group or an admin and resource is not completed or archived
						if ((($user->isAdmin) || ($user->isInGroup($resourceStep->userGroupID))) && ($resourceStep->stepStartDate) &&  ($resource->statusID != $archiveStatusID) && ($resource->statusID != $completeStatusID)){
							echo "<a href='javascript:void(0);' class='markComplete' id='" . $resourceStep->resourceStepID . "'>"._("mark complete")."</a>";
						}
						//track how many open steps there are
						$openStep++;
					}?>
				</td>
				<td style="text-align:center;"> <?php
					//add a delete step option, there will be a modal confirmation before delete.
					if (!$resourceStep->stepEndDate){
						echo '<a href="javascript:void(0);" class="removeResourceStep" id="'. $resourceStep->resourceStepID .'"><img src="images/cross.gif" alt="delete" title="delete"></a>';
					} ?>
				</td>
				</tr>
				<?php


			}
			echo "</table>";
		}


		if ($resource->workflowRestartLoginID){
			$rUser = new User(new NamedArguments(array('primaryKey' => $resource->workflowRestartLoginID)));

			//workflow restart is being used for both completion and restart - until the next database upgrade
			//this was marked complete...
			if (($openStep > 0) && ($resource->statusID == $completeStatusID)){
				if ($rUser->firstName){
					echo "<i>"._("Workflow completed on ") . format_date($resource->workflowRestartDate) . _(" by ") . $rUser->firstName . " " . $rUser->lastName . "</i><br />";
				}else{
					echo "<i>"._("Workflow completed on ") . format_date($resource->workflowRestartDate) . _(" by ") . $resource->workflowRestartLoginID . "</i><br />";
				}
			}else{
				if ($rUser->firstName){
					echo "<i>"._("Workflow restarted on ") . format_date($resource->workflowRestartDate) . " by " . $rUser->firstName . " " . $rUser->lastName . "</i><br />";
				}else{
					echo "<i>"._("Workflow restarted on ") . format_date($resource->workflowRestartDate) . (" by ") . $resource->workflowRestartLoginID . "</i><br />";
				}
			}
		}


		echo "<br /><br />";

		if ($user->canEdit()){
            echo "<img src='images/pencil.gif' />&nbsp;&nbsp;<a href='javascript:void(0);' class='restartWorkflow'>"._("restart workflow")."</a><br />";
        ?>
                <div class="restartWorkflowDiv" id="restartWorkflowDiv" style="display:none;padding:20px;">
                    <form name="restartWorkflowForm" id="restartWorkflowForm">

                        <label for="workflowArchivingDate"><?php echo _("Select a workflow to restart"); ?></label>: 
                        <select id="workflowArchivingDate">
                            <option value="<?php echo $resource->getCurrentWorkflowID(); ?>"><?php echo _("Current workflow"); ?></option>
                            <?php
                            $workflow = new Workflow();
                            $workflowArray = $workflow->allAsArray();
                            foreach ($workflowArray as $wf) {
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

                                echo "<option value=\"" . $wf['workflowID'] . "\">$atName / $rfName / $rtName</option>";
                            }
                            ?>
                        </select><br />
                        <input type="button" value="submit" class="restartWorkflowSubmit" id="<?php echo $resourceID; ?>" />
                    </form>
                    <br />
                </div>
                <?php
				echo "<img id='displayArchivedWorkflowsIcon' src='images/plus_12.gif' />&nbsp;&nbsp;<a href='javascript:void(0);' class='displayArchivedWorkflows' id='" . $resourceID . "'>"._("display archived workflows")."</a><br />";
				echo "<img src='images/pencil.gif' />&nbsp;&nbsp;<a href='ajax_forms.php?action=getCurrentWorkflowForm&height=450&width=750&modal=true&resourceID=$resourceID' class='thickbox'>"._("edit the current workflow")."</a><br />";

				echo "<img src='images/pencil.gif' />&nbsp;&nbsp;<a href='javascript:void(0);' class='markResourceComplete' id='" . $resourceID . "'>"._("mark entire workflow complete")."</a><br />";
		}

?>

