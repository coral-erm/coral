<?php
function register_licensing_provider()
{
	$protected_module_data = [
		"config_file_path" => "licensing/admin/configuration.ini"
	];
	$MODULE_VARS = [
		"uid" => "licensing",
		"translatable_title" => _("Licensing Module"),
	];
	return array_merge( $MODULE_VARS, [
		"bundle" => function($version) use ($MODULE_VARS, $protected_module_data){
			switch ($version) {
				case Installer::VERSION_STRING_INSTALL:
					return [
						"version" => "2.0.0",
						"dependencies_array" => [ "db_tools", "have_read_write_access_to_config", "modules_to_use", "have_default_coral_admin_user", "have_default_db_user" ],
						"sharedInfo" => [
							"database" => [
								"title" => _("Licensing Database"),
								"default_value" => "coral_licensing"
							],
							"config_file" => [
								"path" => $protected_module_data["config_file_path"],
							]
						],
						"function" => function($shared_module_info) use ($MODULE_VARS, $protected_module_data, $version) {
							$return = new stdClass();
							$return->yield = new stdClass();
							$return->success = false;
							$return->yield->title = _("Licensing Module");
							$return->yield->messages = [];

							$this_db_name = $shared_module_info[ $MODULE_VARS["uid"] ]["db_name"];
							$dbconnection = $shared_module_info["provided"]["get_db_connection"]( $this_db_name );

							$result = $shared_module_info["provided"]["check_db"]($MODULE_VARS["uid"], $dbconnection, $shared_module_info[$MODULE_VARS["uid"]], "License", $MODULE_VARS["translatable_title"]);
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

							$uttField = [
								"name" => "useTermsToolFunctionality",
								"label" => _("Use Terms Tool Functionality"),
								"default" => isset($_SESSION[ $MODULE_VARS["uid"] ]["useTermsToolFunctionality"]) ? $_SESSION[ $MODULE_VARS["uid"] ]["useTermsToolFunctionality"] : true
							];
							if (isset($_POST[ $uttField["name"] ]))
							{
								$_SESSION[ $MODULE_VARS["uid"] ]["useTermsToolFunctionality"] = $_POST[ $uttField["name"] ];
							}
							if (!isset($_SESSION[ $MODULE_VARS["uid"] ]["useTermsToolFunctionality"]))
							{
								require_once "install/templates/licensing_module_template.php";
								$return->yield->body = licensing_module_template($uttField);
								$return->success = false;
								return $return;
							}

							$configFile = $protected_module_data["config_file_path"];
							$iniData = array();
							$iniData["settings"] = [
								"useTermsToolFunctionality" => $_SESSION[ $MODULE_VARS["uid"] ]["useTermsToolFunctionality"] ? "Y" : "N"
							];

							$installed_module_details = $shared_module_info["provided"]["get_modules_to_use_config"]($shared_module_info);
							$iniData["settings"] = array_merge($iniData["settings"], $installed_module_details);

							$iniData["database"] = [
								"type" => "mysql",
								"host" => Config::dbInfo("host"),
								"name" => $this_db_name,
								"username" => $shared_module_info["have_default_db_user"]["username"],
								"password" => $shared_module_info["have_default_db_user"]["password"]
							];

							// Setup terms tool config
                            $iniData["terms"] = [
                                "resolver" => "SFX",
                                "open_url" => "",
                                "sid" => "",
                                "client_identifier" => "",
                            ];

							$shared_module_info["provided"]["write_config_file"]($configFile, $iniData);

							$return->success = true;
							return $return;
						}
					];


				case "2.0.0":
					$conf_data = parse_ini_file($protected_module_data["config_file_path"], true);
					return [
						"dependencies_array" => [ "db_tools" ],
						"sharedInfo" => [
							"database_name" => $conf_data["database"]["name"]
						],
						"function" => function($shared_module_info) use ($MODULE_VARS, $protected_module_data, $version) {
							$return = new stdClass();
							$return->success = true;
							$return->yield = new stdClass();
							$return->yield->title = _("Licensing Module");
							$return->yield->messages = [];

							$conf_data = parse_ini_file($protected_module_data["config_file_path"], true);

							// Process sql files
							$db_name = $conf_data["database"]["name"];
							$dbconnection = $shared_module_info["provided"]["get_db_connection"]( $db_name );
                            $ret = $shared_module_info["provided"]["process_sql_files"]( $dbconnection, $version, $MODULE_VARS["uid"] );
							if (!$ret["success"])
							{
								$return->success = false;
								$return->yield->messages = array_merge($return->yield->messages, $ret["messages"]);
								return $return;
							}

							return $return;
						}
					];
				case "3.1.0":
					$conf_data = parse_ini_file($protected_module_data["config_file_path"], true);
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
							$return->yield->title = _("Licensing Module");
							$return->yield->messages = [];

							$conf_data = parse_ini_file($protected_module_data["config_file_path"], true);

							// PROCESS SQL FILES
							$db_name = $conf_data["database"]["name"];
							$dbconnection = $shared_module_info["provided"]["get_db_connection"]( $db_name );
							$ret = $shared_module_info["provided"]["process_sql_files"]( $dbconnection, $version, $MODULE_VARS["uid"] );
							if (!$ret["success"])
							{
								$return->success = false;
								$return->yield->messages = array_merge($return->yield->messages, $ret["messages"]);
								return $return;
							}

							// EDIT CONF FILE
							// Note the "have_read_write_access_to_config" dependency above - it ensure we have the "provided" method below...
							$configFile = $protected_module_data["config_file_path"];
							// Make sure the parent category exists
							if (empty($conf_data["terms"]))
								$conf_data["terms"] = [];
							// Populate the variable with a value
							// Warning: do not set $conf_data["general"] = ["random" => "something"] or you will lose other variables. Rather:
							$iniData["terms"]["resolver"] = "SFX";
							$iniData["terms"]["open_url"] = "";
							$iniData["terms"]["sid"] = "";
							$iniData["terms"]["client_identifier"] = "";
							$shared_module_info["provided"]["write_config_file"]($configFile, $conf_data);

							return $return;
						}
					];
				default:
					return [
						"function" => function($shared_module_info) {
							$return = new stdClass();
							$return->yield = new stdClass();
							$return->success = true;
							$return->yield->title = _("Licensing Module");
							return $return;
						}
					];	
			}
		}
	]);
}