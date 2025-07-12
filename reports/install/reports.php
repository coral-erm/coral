<?php
function register_reports_provider()
{
	$protected_module_data = [
		"config_file_path" => "reports/admin/configuration.ini"
	];
	$MODULE_VARS = [
		"uid" => "reports",
		"translatable_title" => _("Reports Module"),
	];
	return array_merge( $MODULE_VARS, [
		"bundle" => function($version) use ($MODULE_VARS, $protected_module_data) {
			switch ($version) {
				case Installer::VERSION_STRING_INSTALL:
					return [
						"dependencies_array" => ["db_tools", "have_read_write_access_to_config", "modules_to_use", "usage", "have_default_db_user", "some_kind_of_auth"],
						"sharedInfo" => [
							"database" => [
								"title" => _("Reports Database"),
								"default_value" => "coral_reports"
							],
							"config_file" => [
								"path" => $protected_module_data["config_file_path"],
							]
						],
						"function" => function($shared_module_info) use ($MODULE_VARS, $protected_module_data, $version) {
							$return = new stdClass();
							$return->yield = new stdClass();
							$return->success = false;
							$return->yield->title = _("Reports Module");
                            $return->yield->messages = [];
							$return->yield->messages[] = "Incomplete Installer";

							$this_db_name = $shared_module_info[ $MODULE_VARS["uid"] ]["db_name"];
							$dbconnection = $shared_module_info["provided"]["get_db_connection"]( $this_db_name );

							$result = $shared_module_info["provided"]["check_db"]($MODULE_VARS["uid"], $dbconnection, $shared_module_info[$MODULE_VARS["uid"]], "Resource", $MODULE_VARS["translatable_title"]);
							if ($result)
								return $result;

							$ret = $shared_module_info["provided"]["process_sql_files"]( $dbconnection, $version, $MODULE_VARS["uid"] );
							if (!$ret["success"])
							{
								$return->success = false;
								$return->yield->messages = array_merge($return->yield->messages, $ret["messages"]);
								return $return;
							}

							//set up config file
							$configFile = $protected_module_data["config_file_path"];
							$iniData = array();
							$iniData["settings"] = [
								"baseURL" => $shared_module_info["usage"]["baseUrl"]
							];
							//config file: database
							$iniData["database"] = [
								"type" => "mysql",
								"host" => Config::dbInfo("host"),
								"name" => $this_db_name,
								"username" => $shared_module_info["have_default_db_user"]["username"],
								"password" => $shared_module_info["have_default_db_user"]["password"],
								//TODO: yes, this is horrible but when it's unified it will go away - it should at least not be under "database"
								"usageDatabaseName" => $shared_module_info["usage"]["db_name"]
							];
							//config file: ldap
							if (isset($shared_module_info["modules_to_use"]["useModule"]["auth"]) && $shared_module_info["modules_to_use"]["useModule"]["auth"])
							{
								if ($shared_module_info["auth"]["ldap_enabled"])
								{
									$iniData["ldap"] = [
										"host"				=> $shared_module_info["auth"]["host"],
										"search_key"	=> $shared_module_info["auth"]["search_key"],
										"base_dn"		=> $shared_module_info["auth"]["base_dn"],
										"fname_field"	=> $shared_module_info["auth"]["fname"],
										"lname_field"	=> $shared_module_info["auth"]["lname"]
									];
								}
							}
							//config file: write out
							$shared_module_info["provided"]["write_config_file"]($configFile, $iniData);

							$return->success = true;
							return $return;
						}
					];


				case "2.0.0":
					return [
						"function" => function($shared_module_info) {
							$return = new stdClass();
							$return->success = true;
							$return->yield = new stdClass();
							$return->yield->title = _("Reports Module");
							return $return;
						}
					];

        case "3.0.0":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Reports Module");
                    return $return;
                }
            ];
        case "3.0.1":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Reports Module");
                    return $return;
                }
              ];

        case "2020.02":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Reports Module");
                    return $return;
                }
            ];

        case "2020.09":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Reports Module");
                    return $return;
                }
            ];

        case "2024.04":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Reports Module");
                    return $return;
                }
            ];

        case "2024.10":
            return [
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->yield = new stdClass();
                    $return->success = true;
                    $return->yield->title = _("Reports Module");
                    return $return;
                }
            ];

		case "2025.04":
			return [
				"function" => function($shared_module_info) {
					$return = new stdClass();
					$return->yield = new stdClass();
					$return->success = true;
					$return->yield->title = _("Reports Module");
					return $return;
				}
			];	

		case "2025.04.01":
			return [
				"function" => function($shared_module_info) {
					$return = new stdClass();
					$return->yield = new stdClass();
					$return->success = true;
					$return->yield->title = _("Reports Module");
					return $return;
				}
			];

		case "2025.04.02":
			return [
				"function" => function($shared_module_info) {
					$return = new stdClass();
					$return->yield = new stdClass();
					$return->success = true;
					$return->yield->title = _("Reports Module");
					return $return;
				}
			];

		case "2025.04.03":
			return [
				"function" => function($shared_module_info) {
					$return = new stdClass();
					$return->yield = new stdClass();
					$return->success = true;
					$return->yield->title = _("Reports Module");
					return $return;
				}
			];

		case "2025.04.04":
			return [
				"function" => function($shared_module_info) {
					$return = new stdClass();
					$return->yield = new stdClass();
					$return->success = true;
					$return->yield->title = _("Reports Module");
					return $return;
				}
			];

        case "2025.04.05":
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
