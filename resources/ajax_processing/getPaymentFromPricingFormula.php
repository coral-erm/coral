<?php
    $formulaID = $_GET['pricingFormulaID'];
    $pf = new PricingFormula(new NamedArguments(array('primaryKey' => $formulaID)));
    $formula = $pf->formula;
    for ($i = 1; $i <= 10; $i++) {
        $name = "field${i}Name";
        if (isset($_GET[$name]))
            $formulaValues[$name] = $_GET[$name];
    }
    $fieldValues = $pf->getFieldValues();
    foreach ($fieldValues as $dbName => $formulaElementName) {
        if (!is_numeric($formulaValues[$dbName])) {
            die();
        }
        $formula = str_replace($formulaElementName, $formulaValues[$dbName], $formula);
    }
    $formula = preg_replace('/\s+/', '', $formula);
    $formulaAfterCheck = preg_replace('~[^0-9.()-+*/]~', '', $formula);
    if (strcmp($formula, $formulaAfterCheck) == 0) {
        echo eval("return number_format($formulaAfterCheck, 2, '.', '');");
    }
?>
