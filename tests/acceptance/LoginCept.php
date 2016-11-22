<?php
$I = new AcceptanceTester($scenario);
$I->wantTo("ensure that login works");

$I->amOnPage("/auth/");

$I->fillField("loginID", "coral_test");
$I->fillField("password", "coral_test");
$I->click("#loginbutton");

// login success
$I->see("Resources");
$I->see("Licensing");