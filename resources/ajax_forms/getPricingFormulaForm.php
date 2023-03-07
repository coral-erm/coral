<?php

    $pricingFormulaID =  isset($_GET['pricingFormulaID'])  ? $_GET['pricingFormulaID']  : null;
    $resourcePaymentID = isset($_GET['resourcePaymentID']) ? $_GET['resourcePaymentID'] : null;
    $instance = new PricingFormula(new NamedArguments(array('primaryKey' => $pricingFormulaID)));
    if ($resourcePaymentID) {
        $rp = new ResourcePayment(new NamedArguments(array('primaryKey' => $resourcePaymentID)));
    }
?>
<input type="hidden" id="formulaID" class="formulaID" value="<?php echo $instance->pricingFormulaID; ?>" />
<table>
<tr><td><?php echo _("Formula");?>:</td><td><?php echo $instance->formula; ?></td></tr>
<?php
if ($rp->pricingFormulaID) {
    $fieldValues = $instance->getFieldValues();
    $formulaValues = $rp->getFormulaValues();
    $formula = $instance->formula;
    foreach ($fieldValues as $dbName => $formulaElementName) {
        $formula = str_replace($formulaElementName, ($formulaValues[$dbName] ? number_format($formulaValues[$dbName] / 100, 2, '.', '') : 0), $formula);
    }
    echo "<tr><td>" . _("With values") . ":</td><td>" . $formula . "</td></tr>";
}
for ($i = 1; $i <= 10; $i++) {
    $memberName = "field${i}Name";
    $memberValue = "field${i}Value";
    if ($instance->$memberName) {
        echo ("<tr><td><label for='$memberName'>" . $instance->$memberName . "</label>:</td><td><input type='text' name='$memberName'");
        if (isset($rp))
            echo (" value='" . ($rp->$memberValue ? number_format($rp->$memberValue / 100, 2, '.', '') : 0) . "'");
        echo  ('/></td></tr>');
    }
}
?>
</table>
<button class="btn btn-primary" id="addFormulaButton" >Save</button>
