<?php
require_once("common/DBService.php");
require_once("common/DBResult.php");

class Installer {
	const CAUSE_DEPENDENCY_NOT_FOUND = 20041;
	const CAUSE_ALREADY_EXISTED = 20042;

	const ERR_CIRCULAR_DEPENDENCIES = 20044;
	const ERR_MODULE_DOES_NOT_EXIST = 20045;
	const ERR_UPGRADE_DOES_NOT_EXIST = 20046;
	const ERR_INVALID_TEST_RESULT = 20047;
	const ERR_RUNNING_POST_INSTALLATION_TEST_BEFORE_INSTALLATION_COMPLETE = 20048;
	const ERR_CANNOT_READ_PROVIDER_SCRIPT = 20049;

	const REQUIRED_FOR_INSTALL = 501;
	const REQUIRED_FOR_UPGRADE = 502;
	const REQUIRED_FOR_MODIFY  = 503;

	const VERSION_STRING_INSTALL = "INSTALL";
	const VERSION_STRING_MODIFY = "MODIFY";
	const VERSION_STRING_UPGRADE = "UPGRADE";

	protected $checklist = [];
	protected $shared_module_info = [];
	protected $messages = [];
	protected $successfully_completed_tests = [];
	protected $post_installation_mode = false;

	private $version_to_install = 0;

	function __construct($version) {
		$this->version_to_install = $version;
		$this_shared_module_info  = &$this->shared_module_info;
		$this_checklist           = &$this->checklist;
		$this_post_installation_mode = &$this->post_installation_mode;
		$this->shared_module_info = [
			"setSharedModuleInfo" => function($for_module, $key, $value) use (&$this_shared_module_info) {
				$this_shared_module_info[$for_module][$key] = $value;
			},
			"registerInstallationTest" => function($installer_object) use (&$this_checklist) {
				// TODO: What happens if we register a regular old test [and it's required]? Will it run?
				$required_variables = [
					"uid",
					"translatable_title",
					"bundle"
				];
				foreach ($required_variables as $req)
				{
					if (!isset($installer_object[$req]))
					{
						$this->messages[] = _("<b>Warning:</b> A dynamically registered installion test is malformed.");
						// I have turned this off but it could be useful for debugging at this point:
						// debug_print_backtrace();
						return;
					}
				}
				$this_checklist[] = $installer_object;
			},
			"isInPostInstallationMode" => function() use (&$this_post_installation_mode) {
				return isset($_SESSION["installer_post_installation"]) && $_SESSION["installer_post_installation"];
			}
		];
		$this->scanForInstallerProviders();
		$this->post_installation_mode = isset($_SESSION["installer_post_installation"]) && $_SESSION["installer_post_installation"];
	}
	private function getKeyFromUid($test_uid, $haystack = null)
	{
		$haystack = $haystack === null ? $this->checklist : $haystack;
		/**
		 * TODO: remove this shim when we stop caring about PHP <= 5.5.0 being
		 *       able to *run* the installer. Note that this is needed before we
		 *       test for meeting system requirements and the installer needs
		 *       to be more generous about system reqs than CORAL as a whole.
		 */
		require_once("common/array_column.php");
		$key = array_search($test_uid, array_column($haystack, 'uid'));
		if ($key === false)
			throw new OutOfBoundsException("Test '$test_uid' not found in checklist.", self::ERR_MODULE_DOES_NOT_EXIST);

		return $key;
	}
	public function getRequiredProviders($what_for = self::REQUIRED_FOR_INSTALL)
	{
		require_once("common/array_column.php");
		return array_column(array_filter($this->checklist, function($item) use ($what_for) {
			return isset($item["required_for"]) && in_array($what_for, $item["required_for"]);
		}), "uid");
	}
	public function getPostInstallationUids()
	{
		return array_filter($this->checklist, function($item){
			return isset($item["post_installation"]) && $item["post_installation"];
		});
	}
	public function getTitleFromUid($uid)
	{
		return $this->checklist[ $this->getKeyFromUid($uid) ]["translatable_title"];
	}

	public function register_installation_provider($installer_object, $module_name)
	{
		$required_variables = [
			"uid",
			"translatable_title",
			"bundle"
		];
		foreach ($required_variables as $req)
		{
			if (!isset($installer_object[$req]))
			{
				$this->messages[] = "<b>Warning:</b> The installer for '$module_name' is broken (ignoring). [Missing '$req']";
				return;
			}
		}
		$this->checklist[] = $installer_object;
	}

