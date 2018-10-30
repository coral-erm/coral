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

class ResourcePayment extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}

	//returns array of ResourcePayment objects
	public function getPaymentAmountChangeFromPreviousYear(){
		$id = $this->resourceID;
		$year = $this->year;
		$currency = $this->currencyCode;
		if ((isset($year)) && ($year != '')){
			$sql = "SELECT SUM(paymentAmount) AS total FROM ResourcePayment WHERE resourceID = '%s' AND year = '%s' AND currencyCode = '%s'";
			$currency = $this->db->escapeString($currency);
			$query = sprintf($sql, $id, $this->db->escapeString(previous_year($year)), $currency);
			$result = $this->db->processQuery($query, 'assoc');
			if ((isset($result['total'])) && ($result['total'] > 0)){
				$prev_total = $result['total'];
				$query = sprintf($sql, $id, $this->db->escapeString($year), $currency);
				$result = $this->db->processQuery($query, 'assoc');
				if (isset($result['total'])){
					return sprintf('%+.1f', 100 * ( ($result['total'] - $prev_total) / $prev_total ));
				}
			}
		}
		return 'n/a';
	}

    public function getFormulaValues() {
        $fieldValues = array();
        for ($i = 1; $i <= 10; $i++) {
            $memberName = "field${i}Value";
            if ($this->$memberName)
                $fieldValues['field' . $i . 'Name'] = $this->$memberName;
        }
        return $fieldValues;
    }
}

?>
