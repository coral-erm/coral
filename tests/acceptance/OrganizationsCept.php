<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Ensure that I can create/delete an organization and see it in the list');

// Organization creation
$I->amOnPage("/organizations/");
$I->click("New Organization");
$I->waitForPageToBeReady();
$I->fillField("#organizationName", "Test Organization");
$I->click(".submit-button");
// We are redirected to the test organization's details page
$I->waitForText("Created:", 5); // Ensure that we are on details page

// Find organization in list and go to its page
$I->amOnPage("/organizations/");
$I->waitForPageToBeReady();
// Since test_database has several organizations in it already, we'll need to search to get Test Organization in the list
$I->fillField("#searchOrganizationName", "Test Organization");
$I->click("go!"); // run search
$I->waitForPageToBeReady();
$I->click("Test Organization");

// Delete organization
$I->willAcceptTheNextConfirmBox();
$I->waitForPageToBeReady();
$I->click("remove resource"); // button title/name
$I->waitForText("Organization successfully deleted.", 5); // Ensure that the list has loaded by Ajax.
$I->dontSee("Test Organization");

// Attempt to create an organization with the same name as an existing one
$I->amOnPage("/organizations/");
$I->click("New Organization");
$I->fillField("#organizationName", "abc news"); // Organization added by default
$I->waitForText("This organization already exists!");
