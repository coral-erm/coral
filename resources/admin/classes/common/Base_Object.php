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


class Base_Object {

	public function __construct(NamedArguments $arguments = NULL) {
		if (method_exists($this, 'init')) {
			if (!is_a($arguments, 'NamedArguments')) {
				$arguments = new NamedArguments(array());
			}
			$this->init($arguments);
		}
	}

	// An optional initializer to use instead of |__construct()|.
	protected function init(NamedArguments $arguments) {

	}

	public function __destruct() {
		if (method_exists($this, 'dealloc')) {
			$this->dealloc();
		}
	}

	// An optional method called on deconstruction instead of |__deconstruct()|.
	protected function dealloc() {

	}

	// Setters are functions called |$instance->setPropertyName($value)|.
	public function __set($name, $value) {
		$methodName = 'set' . ucfirst($name);
		$this->$methodName($value);
	}

	// Getters are functions called |$instance->propertyName()|.
	public function __get($name) {
		return $this->$name();
	}

	// Default setter uses declared properties.
	protected function setValueForKey($key, $value) {
		if (property_exists($this, $key)) {
			$this->$key = $value;
		} else {
			throw new Exception(_("Cannot set value for undefined key (").$key.").");
		}
	}

	// Default getter uses declared properties.
	protected function valueForKey($key) {
		if (property_exists($this, $key)) {
			return $this->$key;
		} else {
			throw new Exception(_("Cannot set value for undefined key (").$key.").");
		}
	}

	// Call |valueForKey| and |setValueForKey| as default setter and getter.
	public function __call($name, $arguments) {
		if (preg_match('/^set[A-Z]/', $name)) {
			$key = preg_replace('/^set/', '\1', $name);
			$key = lcfirst($key);
			$this->setValueForKey($key, $arguments[0]);
		} else {
			return $this->valueForKey($name);
		}
	}

}

?>
