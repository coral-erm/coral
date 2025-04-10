<?php
function register_modules_to_use_provider()
{
	$MODULE_VARS = [
		"uid" => "modules_to_use",
		"translatable_title" => _("Modules to use"),
		"hide_from_completion_list" => true,
	];

	return array_merge( $MODULE_VARS,[
		"bundle" => function($version = 0) use ($MODULE_VARS) {
			switch ($version) {
				case Installer::VERSION_STRING_INSTALL:
					return [
						"dependencies_array" => ["meets_system_requirements"],
						"function" => function($shared_module_info) use ($MODULE_VARS) {
							$return = new stdClass();
							$return->yield = new stdClass();
							$return->yield->title = _("Modules to use");
							$return->success = true;

							$module_list = $shared_module_info["module_list"];
							$modules_not_to_install = [];

							foreach ($module_list as $i => $mod)
							{
								$mod_chosen = null;
								if (isset($_POST[$mod["uid"]]))
									$mod_chosen = $_POST[$mod["uid"]] == 1;

								if ($mod_chosen !== null || isset($_SESSION[ $MODULE_VARS["uid"] ]["useModule"][ $mod["uid"] ]))
								{
									$mod_chosen = $mod_chosen !== null ? $mod_chosen : $_SESSION[ $MODULE_VARS["uid"] ]["useModule"][ $mod["uid"] ];
									$_SESSION[ $MODULE_VARS["uid"] ]["useModule"] = isset($_SESSION[ $MODULE_VARS["uid"] ]["useModule"]) ? $_SESSION[ $MODULE_VARS["uid"] ]["useModule"] : [];
									$_SESSION[ $MODULE_VARS["uid"] ]["useModule"][ $mod["uid"] ] = $mod_chosen;
									$shared_module_info["setSharedModuleInfo"]($MODULE_VARS["uid"], "useModule", $_SESSION[ $MODULE_VARS["uid"] ]["useModule"]);
									$module_list[$i]["default_value"] = $mod_chosen;
									if (!$mod_chosen)
										$modules_not_to_install[] = $mod["uid"];
								}
								else
								{
									// If the associated session variable is still unset the setup has failed but why?
									$return->messages[] = "For some reason at least one ($mod[uid]) of these variables is not set. There may a problem with the installer please contact the programmers with this error message.";
									$return->success &= false;
								}
							}

							//check that all dependencies are met
							$title_from_uid = function($module_list, $uid) {
								return array_values(array_filter($module_list, function ($m) use ($uid) {
									return $m["uid"] == $uid;
								}))[0]["title"];
							};
							if (is_array($modules_not_to_install) && count($modules_not_to_install) > 0)
							{
								foreach ($_SESSION[ $MODULE_VARS["uid"] ]["useModule"] as $key => $value) {
									if (!$value) continue;

									if (isset( $shared_module_info["dependencies"][$key]) &&
									array_intersect($modules_not_to_install, $shared_module_info["dependencies"][$key]))
									{
										$return->yield->messages[] = _("The modules that you have chosen to install require additional modules.");
										$mod_title = $title_from_uid($module_list, $key);
										foreach (array_intersect($modules_not_to_install, $shared_module_info["dependencies"][$key]) as $dep) {
											$dep_title = $title_from_uid($module_list, $dep);
											$return->yield->messages[] = "$mod_title <i>" . _("requires") . "</i> $dep_title";
										}
										$return->success = false;
									}
								}
							}

							if (!$return->success)
							{
								$instruction = _("Please select the modules that you would like to install:");
								require_once "install/templates/modules_to_use_template.php";
								$return->yield->body = modules_to_use_template($module_list, $instruction);
							}
							else
							{
								$shared_module_info["setSharedModuleInfo"](
									"provided",
									"get_modules_to_use_config",
									function($smi){
										$conf = [];
										$modules_to_use = $smi["modules_to_use"]["useModule"];
										foreach ($modules_to_use as $key => $value) {
											$conf["{$key}Module"] = $value ? 'Y' : 'N';
											if ($value && isset($smi[$key]["db_name"]))
												$conf["{$key}DatabaseName"] = $smi[$key]["db_name"];
										}

										// This assumes too much knowledge of the auth module but since we're going towards a common config file this will go away eventually.
										if ($conf["authModule"] == 'N' && isset($smi["remote_auth"]["remote_auth_variable"]))
										{
											// TODO: ensure that this actually works
											$conf["remoteAuthVariableName"] = $smi["remote_auth"]["remote_auth_variable"];
										}
										return $conf;
									}
								);
							}

							return $return;
						}
						];


				case Installer::VERSION_STRING_MODIFY:
					// one day this will do something intelligent...
					return null;

				default:
					return [
						"dependencies_array" => ["meets_system_requirements"],
						"function" => function($shared_module_info) use ($MODULE_VARS) {
							$return = new stdClass();
							$return->yield = new stdClass();
							$return->yield->title = _("Modules to Upgrade");
							$return->yield->messages = [];
							$return->success = true;

							try
							{
								$installed_modules = Config::getInstalledModules();
								$use_module = [];
								foreach ($installed_modules as $mod) {
									$use_module[$mod] = true;
								}
								$shared_module_info["setSharedModuleInfo"]($MODULE_VARS["uid"], "useModule", $use_module);
							}
							catch (Exception $e)
							{
								$return->success = false;
								$return->yield->messages[] = _("Unable to read from config file for some reason.");
								$return->yield->messages[] = var_export($e, 1);
							}

							return $return;
						}
					];
			}
		}
	]);
}
