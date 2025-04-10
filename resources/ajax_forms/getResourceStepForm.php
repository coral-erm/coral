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
            <div class='formTitle'><h2 class='headerText'><?php echo _("Edit Resource Step");?></h2></div>

            <span class='error' id='span_errors'></span>


            <h3><?php echo _("Reassign Resource Step");?></h3>

            <table>
                <tr>
                    <!--                                                <td>Step name: <pre>--><?php //var_dump($resourceStep); ?><!--</pre></td>-->
                    <th scope="row"><?php echo _("Step name: ") . $stepName;?></th>
                    <td>
                        <label for='userGroupID'><?php echo _("Group: ");?></label>
                        <select name='userGroupID' id='userGroupID' class='changeSelect userGroupID'>
                            <?php

                            foreach ($userGroupArray as $userGroup){
                                $selected = ($userGroup['userGroupID']==$stepGroupID)? 'selected':'';
                                echo "<option value='" . $userGroup['userGroupID'] . "' ".$selected.">" . $userGroup['groupName'] . "</option>\n";
                            }
                            ?>
                        </select>
                    </td>
                    <td><label><input name="applyToAll" id='applyToAll' type="checkbox"><?php echo _("Apply to all later steps?");?></input></label></td>
                </tr>
            </table>
        
            <label for="note"><?php echo _('Note:'); ?></label>
            <textarea name="note" rows="7" cols="50" id="note"><?php echo $resourceStep->note; ?></textarea>
            
            <p class='actions'>
                <input type='submit' class='submit-button primary' value='<?php echo _("submit");?>' name='submitResourceStepForm' id ='submitResourceStepForm'>
                <input type='button' class='cancel-button secondary' value='<?php echo _("cancel");?>' onclick="myCloseDialog()">
            </p>

            <script type="text/javascript" src="js/forms/resourceStepForm.js"></script>
        </form>
    </div>

    <?php

}
