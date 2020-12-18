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


class DynamicObject extends Base_Object {

	protected $properties = array();

	public function valueForKey($key) {
		if (property_exists($this, $key)) {
			return parent::valueForKey($key);
		} else {
			if (array_key_exists($key, $this->properties)) {
				return $this->properties[$key];
			} else {
				return NULL;
			}
		}
	}

	public function setValueForKey($key, $value) {
		if (property_exists($this, $key)) {
			parent::setValueForKey($key, $value);
		} else {
			$this->properties[$key] = $value;
		}
	}

}

?>
