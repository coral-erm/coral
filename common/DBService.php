<?php
/*
 * *************************************************************************************************************************
 * * CORAL Common Module v. 1.9
 * *
 * * Copyright (c) 2010 University of Notre Dame
 * *
 * * This file is part of CORAL.
 * *
 * * CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * *
 * * CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * *
 * * You should have received a copy of the GNU General Public License along with CORAL. If not, see <http://www.gnu.org/licenses/>.
 * *
 * *************************************************************************************************************************
 */

require_once "common/Base_Object.php";
require_once "common/Config.php";
class DBService extends Base_Object {
	const ERR_COULD_NOT_SELECT_DATABASE = 10040;
	const ERR_COULD_NOT_CONNECT = 10041;
	const ERR_ACCESS_DENIED = 10042;

	protected static $db = null;
	protected $error;

	public function __construct($databaseName = null)
	{
		if (!self::$db)
		{
			/**
			 *  NOTE: Warnings and errors are suppressed here (using "@new")
			 *        (we do this so that mysqli doesn't spit out warnings if
			 *        the connection fails) we must manually handle errors!
			 */
			// Load this data outside of the mysqli call so that if an error occurs it is thrown.
			$dbInfo = [
				"host" => Config::dbInfo("host"),
				"username" => Config::dbInfo("username"),
				"password" => Config::dbInfo("password")
			];

			self::$db = @new mysqli($dbInfo["host"], $dbInfo["username"], $dbInfo["password"]);
			if (self::$db->connect_errno)
			{
				switch (self::$db->connect_errno) {
					case 2002:
						// Connection failure
						throw new RuntimeException("Could not connect to server.", self::ERR_COULD_NOT_CONNECT);
						break;
					case 1045:
						throw new RuntimeException("Access denied.", self::ERR_ACCESS_DENIED);
						break;
					default:
						// Unknown error
						echo "Sorry, an error we have not accounted for has occurred .<br />\n";
						throw new RuntimeException("There was a problem with the database: ", self::$db->error);
						break;
				}
			}
		}

		if ($databaseName !== false)
			self::selectDB($databaseName ? $databaseName : Config::dbInfo("name"));
	}

	public function getError()
	{
		return self::$db->error;
	}

	public static function getDatabase()
	{
		return self::$db;
	}
	public static function selectDB($databaseName)
	{
		if (!self::$db->select_db($databaseName))
			throw new RuntimeException("Could not select database '$databaseName': " . self::$db->error, DBService::ERR_COULD_NOT_SELECT_DATABASE);
	}

	public function processQuery($sql, $type = NULL)
	{
		if (strlen(trim("$sql"))===0)
			throw new RuntimeException("Empty DB Query");

		$query_start = microtime(true);
		$result = self::$db->query($sql);
		$query_end = microtime(true);
		//TODO: log out query time (cf. licensing module but use Common Utility)

		if (!$result)
		{
			throw new RuntimeException("There was a problem with the database: " . self::$db->error);
		}
		else if ($result instanceof mysqli_result)
		{
			return new DBResult($result);
		}
		else
		{
			return self::$db->insert_id;
		}
	}

	public static function escapeString($str)
	{
		return self::$db->real_escape_string($str);
	}
}
