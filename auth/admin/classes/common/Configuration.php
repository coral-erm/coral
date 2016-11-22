<?php
/*
**************************************************************************************************************************
** CORAL Usage Statistics Reporting Module v. 1.0
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/


class Configuration extends DynamicObject {

	public function init(NamedArguments $arguments) {
		$global_config = parse_ini_file(BASE_DIR . "../admin/configuration.ini", true);
		$arguments->setDefaultValueForArgumentName("filename", BASE_DIR . "/admin/configuration.ini");
		$module_config = parse_ini_file($arguments->filename, true);
		$config = array_merge_recursive($module_config, $global_config);

		// use other DBs for tests
		if($config["settings"]["environment"] === "test") {
			$this->switchAllDbsToTest($config);
		}

		// Save config array content as Configuration properties
		foreach ($config as $section => $entries) {
			$this->$section = Utility::objectFromArray($entries);
		}
	}


	private function switchAllDbsToTest(&$config) {
		$config["database"]["name"] = "coral_auth_test";
		$config["database"]["username"] = "coral_test";
		$config["database"]["password"] = "coral_test";

		if(isset($config["database"]["usageDatabase"])) {
			$config["database"]["usageDatabase"] = "coral_usage_test";
		}

		if(isset($config["settings"]["authDatabaseName"])) {
			$config["settings"]["authDatabaseName"] = "coral_auth_test";
		}

		if(isset($config["settings"]["licensingDatabaseName"])) {
			$config["settings"]["licensingDatabaseName"] = "coral_licensing_test";
		}

		if(isset($config["settings"]["organizationsDatabaseName"])) {
			$config["settings"]["organizationsDatabaseName"] = "coral_organizations_test";
		}

		if(isset($config["settings"]["resourcesDatabaseName"])) {
			$config["settings"]["resourcesDatabaseName"] = "coral_resources_test";
		}
	}
}

?>