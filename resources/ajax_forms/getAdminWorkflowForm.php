<?php
		$workflowID = $_GET['workflowID'];

		if ($workflowID){
			$workflow = new Workflow(new NamedArguments(array('primaryKey' => $workflowID)));
		}else{
			$workflow = new Workflow();
		}

		$stepArray = $workflow->getSteps();
		$stepDDArray = $workflow->getSteps();

		//get all acquisition types for output in drop down
		$acquisitionTypeArray = array();
		$acquisitionTypeObj = new AcquisitionType();
		$acquisitionTypeArray = $acquisitionTypeObj->sortedArray();

		//get all resource formats for output in drop down
		$resourceFormatArray = array();
		$resourceFormatObj = new ResourceFormat();
		$resourceFormatArray = $resourceFormatObj->sortedArray();

		//get all resource types for output in drop down
		$resourceTypeArray = array();
		$resourceTypeObj = new ResourceType();
		$resourceTypeArray = $resourceTypeObj->allAsArray();


		//get all acquisition types for output in drop down
		$userGroupArray = array();
		$userGroupObj = new UserGroup();
		$userGroupArray = $userGroupObj->allAsArray();


		//used to determine ordering - default to empty
		$key = '0';
?>
		<div id='div_resourceForm'>
		<form id='resourceForm'>
		<input type='hidden' name='editWFID' id='editWFID' value='<?php echo $workflowID; ?>'>

		<div class='formTitle' style='width:705px; margin-bottom:5px;position:relative;'><span class='headerText'><?php echo _("Edit Workflow");?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class='noBorder' style='width:100%;'>
		<tr style='vertical-align:top;'>
		<td style='vertical-align:top;position:relative;'>


			<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='rule'><b><?php echo _("Resource Entry Requirements");?></b></label>&nbsp;&nbsp;</span>

			<table class='surroundBox' style='width:700px;'>
			<tr>
			<td>
				<table class='noBorder' style='width:660px; margin:15px 20px 10px 20px;'>
				<tr>
				<td style='vertical-align:top;text-align:left;font-weight:bold;'><label for='acquisitionTypeID'><?php echo _("Acquisition Type:");?></label></td>
				<td>
				<select name='acquisitionTypeID' id='acquisitionTypeID' style='width:100px;' class='changeSelect' >
				<option value=''></option>
				<?php
				foreach ($acquisitionTypeArray as $acquisitionType){
					if (!(trim(strval($acquisitionType['acquisitionTypeID'])) != trim(strval($workflow->acquisitionTypeIDValue)))){
						echo "<option value='" . $acquisitionType['acquisitionTypeID'] . "' selected>" . $acquisitionType['shortName'] . "</option>\n";
					}else{
						echo "<option value='" . $acquisitionType['acquisitionTypeID'] . "'>" . $acquisitionType['shortName'] . "</option>\n";
					}
				}
				?>
				</select>
				</td>


				<td style='vertical-align:top;text-align:left;font-weight:bold;'><label for='resourceFormatID'><?php echo _("Format:");?></label></td>
				<td>
				<select name='resourceFormatID' id='resourceFormatID' style='width:100px;' class='changeSelect'>
				<option value=''></option>
				<?php
				foreach ($resourceFormatArray as $resourceFormat){
					if (!(trim(strval($resourceFormat['resourceFormatID'])) != trim(strval($workflow->resourceFormatIDValue)))){
						echo "<option value='" . $resourceFormat['resourceFormatID'] . "' selected>" . $resourceFormat['shortName'] . "</option>\n";
					}else{
						echo "<option value='" . $resourceFormat['resourceFormatID'] . "'>" . $resourceFormat['shortName'] . "</option>\n";
					}
				}
				?>
				</select>
				</td>

				<td style='vertical-align:top;text-align:left;font-weight:bold;'><label for='resourceTypeID'><?php echo _("Type:");?></label></td>
				<td>
				<select name='resourceTypeID' id='resourceTypeID' style='width:100px;' class='changeSelect' >
				<option value=''></option>
				<?php
				foreach ($resourceTypeArray as $resourceType){
					if (!(trim(strval($resourceType['resourceTypeID'])) != trim(strval($workflow->resourceTypeIDValue)))){
						echo "<option value='" . $resourceType['resourceTypeID'] . "' selected>" . $resourceType['shortName'] . "</option>\n";
					}else{
						echo "<option value='" . $resourceType['resourceTypeID'] . "'>" . $resourceType['shortName'] . "</option>\n";
					}
				}
				?>
				</select>

				</td>
				</tr>
				</table>
			</td>
			</tr>
			</table>

			<div style='height:20px;'>&nbsp;</div>


			<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='workflowSteps'><b><?php echo _("Workflow Steps");?></b></label>&nbsp;&nbsp;</span>

			<table class='surroundBox' style='width:700px;'>
			<tr>
			<td>

				<table class='noBorder noMargin newStepTable' style='width:660px;  margin:15px 20px 0px 20px;'>
				<tr>
					<td style='vertical-align:top;text-align:left;font-weight:bold;width:48px;'>&nbsp;</td>
					<td style='vertical-align:top;text-align:left;font-weight:bold;width:218px;'><?php echo _("Name:");?></td>
					<td style='vertical-align:top;text-align:left;font-weight:bold;width:175px;'><?php echo _("Approval/Notification Group:");?></td>
					<td style='vertical-align:top;text-align:left;font-weight:bold;width:110px;'><?php echo _("Parent Step");?></td>
					<td style='vertical-align:top;text-align:center;width:40px;'>&nbsp;</td>
				</tr>

				<tr class='newStepTR' id=''>

				<td style='vertical-align:top;text-align:left;width:48px;' class='seqOrder' key=''><img src='images/transparent.gif' style='width:43px;height:20px;' /></td>

				<td style='vertical-align:top;text-align:left;width:218px;'>
					<input type='text' value = '' style='width:200px;' class='stepName changeInput' />
				</td>

				<td style='vertical-align:top;text-align:left;width:175px;'>
					<select style='width:150px; ' class='changeSelect userGroupID'>
					<?php
					foreach ($userGroupArray as $userGroup){
						echo "<option value='" . $userGroup['userGroupID'] . "'>" . $userGroup['groupName'] . "</option>\n";
					}
					?>
					</select>
				</td>

				<td style='vertical-align:top;text-align:left;width:175px;'>
					<select style='width:150px;' class='changeSelect priorStepID'>
					<option value=''></option>
					</select>
					<input type='hidden' class='priorStepKey' key='' value=''>
				</td>

				<td style='vertical-align:top;text-align:center;width:40px;'>
				<a href='javascript:void();'><input class='addStep add-button' title='<?php echo _("add step");?>' type='button' value='<?php echo _("Add");?>'/></a>
				</td>

				</tr>
				</table>
				<div class='smallDarkRedText' id='div_errorStep' style='margin:0px 20px 5px 26px;'></div>

				<table class='noBorder noMargin stepTable' style='width:660px;margin:0px 20px 10px 20px;'>
				<tr>
				<td colspan='5'>
					<hr style='width:650px;margin:0px 0px 15px 5px;' />
				</td>
				</tr>

				<?php
				$stepCount = count($stepArray);

				if ($stepCount > 0){

					foreach ($stepArray as $key => $step){
					$key=$key+1;

					if ($step->priorStepID){
						$priorStep= new Step(new NamedArguments(array('primaryKey' => $step->priorStepID)));
					}else{
						$priorStep= new Step();
					}
					?>
						<tr class='stepTR'>

						<td style='vertical-align:top;text-align:left;width:48px;' class='seqOrder <?php if ($key == ($stepCount)){ echo "lastClass"; } ?>' id='<?php echo $step->stepID; ?>' key='<?php echo $key; ?>'>
							<?php

								$arrowDown = "<a href='javascript:void(0);' class='moveArrow' direction='down'><img src='images/arrow_down.gif'></a>";
								$arrowUp = "<a href='javascript:void(0);' class='moveArrow' direction='up' ><img src='images/arrow_up.gif'></a>";
								$trans = "<img src='images/transparent.gif' style='width:20px;height:20px;' />";

								if ($key == 1){

									//if this is the only step, display the large transparent gif instead of arrows
									if (($stepCount) == 1){
										echo "<img src='images/transparent.gif' style='width:43px;height:10px;' />";
									}else{
										echo $trans . "&nbsp;" . $arrowDown;
									}


								}else if ($key == ($stepCount)){
									echo $arrowUp . "&nbsp;" . $trans;
								}else{
									echo $arrowUp . "&nbsp;" . $arrowDown;
								}
							?>
						</td>

						<td style='vertical-align:top;text-align:left;width:218px;'>
						<input type='text' value = '<?php echo $step->stepName; ?>' style='width:200px;' class='stepName changeInput' />
						</td>

						<td style='vertical-align:top;text-align:left;width:175px;'>
							<select style='width:150px;' class='changeSelect userGroupID'>
							<?php
							foreach ($userGroupArray as $userGroup){
								if ($userGroup['userGroupID'] == $step->userGroupID){
									echo "<option value='" . $userGroup['userGroupID'] . "' selected>" . $userGroup['groupName'] . "</option>\n";
								}else{
									echo "<option value='" . $userGroup['userGroupID'] . "'>" . $userGroup['groupName'] . "</option>\n";
								}
							}
							?>
							</select>
						</td>

						<td style='vertical-align:top;text-align:left;width:175px;'>
							<select style='width:150px;' class='changeSelect priorStepID'>
							<option value=''></option>
							</select>

							<input type='hidden' class='priorStepKey' key='<?php echo $key; ?>' value='<?php echo $priorStep->displayOrderSequence; ?>'>
						</td>


						<td style='vertical-align:top;text-align:center;width:40px;'>
							<a href='javascript:void();'><img src='images/cross.gif' alt="<?php echo _("remove this step");?>" title="<?php echo _("remove this step");?>" class='removeStep' /></a>
						</td>

						</tr>

					<?php
					}
				}

				?>

				</table>



			</td>
			</tr>
			</table>

		</td>
		</tr>
		</table>


		<hr style='width:708px;margin:15px 0px 10px 0px;' />

		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitWorkflowForm' id ='submitWorkflowForm' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="kill(); tb_remove();" class='cancel-button'></td>
			</tr>
		</table>

		<input type='hidden' id='finalKey' value='<?php echo $key; ?>' />

		<script type="text/javascript" src="js/forms/workflowForm.js?random=<?php echo rand(); ?>"></script>

