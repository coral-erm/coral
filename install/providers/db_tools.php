<?php
function register_db_tools_provider()
{
    return [
        "uid" => "db_tools",
        "translatable_title" => _("Database Tools"),
        "hide_from_completion_list" => true,
        "bundle" => function($version = 0) {
            return [
                "dependencies_array" => ["have_database_access"],
                "function" => function($shared_module_info) {
                    $return = new stdClass();
                    $return->success = true;

                    $processSql = function($db, $sql_file) {
                        $ret = [
                            "success" => true,
                            "messages" => []
                        ];

                        if (!file_exists($sql_file))
                        {
                            $ret["messages"][] = sprintf(_("Could not open sql file: %s.<br />If this file does not exist you must download new install files."), $sql_file);
                            $ret["success"] = false;
                        }
                        else
                        {
                          // Run the file - checking for errors at each SQL execution
                            $f = fopen($sql_file,"r");
                            $sqlFile = fread($f,filesize($sql_file));
                            //$sqlFile = mysqli_real_escape_string($db->getDatabase(), $sqlFile);

                            if(mysqli_multi_query($db->getDatabase(), $sqlFile)){
                              do {
                                if ($result = mysqli_store_result($db->getDatabase())) {
                                  mysqli_free_result($result);
                                }

                                if(mysqli_errno($db->getDatabase())) {
                                        // report error
                                        $ret["messages"][] = mysqli_error($db->getDatabase()) .  "<br />For SQL file: " . $sql_file;
                                        $ret["success"] = false;
                                }

                              } while (mysqli_more_results($db->getDatabase()) && mysqli_next_result($db->getDatabase()));

                                if(mysqli_errno($db->getDatabase())) {
                                        // report error
                                        $ret["messages"][] = mysqli_error($db->getDatabase()) .  "<br />For SQL file: " . $sql_file;
                                        $ret["success"] = false;
                                }
                            }else{
                              $ret["messages"][] = mysqli_error($db->getDatabase()) . "<br /> Occurred while trying to run mysqli_multi_query for SQL file: " . $sql_file;
                                  $ret["success"] = false;
                            }
                        }
                        return $ret;
                    };
                    $shared_module_info["setSharedModuleInfo"](
                        "provided",
                        "process_sql_files",
                        function ($db, $version, $muid) use ($processSql)
                        {
                            // If we are supposed to use tables,
                            // we are not supposed to process sql files
                            if (isset($_SESSION["db_tools"]["use_tables"]) && in_array($muid, $_SESSION["db_tools"]["use_tables"]))
                                return [ "success" => true, "messages" => [] ];

                            $sql_dir = "$muid/install/protected";
                            $sql_files_to_process = [];
                            if ($version === Installer::VERSION_STRING_INSTALL) {

                                // If installing, process all dirs by setting an unreasonably large version as the $to_version var
                                $to_version = '100000000.0.0';
                                $from_version = '1.0.0';

                                // If installing, always run the text_create & install.sql first
                                // install.sql should contain all sql prior to version 2.0
                                $sql_files_to_process = [0 => ["$sql_dir/test_create.sql", "$sql_dir/install.sql"]];

                            } else {
                                $to_version = $version;
                                require_once("common/Config.php");
                                try {
                                    $from_version = Config::getInstallationVersion();
                                } catch (Exception $e) {
                                    $from_version = '0.0.0';
                                }
                            }

                            foreach(glob("$sql_dir/*", GLOB_ONLYDIR) as $dir) {
                                $dir_version = str_replace("$sql_dir/",'', $dir);
                                // creating an int to ensure they are sorted correctly
                                $dir_version_as_int = array_sum(explode('.', $dir_version));
                                // The directory name ($dir_version) must be higher than the currently installed version. Not equal to, or it would try and re-run anything for that version.
                                // The directory name ($dir_version) must be lower than or equal to the new version.
                                if (version_compare($dir_version, $from_version) > 0 && version_compare($dir_version, $to_version) <= 0 ){
                                    // Gather all the sql files to process for that versions
                                    $version_sql_files = glob("$dir/*.sql");
                                    // sort them so they run sequentially
                                    asort($version_sql_files);
                                    // Add them to the array of sql files to run
                                    $sql_files_to_process[$dir_version_as_int] = $version_sql_files;
                                }
                            }

                            // sort the array of sql files by version (ensuring they run sequentially), then reduce to a single array
                            ksort($sql_files_to_process);
                            $final_sql_files = [];
                            foreach($sql_files_to_process as $k => $v) {
                                $final_sql_files = array_merge($final_sql_files,$v);
                            }

                            foreach ($final_sql_files as $sql_file)
                            {
                                if (isset($_SESSION["db_tools"]["sql_files"][$muid][$sql_file]) && $_SESSION["db_tools"]["sql_files"][$muid][$sql_file])
                                {
                                    // skipping this file because session says we've already processed it
                                    continue;
                                }

                                $result = $processSql($db, $sql_file);
                                if (!$result["success"])
                                    return [ "success" => false, "messages" => $result["messages"] ];
                                else
                                    $_SESSION["db_tools"]["sql_files"][$muid][$sql_file] = true;
                            }
                            return [ "success" => true ];
                        }
                    );
                    $shared_module_info["setSharedModuleInfo"](
                        "provided",
                        "check_db",
                        function($muid, $db, $module_shared, $column_denoting_existence, $module_title) use ($shared_module_info) {
                            $return = new stdClass();
                            $return->yield = new stdClass();
                            $return->yield->messages = [];
                            $return->yield->title = sprintf(_("DB Check for %s"), $module_title);

                            if ($module_shared["db_feedback"] == DBAccess::DB_CREATED)
                                return false;

                            $databaseFreshReturnFalse = function() use ($muid, $shared_module_info) {
                                $_SESSION["have_database_access"]["db_" . $muid . "_feedback"] = DBAccess::DB_CREATED;
                                $shared_module_info["setSharedModuleInfo"]($muid, "db_feedback", DBAccess::DB_CREATED);
                                return false;
                            };
                            $return->yield->title = sprintf(_("Database Already Exists For: %s"), $module_title);

                            $check_db_namespace = "db_tools_check_db_" . $muid;
                            $option_buttons = [
                                [ "name" => "use_tables",			"title" => _("Use Existing Tables")		],
                                [ "name" => "drop_tables",			"title" => _("Delete Existing Tables")	],
                                [ "name" => "check_again",			"title" => _("Check Again")				]
                            ];
                            $are_you_sure_buttons = [
                                [ "name" => "i_am_sure",			"title" => _("I understand, start destroying my data")	],
                                [ "name" => "no_thanks",			"title" => _("On second thoughts, what else can I do?")	]
                            ];

                            if (isset($_POST[$check_db_namespace . "_option_button"]) && $_POST[$check_db_namespace . "_option_button"] == "no_thanks")
                            {
                                // Just needs to get into the try below and not do these other if things
                            }
                            else if (isset($_POST[$check_db_namespace . "_option_button"]) && $_POST[$check_db_namespace . "_option_button"] == "i_am_sure")
                            {
                                try
                                {
                                    $db->processQuery('SET foreign_key_checks = 0');
                                    $result = $db->processQuery("SHOW TABLES");
                                    if ($result)
                                    {
                                        while($row = $result->fetchRowPersist())
                                        {
                                            $db->processQuery("DROP TABLE IF EXISTS " . $row[0]);
                                        }
                                    }
                                    // Fake that we have created it because we have just emptied it which comes to the same thing
                                    return $databaseFreshReturnFalse();
                                }
                                catch (Exception $e)
                                {
                                    $return->yield->messages[] = sprintf(_("We tried to delete the tables from %s but something went wrong. Maybe your user doesn't have the necessary rights?"), $module_shared['db_name']);
                                    $return->yield->messages[] = "<b>Here is the error we received:</b><br /><pre>" . var_export($e, 1) . "</pre>";
                                    return $return;
                                }
                            }
                            else if (isset($_POST[$check_db_namespace . "_option_button"]) && $module_shared["db_feedback"] == DBAccess::DB_ALREADY_EXISTED)
                            {
                                switch ($_POST[$check_db_namespace . "_option_button"])
                                {
                                    case "use_tables":
                                        if (!isset($_SESSION["db_tools"]["use_tables"]))
                                            $_SESSION["db_tools"]["use_tables"] = [];
                                        if (!in_array($muid, $_SESSION["db_tools"]["use_tables"]))
                                            $_SESSION["db_tools"]["use_tables"][] = $muid;
                                        return false;

                                    case "drop_tables":
                                        $return->success = false;
                                        require_once "install/templates/option_buttons_template.php";
                                        $return->yield->messages[] = sprintf(_("Are you sure you want to delete your %s tables.<br /><b>This action CANNOT BE UNDONE and it WILL DESTROY DATA.</b>"), $module_title);
                                        $return->yield->body = option_buttons_template("", $are_you_sure_buttons, $check_db_namespace);
                                        return $return;

                                    case "check_again":
                                        break;
                                }
                            }

                            if (isset($_SESSION["db_tools"]["use_tables"]) && in_array($muid, $_SESSION["db_tools"]["use_tables"]))
                                return false;

                            try
                            {
                                $query = "SELECT count(*) count FROM `information_schema`.`TABLES` WHERE `table_schema`=\"{$module_shared['db_name']}\" AND `table_name`=\"$column_denoting_existence\"";
                                $result = $db->processQuery($query);
                                if ($result->numRows() > 0)
                                {
                                    // SOLUTION: we're going to ask if the user meant to do an update and then redirect or just use the existing db.
                                    $return->success = false;
                                    $instruction = sprintf(_('The tables for %s already exist. If you would like to perform a fresh install you will need to delete all of the tables in this schema first. Alternatively, if your tables are prepopulated, you can continue the install and we will assume that they are set up correctly.'), $module_title);
                                    require_once "install/templates/option_buttons_template.php";
                                    $return->yield->body = option_buttons_template($instruction, $option_buttons, $check_db_namespace);
                                    return $return;
                                }
                                else
                                {
                                    // Fake that we have created it because it's empty (so it comes to the same thing)
                                    return $databaseFreshReturnFalse();
                                }
                            }
                            catch (Exception $e)
                            {
                                // There are probably exceptions that I have not thought of here ...
                                $return->success = false;
                                $return->yield->messages[] = _("Please verify your database user has access to select from the information_schema MySQL metadata database.");
                                require_once "install/templates/try_again_template.php";
                                $return->yield->body = try_again_template();
                                return $return;
                            }
                        }
                    );
                    $shared_module_info["setSharedModuleInfo"](
                        "provided",
                        "set_up_admin_in_db",
                        function($db, $admin_login) {
                            // $db is connected to the right db already
                            //delete admin user if they exist, then set them back up with correct username
                            $query = "SELECT privilegeID FROM Privilege WHERE shortName like '%admin%';";
                            //we've just inserted this and there was no error - we assume selection will succeed.
                            $result = $db->processQuery($query);
                            $privilegeID = $result->fetchRow()[0];
                            $query = "DELETE FROM User WHERE loginID = '$admin_login';";
                            $db->processQuery($query);
                            $query = "INSERT INTO User (loginID, privilegeID) values ('$admin_login', $privilegeID);";
                            $db->processQuery($query);
                        }
                    );
                    return $return;
                }
            ];
        }
    ];
}
