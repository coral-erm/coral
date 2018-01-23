<?php
/*
**************************************************************************************************************************
** CORAL Organizations Module v. 1.0
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


class Utility {

	public function secondsFromDays($days) {
		return $days * 24 * 60 * 60;
	}

	public function objectFromArray($array) {
		$object = new DynamicObject;
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$object->$key = Utility::objectFromArray($value);
			} else {
				$object->$key = $value;
			}
		}
		return $object;
	}

	//returns file path up to /coral/
	public function getCORALPath(){
		$pagePath = $_SERVER["DOCUMENT_ROOT"];

		$currentFile = $_SERVER["SCRIPT_NAME"];
		$parts = Explode('/', $currentFile);
		for($i=0; $i<count($parts) - 2; $i++){
			$pagePath .= $parts[$i] . '/';
		}

		return $pagePath;
	}

	//returns file path for this module, i.e. /coral/licensing/
	public function getModulePath(){
	  $replace_path = preg_quote(DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."common");
	  return preg_replace("@$replace_path$@", "", dirname(__FILE__));
	}


	//returns page URL up to /coral/
	public function getCORALURL(){
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
		  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
		} else {
		  $pageURL .= $_SERVER["SERVER_NAME"];
		}

		$currentFile = $_SERVER["PHP_SELF"];
		$parts = Explode('/', $currentFile);
		for($i=0; $i<count($parts) - 2; $i++){
			$pageURL .= $parts[$i] . '/';
		}

		return $pageURL;
	}

	//returns page URL up to /licensing/
	public function getPageURL(){
		return $this->getCORALURL() . "licensing/";
	}

	public function getOrganizationURL(){
		return $this->getCORALURL() . "organizations/orgDetail.php?organizationID=";
	}

	public function getResourceURL(){
		return $this->getCORALURL() . "resources/resource.php?resourceID=";
	}




	//this is a workaround for a bug between autocomplete and thickbox causing a page refresh on the add/edit license form when 'enter' key is hit on the autocomplete provider field
	//this will redirect back to the correct license record
	public function fixLicenseFormEnter($editLicenseID){
		//this was an add
		if ($editLicenseID == ""){
			//need to get the most recent added license since it will have been added but we didn''t get the resonse of the new license ID
			//since this will have happened instantly we can be safe to assume this is the correct record
			$this->db = new DBService;

			$result = $this->db->processQuery("select max(licenseID) max_licenseID from License;", 'assoc');

			if ($result['max_licenseID']){
				header('Location: license.php?licenseID=' . $result['max_licenseID']);
				exit; //PREVENT SECURITY HOLE
			}

		}else{
			header('Location: license.php?licenseID=' . $editLicenseID);
			exit; //PREVENT SECURITY HOLE
		}
	}


	//return true if there is a setting in config to use the terms tool
	//setting could be called either useSFXTermsToolFunctionality or useTermsToolFunctionality
	public function useTermsTool(){
		$config = new Configuration();

		if (($config->settings->useSFXTermsToolFunctionality == "Y") || ($config->settings->useTermsToolFunctionality == "Y")){
			return true;
		}else{
			return false;
		}
	}

	public function getLoginCookie(){

		if(array_key_exists('CORALLoginID', $_COOKIE)){
			return $_COOKIE['CORALLoginID'];
		}

	}

	public function getSessionCookie(){

		if(array_key_exists('CORALSessionID', $_COOKIE)){
			return $_COOKIE['CORALSessionID'];
		}

	}
        
        
        
        public function parseTree($tree, $root = null,$path=null) {
                $return = array();
                # Traverse the tree and search for direct children of the root
                foreach($tree as $key => $doc) {
                     if($doc->parentDocumentID == $root) {
                        # Remove item from tree (we don't need to traverse this again)
                        unset($tree[$key]);
                        # Append the child into result array and parse its children
                        $record=array();
                        $currParent=$doc->documentID;
                        //$record['doc']=$doc->documentID;
                        $record['path']=$path.'/'.$currParent;
                        $record['doc']=$doc;
                        $record['children'] = $this->parseTree($tree, $currParent,$record['path']);
                        //$record['parentDocumentID']=$doc->parentDocumentID;
                        $return[] = $record;
                    }
                }
              return empty($return) ? null : $return;    
        }
        /**
         * printTree only deal cases when $displayArchiveInd is '' or 1 excluding 2
         * @param type $tree
         * @param type $countspace
         * @param type $user
         * @param type $displayArchiveInd
         */
        public function printTree($tree,$countspace=0,$user,$displayArchiveInd) {
               if ($displayArchiveInd == ''){
                   $treeClassPrefix='treegrid-';
                   $treeClassSuffix='b';
               }else{
                   $treeClassPrefix='treegrid-';
                   $treeClassSuffix='a';
               }
               
               if(!is_null($tree) && count($tree) > 0) {
                  //$this->sortNestedArray($tree,$sortOrder);
                  //$spaces=  str_repeat("&nbsp", $countspace);
                  //print "<br>$spaces<br>";
                  
                  
                  
                  foreach($tree as $node) {
                      
                  $document=$node['doc'];
                  $licenseID=$document->licenseID;
                  $documentType = new DocumentType(new NamedArguments(array('primaryKey' => $document->documentTypeID)));
                  //determine coloring of the row
                    if(($document->expirationDate != "0000-00-00") && ($document->expirationDate != "")){
					$classAdd="class='archive'";
		    }else if ((strtoupper($documentType->shortName) == 'AGREEMENT') || (strpos(strtoupper($documentType->shortName),'AGREEMENT'))){
					$classAdd="class='agreement'";
                    }else{
					$classAdd="";
                                      
			 }
                                
                   if (($document->expirationDate != "0000-00-00") && ($document->expirationDate != "")){
		      $displayExpirationDate = _("archived on: ") . format_date($document->expirationDate);
		   }else{
		      $displayExpirationDate = '';
				}
                    
                    $path=  $node['path'];      
                    if (count(explode("/",$path))-1 >1) {  
                      echo "<tr class='".$treeClassPrefix.$document->documentID.$treeClassSuffix." ".$treeClassPrefix."parent-".$document->parentDocumentID.$treeClassSuffix."'>";
                    }else{
                      echo "<tr class='".$treeClassPrefix.$document->documentID.$treeClassSuffix."'>";
                    }
                    
                    
                    if (count($node['children']) >0) {
		       echo "<td >".$document->shortName ."(". count($node['children']).")</td>";
                    }else{
                      echo "<td $classAdd>" . $document->shortName ."</td>";
                    }
		    echo "<td $classAdd>" . $documentType->shortName . "</td>";
		    echo "<td $classAdd>" . format_date($document->effectiveDate) . "</td>";
		    echo "<td $classAdd>";
                    
                    
                    $signature= array();
                    $signatureArray = $document->getSignaturesForDisplay();
                    if (count($signatureArray) > 0){
			 echo "<table class='noBorderTable'>";
                         foreach($signatureArray as $signature) {

					if (($signature['signatureDate'] != '') && ($signature['signatureDate'] != "0000-00-00")) {
						$signatureDate = format_date($signature['signatureDate']);
					}else{
						$signatureDate=_("(no date)");
					}

					echo "<tr>";
					echo "<td $classAdd>" . $signature['signerName'] . "</td>";
					echo "<td $classAdd>" . $signatureDate . "</td>";
					echo "</tr>";

					}
					echo "</table>";
					if ($user->canEdit()){
						echo "<a href='ajax_forms.php?action=getSignatureForm&height=270&width=460&modal=true&documentID=" . $document->documentID . "' class='thickbox' id='signatureForm'>"._("add/view details")."</a>";
					}


				}else{
					echo _("(none found)")."<br />";
					if ($user->canEdit()){
						echo "<a href='ajax_forms.php?action=getSignatureForm&height=170&width=460&modal=true&documentID=" . $document->documentID . "' class='thickbox' id='signatureForm'>"._("add signatures")."</a>";
					}
				}

				echo "</td>";
                                echo "<td $classAdd>";
				if (!$user->isRestricted()) {
					if ($document->documentURL != ""){
						echo "<a href='documents/" . $document->documentURL . "' target='_blank'>"._("view document")."</a><br />";
					}else{
						echo _("(none uploaded)")."<br />";
					}
				}
                                
                                
                                if (count($document->getExpressions) > 0){
					echo "<a href='javascript:showExpressionForDocument(" . $document->documentID . ");'>"._("view expressions")."</a>";
				}
                                
                                
                                echo     "</td>";
                                
                                
					echo "<td $classAdd>";
                                        echo "<a href='ajax_forms.php?action=getDocumentNotes&height=295&width=317&modal=true&licenseID=" . $licenseID . "&documentID=" . $document->documentID . "' class='thickbox' id='DocumentNotes'>"._("Notes")."</a><br />";
                                if ($user->canEdit()){
                                        echo "<a href='ajax_forms.php?action=getUploadDocument&height=295&width=317&modal=true&licenseID=" . $licenseID . "&documentID=" . $document->documentID . "' class='thickbox' id='editDocument'>"._("edit document")."</a><br /><a href='javascript:deleteDocument(\"" . $document->documentID . "\");'>"._("remove document")."</a>";
					echo "<br />" . $displayExpirationDate . "</td>";
				}
				echo "</tr>";
                                
                                $this->printTree($node['children'],$countspace+1,$user,$displayArchiveInd);
                    }
                   
                    
                  }
                  
                
        }
        
    public   function cmpiasc($a, $b) 
        { 
            
            return strcmp($a['doc']->shortName, $b['doc']->shortName); 
        } 

    public   function cmpidesc($a, $b) 
        { 
            
            return strcmp($b['doc']->shortName,$a['doc']->shortName); 
        } 


    public   function sortNestedArray(&$tree,$sortField,$sortOrder) {
        // do the array sorting 
            
            $sortf='cmpi'."$sortOrder";
            //print ($sortf);
            //print ($sortField);
            //uasort($tree, array($this,$sortf));
            //We don't use this function to sort signature date and signer names. Instead, we rely on the sql statement sort.
            
            uasort($tree, function($a, $b) use ($sortField, $sortOrder){
                if ($sortField=='D.shortName'){
                    if ($sortOrder=='asc'){
                        return strcmp($a['doc']->shortName, $b['doc']->shortName); 
                    }
                    else{
                        return strcmp($b['doc']->shortName,$a['doc']->shortName);
                    }
                        
                }
                elseif ($sortField=='DT.shortName'){
                    $documentA=$a['doc'];
                    $documentB=$b['doc'];
                    $documentTypeOfA = new DocumentType(new NamedArguments(array('primaryKey' => $documentA->documentTypeID)));
                    $documentTypeOfB = new DocumentType(new NamedArguments(array('primaryKey' => $documentB->documentTypeID)));
                    if ($sortOrder=='asc'){
                        return strcmp($documentTypeOfA->shortName,$documentTypeOfB->shortName); 
                    }else{
                        return strcmp($documentTypeOfB->shortName,$documentTypeOfA->shortName);
                    }
                }elseif ($sortField=='D.effectiveDate') {
                   
                    if ($sortOrder=='asc'){
                        return strcmp($a['doc']->effectiveDate, $b['doc']->effectiveDate); 
                    }
                    else{
                        return strcmp($b['doc']->effectiveDate,$a['doc']->effectiveDate);
                    }
                    
                }else{
                    
                }
                
            });
            foreach($tree as &$node) {
                //print($node["doc"]->shortName);
		if (count($node['children'])>1) 
		{
			$this->sortNestedArray($node['children'],$sortOrder);
			//print_r($node['children'],);
		}
            }
	
 }


}

?>
