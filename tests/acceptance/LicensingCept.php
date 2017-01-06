<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that I can create/delete a license and see it in the list');

// License creation
$I->amOnPage("/licensing/");
$I->click("New License");
$I->waitForPageToBeReady();
$I->fillField("#licenseShortName", "Test License");
$I->fillField("#organizationName", "Test Publisher");
$I->click(".submit-button");
$I->waitForText("License Added Successfully.", 5);
$I->click("Continue");
// we are redirected to the test license's page
$I->waitForText("Edit License", 5); // ensure that we are on the test license's page

// Find license in list and go to its page
$I->amOnPage("/licensing/");
$I->waitForPageToBeReady(); // Ensure that the list has loaded by Ajax
$I->click("Test License");

// Delete license
$I->willAcceptTheNextConfirmBox();
$I->waitForPageToBeReady();
$I->click("remove license");
$I->waitForText("records per page", 5); // Ensure that the list has loaded by Ajax.
// So the next check can't do a false positive
$I->dontSee("Test License");
