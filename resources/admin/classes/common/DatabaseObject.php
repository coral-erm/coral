<?php
/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
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

	protected $childNames = array();
	protected $children = array();

	protected $parentNames = array();
	protected $parents = array();

	protected $peerNames = array();
	protected $peers = array();

	protected function init(NamedArguments $arguments) {
		$arguments->setDefaultValueForArgumentName('tableName', get_class($this));
		$this->tableName = $arguments->tableName;

		$defaultCollectiveName = lcfirst($arguments->tableName) . 's';
		$arguments->setDefaultValueForArgumentName('collectiveName', $defaultCollectiveName);
		$this->collectiveName = $arguments->collectiveName;

		$defaultPrimaryKeyName = lcfirst($arguments->tableName) . 'ID';
		$arguments->setDefaultValueForArgumentName('primaryKeyName', $defaultPrimaryKeyName);
		$this->primaryKeyName = $arguments->primaryKeyName;

		$this->primaryKey = $arguments->primaryKey;
		$this->db = new DBService;

		$arguments->setDefaultValueForArgumentName('db',false);
		$this->db = $arguments->db ? $arguments->db : new DBService;

		$this->defineRelationships();
		//$this->defineAttributes();  //now performed in load
		$this->overridePrimaryKeyName();
		$this->load();


	}

	protected function defineRelationships() {}
	protected function overridePrimaryKeyName() {}


	protected function defineAttributes() {
		// Figure out attributes from existing database
		$query = "SELECT COLUMN_NAME FROM information_schema.`COLUMNS` WHERE table_schema = '";
		$query .= $this->db->config->database->name . "' AND table_name = '$this->tableName'";// MySQL-specific
		foreach ($this->db->processQuery($query) as $result) {
			$attributeName = $result[0];
			if ($attributeName != $this->primaryKeyName) {
				$this->addAttribute($attributeName);
			}
		}
	}

	protected function addAttribute($attributeName, $attributeType = NULL) {
		$this->attributeNames[$attributeName] = $attributeType;
	}

	protected function hasMany($relatedClassName) {
		$exampleRelatedObject = new $relatedClassName;
		$nameArrayName = 'childNames';
		// Check for many-to-many relationship.
		if (array_key_exists($this->collectiveName, $exampleRelatedObject->childNames)) {
			$nameArrayName = 'peerNames';
		}
		$this->$nameArrayName[$exampleRelatedObject->collectiveName] = $relatedClassName;
	}

	protected function hasOne($parentClassName, $parentName = NULL) {
		if (!isset($parentName)) {
			$parentName = lcfirst($parentClassName);
		}
		$this->parentNames[$parentName] = $parentClassName;
	}

	public function getPrimaryKeyName() {
		return $this->primaryKeyName;
	}

	public function valueForKey($key) {
		if (array_key_exists($key, $this->attributeNames)) {
			if (!array_key_exists($key, $this->attributes)) {
				$query = "SELECT `$key` FROM `$this->tableName` WHERE `$this->primaryKeyName` = '$this->primaryKey' LIMIT 1";
				$result = $this->db->processQuery($query);
				if (isset($result[0])) $this->attributes[$key] = stripslashes($result[0]);
			}
			return $this->attributes[$key];
		} else if (array_key_exists($key, $this->parentNames)) {
			if (!array_key_exists($key, $this->parents)) {
				$parentClassName = $this->parentNames[$key];
				$exampleParent = new $parentClassName;
				$parentPrimaryKey = $this->valueForKey($exampleParent->primaryKeyName);
				$this->parents[$key] = new $parentClassName(new NamedArguments(array('primaryKey' => $parentPrimaryKey)));
			}
			return $this->parents[$key];
		} else if (array_key_exists($key, $this->childNames)) {
			if (!array_key_exists($key, $this->children)) {
				$this->children[$key] = array();
				$childClassName = $this->childNames[$key];
				$exampleChild = new $childClassName;
				$whereClause = "`$this->primaryKeyName`='$this->primaryKey'";
				$query = "SELECT `$exampleChild->primaryKeyName` FROM `$exampleChild->tableName` WHERE $whereClause";
				$results = $this->db->processQuery($query);
				foreach ($results as $result) {
					$id = $result[0];
					$child = new $childClassName(new NamedArguments(array('primaryKey' => $id)));
					array_push($this->children[$key], $child);
				}
			}
			return $this->children[$key];
		} else if (array_key_exists($key, $this->peerNames)) {
			if (!array_key_exists($key, $this->peers)) {
				$this->peers[$key] = array();
				$peerClassName = $this->peerNames[$key];
				$examplePeer = new $peerClassName;
				$whereClause = "`$this->primaryKeyName`='$this->primaryKey'";
				$tableNames = array($this->tableName, $examplePeer->tableName);
				$sortedTableNames = sort($tableNames);
				$joinTableName = $sortedTableNames[0] . 'To' . $sortedTableNames[1];
				$query = "SELECT `$examplePeer->primaryKeyName` FROM `$joinTableName` WHERE $whereClause";
				$results = $this->db->processQuery($query);
				foreach ($results as $result) {
					$id = $result[0];
					$peer = new $peerClassName(new NamedArguments(array('primaryKey' => $id)));
					array_push($this->peers[$key], $peer);
				}
			}
			return $this->peers[$key];
		} else {
			return parent::valueForKey($key);
		}
	}

	public function setValueForKey($key, $value) {
		if (array_key_exists($key, $this->parentNames)) {
			if (is_a($value, 'DatabaseObject')) {
				$key = $value->primaryKeyName;
				$value = $value->primaryKey;
			}
		}
		if (array_key_exists($key, $this->attributeNames)) {
			$this->attributes[$key] = $value;
		} else if (array_key_exists($key, $this->childNames) && is_array($value)) {
			if (!array_key_exists($key, $this->children)) {
				$this->children[$key] = array();
			}
			//Add new children
			foreach ($value as $child) {
				if (is_a($child, $this->childNames[$key]) && is_a($child, 'DatabaseObject')) {
					if (!array_key_exists($child->primaryKey, $this->children[$key])) {
						$this->children[$key][$child->primaryKey] = $child;
					}
				}
			}
			//Remove old children
			foreach (array_keys($this->children[$key]) as $childPrimaryKey) {
				if (!array_key_exists($childPrimaryKey, $value)) {
					unset($this->children[$key][$childPrimaryKey]);
				}
			}
		} else if (array_key_exists($key, $this->peerNames) && is_array($value)) {
			if (!array_key_exists($key, $this->peers)) {
				$this->peers[$key] = array();
			}
			//Add new peers
			foreach ($value as $peer) {
				if (is_a($peer, $this->peerNames[$key]) && is_a($peer, 'DatabaseObject')) {
					if (!array_key_exists($peer->primaryKey, $this->peers[$key])) {
						$this->peers[$key][$peer->primaryKey] = $peer;
					}
				}
			}
			//Remove old peers
			foreach (array_keys($this->peers[$key]) as $peerPrimaryKey) {
				if (!array_key_exists($peerPrimaryKey, $value)) {
					unset($this->peers[$key][$peerPrimaryKey]);
				}
			}
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
		foreach (array_keys($this->attributeNames) as $attributeName) {
			$value = $this->attributes[$attributeName];
			if ($value == '' || !isset($value)) {
				$value = "NULL";
			} else {
				$value = $this->db->escapeString($value);
				$value = "'$value'";
			}
			$pair = "`$attributeName`=$value";
			array_push($pairs, $pair);
		}
		$set = implode(', ', $pairs);
		if (isset($this->primaryKey)) {
			// Update object
			$query = "UPDATE `$this->tableName` SET $set WHERE `$this->primaryKeyName` = '$this->primaryKey'";
			//echo $query;
			$this->db->processQuery($query);
		} else {
			// Insert object
			$query = "INSERT INTO `$this->tableName` SET $set";
			//echo $query;
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
		}else{
			// Figure out attributes from existing database
			$query = "SELECT COLUMN_NAME FROM information_schema.`COLUMNS` WHERE table_schema = '";
			$query .= $this->db->config->database->name . "' AND table_name = '$this->tableName'";// MySQL-specific
			foreach ($this->db->processQuery($query) as $result) {
				$attributeName = $result[0];
				$this->addAttribute($attributeName);
			}

		}
	}

}

?>