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
<label for="formulaName">Name: </label><input type="text" value="<?php echo $instance->shortName; ?>" name="formulaName" /><br />
<?php

for ($i = 1; $i <= 10; $i++) {
    $memberName = "field${i}Name";
    echo ("<label for='$memberName'>Field $i: </label><input type='text' value='" . $instance->$memberName . "' name='$memberName' /><br />");
}

?>
<label for="formula">Formula: </label>
<textarea name="formula" id="formula"><?php echo $instance->formula; ?></textarea>
<button class="btn btn-primary" type="submit" >Save</button>
</form>
