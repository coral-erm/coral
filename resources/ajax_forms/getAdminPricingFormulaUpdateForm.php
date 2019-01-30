<?php

    $pricingFormulaID = $_GET['pricingFormulaID'];

    if ($pricingFormulaID){
        $instance = new PricingFormula(new NamedArguments(array('primaryKey' => $pricingFormulaID)));
    } else {
        $instance = new PricingFormula();
    }
?>
<div id='div_updateForm'>
<form id="pricingFormulaConfig">
<input type='hidden' name='pricingFormulaID' id='pricingFormulaID' value='<?php echo $pricingFormulaID; ?>' />

<div class='formTitle' style='width:280px; margin-bottom:5px;position:relative;'><span class='headerText'><?php if ($pricingFormulaID){ echo _("Edit pricing formula"); } else { echo _("Add pricing formula"); } ?></span></div>
<table>
<tr>
<td><label for="formulaName"><b><?php echo _("Name"); ?>:</b></label></td><td><input type="text" value="<?php echo $instance->shortName; ?>" name="formulaName" /></td>
</tr><tr>
<?php

for ($i = 1; $i <= 10; $i++) {
    $memberName = "field${i}Name";
    echo ("<tr><td><label for='$memberName'><b>" . _("Field") ." $i:</b></label></td><td><input type='text' value=\"" . htmlentities($instance->$memberName) . "\" name='$memberName' /></td></tr>");
}

?>
<tr><td><label for="formula"><b><?php echo _("Formula"); ?>:</b></label></td>
<td><textarea name="formula" id="formula"><?php echo $instance->formula; ?></textarea></td></tr>
</table><br />
<button class="btn btn-primary" type="submit" ><?php echo _("Save"); ?></button>
</form>