	private function addModule($path, $module_name, $core_module = false)
	{
		// we should only be trying to add modules that are actually readable
		if (!is_readable($path))
			throw new RuntimeException("Error: For some reason you are trying to add a module that is not readable: `$module_name` -> `$path`", self::ERR_CANNOT_READ_PROVIDER_SCRIPT);

		require $path;
		$function_name = "register_${module_name}_provider";
		if (is_callable($function_name))
		{
			$installer_object = call_user_func($function_name);
			$this->register_installation_provider($installer_object, $module_name);
			if (!$core_module)
			{
				$mod = [
					"directory" => $module_name,
					"uid" => $installer_object["uid"],
					"title" => $installer_object["translatable_title"],
				];
				$this->shared_module_info["module_list"][] = $mod;
			}
			$obj = $installer_object["bundle"]($this->version_to_install);
			if (isset($obj["sharedInfo"]))
			{
				$this->shared_module_info[ $installer_object["uid"] ] = $obj["sharedInfo"];
				$this->shared_module_info["dependencies"][ $installer_object["uid"] ] = $obj["dependencies_array"];
			}
		}
		else
		{
			$this->messages[] = "<b>Warning:</b> There is a problem with the installer for the '$module_name' module (ignoring). The required function '$function_name' is not callable.";
		}
	}
	private function scanForInstallerProviders()
	{
		// Core Providers
		$core_provider_path = "install/providers/";
		$core_providers = scandir($core_provider_path);
		foreach ($core_providers as $provider)
		{
			if (trim($provider, ".") !== "")
			{
				$module_name = basename($provider, ".php");
				$path = $core_provider_path . $provider;
				$this->addModule($path, $module_name, true);
			}
		}

		// Module Install Providers
		$MODULE_ROOT = ".";
		// If we're installing, then find all the modules
		if ($this->version_to_install == self::VERSION_STRING_INSTALL ||
			$this->version_to_install == self::VERSION_STRING_MODIFY)
		{
			$module_directories = scandir($MODULE_ROOT);
			foreach ($module_directories as $dir)
			{
				if (is_dir("$MODULE_ROOT/$dir"))
				{
					$installation_root_file = "$MODULE_ROOT/$dir/install/$dir.php";
					if (file_exists($installation_root_file))
					{
						$this->addModule($installation_root_file, $dir);
					}
				}
			}
		}
		else // Otherwise, only already installed modules are available
		{
			require_once("common/Config.php");
			foreach (Config::getInstalledModules() as $module)
			{
				if (is_dir("$MODULE_ROOT/$module"))
				{
					$installation_root_file = "$MODULE_ROOT/$module/install/$module.php";
					if (file_exists($installation_root_file))
					{
						$this->addModule($installation_root_file, $module);
					}
					else {
						throw new OutOfBoundsException("Error: Although the module folder is there for '$module', the install script cannot be accessed (probably because of wrong permissions)", self::ERR_CANNOT_READ_PROVIDER_SCRIPT);
					}
				}
				else {
					throw new OutOfBoundsException("Error: Although the module '$module' is registered as installed, the folder cannot be accessed (probably because of wrong permissions)", self::ERR_CANNOT_READ_PROVIDER_SCRIPT);
				}
			}
		}
	}

