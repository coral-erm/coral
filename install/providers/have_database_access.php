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
		"bundle" => function($version){
			return [
				"dependencies_array" => ["meets_system_requirements", "modules_to_use", "get_db_connection"],
				"function" => function($shared_module_info) use ($version){
					$return = new stdClass();
					$return->yield = new stdClass();
					$return->success = true;
					$return->yield->messages = [];
					$return->yield->title = _("Have database access");

					// Build up a list of $shared_database_info - information about modules that require a database
					$shared_database_info = [];
					foreach ($shared_module_info["modules_to_use"]["useModule"] as $uid => $use_module) {
						if ($use_module && isset($shared_module_info[$uid]["database"]))
						{
							$db_postvar_name = "db_" . $uid . "_name";
							if (!empty($_POST[$db_postvar_name]))
								{
								if (!isset($_SESSION["have_database_access"]))
									$_SESSION["have_database_access"] = [];
								$_SESSION["have_database_access"][$db_postvar_name] = $_POST[$db_postvar_name];
							}
							$shared_database_info[] = [
								"title"			=> $shared_module_info[$uid]["database"]["title"],
								"default_value"	=> empty($_SESSION["have_database_access"][$db_postvar_name]) ? $shared_module_info[$uid]["database"]["default_value"] : $_SESSION["have_database_access"][$db_postvar_name],
								"name"			=> $db_postvar_name,
								"feedback"		=> "db_" . $uid . "_feedback",
								"key"			=> $uid,
							];
						}
					}

					// The logic for getting user settings from POST into SESSION
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
							"name"			=> $db_access_postvar_names["username"],
							"default"		=> ""
						],
						"password"	=> [
							"title"			=> _("Database Password"),
							"placeholder"	=> isset($_SESSION["have_database_access"][$db_access_postvar_names["password"]]) ? _("leave blank to leave unchanged") : _("Password"),
							"name"			=> $db_access_postvar_names["password"],
							"default"		=> ""
						],
						"host"		=> [
							"title"			=> _("Database Host"),
							"placeholder"	=> isset($_SESSION["have_database_access"][$db_access_postvar_names["host"]]) ? $_SESSION["have_database_access"][$db_access_postvar_names["host"]] : _("Hostname"),
							"name"			=> $db_access_postvar_names["host"],
							"default"		=> "localhost"
						]
					];

					/*
						We're trying to figure out how to use the common config's host setting if we are in upgrade mode.
						I don't like the empty catch on ln 94ish and I don't like the random if on ln 117ish
					 */

					require "install/templates/database_details_template.php";
					switch ($version) {
						case Installer::VERSION_STRING_INSTALL:
							$instruction = "<b>"
                    . _("Create Databases")
                    . "</b><br/>"
                    .  _("To create the databases CORAL will use, we need a mysql username and password with permission to create databases. This username and password will not be stored.")
										. "<br /><br /><b>"
                    . _("Use Existing or Custom Databases")
                    . "</b><br/>"
										.  _("If you would like to use pre-existing databases or custom database names. Use the advanced section to configure these settings.")
                    . "<br /><br />";
							$db_access_vars = array_intersect_key($db_access_vars, ["username" => 1, "password" => 1, "host" => 1]);
							break;
						default: // Upgrade
							$instruction = _("In order to run the upgrade, we need database credentials that allow us to create and delete tables.");
							$db_access_vars = array_intersect_key($db_access_vars, ["username" => 1, "password" => 1]);
							if (!isset($_SESSION["have_database_access"][$db_access_postvar_names["host"]]))
							{
								try {
									$_SESSION["have_database_access"][$db_access_postvar_names["host"]] = Config::dbInfo("host");
								} catch (Exception $e) { }
							}
							break;
					}

					$return->yield->body = database_details_template($instruction, $db_access_vars, $shared_database_info);
					if (!empty($_SESSION["have_database_access"][$db_access_postvar_names["username"]]) && !empty($_SESSION["have_database_access"][$db_access_postvar_names["password"]]))
					{
						Config::loadTemporaryDBSettings([
							"username" => $_SESSION["have_database_access"][$db_access_postvar_names["username"]],
							"password" => $_SESSION["have_database_access"][$db_access_postvar_names["password"]]
						]);
						if (!empty($_SESSION["have_database_access"][$db_access_postvar_names["host"]]))
						{
							Config::loadTemporaryDBSettings([
								"host" => $_SESSION["have_database_access"][$db_access_postvar_names["host"]],
							]);
						}
					}

					try
					{
						/**
						 * If it's in SESSION, it's just been loaded, otherwise
						 * this should try to get it from the conf file (which
						 * will throw an error if it can't find the file and
						 * values haven't been loaded)
						 */
						Config::dbInfo("username");
					}
					catch (Exception $e)
					{
						switch ($e->getCode()) {
							case Config::ERR_FILE_NOT_READABLE:
							case Config::ERR_VARIABLES_MISSING:
								// Config file not yet set up
								// Figure out which settings are missing
								$missing_vars = [];
								foreach ($db_access_vars as $key => $value) {
									if (empty($_SESSION["have_database_access"][$value["name"]]))
									{
										$missing_vars[] = $value["title"];
									}
								}
								if (is_array($missing_vars) && count($missing_vars) > 0)
								{
									if (isset($_SESSION["have_database_access"]["variables_missing"]) && $_SESSION["have_database_access"]["variables_missing"])
									{
										$_SESSION["have_database_access"]["variables_missing"] = true;
										$return->yield->messages[] = _("To access your database, please fill in all the required fields.");
										$return->yield->messages[] = _("You are missing: ") . join($missing_vars, ", ");
									}
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
					$list_of_dbnames = [];
					if ($version == Installer::VERSION_STRING_INSTALL)
					{
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
							$list_of_dbnames[] = $dbname;
						}
					}
					else if ($version == Installer::VERSION_STRING_MODIFY)
					{
						var_dump("epic fail - we're not ready for this...");
						exit(999);
					}
					else
					{
						// UPGRADE
						foreach ($shared_module_info["modules_to_use"]["useModule"] as $uid => $use_module) {
							if ($use_module && !empty($shared_module_info[$uid]["database_name"]))
							{
								$list_of_dbnames[] = $shared_module_info[$uid]["database_name"];
							}
						}
					}

					try
					{
						$temporary_test_table_name = "temp_test";
						foreach ($list_of_dbnames as $db_name_to_use)
						{
							$dbconnection->selectDB($db_name_to_use);
							$result = $dbconnection->processQuery("DROP TABLE IF EXISTS `$temporary_test_table_name`;");
							$result = $dbconnection->processQuery("CREATE TABLE `$temporary_test_table_name` (id int);");
							$result = $dbconnection->processQuery("INSERT INTO `$temporary_test_table_name` VALUES (0);");
							$result = $dbconnection->processQuery("DROP TABLE IF EXISTS `$temporary_test_table_name`;");
						}
					}
					catch (Exception $e)
					{
						if (!empty($_SESSION["have_database_access"]["tried_createdelete_already"]))
						{
							$return->yield->messages[] = _("We were unable to create/delete a table. Please provide credentials for a user with privileges to create and delete tables.");
						}
						$_SESSION["have_database_access"]["tried_createdelete_already"] = true;
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
