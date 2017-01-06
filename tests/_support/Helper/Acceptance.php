<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module {

    public function _beforeSuite($settings = array()) {
		$this->switchToTestEnvironement();
    }


    public function _afterSuite($settings = array()) {
		$this->switchBackToProdEnvironement();
    }


    public function _before(\Codeception\TestInterface $test) {
		$this->resetTestDatabase();
		$I = $this->getModule("WebDriver");
		$I->amOnPage("/");

		$this->setCookieWithWorkaround("lang", "en_US");
		$this->setCookieWithWorkaround("CORALLoginID", "coral_test");
		$this->setCookieWithWorkaround("CORALSessionID", "bNWUrFmjzDtoyxXSyxlwLMROC5W5LwvnAH7sMkRBnBqcyDum1VZCiqRlmngyaRbbYZJl9anncTFQX03PMSSu9jWlN2ZoJ1FiQPJQ");
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


	/**
	 * Wrapper for setCookie() (without additional params) that allows to
	 * circumvent this issue:
	 * https://github.com/Codeception/Codeception/issues/2900#issuecomment-234702552
	 */
	function setCookieWithWorkaround($name, $value) {
		$I = $this->getModule("WebDriver");
		try {
			$I->setCookie($name, $value);
		} catch (\Facebook\WebDriver\Exception\UnableToSetCookieException $e) {}
	}


	function resetTestDatabase() {
		codecept_debug("resetting the test database: it may take from 7 to 20 seconds as there is a lot of data");
		$user = "coral_test";
		$passphrase = "coral_test";
		$db = new \PDO("mysql:host=localhost", $user, $passphrase);
		$sql = file_get_contents("tests/_data/test_database.sql");
		$qr = $db->exec($sql);
	}


	public function switchToTestEnvironement() {
		$config = new \Config_Lite("admin/configuration.ini");
		$config->set("settings", "environment", "test");
		$config->save();
	}


	public function switchBackToProdEnvironement() {
		$config = new \Config_Lite("admin/configuration.ini");
		$config->set("settings", "environment", "prod");
		$config->save();
	}

}
