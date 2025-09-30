<?php 
	require_once("test_results_yielder.php");
	require_once("installer.php");

	function run_loop($version) {
		$_SESSION["run_loop_version"] = $version;
		switch ($version) {
			case Installer::VERSION_STRING_INSTALL:
				$requirement_filter = Installer::REQUIRED_FOR_INSTALL;
				break;
			case Installer::VERSION_STRING_MODIFY:
				$requirement_filter = Installer::REQUIRED_FOR_MODIFY;
				break;
			default:
				$requirement_filter = Installer::REQUIRED_FOR_UPGRADE;
				break;
		}
		try {
			$installer = new Installer($version);
		}
		catch (Exception $e) {
			switch ($e->getCode()) {
				case Installer::ERR_CANNOT_READ_PROVIDER_SCRIPT:
					$yield = new stdClass();
					$yield->messages = [ _("While trying to load module scripts an error occurred."), _("Please check that PHP has execute (probably 644) permission on your install folders.") ];
					yield_test_results_and_exit($yield, [], 0);
					break;
			}
		}
		$requirements = $installer->getRequiredProviders($requirement_filter);
		foreach ($requirements as $i => $requirement) {
			$testResult = $installer->runTestForResult($requirement);

			if (isset($testResult->skipped)) {
				switch ($testResult->cause) {
					case Installer::CAUSE_ALREADY_EXISTED:
						continue 2; // break switch & continue foreach
						break;
					case Installer::CAUSE_DEPENDENCY_NOT_FOUND:
						$testResult->yield = new stdClass();
						$testResult->yield->messages = [ sprintf(_("Dependency for '%s' not found: %s"), $installer->getTitleFromUid($requirement), $testResult->missing_dependency) ];
						yield_test_results_and_exit($testResult->yield, $installer->getSuccessfullyCompletedTestTitles(), $installer->getApproxiamateCompletion());
						break;
				}
			} elseif (!$testResult->success) {
				$installer_messages = $installer->getMessages();
				$test_messages = isset($testResult->yield->messages) ? $testResult->yield->messages : [];
				$testResult->yield->messages = array_merge($installer_messages, $test_messages);
				yield_test_results_and_exit($testResult->yield, $installer->getSuccessfullyCompletedTestTitles(), $installer->getApproxiamateCompletion());
			} else {
				if (isset($testResult->completionMessages))
					$completionMessages[ $requirement ] = $testResult->completionMessages;
			}
		}

		$installer->declareInstallationComplete();
		$completed_tests = $installer->getSuccessfullyCompletedTestTitles();
		while ($failingPostInstallationTest = $installer->postInstallationTest()){
			yield_test_results_and_exit($failingPostInstallationTest->yield, $completed_tests, 97/100);
		}
		// Success!
		$return = new stdClass();
		$return->show_completion = true;
		$return->completion_title = _("Congratulations");
		$return->redirection_message = _("Redirecting Home: ");
		switch ($version) {
			case Installer::VERSION_STRING_INSTALL:
				$return->completion_message = _("Installation has been successfully completed.");
				break;
			case Installer::VERSION_STRING_MODIFY:
				$return->completion_message = _("Installation modification has been successfully completed.");
				break;
			default:
				$return->completion_message = _("Upgrade has been successfully completed.");
				break;
		}
		session_unset();
		yield_test_results_and_exit($return, $completed_tests, 100/100);
	}
?>