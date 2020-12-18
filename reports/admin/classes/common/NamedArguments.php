<?php
/*
**************************************************************************************************************************
** CORAL Licensing Module v. 1.0
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


class NamedArguments {

	protected $arguments = array();

	public function __construct($array) {
		$this->arguments = $array;
	}

	public function __get($name) {
		if (array_key_exists($name, $this->arguments)) {
			return $this->arguments[$name];
		}
	}

	public function __set($name, $value) {
		$this->arguments[$name] = $value;
	}

	public function setDefaultValueForArgumentName($argumentName, $value) {
		if (!array_key_exists($argumentName, $this->arguments)) {
			$this->arguments[$argumentName] = $value;
		}
	}

	public function toJsonString() {
		return json_encode($this->arguments);
	}

	public function namedArgumentsFromJsonString($string) {
		return new NamedAgruments(json_decode($string, true));
	}

}

?>
