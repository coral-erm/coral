<?php

    $pricingFormulaID =  isset($_GET['pricingFormulaID'])  ? $_GET['pricingFormulaID']  : null;
    $resourcePaymentID = isset($_GET['resourcePaymentID']) ? $_GET['resourcePaymentID'] : null;
    $instance = new PricingFormula(new NamedArguments(array('primaryKey' => $pricingFormulaID)));
    if ($resourcePaymentID) {
        $rp = new ResourcePayment(new NamedArguments(array('primaryKey' => $resourcePaymentID)));
    }
?>
<p>Formula: <?php echo $instance->formula; ?></p>
<input type="hidden" id="formulaID" class="formulaID" value="<?php echo $instance->pricingFormulaID; ?>" />
<?php
for ($i = 1; $i <= 10; $i++) {
    $memberName = "field${i}Name";
    $memberValue = "field${i}Value";
    if ($instance->$memberName) {
        echo ("<label for='$memberName'>" . $instance->$memberName . "</label>: <input type='text' name='$memberName'");
        if (isset($rp) && $rp->$memberValue)
            echo (" value='" . integer_to_cost($rp->$memberValue) . "'");
        echo  ('/><br />');
    }
}
?>
<button class="btn btn-primary" id="addFormulaButton" >Save</button>
