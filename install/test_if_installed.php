<?php
function continue_installing()
{
	$rin = "installation_root";
	$ns = [ // "ns" = "namespace" : its just too unwieldly without this abbreviation
		"root_installation" => $rin,
		"install_anyway" => $rin . "_do_install_anyway",
		"option_button" => $rin . "_option_button",
		"already_installed" => $rin . "_do_already_installed"
	];

	require_once "install/test_results_yielder.php";
	if (!isset($_SESSION[$ns["install_anyway"]]) || (isset($_SESSION[$ns["install_anyway"]]) && $_SESSION[$ns["install_anyway"]] !== true))
	{
		$option_button_set = [
			[ "name" => "install_anyway", "title" => _("Install CORAL") ],
			[ "name" => "already_installed", "title" => _("CORAL Already Installed (Upgrade/Repair)") ],
		];
		if ((isset($_POST[$ns["option_button"]]) && $_POST[$ns["option_button"]] == "already_installed") || (isset($_SESSION[$ns["already_installed"]]) && $_SESSION[$ns["already_installed"]]))
		{
			$_SESSION[$ns["already_installed"]] = true;
			upgradeToUnifiedInstaller($ns);
			return false; //i.e. do not continue installing
		}
		elseif (isset($_POST[$ns["option_button"]]) && $_POST[$ns["option_button"]] == "install_anyway")
		{
			$_SESSION[$ns["install_anyway"]] = true;
		}
		else // ((isset($_POST[$ns["option_button"]]) && $_POST[$ns["option_button"]] !== "install_anyway") || !isset($_POST[$ns["option_button"]]))
		{
			$possible_modules_with_conf_files = [ "auth", "licensing", "management", "organizations", "reports", "resources", "usage" ];
			$maybe_installed = array_reduce($possible_modules_with_conf_files, function($carry, $item) {
				return $carry || file_exists($item . "/admin/configuration.ini");
			});
			if ($maybe_installed)
			{
				$allowed_options = function($allowed_array) use ($option_button_set) {
					return array_filter($option_button_set, function($item) use ($allowed_array) {
						return in_array($item["name"], $allowed_array);
					});
				};

				$yield = new stdClass();
				$yield->messages = [];
				$yield->title = _("CORAL Pre-Installation Check");

				$instruction = "";
				$option_buttons = [];

				// Installed not with unified installer
				// OR not installed
				$yield->messages[] = _("We cannot tell whether or not CORAL is installed. Either it is not installed or it was installed using another installer.");
				$yield->messages[] = _("If CORAL is already installed you should <b>NOT</b> try to install.");
				$instruction = _("Please choose one of the options below:");
				$option_buttons = $allowed_options(["already_installed", "install_anyway"]);

				require_once "install/templates/option_buttons_template.php";
				$yield->body = option_buttons_template($instruction, $option_buttons, $ns["root_installation"]);
				yield_test_results_and_exit($yield, [], 0);
			}
			// else falls through to return true (i.e. "continue installing")
		}
	}
	return true;
}

