<?php
function register_management_provider()
{
	$protected_module_data = [
		"config_file_path" => "management/admin/configuration.ini"
	];
	$MODULE_VARS = [
		"uid" => "management",
		"translatable_title" => _("Management Module"),
	];
	return array_merge( $MODULE_VARS, [
		"bundle" => function($version) use ($MODULE_VARS, $protected_module_data) {
			$configFileExists = file_exists($protected_module_data["config_file_path"]);
			$conf_data = ($configFileExists) ? parse_ini_file($protected_module_data["config_file_path"], true) : [];
			switch ($version) {
				case Installer::VERSION_STRING_INSTALL:
					return [
						"dependencies_array" => ["db_tools", "have_read_write_access_to_config", "modules_to_use", "have_default_coral_admin_user", "have_default_db_user", "some_kind_of_auth"],
						"sharedInfo" => [
							"database" => [
								"title" => _("Management Database"),
								"default_value" => "coral_management"
							],
							"config_file" => [
								"path" => $protected_module_data["config_file_path"],
							]
						],
						"function" => function($shared_module_info) use ($MODULE_VARS, $protected_module_data, $version) {
							$return = new stdClass();
							$return->yield = new stdClass();
							$return->success = true;
							$return->yield->title = _("Management Module");
							$return->yield->messages = [];

							$this_db_name = $shared_module_info[ $MODULE_VARS["uid"] ]["db_name"];
							$dbconnection = $shared_module_info["provided"]["get_db_connection"]( $this_db_name );


							$result = $shared_module_info["provided"]["check_db"]($MODULE_VARS["uid"], $dbconnection, $shared_module_info[$MODULE_VARS["uid"]], "Management", $MODULE_VARS["translatable_title"]);
							if ($result)
								return $result;


							// Process sql files
                            $ret = $shared_module_info["provided"]["process_sql_files"]( $dbconnection, $version, $MODULE_VARS["uid"] );
							if (!$ret["success"])
							{
								$return->success = false;
								$return->yield->messages = array_merge($return->yield->messages, $ret["messages"]);
								return $return;
							}

							$shared_module_info["provided"]["set_up_admin_in_db"]($dbconnection, $shared_module_info["have_default_coral_admin_user"]["default_user"]);

							$configFile = $protected_module_data["config_file_path"];

							$iniData = array();
							$iniData["settings"] = $shared_module_info["provided"]["get_modules_to_use_config"]($shared_module_info);

							$iniData["database"] = [
								"type" => "mysql",
								"host" => Config::dbInfo("host"),
								"name" => $this_db_name,
								"username" => $shared_module_info["have_default_db_user"]["username"],
								"password" => $shared_module_info["have_default_db_user"]["password"]
							];
							$shared_module_info["provided"]["write_config_file"]($configFile, $iniData);

							return $return;
						}
					];

				case "2.0.0":
					return [
						"dependencies_array" => [ "db_tools" ],
						"sharedInfo" => [
							"database_name" => $conf_data["database"]["name"]
						],
						"function" => function($shared_module_info) use ($MODULE_VARS, $protected_module_data, $version, $conf_data) {
							$return = new stdClass();
							$return->success = true;
							$return->yield = new stdClass();
							$return->yield->title = _("Management Module");
							$return->yield->messages = [];

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
				default:
					return [
						"function" => function($shared_module_info) {
							$return = new stdClass();
							$return->yield = new stdClass();
							$return->success = true;
							$return->yield->title = _("Management Module");
							return $return;
						}
					];	
			}
		}
	]);
}
