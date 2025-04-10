<?php
/*
If I remember correctly, we are at the point where I need to run through an
upgrade of “1.9” to 2.0 with the current upgrade branch? Is that correct?

The remaining priorities will be to:

	1. Fix any bugs in the upgrader
	2. Make sure developer documentation for installer and upgrader are as
		complete as possible.
	3. Make sure that we can enable or disable modules that were previously
		installed/uninstalled. (i.e. the checkboxes in the installer)
 */


//Used to avoid issues that occur when Windows is the PHP OS, as it treats both backslashes and forward slashes as valid separators.
$uses_backslash = DIRECTORY_SEPARATOR == '\\';

// Replace backslashes in dirname(__DIR__) with forward slashes (for Windows, since other systems should always have forward slashes according to the dirname and basename documentation)
$slash_fix = function ($to_fix) {return str_replace('\\', '/', $to_fix); };

// dirnames and basenames used in below if statement
// dirname($_SERVER["SCRIPT_FILENAME"])
$dirname_script_filename = dirname($_SERVER["SCRIPT_FILENAME"]);
// dirname(__DIR__)
$dirname_dir = dirname(__DIR__);
// basename($_SERVER["SCRIPT_FILENAME"])
$basename_script_filename = basename($_SERVER["SCRIPT_FILENAME"]);
// basename(__FILE__)
$basename_file = basename(__FILE__);

// make appropriate changes to the dirnames and basenames if uses_backslash is true
if ($uses_backslash)
{
	$dirname_script_filename = $slash_fix($dirname_script_filename);
	$dirname_dir = $slash_fix($dirname_dir);
	$basename_script_filename = $slash_fix($basename_script_filename);
	$basename_file = $slash_fix($basename_file);
}


// TODO: go through template.php and remove hard coded vars
if ($dirname_script_filename !== $dirname_dir || $basename_script_filename !== $basename_file)
{
	// Calculating $location allows the root to be something other than / (e.g. /Coral/)
	$trim_from_left = function ($str_to_trim, $trim) { return preg_replace('/^' . preg_quote($trim, '/') . '/', '', $str_to_trim); };
	$location = $trim_from_left($dirname_dir, $slash_fix($_SERVER["DOCUMENT_ROOT"]));


	header("Location: " . $location);
	exit();
}

/**
 * $INSTALLATION_VERSIONS is an array of all version strings that can be upgraded from
 * INSTALLATION_VERSION is the current version string (which should be the last element in the $INSTALLATION_VERSIONS array)
 *
 * NOTE: It is assumed that version strings can be understood by php's version_compare function
 */
$INSTALLATION_VERSION = "2025.04";
$INSTALLATION_VERSIONS = ["1.9.0", "2.0.0", "3.0.0", "3.0.1", "2020.02", "2020.09", "2024.04", "2024.10", "2025.04"];

function make_sure_template_is_drawn()
{
	if (!isset($_POST["installing"]))
	{
		require_once "templates/install_page_template.php";
		draw_install_page_template();
		exit();
	}
}
function is_installed()
{
	require_once("common/Config.php");
	try {
		$return = Config::getInstallationVersion();
	} catch (Exception $e) {
		$return = false;
	}
	return $return;
}

function run_loop($version)
{
	$_SESSION["run_loop_version"] = $version;
	require_once "installer.php";
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

		if (isset($testResult->skipped))
		{
			switch ($testResult->cause)
			{
				case Installer::CAUSE_ALREADY_EXISTED:
					continue 2; // break switch & continue foreach
					break;
				case Installer::CAUSE_DEPENDENCY_NOT_FOUND:
					$testResult->yield = new stdClass();
					$testResult->yield->messages = [ sprintf(_("Dependency for '%s' not found: %s"), $installer->getTitleFromUid($requirement), $testResult->missing_dependency) ];
					yield_test_results_and_exit($testResult->yield, $installer->getSuccessfullyCompletedTestTitles(), $installer->getApproxiamateCompletion());
					break;
			}
		}
		else if (!$testResult->success)
		{
			$installer_messages = $installer->getMessages();
			$test_messages = isset($testResult->yield->messages) ? $testResult->yield->messages : [];
			$testResult->yield->messages = array_merge($installer_messages, $test_messages);
			yield_test_results_and_exit($testResult->yield, $installer->getSuccessfullyCompletedTestTitles(), $installer->getApproxiamateCompletion());
		}
		else
		{
			if (isset($testResult->completionMessages))
				$completionMessages[ $requirement ] = $testResult->completionMessages;
		}
	}

	$installer->declareInstallationComplete();
	$completed_tests = $installer->getSuccessfullyCompletedTestTitles();
	while ($failingPostInstallationTest = $installer->postInstallationTest())
		yield_test_results_and_exit($failingPostInstallationTest->yield, $completed_tests, 97/100);

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

