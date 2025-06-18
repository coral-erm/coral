<?php
function register_organizations_provider()
{
	$protected_module_data = [
		"config_file_path" => "organizations/admin/configuration.ini"
	];
	$MODULE_VARS = [
		"uid" => "organizations",
		"translatable_title" => _("Organizations Module"),
	];
	return array_merge( $MODULE_VARS, [
		"bundle" => function($version) use ($MODULE_VARS, $protected_module_data) {
			switch ($version) {
				case Installer::VERSION_STRING_INSTALL:
					return [
						"dependencies_array" => [
							"modules_to_use",
							"db_tools",
							"have_read_write_access_to_config",
							"have_default_coral_admin_user",
							"have_default_db_user",
							"some_kind_of_auth"],
						"sharedInfo" => [
							"database" => [
								"title" => _("Organizations Database"),
								"default_value" => "coral_organizations"
							],
							"config_file" => [
								"path" => $protected_module_data["config_file_path"],
							]
						],
						"function" => function($shared_module_info) use ($MODULE_VARS, $protected_module_data, $version) {
							$return = new stdClass();
							$return->yield = new stdClass();
							$return->success = false;
							$return->yield->title = _("Organizations Module");
                            $return->yield->messages = [];

							$this_db_name = $shared_module_info[ $MODULE_VARS["uid"] ]["db_name"];
							$dbconnection = $shared_module_info["provided"]["get_db_connection"]( $this_db_name );

							$result = $shared_module_info["provided"]["check_db"]($MODULE_VARS["uid"], $dbconnection, $shared_module_info[$MODULE_VARS["uid"]], "Organization", $MODULE_VARS["translatable_title"]);
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

							// BUILD AND WRITE CONFIG FILE
							$configFile = $protected_module_data["config_file_path"];
							$iniData = array();
							//config file: database
							$iniData["database"] = [
								"type" => "mysql",
								"host" => Config::dbInfo("host"),
								"name" => $this_db_name,
								"username" => $shared_module_info["have_default_db_user"]["username"],
								"password" => $shared_module_info["have_default_db_user"]["password"]
							];
							//config file: ldap
							if (isset($shared_module_info["modules_to_use"]["useModule"]["auth"]) && $shared_module_info["modules_to_use"]["useModule"]["auth"])
							{
								if ($shared_module_info["auth"]["ldap_enabled"])
								{
									$iniData["ldap"] = [
										"host"			=> $shared_module_info["auth"]["host"],
										"search_key"	=> $shared_module_info["auth"]["search_key"],
										"base_dn"		=> $shared_module_info["auth"]["base_dn"],
										"fname_field"	=> $shared_module_info["auth"]["fname"],
										"lname_field"	=> $shared_module_info["auth"]["lname"]
									];
								}
							}
							//config file: settings
							$installed_module_details = $shared_module_info["provided"]["get_modules_to_use_config"]($shared_module_info);
							if (!empty($installed_module_details))
								$iniData["settings"] = $installed_module_details;
							//write out config file
							$shared_module_info["provided"]["write_config_file"]($configFile, $iniData);

							$return->success = true;
							return $return;
						}
					];

				case "2.0.0":
					return [
						"function" => function($shared_module_info) {
							$return = new stdClass();
							$return->yield = new stdClass();
							$return->success = true;
							$return->yield->title = _("Organizations Module");
							return $return;
						}
					];

        case "3.0.0":
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
                    $return->yield->title = _("Organizations Module");
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
        case "3.0.1":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Organizations Module");
                    return $return;
                }
            ];

          case "2020.02":
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
                    $return->yield->title = _("Organizations Module");
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

        case "2020.09":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Organizations Module");
                    return $return;
                }
            ];

        case "2024.04":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Organizations Module");
                    return $return;
                }
            ];

        case "2024.10":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Organizations Module");
                    return $return;
                }
            ];

        case "2025.04":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Organizations Module");
                    return $return;
                }
            ];   
             
        case "2025.04.01":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Organizations Module");
                    return $return;
                }
            ];

        case "2025.04.02":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Organizations Module");
                    return $return;
                }
            ];

        case "2025.04.03":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Organizations Module");
                    return $return;
                }
            ];
        
				default:
					return null;
			}
		}
	]);
}
