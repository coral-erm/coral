<?php

    $formulaID = $_GET['pricingFormulaID'];
    #$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
    $pf = new PricingFormula(new NamedArguments(array('primaryKey' => $formulaID)));
    #$ra = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));
    $formula = $pf->formula;
    for ($i = 1; $i <= 10; $i++) {
        $name = "field${i}Name";
        if (isset($_GET[$name]))
            $formulaValues[$name] = $_GET[$name];
    }
    $fieldValues = $pf->getFieldValues();
    foreach ($fieldValues as $dbName => $formulaElementName) {
        //echo "replacing $formulaElementName with " . $formulaValues[$dbName] . "\n";
        $formula = str_replace($formulaElementName, $formulaValues[$dbName], $formula);
        #$raDbName = str_replace("Name", "Value", $dbName);
        #$ra->$raDbName = $formulaValues[$dbName];
        //echo $formula . "\n";
    }
    #$ra->save();
    echo eval("return $formula;");
?>