function do_install()
{
	require_once "test_if_installed.php";
	if (!continue_installing())
	{
		session_unset();
		$return = new stdClass();
		$return->redirect_home = true;
		yield_test_results_and_exit($return, [], 1);
	}
	require_once "installer.php";
	run_loop(Installer::VERSION_STRING_INSTALL);
}

function do_upgrade($version)
{
	global $INSTALLATION_VERSIONS;
	$current_version_index = array_search($version, $INSTALLATION_VERSIONS);
	run_loop($INSTALLATION_VERSIONS[++$current_version_index]);
}


$CURRENT_VERSION = is_installed();
if ($CURRENT_VERSION !== $INSTALLATION_VERSION || !empty($_SESSION["run_loop_version"]))
{
	make_sure_template_is_drawn();
	require_once "test_results_yielder.php";
	if (!empty($_SESSION["run_loop_version"]))
	{
		run_loop($_SESSION["run_loop_version"]);
		exit();
	}
	elseif (!$CURRENT_VERSION)
	{
		do_install();
		exit();
	}
	else
	{
		$return = new stdClass();
		$return->messages = [];
		if (array_slice($INSTALLATION_VERSIONS, -1)[0] !== $INSTALLATION_VERSION)
		{
			// The instllation constants are not correctly set up
			$return->messages[] = "<b>" . _("An error has occurred:") . "</b><br />" . _("Sorry but the installer has been incorrectly configured. Please contact the developer.");
			$return->messages[] = _("Version of Installer does not match the last installation version in INSTALLATION_VERSIONS.");
			yield_test_results_and_exit($return, [], 0);
		}
		elseif (!in_array($CURRENT_VERSION, $INSTALLATION_VERSIONS))
		{
			$return->messages[] = "<b>" . _("An error has occurred:") . "</b><br />" . _("Sorry but the installer has been incorrectly configured. Please contact the developer.");
			$return->messages[] = _("The version currently installed is not a recognised version.");
			yield_test_results_and_exit($return, [], 0);
		}

		// Do upgrade
		do_upgrade($CURRENT_VERSION);
		exit();
	}
}


// TODO: Handle these variations
// TAKEN FROM test_if_installed.php -> needs to be handled in do_upgrade()

// elseif (version_compare(INSTALLATION_VERSION, $old_version) > 0)
// {
// 	// This installer installs a newer version
// 	$instruction = _("This installer installs a newer version of CORAL than the one currently installed. This is <b>highly discouraged</b> and will probably result in the loss of data. Instead you should try to upgrade.");
// 	$option_buttons = $allowed_options(["take_me_home", "try_upgrade", "install_anyway"]);
// }
// else if (version_compare(INSTALLATION_VERSION, $old_version) === 0)
// {
// 	// Already installed and current version
// 	$instruction = _("You already have the current version installed. Are you looking for the home page?");
// 	$option_buttons = $allowed_options(["take_me_home"]);
// }
// else if (version_compare(INSTALLATION_VERSION, $old_version) < 0)
// {
// 	// Apparently the already installed version is newer than this installer
// 	$yield->messages[] = _("<b>Warning:</b> A problem exists in your CORAL installation.");
// 	$yield->messages[] = _("<b>Warning:</b> The CORAL version already installed is newer than this software version. You should notify your administrator or the developer.");
// 	$instruction = _("The installed version of CORAL is newer than the newest version this installer can install.");
// 	$option_buttons = $allowed_options(["take_me_home"]);
// }
