<?php

/*
**************************************************************************************************************************
** CORAL Management Module v. 1.0
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
**************************************************************************************************************************
** ajax_processing.php contains processing (adds/updates/deletes) on data sent using ajax from forms and other pages
**
** when ajax_processing.php is called through ajax, 'action' parm is required to dictate which form will be returned
**
**************************************************************************************************************************
*/
include_once 'directory.php';
include_once 'user.php';

switch ($_GET['action']) {

	//for document adds or updates - note that actual file is done on form (processing done in uploadDocument)
	//this just saves the URL in db

    case 'submitDocument':
    	//if documentID is sent then this is an update
    	if ((isset($_POST['documentID'])) && ($_POST['documentID'] != '')){
 			$document = new Document(new NamedArguments(array('primaryKey' => $_POST['documentID'])));

			if ((($document->expirationDate == "") || ($document->expirationDate == '0000-00-00')) && ($_POST['archiveInd'] == "1")){
				$document->expirationDate = date( 'Y-m-d H:i:s' );
			}else if ($_POST['archiveInd'] == "0"){
				$document->expirationDate = '';
			}


    	}else{
 			$document = new Document();
 			$document->documentID = '';

			if ($_POST['archiveInd'] == "1"){
				$document->expirationDate = date( 'Y-m-d H:i:s' );
			}else{
				$document->expirationDate = '';
			}

		}

		//first set effective Date for proper saving
		if ((isset($_POST['effectiveDate'])) && ($_POST['effectiveDate'] != '')){
			$document->effectiveDate = create_date_from_js_format($_POST['effectiveDate'])->format('Y-m-d');
		}else{
			$document->effectiveDate = null;
		}

		if ((isset($_POST['revisionDate'])) && ($_POST['revisionDate'] != '')) {
			$document->revisionDate = create_date_from_js_format($_POST['revisionDate'])->format('Y-m-d');
		}


		$document->shortName=$_POST['shortName'];
		$document->documentTypeID=$_POST['documentTypeID'];
		$document->parentDocumentID=$_POST['parentDocumentID'];
		$document->licenseID=$_POST['licenseID'];
		$document->documentURL=$_POST['uploadDocument'];

		$license = new License(new NamedArguments(array('primaryKey' => $_POST['licenseID'])));
		$license->typeID		= $_POST['documentTypeID'];
		$license->statusDate = date( 'Y-m-d H:i:s' );

		try {
			$document->save();
			$license->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->POSTMessage();
			echo "</span>";
		}

        break;



    case 'deleteLicense':

		//note: does not delete physical documents

		$licenseID = $_GET['licenseID'];

		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));

		//remove licenses removes all children data as well
		try {
			$license->removeLicense();
			echo "<span class='success'>";
			echo _("License successfully deleted.");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

		break;


	//archives (expires) document - defaults to current date/time
    case 'archiveDocument':
		$document = new Document(new NamedArguments(array('primaryKey' => $_GET['documentID'])));
		$document->expirationDate = date( 'Y-m-d H:i:s' );

		try {
			$document->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

        break;


	//verify that the new document name doesn't have bad characters and the name isn't already being used
    case 'checkUploadDocument':
		$uploadDocument = $_POST['uploadDocument'];
		$document = new Document();

		$exists = 0;

    if (!is_writable("documents")) {
      echo 3;
      break;
    }

		//first check that it doesn't have any offending characters
		if ((strpos($uploadDocument,"'") > 0) || (strpos($uploadDocument,'"') > 0) || (strpos($uploadDocument,"&") > 0) || (strpos($uploadDocument,"<") > 0) || (strpos($uploadDocument,">") > 0)){
			echo 2;
		}else{
			//loop through each existing document to verify this name isn't already being used
			foreach ($document->allAsArray() as $documentTestArray) {
				if (strtoupper($documentTestArray['documentURL']) == strtoupper($uploadDocument)) {
					$exists++;
				}
			}

			echo $exists;
		}

		break;


	//performs document upload
    case 'uploadDocument':
		$documentName = basename($_FILES['myfile']['name']);

		$document = new Document();

		$exists = 0;

		//verify the name isn't already being used
		foreach ($document->allAsArray() as $documentTestArray) {
			if (strtoupper($documentTestArray['documentURL']) == strtoupper($documentName)) {
				$exists++;
			}
		}

		//if match was found
		if ($exists == 0){

			$target_path = "documents/" . basename($_FILES['myfile']['name']);

			//note, echos are meant for debugging only - only file name gets sent back
			if(move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)) {
				//set to web rwx, everyone else rw
				//this way we can edit the document directly on the server
				chmod ($target_path, 0766);
				echo "<span class='success'>";
				echo _("success uploading!");
				echo "</span>";
			}else{
			  header('HTTP/1.1 500 Internal Server Error');
				echo "<span class='error'>";
			  echo "<div id=\"error\">" . _("There was a problem saving your file to ") . $target_path . "</div>";
				echo "</span>";
			}

		}

		break;


    case 'deleteDocument':

		//note - does not delete physical document

		$document = new Document(new NamedArguments(array('primaryKey' => $_GET['documentID'])));


		//delete children sfx providers
		$sfxProvider = new SFXProvider();
		foreach ($document->getSFXProviders() as $sfxProvider) {
			$sfxProvider->delete();
		}

		//delete children signatures
		$signature = new Signature();
		foreach ($document->getSignatures() as $signature) {
			$signature->delete();
		}

		try {
			$document->delete();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

		break;



    case 'submitSignature':
    	//set date for proper saving
        if ((isset($_POST['signatureDate'])) && ($_POST['signatureDate'] != '')){
			$signatureDate = create_date_from_js_format($_POST['signatureDate'])->format('Y-m-d');
		}else{
			$signatureDate = "";
		}

    	//if signatureID is sent then this is an update
    	if ((isset($_POST['signatureID'])) && ($_POST['signatureID'] != '')){
 			$signature = new Signature(new NamedArguments(array('primaryKey' => $_POST['signatureID'])));
    	}else{
 			$signature = new Signature();
 			$signature->signatureID = '';
 		}


		$signature->signerName		= $_POST['signerName'];
		$signature->signatureTypeID = $_POST['signatureTypeID'];
		$signature->documentID		= $_POST['documentID'];
		$signature->signatureDate	= $signatureDate;

		try {
			$signature->save();
			echo "<span class='success'>";
			echo _("Document Saved Successfully.");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

		break;


    case 'deleteSignature':

		$signature = new Signature(new NamedArguments(array('primaryKey' => $_GET['signatureID'])));

		try {
			$signature->delete();
			echo "<span class='success'>";
			echo _("Signature Deleted Successfully.");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

		break;


	//add/update expression
    case 'submitExpression':

    	//if expressionID is sent then this is an update
    	if ((isset($_POST['expressionID'])) && ($_POST['expressionID'] != '')){
    		$expressionID = $_POST['expressionID'];
 			$expression = new Expression(new NamedArguments(array('primaryKey' => $expressionID)));
    	}else{
 			$expression = new Expression();
 			//default production use (terms tool indicator) to off if this is an add, otherwise we leave it alone
			$expression->productionUseInd	= 0;
			$expression->expressionID = '';
		}

		$expression->documentText		= $_POST['documentText'];
		$expression->documentID 		= $_POST['documentID'];
		$expression->expressionTypeID	= $_POST['expressionTypeID'];
		$expression->productionUseInd	= '0';
		$expression->simplifiedText		= '';

		try {
			$expression->save();

			if (!$expressionID){
				$expressionID=$expression->primaryKey;
			}

			//first remove all qualifiers, then we'll add them back
			$expression->removeQualifiers();

			foreach (explode(',', $_POST['qualifiers']) as $id){
				if ($id){
					$expressionQualifierProfile = new ExpressionQualifierProfile();
					$expressionQualifierProfile->expressionID = $expressionID;
					$expressionQualifierProfile->qualifierID = $id;
					$expressionQualifierProfile->save();
				}
			}



		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

        break;


    case 'deleteExpression':

		$expression = new Expression(new NamedArguments(array('primaryKey' => $_GET['expressionID'])));

		try {
			$expression->delete();
			echo "<span class='success'>";
			echo _("Expression Removed Successfully.");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

		break;


    case 'setProdUse':

		$expressionID = $_GET['expressionID'];
		$licenseID = $_GET['licenseID'];
		$productionUseInd = $_GET['productionUseInd'];

		//send email if prod use is being set
		if ($productionUseInd == "1"){
			$user = new User();
			$toList = array();
			$toList = $user->getSFXUpdateList();

			$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
			$util = new Utility();

			$emailMessage = _("An expression in the licensing module has been approved for terms tool use.") . "\n";
			$emailMessage.= _("License: ") . $license->shortName . "\n\n";
			$emailMessage.= _("View License Record: ") . $util->getPageURL() . "license.php?licenseID=" . $licenseID;

			$email = new Email();
			$email->to 			= implode(", ", $toList);
			$email->subject		= _("Licensing - expression set to production use");
			$email->message		= $emailMessage;

			$email->send();

			$response = _("Approved for terms tool display.");
		}else{
			$response = _("Removed from terms tool display.");
		}

		//save it in the expression record
		$expression = new Expression(new NamedArguments(array('primaryKey' => $_GET['expressionID'])));
		$expression->productionUseInd = $productionUseInd;

		try {
			$expression->save();
			echo "<span class='success'>";
			echo $response;
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

		break;


     case 'submitExpressionNote':

		//if note id is sent in, this is an update
    	if ((isset($_POST['expressionNoteID'])) && ($_POST['expressionNoteID'] != '')){
 			$expressionNote = new ExpressionNote(new NamedArguments(array('primaryKey' => $_POST['expressionNoteID'])));
			$expressionNote->note					= $_POST['expressionNote'];
			$expressionNote->displayOrderSeqNumber	= $_POST['displayOrderSeqNumber'];

			try {
				$expressionNote->save();
				echo "<span class='success'>";
				echo _("Expression Note Updated Successfully.");
				echo "</span>";
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		}else{
			//adding new
			$expression = new Expression(new NamedArguments(array('primaryKey' => $_POST['expressionID'])));

 			$expressionNote = new ExpressionNote();
 			$expressionNote->expressionNoteID 		= '';
			$expressionNote->note					= $_POST['expressionNote'];
			$expressionNote->expressionID			= $_POST['expressionID'];
			$expressionNote->displayOrderSeqNumber	= $expression->getNextExpressionNoteSequence;

			try {
				$expressionNote->save();
				echo "<span class='success'>";
				echo _("Expression Note Added Successfully.");
				echo "</span>";
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		}

 		break;


	 //when the arrows for reordering are clicked
     case 'reorderExpressionNote':
		$expressionNote = new ExpressionNote(new NamedArguments(array('primaryKey' => $_GET['expressionNoteID'])));

		echo $expressionNote->reorder($_GET['direction'], $_GET['oldSeq']);

 		break;


     case 'deleteExpressionNote':

		$expressionNote = new ExpressionNote(new NamedArguments(array('primaryKey' => $_GET['expressionNoteID'])));

		try {
			$expressionNote->delete();
			echo "<span class='success'>";
			echo _("Note Removed Successfully.");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

 		break;


     case 'submitSFXProvider':

    	//if expressionID is sent then this is an update
    	if ((isset($_POST['providerID'])) && ($_POST['providerID'] != '')){
 			$sfxProvider = new SFXProvider(new NamedArguments(array('primaryKey' => $_POST['providerID'])));
			$sfxProvider->shortName		= $_POST['shortName'];
			$sfxProvider->documentID 	= $_POST['documentID'];
    	}else{
 			$sfxProvider = new SFXProvider();
 			$sfxProvider->sfxProviderID = '';
			$sfxProvider->shortName		= $_POST['shortName'];
			$sfxProvider->documentID 	= $_POST['documentID'];
		}

		try {
			$sfxProvider->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

 		break;


     case 'deleteSFXProvider':

		$sfxProvider = new SFXProvider(new NamedArguments(array('primaryKey' => $_GET['sfxProviderID'])));

		try {
			$sfxProvider->delete();
			echo "<span class='success'>";
			echo _("Terms Tool Resource Link successfully deleted");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}
 		break;


    case 'submitLicense':

    	//may have been sent through despite missing license name or provider- do check here to make sure that isn't the case before insert into DB
    	if (isset($_POST['shortName'])  && ($_POST['shortName'] != '')) {
			//if licenseID is sent then this is an update
			if ($_POST['licenseID'] <> ""){
				//update data
				$license = new License(new NamedArguments(array('primaryKey' => $_POST['licenseID'])));
				$response = '<span class="success">' . _("Document Updated Successfully.") . '</span>';

			}else{
				//add data
				$license = new License();
				$license->licenseID  = '';
				$license->createDate = date( 'Y-m-d H:i:s' );
				$license->createLoginID = $user->primaryKey;
				$license->statusID='';
				$license->statusDate = date( 'Y-m-d H:i:s' );
				$response = _("Document Added Successfully.");

			}

			$license->shortName			= $_POST['shortName'];
			$license->description			= $_POST['description'];
			$license->typeID		= $_POST['documentTypeID'];
			$license->statusDate = date( 'Y-m-d H:i:s' );
			$license->statusLoginID = $user->primaryKey;
			//this method will save to either organization or provider depending on the settings
			//also, if this organization or provider doesn't exist it will create a new org/provider
			$license->setOrganization($_POST['organizationID'], $_POST['organizationName']);


			//this is the html that will be displayed in the form after submitting.
			//this is the only form in which this is done.
			try {
				$license->save();

				$license->setConsortiums($_POST['consortiumID']);

				if ((isset($_POST['licenseID'])) && $_POST['licenseID'] != ''){
					$licenseID = $_POST['licenseID'];
				}else{

					// I am adding a new license so go ahead and create the document
					$licenseID = $license->primaryKey;

					// Save the document

					$document = new Document();
					$document->documentID = '';
					$document->effectiveDate = date( 'Y-m-d H:i:s' );
					if ((isset($_POST['revisionDate'])) && ($_POST['revisionDate'] != '')) {
						$document->revisionDate = create_date_from_js_format($_POST['revisionDate'])->format('Y-m-d');
					}


					$document->shortName=$_POST['shortName'];
					$document->documentTypeID=$_POST['documentTypeID'];
					$document->parentDocumentID=$_POST['parentDocumentID'];
					$document->licenseID=$licenseID;
					$document->documentURL=$_POST['uploadDocument'];

					if ((($document->expirationDate == "") || ($document->expirationDate == '0000-00-00')) && ($_POST['archiveInd'] == "1")){
						$document->expirationDate = date( 'Y-m-d H:i:s' );
					}else if ($_POST['archiveInd'] == "0"){
						$document->expirationDate = '';
					}

					try {
						$document->save();
					} catch (Exception $e) {
						//echo $e->POSTMessage();
						echo "<span class='error'>";
						echo $e;
						echo "</span>";
					}

					if ($_POST['note']['body']) {
			 			$note = new DocumentNote(new NamedArguments(array('primaryKeyName'=>'documentNoteID')));
			 			$note->documentNoteID = '';
						$note->createDate 	=  date( 'Y-m-d H:i:s' );
						$note->licenseID = $licenseID;
						//avoid null values for notetypeid
						$note->documentNoteTypeID = ($_POST['note']['documentNoteTypeID']) ? $_POST['note']['documentNoteTypeID']:0;
						$note->documentID = $document->primaryKey;
						$note->body = $_POST['note']['body'];
						try {
							$note->save();
						} catch (Exception $e) {
							echo "<span class='error'>";
							echo $e;
							echo "</span>";
						}
					}
				}
				?>
				<p><?php echo $response; ?></p>
				
				<p class="actions"><a href='#' onclick='myCloseDialog(); window.parent.location=("license.php?licenseID=<?php echo $licenseID; ?>"); return false'><?php echo _("Continue");?></a>
			
				<?php
			} catch (Exception $e) {
				?>
				<h2 class='headerText'><?php echo _("SQL Insert Failed."); ?></h2>
				<p class="error"><?php echo $e->getMessage(); ?>
				<?php echo _(" Please make sure everything is filled out correctly.");?></p>
				
				<p class="actions"><a href='#' onclick='myCloseDialog()'><?php echo _("Continue");?></a>
				<?php
			}
		}else{
			?>
			
			<h2 class='headerText'><?php echo _("SQL Insert Failed."); ?></h2>
			<p class="error"><?php echo $e->getMessage(); ?>
			<?php echo _(" Please make sure everything is filled out correctly.");?></p>
				
			<p class="actions"><a href='#' onclick='myCloseDialog()'><?php echo _("Continue");?></a></p>
			<?php
		}

        break;

	 //new consortium being added directly on license form - returns updated drop down list
	 //used only when licensing inter-op is turned off
     case 'addConsortium':

		if ((isset($_GET['shortName'])) && ($_GET['shortName'] != '')){
			$consortium = new Consortium();
			$consortium->consortiumID='';
			$consortium->shortName		= $_GET['shortName'];

			try {
				$consortium->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		}

		echo "<select multiple='multiple' name='licenseConsortiumID' id='licenseConsortiumID'>";
		echo "<option value=''></option>";
		if ($_GET['editLicenseID']) {
			$license = new License(new NamedArguments(array('primaryKey' => $_GET['editLicenseID'])));
			$licenseconsortiumids = $license->getConsortiumsByLicense();
		} else {
			$license = new License();
		}
		$display = array();
		foreach($license->getConsortiumList() as $display) {
			if ((is_array($licenseconsortiumids) && in_array($display['consortiumID'],$licenseconsortiumids)) || $_GET['shortName'] == $display['name']) {
//			if ($_GET['shortName'] == $display['name']){
				echo "<option value='" . $display['consortiumID'] . "' selected>" . $display['name'] . "</option>";
			}else{
				echo "<option value='" . $display['consortiumID'] . "'>" . $display['name'] . "</option>";
			}
		}

		echo "</select>";

 		break;

     case 'addType':

		if ((isset($_GET['shortName'])) && ($_GET['shortName'] != '')){
			$type = new Type();
			$type->typeID='';
			$type->shortName		= $_GET['shortName'];

			try {
				$type->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		}

		echo "<select name='licenseTypeID' id='licenseTypeID'>";
		echo "<option value=''></option>";

		$license = new License();
		$display = array();

		foreach($license->getTypeList() as $display) {
			if ($_GET['shortName'] == $display['name']){
				echo "<option value='" . $display['typeID'] . "' selected>" . $display['name'] . "</option>";
			}else{
				echo "<option value='" . $display['typeID'] . "'>" . $display['name'] . "</option>";
			}
		}

		echo "</select>";

 		break;

     case 'addProvider':
		if ((isset($_GET['shortName'])) && ($_GET['shortName'] != '')){
			$provider = new Provider();
			$provider->providerID='';
			$provider->shortName		= $_GET['shortName'];

			try {
				$provider->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		}

		echo "<select name='licenseProviderID' id='licenseProviderID'>";
		echo "<option value=''></option>";

		$displayArray = array();
		$display = array();
		$provider = new Provider();
		$displayArray = $provider->allAsArray();

		foreach($displayArray as $display) {
			if ($_GET['shortName'] == $display['shortName']){
				echo "<option value='" . $display['providerID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['providerID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

		echo "</select>";

 		break;

	//AJAX helper for checking New Doc Type,Category,and Note Type submissions for uniqueness
	// echo 1 if the value of $_REQUEST['shortName'] is not in use OR we're unable to check due to input errors
	// echo 0 if the value of $_REQUEST['shortName'] is already in use
	 case 'checkForDuplicates':
		$validTypes = array('DocumentType','DocumentNoteType','Consortium');
		if ($_REQUEST['shortName'] && ($_REQUEST['newType'] && in_array($_REQUEST['newType'],$validTypes))) {
			$tempObject = new $_REQUEST['newType']();
			$currentItems = $tempObject->allAsArray();
			$inUse = false;
			foreach ($currentItems as $item) {
				if (strtoupper($item['shortName']) == strtoupper($_REQUEST['shortName'])) {
					$inUse = true;
					break;
				}
			}
		}
		$result = 0;
		if (!$inUse) {
			$result = 1;
		}
		echo $result;
	 break;

	 //new doc type being added directly on document form - returns updated drop down list
     case 'addDocumentType':

		if ((isset($_REQUEST['shortName'])) && ($_REQUEST['shortName'] != '')){
			$documentType = new DocumentType();
			$documentType->documentTypeID='';
			$documentType->shortName		= $_REQUEST['shortName'];

			try {
				$documentType->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		}


		echo "<select name='docTypeID' id='docTypeID'>";

		$displayArray = array();
		$display = array();
		$documentType = new DocumentType();
		$displayArray = $documentType->allAsArray();

		foreach($displayArray as $display) {
			if ($_POST['shortName'] == $display['shortName']){
				echo "<option value='" . $display['documentTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['documentTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

		echo "</select>";

 		break;

     case 'addNoteType':
		$noteType = new DocumentNoteType();
		if ((isset($_REQUEST['shortName'])) && ($_REQUEST['shortName'] != '')){
			$noteType->documentNoteTypeID='';
			$noteType->shortName = $_REQUEST['shortName'];

			try {
				$noteType->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		}
/*
<select name="note[documentNoteTypeID]" id="noteDocumentNoteTypeID">
			<option value="1">Test Type 1</option>
			<option value="2">Test Type 2</option>			<option value="3">Test Type 3</option>			<option value="4">Test Type 4</option>			</select>
*/
			echo '			<select id="noteDocumentNoteTypeID" name="note[documentNoteTypeID]">';
			foreach($noteType->allAsArray() as $display) {
				echo "			<option value='" . $display['documentNoteTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
			echo '			</select>';
 		break;


	 //new signature type being added directly on signature form - returns updated drop down list
	 //no longer used.... must add signature types from admin form
     case 'addSignatureType':

		if ((isset($_POST['shortName'])) && ($_POST['shortName'] != '')){
			$signatureType = new SignatureType();
			$signatureType->signatureTypeID='';
			$signatureType->shortName		= $_POST['shortName'];

			try {
				$signatureType->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		}

		echo "<select name='signatureTypeID' id='signatureTypeID'>";
		echo "<option value=''></option>";

		$displayArray = array();
		$display = array();
		$signatureType = new SignatureType();
		$displayArray = $signatureType->allAsArray();

		foreach($displayArray as $display) {
			if ($_POST['shortName'] == $display['shortName']){
				echo "<option value='" . $display['signatureTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['signatureTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

		echo "</select>";

 		break;


	 //new expression type being added directly on expression form - returns updated drop down list
	 //note default type is 'internal'.  this will need to be updated by user in admin if it's decided to be used for display
     case 'addExpressionType':


		if ((isset($_POST['shortName'])) && ($_POST['shortName'] != '')){
			$expressionType = new ExpressionType();
			$expressionType->expressionTypeID='';

			$expressionType->shortName		= $_POST['shortName'];
			$expressionType->noteType		= 'Internal';

			try {
				$expressionType->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		}

		echo "<select name='expressionTypeID' id='expressionTypeID'>";

		$displayArray = array();
		$display = array();
		$expressionType = new ExpressionType();
		$displayArray = $expressionType->allAsArray();

		foreach($displayArray as $display) {
			if ($_POST['shortName'] == $display['shortName']){
				echo "<option value='" . $display['expressionTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['expressionTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

		echo "</select>";

 		break;


	 //generically adds data for admin screen
	 //error is echoed back
     case 'addData':

 		$className = $_POST['tableName'];
 		$shortName = $_POST['shortName'];

		$instance = new $className();
		$instance->shortName = $shortName;

		try {
			$instance->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->POSTMessage();
			echo "</span>";
		}

 		break;

	 //generically updates data for admin screen
	 //error is echoed back
     case 'updateData':
 		$className = $_POST['tableName'];
 		$updateID = $_POST['updateID'];
 		$shortName = $_POST['shortName'];

		$instance = new $className(new NamedArguments(array('primaryKey' => $updateID)));
		$instance->shortName = $shortName;

		try {
			$instance->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->POSTMessage();
			echo "</span>";
		}

 		break;


	 //generically deletes data for admin screen
	 //error is echoed back
     case 'deleteData':

 		$className = $_GET['tableName'];
 		$deleteID = $_GET['deleteID'];

		//since we're using MyISAM which doesn't support FKs, must verify that there are no records of children or they could disappear
		$instance = new $className(new NamedArguments(array('primaryKey' => $deleteID)));
		$numberOfChildren = $instance->getNumberOfChildren();

		if ($numberOfChildren > 0){
			$type = ($className == 'Consortium') ? 'category':strtolower(preg_replace("/[A-Z]/", " \\0" , lcfirst($className)));
			echo "<span class='error'>";
			//print out a friendly message...
			printf(_("Unable to delete  - this %s is in use.  Please make sure no documents are set up with this information."), $type);
			echo "</span>";
		}else{
			try {
				$instance->delete();
			} catch (Exception $e) {
				echo "<span class='error'>";
				//print out a friendly message...
				echo _("Unable to delete.  Please make sure no documents are set up with this information.");
				echo "</span>";
			}
		}

 		break;



     case 'submitExpressionType':
		if ((isset($_POST['expressionTypeID'])) && ($_POST['expressionTypeID'] != '')){
 			$expressionType = new ExpressionType(new NamedArguments(array('primaryKey' => $_POST['expressionTypeID'])));
		}else{
 			$expressionType = new ExpressionType();
 			$expressionType->expressionTypeID = '';
		}

		$expressionType->shortName	= $_POST['shortName'];
		$expressionType->noteType 	= $_POST['noteType'];

		try {
			$expressionType->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->POSTMessage();
			echo "</span>";
		}

 		break;



     case 'submitQualifier':
		if ((isset($_POST['qualifierID'])) && ($_POST['qualifierID'] != '')){
 			$qualifier = new Qualifier(new NamedArguments(array('primaryKey' => $_POST['qualifierID'])));
		}else{
 			$qualifier = new Qualifier();
 			$qualifier->qualifierID = '';
		}

		$qualifier->expressionTypeID 	= $_POST['expressionTypeID'];
		$qualifier->shortName			= $_POST['shortName'];

		try {
			$qualifier->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->POSTMessage();
			echo "</span>";
		}

 		break;

     case 'submitUserData':
		if ($_POST['orgLoginID']){
 			$user = new User(new NamedArguments(array('primaryKey' => $_POST['orgLoginID'])));
		}else{
  			$user = new User();
		}

		$user->loginID		= $_POST['loginID'];
		$user->firstName 	= $_POST['firstName'];
		$user->lastName		= $_POST['lastName'];
		$user->privilegeID	= $_POST['privilegeID'];
		$user->emailAddressForTermsTool	= $_POST['emailAddressForTermsTool'];

		try {
			$user->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->POSTMessage();
			echo "</span>";
		}


 		break;



     case 'deleteUser':

 		$loginID = $_GET['loginID'];

		$user = new User(new NamedArguments(array('primaryKey' => $loginID)));

		try {
			$user->delete();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

 		break;




     case 'deleteExpressionType':

 		$expressionTypeID = $_GET['expressionTypeID'];

		$expressionType = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));

		try {
			$expressionType->removeExpressionType();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

 		break;



	//verify file name for uploaded attachments (4th tab) aren't already being used
    case 'checkUploadAttachment':

		$uploadAttachment = $_POST['uploadAttachment'];
		$attachmentFile = new AttachmentFile();

		$exists = 0;

    if (!is_writable("attachments")) {
      echo 3;
      break;
    }

		foreach ($attachmentFile->allAsArray() as $attachmentTestArray) {
			if (strtoupper($attachmentTestArray['attachmentURL']) == strtoupper($uploadAttachment)) {
				$exists++;
			}
		}

		echo $exists;

		break;

	//perform actual upload for attachments (4th tab)
    case 'uploadAttachment':

		$documentName = basename($_FILES['myfile']['name']);

		$target_path = "attachments/" . basename($_FILES['myfile']['name']);

		$attachmentFile = new AttachmentFile();

		$exists = 0;

		//loop through existing log attachments to verify that this name isn't already taken
		foreach ($attachmentFile->allAsArray() as $attachmentTestArray) {
			if (strtoupper($attachmentTestArray['attachmentURL']) == strtoupper($documentName)) {
				$exists++;
			}
		}

		//if match was not found
		//note, echoes are not being sent anywhere
        if ($exists == 0){
            if ($_FILES['myfile']['error'] === UPLOAD_ERR_OK) {
                if(move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)) {
                    //set to web rwx, everyone else rw
                    //this way we can edit the document directly on the server
                    chmod ($target_path, 0766);
										echo "<span class='success'>";
                    echo _("success uploading!");
										echo "</span>";
                }else{
                  header('HTTP/1.1 500 Internal Server Error');
									echo "<span class='error'>";
                  echo _("There was a problem saving your file to ") . $target_path;
									echo "</span>";
                }
            } else {
                header('HTTP/1.1 500 Internal Server Error');
								echo "<span class='error'>";
                echo uploadErrorMessage($_FILES['myfile']['error']);
								echo "</span>";
            }
        }
		break;

	//add/update for attachment - 4th tab
    case 'submitAttachment':
        error_log("submitAttachment" . $_POST['attachmentID']);
    	//if attachmentID is sent then this is an update
    	if ((isset($_POST['attachmentID'])) && ($_POST['attachmentID'] <> "")){
 			$attachment = new Attachment(new NamedArguments(array('primaryKey' => $_POST['attachmentID'])));
    	}else{
 			$attachment = new Attachment();
 			$attachment->attachmentID = '';
		}

    	if ((isset($_POST['sentDate'])) && ($_POST['sentDate'] <> "")){
    		$attachment->sentDate = create_date_from_js_format($_POST['sentDate'])->format('Y-m-d');
    	}else{
    		$attachment->sentDate = "";
    	}

		$attachment->attachmentText	= $_POST['attachmentText'];
		$attachment->licenseID 	= $_POST['licenseID'];


		try {
			$attachment->save();
			echo $attachment->primaryKey;
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}


        break;


	//adding the attachment file to the db - saves the URL to it only
    case 'addAttachmentFile':

		$attachmentFile = new AttachmentFile();
		$attachmentFile->attachmentID		= $_GET['attachmentID'];
		$attachmentFile->attachmentURL  = $_GET['attachmentURL'];

		try {
			$attachmentFile->save();
			echo $attachmentFile->primaryKey;
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

        break;


     case 'deleteAttachment':

 		$attachment = new Attachment(new NamedArguments(array('primaryKey' => $_GET['attachmentID'])));

		//first delete attachments
		foreach ($attachment->getAttachmentFiles() as $attachmentFile) {
			$attachmentFile->delete();
		}

		try {
			$attachment->delete();
			echo "<span class='success'>";
			echo _("Attachment successfully deleted");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

 		break;


     case 'deleteAttachmentFile':

 		$attachmentFile = new AttachmentFile(new NamedArguments(array('primaryKey' => $_GET['attachmentFileID'])));

		try {
			$attachmentFile->delete();
			echo "<span class='success'>";
			echo _("Attachment file successfully deleted");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

 		break;

     case 'deleteNote':

 		$note = new DocumentNote(new NamedArguments(array('primaryKey' => $_GET['documentNoteID'])));

		try {
			$note->delete();
			echo "<span class='success'>";
			echo _("Note successfully deleted");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

 		break;


	//add/update for note
    case 'submitNote':
    	//if noteID is sent then this is an update

    	if ((isset($_POST['documentNoteID'])) && ($_POST['documentNoteID'])){
 			$note = new DocumentNote(new NamedArguments(array('primaryKey' => $_POST['documentNoteID'])));
    	} else {
 			$note = new DocumentNote(new NamedArguments(array('primaryKeyName'=>'documentNoteID')));
 			$note->documentNoteID = '';
			$note->createDate 	=  date( 'Y-m-d H:i:s' );
		}

		$note->body	= $_POST['body'];
		$note->licenseID 	= $_POST['licenseID'];
		$note->documentNoteTypeID 	= $_POST['documentNoteTypeID'];
		$note->documentID 	= $_POST['documentID'];


		try {
			$note->save();
			echo $note->primaryKey;
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

	break;

	 //updates license status when a new one is selected in dropdown box
     case 'updateStatus':
		$licenseID = $_GET['licenseID'];
		$statusID = $_GET['statusID'];
 		$statusDate = date( 'Y-m-d H:i:s' );

 		//update license
 		$license = new License(new NamedArguments(array('primaryKey' => $_GET['licenseID'])));
		$license->statusID = $statusID;
		$license->statusDate = $statusDate;

		try {
			$license->save();
			echo "<span class='success'>";
			echo _("Status has been updated");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

 		break;


	//used for autocomplete of signer name
    case 'getSigners':

		if (isset($_GET['searchMode'])) $searchMode = $_GET['searchMode']; else $searchMode='';
		if (isset($_GET['limit'])) $limit = $_GET['limit']; else $limit = '';

		$q = $_GET['q'];
		$q = str_replace(" ", "+",$q);
		$q = str_replace("&", "%",$q);

		$signature = new Signature();
		$signerArray = $signature->search($q);

		echo implode("\n", $signerArray);

		break;




	//used for autocomplete of provider names (from organizations module)
    case 'getOrganizations':

		if (isset($_GET['searchMode'])) $searchMode = $_GET['searchMode']; else $searchMode='';
		if (isset($_GET['limit'])) $limit = $_GET['limit']; else $limit = '';

		$q = $_GET['q'];
		$q = str_replace(" ", "+",$q);
		$q = str_replace("&", "%",$q);

		$license = new License();
		$orgArray = $license->searchOrganizations($q);

		echo implode("\n", $orgArray);

		break;


	//used to verify document name isn't already being used as it's added
	case 'getExistingDocumentName':
		$shortName = $_GET['shortName'];

		if (isset($_GET['documentID'])) $documentID = $_GET['documentID']; else $documentID='';


		$document = new Document();
		$documentArray = array();

		$exists = 0;

		foreach ($document->allAsArray() as $documentArray) {
			if ((strtoupper($documentArray['shortName']) == strtoupper($shortName)) && ($documentArray['documentID'] != $documentID)) {
				$exists++;
			}
		}

		echo $exists;

		break;

	//used to verify license name isn't already being used as it's added
	case 'getExistingLicenseName':
		$shortName = $_GET['shortName'];


		$license = new License();
		$licenseArray = array();

		$exists = 0;

		foreach ($license->allAsArray() as $licenseArray) {
			if (strtoupper($licenseArray['shortName']) == strtoupper($shortName)) {
				$exists = $licenseArray['licenseID'];
			}
		}

		echo $exists;

		break;


	//used to verify organization name isn't already being used as it's added
	case 'getExistingOrganizationName':
		$shortName = $_GET['shortName'];


		$license = new License();
		$licenseArray = array();

		$exists = 0;

		foreach ($license->getOrganizationList() as $orgArray) {
			if (strtoupper($orgArray['name']) == strtoupper($shortName)) {
				$exists = $orgArray['organizationID'];
			}
		}

		echo $exists;

		break;

	default:
			if (empty($action))
        return;
      printf(_("Action %s not set up!"), $action);
      break;


}



?>
