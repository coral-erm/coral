<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Import usage statistics files');

// Import usage statistics
$I->amOnPage('/usage/');
$I->click("File Import");
// Attempt to upload without importing a file
$I->click('#submitFile');
// We expect to get an error message and remain on the same page
$I->see(' Please select a file.');
// Attempt to import a non-txt file
$I->attachFile('input[type="file"]', 'bad_import.md');
$I->click('#submitFile');
// We expect an error here
$I->see('Incorrect File format, must be .txt!');

// Import correctly formatted file with txt extension
$I->amOnPage('/usage/');
$I->click("File Import");
// Attach txt file in BR2 (R4)
$I->attachFile('input[type="file"]', 'BR2.txt');
// Select layout option for BR2 (R4)
$I->selectOption('select[name=layoutID]', 'Book Sections (BR2) R4');
$I->click('#submitFile');
$I->see('The file BR2.txt has been uploaded successfully');
// Click confirm button to complete import
$I->click('#submitForm');
$I->waitForText('Process completed', 30);

// See that the list of platform records has updated
$I->amOnPage('/usage/');
$I->waitForPageToBeReady(); // Ensure that the list has loaded by Ajax.
$I->see('EBSCOhost'); // Should now appear under platform name column

