<?php 
require_once('versionManager.php');
$versionManager = new versionManager();

$needToInstallOrUpgrade = $versionManager->checkForChange();
$currentlyLooping = !empty($_SESSION['run_loop_version']);
if($needToInstallOrUpgrade || $currentlyLooping) {
	//Go ahead and require all the files we might need for installing.
	require_once("test_if_installed.php");
	require_once("test_results_yielder.php");
	require_once("templates/install_page_template.php");
	require_once("installer.php");
	require_once("run_loop.php");

	//Draw the basic Template page if it hasn't been drawn yet.
	if (!isset($_POST["installing"])) {
		draw_install_page_template();
		exit();
	}

	//This is currently looping through a Session Variable.
	if ($currentlyLooping){
		run_loop($_SESSION["run_loop_version"]);
		exit();
	} 

	//It is not looping. Validate the versions.
	$errorMessageExists = $versionManager->validateVersions();
	if($errorMessageExists){
		$return = new stdClass();
		$return->messages = [];
		$return->messages[] = $errorMessageExists;
		yield_test_results_and_exit($return, [], 0);
		exit();
	}

	//This needs to be either installed or upgraded.
	$versionLoad = $versionManager->getVersionsToLoad();
	$actionToDo = $versionLoad['action'];
	switch ($actionToDo) {
		case Installer::VERSION_STRING_INSTALL:
			if (!continue_installing()) {
				$return = new stdClass();
				$return->redirect_home = true;
				yield_test_results_and_exit($return, [], 1);
				session_unset();
			}
			run_loop(Installer::VERSION_STRING_INSTALL);
			break;
		case Installer::VERSION_STRING_UPGRADE;
			$nextVersion = ($versionLoad['versions'][0]) ?? FALSE;
			if(!$nextVersion){
				$return = new stdClass();
				$return->messages = [
					"<b>" . _("An error has occurred:") . "</b><br />" . _("Sorry but the installer has been incorrectly configured. Please contact the developer."),
					"<br>",
					_("The next version to be upgraded could not be read by the installer."),
				];			
				yield_test_results_and_exit($return, [], 1);
				session_unset();
			}
			run_loop($nextVersion);
			break;
		default:
			$return = new stdClass();
			$return->messages = [
                "<b>" . _("An error has occurred:") . "</b><br />" . _("Sorry but the installer has been incorrectly configured. Please contact the developer."),
				"<br>",
				_("The action sent by the version manager is not recognized by the program."),
			];			
			yield_test_results_and_exit($return, [], 1);
			session_unset();
			break;
	}
}