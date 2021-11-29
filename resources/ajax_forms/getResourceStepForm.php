<?php
if (!isset($_GET['resourceStepID'])){
    echo "<div><p>You must supply a valid resource step ID.</p></div>";
}else{
    $resourceStepID = $_GET['resourceStepID'];
    $resourceStep = new ResourceStep(new NamedArguments(array('primaryKey' => $resourceStepID)));
    //get step name & group
    $stepName = $resourceStep->attributes['stepName'];
    $stepGroupID = $resourceStep->attributes['userGroupID'];
    $orderNum = $resourceStep->attributes['displayOrderSequence'];
    $remainingSteps = $resourceStep->getNumberOfOpenSteps();
    //echo "the step name is ".$stepName.", and the group id is ". $stepGroup.".<br>\n";
    //get possible groups
    $userGroupArray = array();
    $userGroupObj = new UserGroup();
    $userGroupArray = $userGroupObj->allAsArray();

    //make form
    ?>
    <div id='div_resourceStepForm'>
        <form id='resourceStepForm'>
            <input type='hidden' name='editRSID' id='editRSID' value='<?php echo $resourceStepID; ?>'>
            <input type='hidden' name='orderNum' id='orderNum' value='<?php echo $orderNum; ?>'>
            <input type='hidden' name='currentGroupID' id='currentGroupID' value='<?php echo $stepGroupID; ?>'>
            <div class='formTitle' style='width:705px; margin-bottom:5px;position:relative;'><span class='headerText'><?php echo _("Edit Resource Step");?></span></div>

            <span class='smallDarkRedText' id='span_errors'></span>

            <table class='noBorder' style='width:100%;'>
                <tr style='vertical-align:top;'>
                    <td style='vertical-align:top;position:relative;'>
                        <span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='rule'><b><?php echo _("Reassign Resource Step");?></b></label>&nbsp;&nbsp;</span>

                        <table class='surroundBox' style='width:700px;'>
                            <tr>
                                <td>
                                    <table class='noBorder' style='width:660px; margin:15px 20px 10px 20px;'>
                                        <tr>
                                            <!--                                                <td>Step name: <pre>--><?php //var_dump($resourceStep); ?><!--</pre></td>-->
                                            <td><?php echo _("Step name: ") . $stepName;?></td>
                                            <td style='vertical-align:top;text-align:left;'>
                                                <label for='userGroupID'><?php echo _("Group: ");?></label>
                                                <select name='userGroupID' id='userGroupID' style='width:150px;' class='changeSelect userGroupID'>
                                                    <?php

                                                    foreach ($userGroupArray as $userGroup){
                                                        $selected = ($userGroup['userGroupID']==$stepGroupID)? 'selected':'';
                                                        echo "<option value='" . $userGroup['userGroupID'] . "' ".$selected.">" . $userGroup['groupName'] . "</option>\n";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td><input name="applyToAll" id='applyToAll' type="checkbox"><?php echo _("Apply to all later steps?");?></input></td>
                                        </tr>
                                    </table>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <label for="note">Note:</label>
            <textarea name="note" rows="7" cols="50" id="note"><?php echo $resourceStep->note; ?></textarea>
            <table class='noBorderTable' style='width:125px;'>
                <tr>
                    <td style='text-align:left'><input type='button' class='submit-button' value='<?php echo _("submit");?>' name='submitResourceStepForm' id ='submitResourceStepForm'></td>
                    <td style='text-align:right'><input type='button' class='cancel-button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()"></td>
                </tr>
            </table>

            <script type="text/javascript" src="js/forms/resourceStepForm.js"></script>
        </form>
    </div>

    <?php

}
