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


class Document extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}

    public function asArray() {
        $aarray = array();
		foreach (array_keys($this->attributeNames) as $attributeName) {
			if ($this->$attributeName != null) {
				$aarray[$attributeName] = $this->$attributeName;
			}
		}
        $documentType = new DocumentType(new NamedArguments(array("primaryKey" => $this->documentTypeID)));
        $aarray['documentType'] = $documentType->shortName;
        return $aarray;
    }


	//returns 1 or 0 indicating if this particular document has children agreements
	public function getNumberOfChildren(){

		$query = "SELECT count(*) childCount FROM Document D, DocumentType DT WHERE D.documentTypeID = DT.documentTypeID AND parentDocumentID = '" . $this->documentID . "';";

		$result = $this->db->processQuery($query, 'assoc');

		return $result['childCount'];

	}


	//returns array of Document objects - used by document display on license record
	public function getChildrenDocuments($orderBy){
		//also gets children of children

		$query = "SELECT D.* FROM DocumentType DT, Document D LEFT JOIN Signature S ON (S.documentID = D.documentID)
								WHERE D.documentTypeID = DT.documentTypeID
								AND (parentDocumentID = '" . $this->documentID . "' OR parentDocumentID in (select documentID from Document where parentDocumentID = '" . $this->documentID . "'))
								GROUP BY D.documentID
								ORDER BY " . $orderBy . ";";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['documentID'])){
			$object = new Document(new NamedArguments(array('primaryKey' => $result['documentID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Document(new NamedArguments(array('primaryKey' => $row['documentID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}
        
        public function setDocExpirationDate($docIDs,$expirationDate){
            
            $query="update Document set expirationDate='".$expirationDate."' where documentID in (".$docIDs.");";
            $this->db->processQuery($query, 'assoc');
            //echo $query;
        }

        public function getChildrenDocumentIDs($parentID){ //Added By FondrenLibrary
            //echo($parentID);
            $query = "SELECT licenseID,documentID,parentDocumentID FROM Document WHERE parentDocumentID = ".$parentID;
            $result = $this->db->processQuery($query, 'assoc');
            $childIDs=$parentID.",";
            if (isset($result['documentID'])){//be sure to incude this, otherwise, we will see weired result
               $childIDs.=$this->getChildrenDocumentIDs($result['documentID']);
            }else{
              foreach($result as $row ){
               $childIDs.=$this->getChildrenDocumentIDs($row['documentID']);
              }
            }
          return $childIDs;
          
        }
        
        

	//returns array of signatures for license display page
	public function getSignaturesForDisplay(){

		$query = "SELECT S.signatureID, IF(signatureDate = '0000-00-00','', signatureDate) signatureDate, signerName, ST.shortName signatureTypeName, S.signatureTypeID
										FROM Signature S, SignatureType ST
										WHERE S.signatureTypeID = ST.signatureTypeID
										AND documentID = '" . $this->documentID . "'
										ORDER BY signatureDate desc;";

		$result = $this->db->processQuery($query, 'assoc');

		$signatureArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['signatureID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($signatureArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($signatureArray, $resultArray);
			}
		}

		return $signatureArray;

	}



	//returns array of Expression objects
	public function getExpressions(){

		$query = "SELECT * FROM Expression where documentID = '" . $this->documentID . "' ORDER BY expressionID";

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



	//returns array of Signature objects
	public function getSignatures(){

		$query = "SELECT * FROM Signature where documentID = '" . $this->documentID . "' ORDER BY signatureID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['signatureID'])){

			$object = new Signature(new NamedArguments(array('primaryKey' => $result['signatureID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Signature(new NamedArguments(array('primaryKey' => $row['signatureID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}



	//returns array of SFXProvider objects
	public function getSFXProviders(){

		$query = "SELECT * FROM	SFXProvider	WHERE documentID = '" . $this->documentID . "' ORDER BY shortName asc;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['sfxProviderID'])){
			$object = new SFXProvider(new NamedArguments(array('primaryKey' => $result['sfxProviderID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new SFXProvider(new NamedArguments(array('primaryKey' => $row['sfxProviderID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//returns array of Document objects
	public function getDocumentsForExpressionDisplay(){

		$query="SELECT distinct D.documentID, D.shortName documentName
								FROM Document D, Expression E
								WHERE E.documentID = D.documentID
								AND D.documentID = '" . $this->documentID . "'
								ORDER BY D.documentID;";


		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['documentID'])){
			$object = new Document(new NamedArguments(array('primaryKey' => $result['documentID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Document(new NamedArguments(array('primaryKey' => $row['documentID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//returns array of Expressions
	public function getExpressionsForDisplay(){

		$query = "SELECT E.expressionID, documentText, simplifiedText, productionUseInd, ET.shortName expressionTypeName, ET.noteType noteType
						FROM ExpressionType ET, Expression E
						WHERE ET.expressionTypeID = E.expressionTypeID
						AND documentID = '" . $this->documentID . "'
						ORDER BY ET.shortName;";

		$result = $this->db->processQuery($query, 'assoc');

		$expressionArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		$this->db->processQuery($query, 'assoc');if (isset($result['expressionID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($expressionArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($expressionArray, $resultArray);
			}
		}

		return $expressionArray;
	}

 //Added by Mang to get document notes.
	public function getNotes() {

		
		$query = "SELECT DN.*
		        		FROM DocumentNote DN
                                        LEFT JOIN NoteType NT ON NT.noteTypeID = DN.noteTypeID
					WHERE documentID = '" . $this->documentID . "'
					ORDER BY updateDate desc, NT.shortName";
		

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['documentNoteID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new DocumentNote(new NamedArguments(array('primaryKey' => $row['documentNoteID'])));
			array_push($objects, $object);
		}

		return $objects;
	}        


}

?>
