<?php
if (!isset($_GET['resourceID'])){
    echo "<div><p>You must supply a valid resource ID.</p></div>";
}else{
    $resourceID = $_GET['resourceID'];
    $resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

    $userGroupObj = new UserGroup();
    $userGroupArray = $userGroupObj->allAsArray();

    $resourceSteps = $resource->getCurrentWorkflowResourceSteps();
    $parentSteps = $resource->getCurrentWorkflowResourceSteps();

    //make form
    ?>
    <div id='div_resourceStepForm'>
        <form id='resourceStepForm'>
            <input type='hidden' name='editRID' id='editRID' value='<?php echo $resourceID; ?>'>

            <div class='formTitle' style='width:705px; margin-bottom:5px;position:relative;'><span class='headerText'>Edit Workflow</span></div>

            <span class='smallDarkRedText' id='span_errors'></span>

            <table class='noBorder' style='width:100%;'>
                <tr style='vertical-align:top;'>
                    <td style='vertical-align:top;position:relative;'>
                        <span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='rule'><b>Workflow Steps</b></label>&nbsp;&nbsp;</span>

                        <table class='surroundBox' style='width:700px;'>
                            <tr>
                                <td>
                                    <table class='noBorder newStepTable' style='width:660px; margin:15px 20px 10px 20px;'>
                                        <tr>
                                            <td><?php echo _("Order"); ?></td>
                                            <td><?php echo _("Reminder delay (in days)"); ?></td>
                                            <td><?php echo _("Name"); ?></td>
                                            <td><?php echo _("Approval/Notification group"); ?></td>
                                            <td><?php echo _("Parent Step"); ?></td>
                                            <td><?php echo _("Action"); ?></td>
                                        </tr>
                                        <tr class="newStepTR">

                                            <td style='vertical-align:top;text-align:left;width:48px;' class='seqOrder' key=''><img src='images/transparent.gif' style='width:43px;height:20px;' /></td>
											<td><input type="text" class="mailReminderDelay" size="2" /></td>
                                            <td>
                                            <input type="hidden" class="stepID" value="-1">
                                            <input type="text" class="stepName"></td>
                                            <td>
                                                <select name='userGroupID' id='userGroupID' style='width:150px;' class='changeSelect userGroupID'>
                                                        <?php
                                                        foreach ($userGroupArray as $userGroup){
                                                            echo "<option value='" . $userGroup['userGroupID'] . "'>" . $userGroup['groupName'] . "</option>\n";
                                                        }
                                                        ?>
                                                </select>
                                            </td>
                                            <td>
                                               <select name='priorStepID' id='priorStepID' style='width:150px;' class='changeSelect priorStepID'>
                                                    <option value=""></option>
                                                    <?php
                                                    foreach ($parentSteps as $parentStep) {
                                                        echo "<option value='" . $parentStep->stepID . "'>" . $parentStep->stepName . "</option>\n";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><a href="javascript:void(0)"><img src="images/add.gif" class="addStep" alt="Add" /></a></td>
                                        </tr>
                                    </table>

                                        <hr />

                                    <table class='noBorder stepTable' style='width:660px; margin:15px 20px 10px 20px;'>
                                        <?php
                                        $count = count($resourceSteps);
                                        $i = 0;
				if ($resourceSteps > 0){
                                        foreach ($resourceSteps as $key => $resourceStep) {
                                        $disabled = ($resourceStep->stepEndDate) ? 'disabled="disabled"':'';
                                        $i++;
                                        if ($i == $count) $lastStepClass = ' class="lastStep"';


					$key=$key+1;

					if ($step->priorStepID){
						$priorStep= new ResourceStep(new NamedArguments(array('primaryKey' => $step->priorStepID)));
					}else{
						$priorStep= new ResourceStep();
					}

                                        ?>
                                        <tr class="stepTR">
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

											<td><input type="text" class="mailReminderDelay" size="2" value="<?php echo $resourceStep->mailReminderDelay; ?>" /></td>
                                            <td>
                                            <input type="hidden" class="action" value="keep">
                                            <input type="hidden" class="stepID" value="<?php echo $resourceStep->resourceStepID; ?>">
                                            <input type="text" class="stepName changeInput" value="<?php echo $resourceStep->stepName; ?>"></td>
                                            <td style='vertical-align:top;text-align:left;'>
                                                <select name='userGroupID' id='userGroupID' style='width:150px;' class='changeSelect userGroupID' <?php echo $disabled; ?>>
                                                    <?php
                                                    foreach ($userGroupArray as $userGroup){
                                                        $selected = ($userGroup['userGroupID'] == $resourceStep->userGroupID)? 'selected':'';
                                                        echo "<option value='" . $userGroup['userGroupID'] . "' ".$selected.">" . $userGroup['groupName'] . "</option>\n";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select name='priorStepID' id='priorStepID' style='width:150px;' class='changeSelect priorStepID'>
                                                    <option value=""></option>
                                                    <?php
                                                    foreach ($parentSteps as $parentStep) {
                                                        $selected = ($parentStep->stepID != null && $parentStep->stepID == $resourceStep->priorStepID) ? 'selected="selected"' : '';
                                                        echo "<option value='" . $parentStep->stepID . "' ".$selected.">" . $parentStep->stepName . "</option>\n";
                                                    }
                                                    ?>
                                                </select>
							<input type='hidden' class='priorStepKey' key='<?php echo $key; ?>' value='<?php echo $priorStep->displayOrderSequence; ?>'>

                                            </td>
                                            <td><a href="javascript:void(0)"><img src="images/cross.gif" class="removeStep" alt="Delete" /></a></td>
                                        </tr>
                                        <?php
                                        }
                                        }
                                        ?>
                                    </table>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class='noBorderTable' style='width:125px;'>
                <tr>
                    <td style='text-align:left'><input type='button' value='submit' name='submitCurrentWorkflowForm' id ='submitCurrentWorkflowForm'></td>
                    <td style='text-align:right'><input type='button' value='cancel' onclick="kill(); tb_remove();"></td>
                </tr>
            </table>

            <input type='hidden' id='finalKey' value='<?php echo $key; ?>' />
            <script type="text/javascript" src="js/forms/currentWorkflowForm.js?random=<?php echo rand(); ?>"></script>
        </form>
    </div>

    <?php

}
