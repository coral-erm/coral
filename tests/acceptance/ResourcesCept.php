<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("ensure that I can create/delete a resource and see it in the list");

// Resources creation
$I->amOnPage("/resources/");
$I->click("New Resource");
$I->waitForPageToBeReady(); // Ensure that the modal form loaded
$I->fillField("#titleText", "test resource");
$I->click(".submit-button");
// we are redirected to the resource details page
$I->waitForText("Edit Product Details", 5);  // ensures we are on the issue page

// find resource in list an go to it's page
$I->amOnPage("/resources/");
$I->waitForPageToBeReady(); // Ensure that the list has loaded by Ajax.
$I->click("test resource");

// delete resource
$I->willAcceptTheNextConfirmBox();
$I->waitForPageToBeReady(); // Ensure that the modal form loaded
$I->click("remove resource"); // button title/name
$I->waitForText("records per page"); // Ensure that the list has loaded by Ajax.
// So the next check can't do a false positive (classic trap when asserting that
// something is not here).
$I->dontSee("test resource");
