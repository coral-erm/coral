<?php
if ($_POST['pricingFormulaID']) {
    $instance = new PricingFormula(new NamedArguments(array('primaryKey' => $_POST['pricingFormulaID'])));
} else {
    $instance = new PricingFormula();
}

$instance->shortName = $_POST['formulaName'];
for ($i = 1; $i <= 10; $i++) {
    $memberName = "field${i}Name";
    $instance->$memberName = $_POST[$memberName] ? $_POST[$memberName] : null;
}
$instance->formula = $_POST['formula'];
$instance->save();

?>