	public function runTestForResult($test_uid, $required_for = [])
	{
		$key = $this->getKeyFromUid($test_uid);
		if ($key === false)
			throw new OutOfBoundsException("Test '{$this->getTitleFromUid($test_uid)}' not found in checklist.", self::ERR_MODULE_DOES_NOT_EXIST);

		if (isset($this->checklist[$key]["result"]))
		{
			$return = new stdClass();
			$return->skipped = true;
			$return->cause = self::CAUSE_ALREADY_EXISTED;
			return $return;
		}
		if (!$this->shared_module_info["isInPostInstallationMode"]() && isset($this->checklist[$key]["post_installation"]) && $this->checklist[$key]["post_installation"])
		{
			throw new RuntimeException("Error: You're trying to run the '$test_uid' post-installation test before the installation is complete.", self::ERR_RUNNING_POST_INSTALLATION_TEST_BEFORE_INSTALLATION_COMPLETE);
		}

		// The current solution for just in time dependencies
		/**
		 *  The problem was introduced in
		 *  51dfdfa80c44c40090ea11eb72fa86ef0ce8d902:
		 *
		 * That commit is right but breaks the installer by not forcing a run of
		 * the loop which resulted in modules_to_use never being able to set its
		 * 'dynamic' dependencies - I think the installer would still have worked
		 * but it would have done silly things like tell the user to take away
		 * write permissions to the common/config file and then go back to the
		 * start of the process (and possibly fail to write the conf file at the
		 * end).
		 *
		 * TODO: What we really need is a way to request dependencies using
		 * $shared_module_info but for now, we are just going to check that the
		 * module hasn't changed its dependencies between start and finish.
		 */
		do {
			$bundle = $this->checklist[$key]["bundle"]($this->version_to_install);
			foreach ($this->getDependencies($bundle) as $dependency) {
				require_once("common/array_column.php");
				$dep_key = array_search($dependency, array_column($this->checklist, 'uid'));
				if ($dep_key === false)
				{
					$return = new stdClass();
					$return->skipped = false;
					$return->cause = self::CAUSE_DEPENDENCY_NOT_FOUND;
					$return->missing_dependency = $dependency;
					return $return;
				}

				if (!isset($this->checklist[$dep_key]["result"]))
				{
					if (in_array($dependency, $required_for))
					{
						$required_array = var_export($required_for, true);
						throw new RuntimeException("Error: Circular dependencies ('$test_uid' in $required_array)", self::ERR_CIRCULAR_DEPENDENCIES);
					}
					$required_for[] = $dependency;
					$result = $this->runTestForResult($dependency, $required_for);
					// If one of the requirements fails, we need its result to be yielded
					if (!$result->success)
						return $result;
				}
			}
		} while ($this->getDependencies($bundle) !== $this->getDependencies($this->checklist[$key]["bundle"]($this->version_to_install)));
		return $this->actuallyRunTest($test_uid, $bundle);
	}
	private function getDependencies($versioned_bundle)
	{
		return isset($versioned_bundle["dependencies_array"]) ? $versioned_bundle["dependencies_array"] : [];
	}
	private function actuallyRunTest($uid, $versioned_bundle)
	{
		$key = $this->getKeyFromUid($uid);
		$result = call_user_func( $versioned_bundle["function"], $this->shared_module_info );
		// TODO: we need to test this throw
		if ($result === null)
			throw new UnexpectedValueException("The script for '{$this->getTitleFromUid($uid)}' has returned a null result (which is not allowed).", self::ERR_INVALID_TEST_RESULT);

		$this->shared_module_info["debug"][] = $uid;
		$this->checklist[$key]["result"] = $result;
		if ($result->success)
			$this->successfully_completed_tests[] = $uid;

		return $result;
	}

	public function getMessages()
	{
		$messages = $this->messages;
		$this->messages = [];
		return $messages;
	}
	public function getSuccessfullyCompletedTests()
	{
		$uids = [];
		foreach ($this->successfully_completed_tests as $uid)
		{
			// Only return uid if it's not hid[den]_from_completion_list
			// And it's not post_installation
			$hide = isset($this->checklist[ $this->getKeyFromUid($uid) ]["hide_from_completion_list"]) ? $this->checklist[ $this->getKeyFromUid($uid) ]["hide_from_completion_list"] : false;
			$post_installation = isset($this->checklist[ $this->getKeyFromUid($uid) ]["post_installation"]) ? $this->checklist[ $this->getKeyFromUid($uid) ]["post_installation"] : false;
			if (!$hide && !$post_installation)
				$uids[] = $uid;
		}
		return $uids;
	}
	public function getSuccessfullyCompletedTestTitles()
	{
		return array_map(function($uid){
			return $this->getTitleFromUid($uid);
		}, $this->getSuccessfullyCompletedTests());
	}
	public function getApproxiamateCompletion()
	{
		return count($this->successfully_completed_tests) / (float) count($this->checklist);
	}

	public function declareInstallationComplete()
	{
		// $completed_tests = $this->successfully_completed_tests;
		// // $isRequired = $this->isRequired;
		// $tests_to_complete = array_filter($this->getRequiredProviders, function ($uid) use ($isRequired) {
		// 	return $isRequired($uid);
		// });
		// var_dump(array_diff($tests_to_complete, $completed_tests));
		// exit();
		// TODO: Perhaps we should check that all the (required) getRequiredProviders
		//       are installed before we allow this.
		$this->shared_module_info["post_installation_mode"] = true;
		$_SESSION["installer_post_installation"] = true;
	}
	public function isInPostInstallationMode()
	{
		return $this->shared_module_info["isInPostInstallationMode"]();
	}
	public function postInstallationTest()
	{
		foreach ($this->getPostInstallationUids() as $test)
		{
			$return = $this->runTestForResult($test["uid"]);
			if (!$return->success)
				return $return;
		}
		return false;
	}
}
