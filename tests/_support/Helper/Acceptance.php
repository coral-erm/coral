<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module {

	public function _beforeSuite($settings = []) {
		$I = $this->getModule("WebDriver");
		$this->assertThatItsNot(getenv("CORAL_LOGIN"), false,
								"CORAL_LOGIN not in environment variables");
		$this->assertThatItsNot(getenv("CORAL_PASS"), false,
								"CORAL_PASS not in environment variables");

		$I->amOnPage("/auth/");
		codecept_debug("I ensure that the language is English"); // not matter the system's locale
		$I->selectOption("lang", "Français"); // See issue #103 for why we must switch twice.
		$I->selectOption("lang", "English");

		codecept_debug("I login");
		$I->fillField("loginID", getenv("CORAL_LOGIN"));
		$I->fillField("password", getenv("CORAL_PASS"));
		$I->click("#loginbutton");
		$I->see("Resources"); // login success
		$I->see("Licensing");
	}

	function willAcceptTheNextConfirmBox() {
		$acceptNextConfirmBox = <<<JS
var realConfirm = window.confirm;
window.confirm = function() {
	window.confirm = realConfirm;
	return true;
};
JS;
		$this->getModule("WebDriver")->executeJS($acceptNextConfirmBox);
	}

	function waitForPageToBeReady() {
		$javaScriptExpression = <<<JS
// First: check that all events finished: onClick, onChange, onLoad, Ajax and maybe more.
// Flag is stored in window because this isn't evaluated in the global scope so 'var myVar' woudn't work
setTimeout(function() { window.allEventsFinished = true;}, 0);
return window.allEventsFinished === true &&

// no active Ajax request
$.active == 0 &&

// Check that page + resources loaded. Which isn't covered by previous check.
// Maybe it's already covered by Codeception.
document.readyState === 'complete';
JS;
		$this->getModule("WebDriver")->waitForJS($javaScriptExpression, 5);
	}
}
