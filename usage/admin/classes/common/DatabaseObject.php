<?php
/*
**************************************************************************************************************************
** CORAL Usage Statistics Module
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


class DatabaseObject extends DynamicObject {

	protected $db;

	protected $tableName;
	protected $collectiveName;

	protected $primaryKeyName;
	protected $primaryKey;

	public $attributeNames = array();
	protected $attributes = array();


	protected function init(NamedArguments $arguments) {
		$arguments->setDefaultValueForArgumentName('tableName', get_class($this));
		$this->tableName = $arguments->tableName;

		$defaultCollectiveName = lcfirst($arguments->tableName) . 's';
		$arguments->setDefaultValueForArgumentName('collectiveName', $defaultCollectiveName);
		$this->collectiveName = $arguments->collectiveName;

		$defaultPrimaryKeyName = lcfirst($arguments->tableName) . 'Id';
		$arguments->setDefaultValueForArgumentName('primaryKeyName', $defaultPrimaryKeyName);
		$this->primaryKeyName = $arguments->primaryKeyName;

		$this->primaryKey = $arguments->primaryKey;

        $arguments->setDefaultValueForArgumentName('db',false);
		$this->db = $arguments->db ? $arguments->db : DBService::getInstance();

		$this->defineAttributes();
		$this->overridePrimaryKeyName();
		$this->load();


	}

	protected function defineRelationships() {}
	protected function overridePrimaryKeyName() {}


	protected function defineAttributes() {
		// Figure out attributes from existing database
		$query = "SELECT COLUMN_NAME, IS_NULLABLE FROM information_schema.`COLUMNS` WHERE table_schema = '";
		$query .= $this->db->config->database->name . "' AND table_name = '$this->tableName'";// MySQL-specific
		foreach ($this->db->processQuery($query) as $result) {
			$attributeName = $result[0];
			$isNullable = $result[1];
			if ($attributeName != $this->primaryKeyName) {
				$this->addAttribute($attributeName, $isNullable);
			}
		}
	}

	protected function addAttribute($attributeName, $isNullable = NULL) {
		$this->attributeNames[$attributeName] = $isNullable;
	}


	public function valueForKey($key) {
		if (array_key_exists($key, $this->attributeNames)) {
			if (!array_key_exists($key, $this->attributes)) {
				$query = "SELECT `$key` FROM `$this->tableName` WHERE `$this->primaryKeyName` = '$this->primaryKey' LIMIT 1";
				$result = $this->db->processQuery($query);
				if(isset($result[0])){
					$this->attributes[$key] = stripslashes($result[0]);
				}else{
					$this->attributes[$key] = '';
				}
			}
			return $this->attributes[$key];
		} else {
			return parent::valueForKey($key);
		}

	}



	public function setValueForKey($key, $value) {

		if (array_key_exists($key, $this->attributeNames)) {
			$this->attributes[$key] = $value;
		} else {
			parent::setValueForKey($key, $value);
		}

	}



	public function delete() {
		$query = "DELETE FROM `$this->tableName` WHERE  `$this->primaryKeyName` = '$this->primaryKey'";
		return $this->db->processQuery($query);
	}

	public function save() {
		$pairs = array();

		foreach ($this->attributeNames as $attributeName => $isNullable) {

			$value = $this->attributes[$attributeName];
			if (($value == '' || !isset($value) && (strtoupper($isNullable) == "YES")) || strtoupper($value) == 'NULL'){
				$value = "NULL";
			} else {
				$value = addslashes($value);
				$value = "'$value'";
			}

			$pair = "`$attributeName`=$value";
			array_push($pairs, $pair);
		}
		$set = implode(', ', $pairs);
		if (isset($this->primaryKey)) {
			// Update object
			$query = "UPDATE `$this->tableName` SET $set WHERE `$this->primaryKeyName` = '$this->primaryKey'";
			$this->db->processQuery($query);
		} else {
			// Insert object
			$query = "INSERT INTO `$this->tableName` SET $set";
			$this->primaryKey = $this->db->processQuery($query);
		}
	}


	public function all() {
		$query = "SELECT * FROM `$this->tableName` ORDER BY 2, 1";
		$result = $this->db->processQuery($query);
		$objects = array();
		foreach ($result as $row) {
			$className = get_class($this);
			$object = new $className(new NamedArguments(array('primaryKey' => $row[0])));
			array_push($objects, $object);
		}

		return $objects;
	}


	public function allAsArray() {
		$query = "SELECT * FROM `$this->tableName` ORDER BY 2, 1";
		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();
		$rowArray = array();

		if (isset($result[lcfirst($this->tableName) . 'ID'])){
			foreach (array_keys($result) as $attributeName) {
				$rowArray[$attributeName] = $result[$attributeName];
			}
			array_push($resultArray, $rowArray);
		}else{
			foreach ($result as $row) {
				$rowArray[$this->primaryKeyName] = $row[lcfirst($this->tableName) . 'ID'];

				foreach (array_keys($this->attributeNames) as $attributeName) {
					$rowArray[$attributeName] = $row[$attributeName];
				}
				array_push($resultArray, $rowArray);
			}
		}

		return $resultArray;
	}


	public function load() {

		//if exists in the database
		if (isset($this->primaryKey)) {
			$query = "SELECT * FROM `$this->tableName` WHERE `$this->primaryKeyName` = ?";
			$result = $this->db->processPreparedQuery($query, "assoc",
													  "s",
													  $this->primaryKey);

			foreach (array_keys($result) as $attributeName) {
				$this->addAttribute($attributeName);
				$this->attributes[$attributeName] = $result[$attributeName];
			}

		}
	}


	public function getDatabase() {
		return $this->db->getDatabase();
	}
}



?>
