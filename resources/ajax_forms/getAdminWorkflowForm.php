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

		//check to see if there are unselected formats, acquisition types, or types.
		$noAcqType = ($workflow->acquisitionTypeIDValue == NULL);
		$noFormat = ($workflow->resourceFormatIDValue == NULL);
		$noResourceType = ($workflow->resourceTypeIDValue == NULL);
		//used to determine ordering - default to empty
		$key = '0';
?>
		<div id='div_resourceForm'>
		<form id='resourceForm'>
		<input type='hidden' name='editWFID' id='editWFID' value='<?php echo $workflowID; ?>'>

		<div class='formTitle'><h2 class='headerText'><?php echo _("Edit Workflow");?></h2></div>

		<span class='error' id='span_errors'></span>
		<!-- TODO: a11y: eliminate nested table -->
		<table class='noBorder'>
			<tr>
				<td>

					<!-- TODO: a11y: eliminate nested table -->
					<h3 class='surroundBoxTitle'><?php echo _("Resource Entry Requirements");?></h3>

					<table class='surroundBox'>
						<tr>
							<td>
								<table class='noBorder'>
								<tr>
								<td><label for='acquisitionTypeID'><?php echo _("Acquisition Type:");?></label></td>
								<td>
								<select name='acquisitionTypeID' id='acquisitionTypeID' class='changeSelect' >
								<?php 
									$selected = ($noAcqType) ? "selected" : "";
									echo "<option value='' {$selected} disabled></option>";
									foreach ($acquisitionTypeArray as $acquisitionType){
										$selected = (trim(strval($acquisitionType['acquisitionTypeID'])) == trim(strval($workflow->acquisitionTypeIDValue))) ? "selected" : "";
										echo "<option value='{$acquisitionType['acquisitionTypeID']}' {$selected}>{$acquisitionType['shortName']}</option>";
									}
								?>
								</select>
								</td>


								<td><label for='resourceFormatID'><?php echo _("Format:");?></label></td>
								<td>
								<select name='resourceFormatID' id='resourceFormatID' class='changeSelect'>
								<?php
									$selected = ($noFormat) ? "selected" : "";
									echo "<option value='' {$selected} disabled></option>";
									foreach ($resourceFormatArray as $resourceFormat){
										$selected = (trim(strval($resourceFormat['resourceFormatID'])) == trim(strval($workflow->resourceFormatIDValue))) ? "selected" : "";
										echo "<option value='{$resourceFormat['resourceFormatID']}' {$selected}>{$resourceFormat['shortName']}</option>";
									}
								?>
								</select>
								</td>

								<td><label for='resourceTypeID'><?php echo _("Type:");?></label></td>
								<td>
								<select name='resourceTypeID' id='resourceTypeID' class='changeSelect'>
								<?php
									$selected = ($noResourceType) ? "selected" : "";
									echo "<option value='' {$selected} disabled></option>";
									foreach ($resourceTypeArray as $resourceType){
										$selected = (trim(strval($resourceType['resourceTypeID'])) == trim(strval($workflow->resourceTypeIDValue))) ? "selected" : "";
										echo "<option value='{$resourceType['resourceTypeID']}' {$selected}>{$resourceType['shortName']}</option>";
									}
								?>
								</select>

								</td>
								</tr>
								</table>
							</td>
						</tr>
					</table>

					<h3 class='surroundBoxTitle'><?php echo _("Workflow Steps");?></h3>
					<!-- TODO: a11y: eliminate nested table -->
					<table class='surroundBox'>
						<tr>
							<td>
								<table class='noBorder noMargin newStepTable'>
									<thead>
										<tr>
											<td>&nbsp;</td>
											<td id="stepNameLabel"><?php echo _("Name:");?></td>
											<td id="userGroupIDLabel"><?php echo _("Approval/Notification Group:");?></td>
											<td id="priorStepIDLabel"><?php echo _("Parent Step");?></td>
											<td>&nbsp;</td>
										</tr>
									</thead>
									<tbody>
										<tr class='newStepTR' id="inputRow">
											<td class='seqOrder'>
												<button type='button' class='btn moveArrow' direction='up' ><img src='images/arrow_up.gif'></button>
												<button type='button' class='btn moveArrow' direction='down'><img src='images/arrow_down.gif'></button>
											</td>
											<td>
												<input type='text' value ='' name='stepName' class='stepName changeInput' aria-labelledby="stepNameLabel" />
											</td>
											<td>
												<select name='userGroupID' class='changeSelect userGroupID' aria-labelledby="userGroupIDLabel">
													<?php
														foreach ($userGroupArray as $userGroup){
															echo "<option value='{$userGroup['userGroupID']}'>{$userGroup['groupName']}</option>";
														}
													?>
												</select>
											</td>

											<td >
												<select id='priorStepID' class='changeSelect priorStepID' aria-labelledby="priorStepIDLabel">
													<option value='' disabled></option>
												</select>
											</td>

											<td>
												<input class='addStep add-button' title='<?php echo _("add step");?>' type='button' value='<?php echo _("Add");?>'/>
											</td>
										</tr>
									</tbody>
								</table>
								<div class='error' id='div_errorStep'></div>


								<?php
									$stepCount = count($stepArray);
									$stepsExist = ($stepCount > 0);
									if($stepsExist){ ?>
										<hr>
										<table class='noBorder noMargin stepTable'>
											<?php 
												foreach ($stepArray as $key => $step){
													$key=$key+1;
													$newStep = ($step->priorStepID) ? new NamedArguments(array('primaryKey' => $step->priorStepID)) : NULL;
													$priorStep = new Step($newStep);
												?>
													<tr class='stepTR'>

														<td class='seqOrder <?php if ($key == ($stepCount)){ echo "lastClass"; } ?>' id='<?php echo $step->stepID; ?>' key='<?php echo $key; ?>'>
															<button type='button' class='btn moveArrow' direction='up' ><img src='images/arrow_up.gif'></button>
															<button type='button' class='btn moveArrow' direction='down'><img src='images/arrow_down.gif'></button>
														</td>

														<td>
															<input type='text' value = '<?php echo $step->stepName; ?>' class='stepName changeInput' aria-labelledby="stepNameLabel" />
														</td>

														<td>
															<select class='changeSelect userGroupID' aria-labelledby="userGroupIDLabel">
																<?php
																	foreach ($userGroupArray as $userGroup){
																		$selected = ($userGroup['userGroupID'] == $step->userGroupID) ? "selected" : "";
																		echo "<option value='{$userGroup['userGroupID']}' {$selected}>{$userGroup['groupName']}</option>";
																	}
																?>
															</select>
														</td>

														<td>
															<select class='changeSelect priorStepID' aria-labelledby="priorStepIDLabel">
															<option value=''></option>
															</select>

															<input type='hidden' class='priorStepKey' key='<?php echo $key; ?>' value='<?php echo $priorStep->displayOrderSequence; ?>'>
														</td>


														<td class="actions">
															<img src='images/cross.gif' alt="<?php echo _("remove this step");?>" title="<?php echo _("remove this step");?>" class='removeStep' />
														</td>

													</tr>

											<?php } ?>
										</table>
									<?php } 
								?>
							</td>
						</tr>
					</table>

				</td>
			</tr>
		</table>
		<hr />

		<p class='actions'>
				<input type='submit' value='<?php echo _("submit");?>' name='submitWorkflowForm' id ='submitWorkflowForm' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog();" class='cancel-button secondary'>
		</p>

		<input type='hidden' id='finalKey' value='<?php echo $key; ?>' />

		<script type="text/javascript" src="js/forms/workflowForm.js?random=<?php echo rand(); ?>"></script>

