<?php

class DBAccess {
	const DB_FAILED = 30001;
	const DB_ALREADY_EXISTED = 30002;
	const DB_CREATED = 30003;
}

function register_have_database_access_provider()
{
	return [
		"uid" => "have_database_access",
		"translatable_title" => _("Database Access"),
		"bundle" => function($version = 0){
			return [
				"dependencies_array" => ["meets_system_requirements", "modules_to_use", "get_db_connection"],
				"function" => function($shared_module_info){
					$return = new stdClass();
					$return->yield = new stdClass();
					$return->success = true;
					$return->yield->messages = [];
					$return->yield->title = _("Have database access");

					$shared_database_info = [];
					foreach ($shared_module_info["modules_to_use"]["useModule"] as $key => $value) {
						if ($value && isset($shared_module_info[$key]["database"]))
						{
							$db_postvar_name = "db_" . $key . "_name";
							if (!empty($_POST[$db_postvar_name]))
								{
								if (!isset($_SESSION["have_database_access"]))
									$_SESSION["have_database_access"] = [];
								$_SESSION["have_database_access"][$db_postvar_name] = $_POST[$db_postvar_name];
							}
							$shared_database_info[] = [
								"title"			=> $shared_module_info[$key]["database"]["title"],
								"default_value"	=> empty($_SESSION["have_database_access"][$db_postvar_name]) ? $shared_module_info[$key]["database"]["default_value"] : $_SESSION["have_database_access"][$db_postvar_name],
								"name"			=> $db_postvar_name,
								"feedback"		=> "db_" . $key . "_feedback",
								"key"			=> $key,
							];
						}
					}

					$db_access_postvar_names = [
						"username"	=> "dbusername",
						"password"	=> "dbpassword",
						"host"		=> "dbhost"
					];
					foreach ($db_access_postvar_names as $value) {
						if (!empty($_POST[$value]))
						{
							$_SESSION["have_database_access"][$value] = $_POST[$value];
						}
					}
					$db_access_vars = [
						"username"	=> [
							"title"			=> _("Database Username"),
							"placeholder"	=> isset($_SESSION["have_database_access"][$db_access_postvar_names["username"]]) ? $_SESSION["have_database_access"][$db_access_postvar_names["username"]] : _("Username"),
							"name"			=> $db_access_postvar_names["username"]
						],
						"password"	=> [
							"title"			=> _("Database Password"),
							"placeholder"	=> isset($_SESSION["have_database_access"][$db_access_postvar_names["password"]]) ? _("leave blank to leave unchanged") : _("Password"),
							"name"			=> $db_access_postvar_names["password"]
						],
						"host"		=> [
							"title"			=> _("Database Host"),
							"placeholder"	=> isset($_SESSION["have_database_access"][$db_access_postvar_names["host"]]) ? $_SESSION["have_database_access"][$db_access_postvar_names["host"]] : _("Hostname"),
							"name"			=> $db_access_postvar_names["host"]
						]
					];

					require "install/templates/database_details_template.php";
					$instruction = _("If you would like to use pre-existing databases or custom database names. Use the advanced section to configure these settings.");
					$return->yield->body = database_details_template($instruction, $db_access_vars, $shared_database_info);

					try
					{
						Config::dbInfo("username");
					}
					catch (Exception $e)
					{
						switch ($e->getCode()) {
							case Config::ERR_FILE_NOT_READABLE:
							case Config::ERR_VARIABLES_MISSING:
								// Config file not yet set up
								if (isset($_SESSION["have_database_access"][$db_access_postvar_names["username"]]))
								{
									Config::loadTemporaryDBSettings([
										"host" => $_SESSION["have_database_access"][$db_access_postvar_names["host"]],
										"username" => $_SESSION["have_database_access"][$db_access_postvar_names["username"]],
										"password" => $_SESSION["have_database_access"][$db_access_postvar_names["password"]]
									]);
								}
								else
								{
									$return->yield->messages[] = _("To begin with, we need a username and password to create the databases CORAL and its modules will be using.");
									$return->success = false;
									return $return;
								}
								break;

							default:
								throw new LogicException("I don't know what error you managed to get so you need to debug more deeply", 1001);
								break;
						}
					}

					// Try to connect
					$get_db_connection_return_value = $shared_module_info["provided"]["get_db_connection"](false);
					if (is_array($get_db_connection_return_value))
					{
						$return->yield->messages = array_merge($return->yield->messages, $get_db_connection_return_value);
						$return->success = false;
						return $return;
					}
					else
					{
						$dbconnection = $get_db_connection_return_value;
					}

					// Go through the databases and try to create them all (or see if they already exist)
					foreach ($shared_database_info as $db)
					{
						// $db["key"] is the module uid - dbtools uses this fact so if it changes dbtools will need to be fixed as well
						$dbfeedback = $db["feedback"];
						$dbnamestr = $db["name"];
						$dbname = empty($_SESSION["have_database_access"][$dbnamestr]) ? $db["default_value"] : $_SESSION["have_database_access"][$dbnamestr];
						$_SESSION["have_database_access"][$dbfeedback] = !empty($_SESSION["have_database_access"][$dbfeedback]) ? $_SESSION["have_database_access"][$dbfeedback] : DBAccess::DB_FAILED;
						try
						{
							$dbconnection->selectDB($dbname);
							$result = $dbconnection->processQuery("SELECT * FROM `information_schema`.`tables` WHERE `table_schema`='$dbname';");
							// If DB is empty, pretend we created it
							if ($result && $result->numRows() == 0)
							{
								$_SESSION["have_database_access"][$dbfeedback] = DBAccess::DB_CREATED;
							}
							else
							{
								if ($_SESSION["have_database_access"][$dbfeedback] == DBAccess::DB_CREATED)
								{
									$_SESSION["db_tools"]["use_tables"] = isset($_SESSION["db_tools"]["use_tables"]) ? $_SESSION["db_tools"]["use_tables"] : [];
									$_SESSION["db_tools"]["use_tables"][] = $db["key"];
								}
								$_SESSION["have_database_access"][$dbfeedback] = DBAccess::DB_ALREADY_EXISTED;
							}
						}
						catch (Exception $e)
						{
							$_SESSION["have_database_access"][$dbfeedback] == DBAccess::DB_FAILED;
							switch ($e->getCode())
							{
								case DBService::ERR_COULD_NOT_SELECT_DATABASE:
									try {
										// The commented line is preferable (see http://stackoverflow.com/a/766996/123415) but we need to be backwards compatible to mysql 5.5
										// $result = $dbconnection->processQuery("CREATE DATABASE `$dbname` DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;");
										$result = $dbconnection->processQuery("CREATE DATABASE `$dbname` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci;");
										$_SESSION["have_database_access"][$dbfeedback] = DBAccess::DB_CREATED;

										// If we have actually just created it, make sure that use_tables is not set because process Sql needs to happen!
										if (isset($_SESSION["db_tools"]["use_tables"]) && in_array($db["key"], $_SESSION["db_tools"]["use_tables"]))
											unset($_SESSION["db_tools"]["use_tables"][$db["key"]]);
									} catch (Exception $e) {
										$return->yield->messages[] = _("We tried to select a database with the name $dbname but failed. We also could not create it.");
										$return->yield->messages[] = _("In order to proceed, we need access rights to create databases or you need to manually create the databases and provide their names and the credentials for a user with access rights to them.");
										$return->success = false;
										return $return;
									}
									// THIS SHOULDN'T FAIL BECAUSE WE'VE JUST CREATED THE DB SUCCESSFULLY.
									$result = $dbconnection->selectDB( $dbname );
									break;

								default:
									echo "We haven't prepared for the following error (have_database_access.php #2):<br />\n";
									var_dump($e);
									break;
							}
						}
						$shared_module_info["setSharedModuleInfo"]($db["key"], "db_name", $dbname);
						$shared_module_info["setSharedModuleInfo"]($db["key"], "db_feedback", $_SESSION["have_database_access"][$dbfeedback]);
					}

					try
					{
						$temporary_test_table_name = "temp_test";
						$result = $dbconnection->processQuery("DROP TABLE IF EXISTS `$temporary_test_table_name`;");
						$result = $dbconnection->processQuery("CREATE TABLE `$temporary_test_table_name` (id int);");
						$result = $dbconnection->processQuery("INSERT INTO `$temporary_test_table_name` VALUES (0);");
						$result = $dbconnection->processQuery("DROP TABLE IF EXISTS `$temporary_test_table_name`;");
					}
					catch (Exception $e)
					{
						$return->yield->messages[] = _("We were unable to create/delete a table. Please check your user rights. ({$e->getCode()})");
						$return->success = false;
						return $return;
					}
					$shared_module_info["setSharedModuleInfo"](
						"provided",
						"get_db_connection",
						function($db_name) use ($dbconnection) {
							$dbconnection->selectDB($db_name);
							return $dbconnection;
						}
					);
					return $return;
				}
			];
		}
	];
}