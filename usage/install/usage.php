<?php
function register_usage_provider()
{
	$protected_module_data = [
		"config_file_path" => "usage/admin/configuration.ini"
	];
	$MODULE_VARS = [
		"uid" => "usage",
		"translatable_title" => _("Usage Module"),
	];
	return array_merge( $MODULE_VARS, [
		"bundle" => function($version) use ($MODULE_VARS, $protected_module_data) {
			$configFileExists = file_exists($protected_module_data["config_file_path"]);
			$conf_data = ($configFileExists) ? parse_ini_file($protected_module_data["config_file_path"], true) : [];
			switch ($version) {
				case Installer::VERSION_STRING_INSTALL:
					return [
						"dependencies_array" => [ "db_tools", "have_read_write_access_to_config", "modules_to_use", "have_default_coral_admin_user", "have_default_db_user" ],
						"sharedInfo" => [
							"database" => [
								"title" => _("Usage Database"),
								"default_value" => "coral_usage"
							],
							"config_file" => [
								"path" => $protected_module_data["config_file_path"],
							]
						],
						"function" => function($shared_module_info) use ($MODULE_VARS, $protected_module_data, $version) {
							$return = new stdClass();
							$return->yield = new stdClass();
							$return->success = false;
							$return->yield->title = _("Usage Module");
                            $return->yield->messages = [];

							$this_db_name = $shared_module_info[ $MODULE_VARS["uid"] ]["db_name"];
							$dbconnection = $shared_module_info["provided"]["get_db_connection"]( $this_db_name );

							$result = $shared_module_info["provided"]["check_db"]($MODULE_VARS["uid"], $dbconnection, $shared_module_info[$MODULE_VARS["uid"]], "Publisher", $MODULE_VARS["translatable_title"]);
							if ($result)
								return $result;

							$ret = $shared_module_info["provided"]["process_sql_files"]( $dbconnection, $version, $MODULE_VARS["uid"] );
							if (!$ret["success"])
							{
								$return->success = false;
								$return->yield->messages = array_merge($return->yield->messages, $ret["messages"]);
								return $return;
							}

							$shared_module_info["provided"]["set_up_admin_in_db"]($dbconnection, $shared_module_info["have_default_coral_admin_user"]["default_user"]);

							$defaultUseOutliers = isset($_SESSION[$MODULE_VARS["uid"]]["useOutliers"]) ? $_SESSION[$MODULE_VARS["uid"]]["useOutliers"] : true;
							$defaultBaseURL = isset($_SESSION[$MODULE_VARS["uid"]]["baseURL"]) ? $_SESSION[$MODULE_VARS["uid"]]["baseURL"] : "";

							$_SESSION[$MODULE_VARS["uid"]]["useOutliers"] = isset($_POST["useOutliers"]) ? $_POST["useOutliers"] : $defaultUseOutliers;
							$_SESSION[$MODULE_VARS["uid"]]["baseURL"] = isset($_POST["baseURL"]) ? $_POST["baseURL"] : $defaultBaseURL;
							$usage_fields = [
								[
									"key" => "baseURL",
									"title" => _("Link Resolver Base URL (optional)"),
									"type" => "text",
									"default_value" => $_SESSION[$MODULE_VARS["uid"]]["baseURL"]
								],[
									"key" => "useOutliers",
									"title" => _("Use Outlier Flagging Feature"),
									"type" => "checkbox",
									"default_value" => $_SESSION[$MODULE_VARS["uid"]]["useOutliers"]
								]
							];
							if (!isset($_SESSION[$MODULE_VARS["uid"]]["formCompleted"]) || !$_SESSION[$MODULE_VARS["uid"]]["formCompleted"])
							{
								require_once "install/templates/usage_module_template.php";
								$title = _("Please set up the following options for the Usage module.");
								$return->yield->body = usage_module_template($title, $usage_fields);
								$return->success = false;
								$_SESSION[$MODULE_VARS["uid"]]["formCompleted"] = true;
								return $return;
							}
							// To validate form - put validation code here and set `$_SESSION[$MODULE_VARS["uid"]]["formCompleted"] = false`
							$shared_module_info["setSharedModuleInfo"]($MODULE_VARS["uid"], "baseUrl", $_SESSION[$MODULE_VARS["uid"]]["baseURL"]);


							//set up config file
							$configFile = $protected_module_data["config_file_path"];
							$iniData = array();
							//config file: settings
							$iniData["settings"] = [
								"useOutliers" => $_SESSION[$MODULE_VARS["uid"]]["useOutliers"] ? "Y" : "N",
								"baseURL" => $_SESSION[$MODULE_VARS["uid"]]["baseURL"]
							];
							$installed_module_details = $shared_module_info["provided"]["get_modules_to_use_config"]($shared_module_info);
							$iniData["settings"] = array_merge($iniData["settings"], $installed_module_details);
							//config file: database
							$iniData["database"] = [
								"type" => "mysql",
								"host" => Config::dbInfo("host"),
								"name" => $this_db_name,
								"username" => $shared_module_info["have_default_db_user"]["username"],
								"password" => $shared_module_info["have_default_db_user"]["password"]
							];
							//config file: write out
							$shared_module_info["provided"]["write_config_file"]($configFile, $iniData);

							$return->success = true;
							return $return;
						}
					];




				case "2020.09":
					return [
						"dependencies_array" => [ "db_tools", "have_read_write_access_to_config" ],
						"sharedInfo" => [
							"config_file" => [
								"path" => $protected_module_data["config_file_path"],
							],
							"database_name" => $conf_data["database"]["name"]
						],
						"function" => function($shared_module_info) use ($MODULE_VARS, $protected_module_data, $version) {
							$return = new stdClass();
							$return->success = true;
							$return->yield = new stdClass();
							$return->yield->title = _("Usage Module");
							$return->yield->messages = [];

							// Process SQL files.
							$db_name = $conf_data["database"]["name"];
							$dbconnection = $shared_module_info["provided"]["get_db_connection"]( $db_name );
							$ret = $shared_module_info["provided"]["process_sql_files"]( $dbconnection, $version, $MODULE_VARS["uid"] );
							if (!$ret["success"]) {
								$return->success = false;
								$return->yield->messages = array_merge($return->yield->messages, $ret["messages"]);
								return $return;
							}
							return $return;
						}
					];
				case "2024.04":
					return [
							"dependencies_array" => [ "db_tools", "have_read_write_access_to_config" ],
							"sharedInfo" => [
									"config_file" => [
											"path" => $protected_module_data["config_file_path"],
									],
									"database_name" => $conf_data["database"]["name"]
							],
							"function" => function($shared_module_info) use ($MODULE_VARS, $protected_module_data, $version) {
									$return = new stdClass();
									$return->success = true;
									$return->yield = new stdClass();
									$return->yield->title = _("Usage Module");
									$return->yield->messages = [];

									// Process SQL files.
									$db_name = $conf_data["database"]["name"];
									$dbconnection = $shared_module_info["provided"]["get_db_connection"]( $db_name );
									$ret = $shared_module_info["provided"]["process_sql_files"]( $dbconnection, $version, $MODULE_VARS["uid"] );
									if (!$ret["success"]) {
											$return->success = false;
											$return->yield->messages = array_merge($return->yield->messages, $ret["messages"]);
											return $return;
									}
									return $return;
							}
					];

				default:
				return [
					"function" => function($shared_module_info) {
						$return = new stdClass();
						$return->yield = new stdClass();
						$return->success = true;
						$return->yield->title = _("Usage Module");
						return $return;
					}
				];
			}
		}
	]);
}