function upgradeToUnifiedInstaller($ns)
{
	$ns["module_selections"] = "upgradeToUnifiedInstaller_module_selections";
	// These can be hard coded because they are all the modules that could exist pre-unified installer
	$fields = [
		[ "uid" => "ui_upgrade_auth", "title" => "Auth", "required" => false, "module_name" => "auth" ],
		[ "uid" => "ui_upgrade_licensing", "title" => "Licensing", "required" => false, "module_name" => "licensing" ],
		[ "uid" => "ui_upgrade_management", "title" => "Management", "required" => false, "module_name" => "management" ],
		[ "uid" => "ui_upgrade_organizations", "title" => "Organizations", "required" => false, "module_name" => "organizations" ],
		[ "uid" => "ui_upgrade_reports", "title" => "Reports", "required" => false, "module_name" => "reports" ],
		[ "uid" => "ui_upgrade_resources", "title" => "Resources", "required" => false, "module_name" => "resources" ],
		[ "uid" => "ui_upgrade_usage", "title" => "Usage", "required" => false, "module_name" => "usage" ],
	];

	// If modules are selected, check we can access their config files
	$chosen_modules_missing_config_files = [];
	if (isset($_POST["ui_upgrade_auth"]))
	{
		foreach ($fields as $field)
		{
			if ($_POST[$field["uid"]] && !is_readable($field["module_name"]."/admin/configuration.ini"))
			{
				$chosen_modules_missing_config_files[] = $field["title"];
			}
		}
	}

	// test $_SESSION[$ns["module_selections"]]["ui_upgrade_auth"] for a t/f value - that it's auth is irrelevant
	if (count($chosen_modules_missing_config_files) == 0 &&
		(isset($_POST["ui_upgrade_auth"]) || isset($_SESSION[$ns["module_selections"]]["ui_upgrade_auth"])))
	{
		require_once "common/Config.php";
		$configFilePath = Config::CONFIG_FILE_PATH;
		$iniFile = file_exists($configFilePath) ? parse_ini_file($configFilePath, true) : [];
		$iniFile["installation_details"] = ["version" => "1.9.0"];

		if (empty($_SESSION[$ns["module_selections"]]))
			$_SESSION[$ns["module_selections"]] = [];

		$matching_db_details = true;
		foreach ($fields as $field)
		{
			$_SESSION[$ns["module_selections"]][$field["uid"]] =
				isset($_POST[$field["uid"]]) ? ($_POST[$field["uid"]] ? "Y" : "N") :
				(isset($_SESSION[$ns["module_selections"]][$field["uid"]]) ? $_SESSION[$ns["module_selections"]][$field["uid"]] : "N");

			$iniFile[$field["module_name"]] = [
				"installed" => $_SESSION[$ns["module_selections"]][$field["uid"]],
				"enabled" => $_SESSION[$ns["module_selections"]][$field["uid"]]
			];

			if ($_SESSION[$ns["module_selections"]][$field["uid"]] == "Y")
			{
				// Check db variables are the same so they can be stored in common
				$field_conf = parse_ini_file($field["module_name"]."/admin/configuration.ini", true);
				$field_conf_db = $field_conf["database"];
				$allowed_fields = ["host", "type", "username", "password"];
				$field_conf_db_allowed = array_intersect_key($field_conf_db, array_flip($allowed_fields));
				$matching_db_details = $matching_db_details === true ? $field_conf_db_allowed :
					($field_conf_db_allowed == $matching_db_details ? $matching_db_details : false);
				if (!$matching_db_details)
					break;
			}
		}
		if (!$matching_db_details)
		{
			// Fail because matching db details are required for a common conf file
			$yield = new stdClass();
			$yield->messages[] = _("In order to upgrade to Coral 2.0, you need to have a database user with SELECT, INSERT, UPDATE and DELETE rights on each module's database.");
			$yield->messages[] = _("The installation will continue when your config files have matching database access details.");
			require_once "install/templates/try_again_template.php";
			$yield->body = try_again_template();
			yield_test_results_and_exit($yield, [], 0);
		}
		$iniFile["database"] = $matching_db_details;


		$file = @fopen($configFilePath, 'w');
		if ($file)
		{
			$dataToWrite = [];
			foreach ($iniFile as $key => $value)
			{
				$dataToWrite[] = "[$key]";
				foreach ($value as $k => $v)
				{
					$escaped_value = addslashes($v);
					$dataToWrite[] = "$k = \"$escaped_value\"";
				}
				$dataToWrite[] = "";
			}
			fwrite($file, implode("\r\n",$dataToWrite));
			fclose($file);
			$_SESSION[$ns["already_installed"]] = false;
			return true;
		}
		else
		{
			$yield = new stdClass();
			$yield->messages[] = sprintf(_("In order to proceed with the installation, we must be able to write to the main configuration file at '<span class=\"highlight\">%s</span>'. Try:"), $configFilePath);
			if (!file_exists($configFilePath))
			{
				$yield->messages[] = sprintf("<span class=\"highlight\">touch %s</span>", $configFilePath);
			}
			$yield->messages[] = sprintf("<span class=\"highlight\">chmod 777 %s</span>", $configFilePath);
			require_once "install/templates/try_again_template.php";
			$yield->body = try_again_template();
			yield_test_results_and_exit($yield, [], 0);
		}
	}
	else
	{
		require_once "install/templates/modules_to_use_template.php";
		$yield = new stdClass();
		$yield->title = _("Select Installed Modules");
		$yield->messages = [];
		$yield->messages[] = _("It is recommended that you backup your databases before continuing.");
		$yield->messages[] = _("Please note that to upgrade to CORAL 2.0.0, you need to have upgraded your modules to the latest pre-2.0.0 versions.");
		if (is_array($chosen_modules_missing_config_files) && count($chosen_modules_missing_config_files) > 0)
		{
			$yield->messages[] = _("You seem to have chosen modules that are not installed (in other words, they are missing configuration files).");
			$yield->messages[] = _("The problematic modules are: ") . join(", ", $chosen_modules_missing_config_files);
		}
		$yield->body = modules_to_use_template($fields, _("Please select the modules that you have installed."));
		yield_test_results_and_exit($yield, [], 0);
	}
}
