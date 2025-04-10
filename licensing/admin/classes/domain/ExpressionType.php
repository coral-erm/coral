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


class ExpressionType extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}


	//returns array
	public function getComparisonList($qualifierID){

		if ($this->shortName == "Interlibrary Loan"){
			$whereAdd = " AND ET.shortName in ('Interlibrary Loan','Interlibrary Loan (additional notes)')";
		}else{
			$whereAdd = " AND E.expressionTypeID = " . $this->expressionTypeID;
		}

		if ($qualifierID){
			$whereAdd .= " AND E.expressionID IN (SELECT expressionID FROM ExpressionQualifierProfile WHERE  qualifierID = '" . $qualifierID . "')";
		}

		$query = ("SELECT E.expressionID, licenseID, D.shortName document, documentText, simplifiedText, documentURL, noteType, GROUP_CONCAT(DISTINCT Q.shortName ORDER BY Q.shortName SEPARATOR ', ') qualifiers
								FROM Document D, ExpressionType ET, Expression E
									LEFT JOIN ExpressionQualifierProfile EQP ON EQP.expressionID = E.expressionID
									LEFT JOIN Qualifier Q ON EQP.qualifierID = Q.qualifierID
								WHERE D.documentID = E.documentID
								AND E.expressionTypeID = ET.expressionTypeID
								AND (D.expirationDate is null OR D.expirationDate = '0000-00-00')
								" . $whereAdd . "
								GROUP BY E.expressionID, licenseID, D.shortName, documentText, simplifiedText, documentURL, noteType
								ORDER BY D.shortName;");


		$result = $this->db->processQuery($query, 'assoc');

		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['expressionID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($searchArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($searchArray, $resultArray);
			}
		}

		return $searchArray;
	}



	//returns array
	public function getTermsReport(){

		$query = ("SELECT E.expressionID, licenseID, D.shortName document, documentText, simplifiedText, documentURL, noteType
								FROM Expression E, Document D, ExpressionType ET
								WHERE D.documentID = E.documentID
								AND E.expressionTypeID = ET.expressionTypeID
								AND (D.expirationDate is null OR D.expirationDate = '0000-00-00')
								AND E.expressionTypeID = " . $this->expressionTypeID . "
								AND productionUseInd=1
								ORDER BY D.shortName;");

		$result = $this->db->processQuery($query, 'assoc');

		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['expressionID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($searchArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($searchArray, $resultArray);
			}
		}

		return $searchArray;
	}


	//returns number of children for this particular expression type
	public function getNumberOfChildren(){

		$query = "SELECT count(*) childCount FROM Expression WHERE expressionTypeID = '" . $this->expressionTypeID . "';";

		$result = $this->db->processQuery($query, 'assoc');

		return $result['childCount'];

	}


	//returns array of Document objects
	public function getQualifiers(){

		$query = "SELECT Q.*
						FROM Qualifier Q
						WHERE expressionTypeID = '" . $this->expressionTypeID . "'
						ORDER BY shortName asc;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['qualifierID'])){
			$object = new Qualifier(new NamedArguments(array('primaryKey' => $result['qualifierID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Qualifier(new NamedArguments(array('primaryKey' => $row['qualifierID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}






	//removes this expression type and children qualifiers
	public function removeExpressionType(){

		//delete all associated qualifiers
		foreach ($this->getQualifiers() as $qualifier) {
			$qualifier->delete();
		}

		$this->delete();
	}






    public function reorderTargets($targetsArray){
        $topTargetArray = array();
        $bottomTargetArray = array();
        $reorderedArray = array();

        $targetArray = array();
        foreach ($targetsArray as $i => $targetArray){
            if (is_array($this->getExpressionsByResource($targetArray['public_name'])) && count($this->getExpressionsByResource($targetArray['public_name'])) > 0) {
                array_push($topTargetArray, $targetArray);
            }else{
                array_push($bottomTargetArray, $targetArray);
            }
        }

        $targetArray = array();
        foreach ($topTargetArray as $targetArray){
            array_push($reorderedArray, $targetArray);
        }


        foreach ($bottomTargetArray as $targetArray){
            array_push($reorderedArray, $targetArray);
        }

        return $reorderedArray;

    }





	//returns array of expression objects
    public function getExpressionsByResource($resourceName){

        $query = ("SELECT E.expressionID
					FROM Document D, SFXProvider SP, Expression E
					WHERE D.documentID = E.documentID
					AND (D.expirationDate is null || D.expirationDate = '0000-00-00')
					AND SP.documentID = D.documentID
					AND E.productionUseInd='1'
					AND E.expressionTypeID = '" . $this->expressionTypeID . "'
					AND SP.shortName = '" . $resourceName . "';");


        $result = $this->db->processQuery($query, 'assoc');

        $objects = array();

        //need to do this since it could be that there's only one request and this is how the dbservice returns result
        if (isset($result['expressionID'])){
            $object = new Expression(new NamedArguments(array('primaryKey' => $result['expressionID'])));
            array_push($objects, $object);
        }else{
            foreach ($result as $row) {
                $object = new Expression(new NamedArguments(array('primaryKey' => $row['expressionID'])));
                array_push($objects, $object);
            }
        }

        return $objects;
    }






    //returns array of expression type ids
    public function getExpressionTypesByResource($resourceName){

        $query = ("SELECT distinct E.expressionTypeID
					FROM Document D, SFXProvider SP, Expression E, ExpressionType ET
					WHERE D.documentID = E.documentID
					AND (D.expirationDate is null || D.expirationDate = '0000-00-00')
					AND SP.documentID = D.documentID
					AND E.productionUseInd='1'
					AND ET.expressionTypeID = E.expressionTypeID
					AND SP.shortName = '" . $resourceName . "'
					ORDER BY ET.shortName;");


        $result = $this->db->processQuery($query, 'assoc');

        $expressionTypeArray = array();

        //need to do this since it could be that there's only one result and this is how the dbservice returns result
        if (isset($result['expressionTypeID'])){
            $expressionTypeArray[] = $result['expressionTypeID'];
        }else{
            foreach ($result as $row) {
                $expressionTypeArray[] = $row['expressionTypeID'];
            }
        }

        return $expressionTypeArray;
    }


}

?>
