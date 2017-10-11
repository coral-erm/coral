<?php

class Downtime extends DatabaseObject {
	protected $overloadKeys = array();

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}

	protected function init(NamedArguments $arguments) {
		//these are values from other tables that we'll be SELECTing in load(), but don't want to persist as part of DB update operations
		$this->overloadKeys = array("shortName","subjectText");
		parent::init($arguments);
	}

	public function load() {

		//This is a custom load method that joins the downtime type name into the attributes

		//if exists in the database
		if (isset($this->primaryKey)) {
			$query = "SELECT d.*, dt.shortName, i.subjectText
				  FROM Downtime d
				  LEFT JOIN DowntimeType dt ON dt.downtimeTypeID=d.downtimeTypeID
				  LEFT JOIN Issue i ON i.issueID=d.issueID
				  WHERE d.downtimeID='$this->primaryKey'";

			$result = $this->db->processQuery($query, 'assoc');

			foreach (array_keys($result) as $attributeName) {
				$this->addAttribute($attributeName);
				$this->attributes[$attributeName] = $result[$attributeName];
			}

		} else {
			// Figure out attributes from existing database
			$query = "SELECT COLUMN_NAME
					FROM information_schema.`COLUMNS`
					WHERE table_schema = '{$this->db->config->database->name}' AND table_name = '{$this->tableName}'";// MySQL-specific
			foreach ($this->db->processQuery($query) as $result) {
				$this->addAttribute($result[0]);
			}

			//Add additional keys from joined tables
			foreach ($this->overloadKeys as $attributeName) {
				$this->addAttribute($attributeName);
			}
		}
	}

	public function save() {
		//remove any overloadedKeys before attempting to save
		foreach ($this->overloadKeys as $attributeName) {
			unset($this->attributes[$attributeName]);
			unset($this->attributeNames[$attributeName]);
		}
		parent::save();
	}

	public function getDowntimeTypesArray() {
		$query = "SELECT dt.*
				  FROM DowntimeType dt";

		$result = $this->db->processQuery($query, "assoc");
		$names = array();

		if (isset($result[0]) && is_array($result[0])) {
			foreach ($result as $name) {
				array_push($names, $name);
			}
		} else {
			$names[] = $result;
		}

		return $names;
	}

}

?>
