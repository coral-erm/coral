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
**************************************************************************************************************************
** ajax_forms.php contains all forms that are displayed using thickbox
**
** when ajax_forms.php is called through ajax, 'action' parm is required to dictate which form will be returned
**
** each form should have a corresponding javascript file located in /js/forms/
**************************************************************************************************************************
*/

include_once 'directory.php';
include_once 'user.php';


switch ($_GET['action']) {

	//form to edit license record
    case 'getLicenseForm':
		if (isset($_GET['licenseID'])) $licenseID = $_GET['licenseID']; else $licenseID = '';

		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
		if ($licenseID) $organizationName = $license->getOrganizationName; else $organizationName = '';

		?>
		<div id='div_licenseForm'>
		<form id='licenseForm'>
		<input type='hidden' id='editLicenseID' name='editLicenseID' value='<?php echo $licenseID; ?>'>
		<input type='hidden' id='editLicenseForm' name='editLicenseForm' value='Y'>
		<h2 id='license-form-title'><?php echo _("License");?></h2>
	
		<div class="form-grid">
			<label for="licenseShortName" class="formText"><?php echo _("License Name:");?></label>  
			<p id='span_error_licenseShortName' class='error'></p>
			<textarea name='licenseShortName' id = 'licenseShortName' cols='38' rows='2' aria-describedby="span_error_licenseShortName"><?php echo $license->shortName; ?></textarea>
		
			<label for="organizationName" class="formText"><?php echo _("Publisher / Provider:");?></label>  
			<p id='span_error_organizationName' class='error'></p>
			
			<input type='text' id='organizationName' name='organizationName' value="<?php echo $organizationName; ?>" aria-describedby="span_error_organizationName" />
			<input type='hidden' id='licenseOrganizationID' name='licenseOrganizationID' value='<?php echo $license->organizationID; ?>'>
			<p id='span_error_organizationNameResult' class='warning'></p>
			
			<label for="licenseConsortiumID" class="formText"><?php echo _("Consortium:");?></label>
			<span id='span_consortium'>
			<?php
			try{
				$consortiaArray = array();
				$consortiaArray=$license->getConsortiumList()

				?>
				<select name='licenseConsortiumID' id='licenseConsortiumID'>
				<option value=''></option>
				<?php


				$display = array();


				foreach($consortiaArray as $display) {
					if ($license->consortiumID == $display['consortiumID']){
						echo "<option value='" . $display['consortiumID'] . "' selected>" . $display['name'] . "</option>";
					}else{
						echo "<option value='" . $display['consortiumID'] . "'>" . $display['name'] . "</option>";
					}
				}

				?>
				</select>
			<?php
			}catch(Exception $e){
				echo "<p class='error'>"._("There was an error processing this request - please verify configuration.ini is set up for organizations correctly and the database and tables have been created.")."</p>";
			}
			
			$config = new Configuration;

			//if the org module is not installed allow to add consortium from this screen
			if (($config->settings->organizationsModule == 'N') || (!$config->settings->organizationsModule)){
			?>
			<span id='span_newConsortium'><button type="button" class="btn btn-sm link" onclick="newConsortium();"><?php echo _("add consortium");?></button></span>
			<?php } ?>
			</span>
			<p class="actions">
				<input type='button' value='<?php echo _("submit");?>' name='submitLicense' id ='submitLicense' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog('#licenseForm')" class='cancel-button secondary'>
			</p>
		</div>
		<script type="text/javascript" src="js/forms/licenseForm.js?random=<?php echo rand(); ?>"></script>
		</form>
		</div>


		<?php

        break;




	//form to edit/upload documents
    case 'getUploadDocument':

		//document ID passed in for updates only
		if (isset($_GET['documentID'])) $documentID = $_GET['documentID']; else $documentID = '';
		$licenseID = $_GET['licenseID'];

		$document = new Document(new NamedArguments(array('primaryKey' => $documentID)));
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));

		//some dates get in as 0000-00-00
		if (($document->effectiveDate == "0000-00-00") || ($document->effectiveDate == "")){
			$effectiveDate='';
		}else{
			$effectiveDate=format_date($document->effectiveDate);
		}


		if (($document->expirationDate) && ($document->expirationDate != '0000-00-00')){
			$archiveChecked = 'checked';
		}else{
			$archiveChecked = '';
		}


		?>
		<div id='div_uploadDoc'>
		<form id="uploadDoc" action="ajax_processing.php?action=submitDocument" method="POST" enctype="multipart/form-data">
		<!-- <form id="uploadDoc" enctype="multipart/form-data"> -->
		<input type='hidden' id='licenseID' name='licenseID' value='<?php echo $licenseID; ?>'>
		<input type='hidden' id='documentID' name='documentID' value='<?php echo $documentID; ?>'>
		
		<h2><?php echo _("Document Upload");?></h2>
		<div class="form-grid">
		<p id='span_errors' class='error'></p>
		
		<label for="effectiveDate" class="formText"><?php echo _("Effective Date:");?></label>
		<div class="form-group">
			<input class='date-pick' id='effectiveDate' name='effectiveDate' value='<?php echo $effectiveDate; ?>' aria-describedby="span_error_effectiveDate" />
			<span id='span_error_effectiveDate' class='error'></span>
		</div>
		<label for="documentTypeID" class="formText"><?php echo _("Document Type:");?></label>
		<span id='span_documentType' class="form-group">
		<select name='documentTypeID' id='documentTypeID' aria-describedby="span_error_documentTypeID">
		<?php

		$display = array();
		$documentType = new DocumentType();

		foreach($documentType->allAsArray() as $display) {
			if ($document->documentTypeID == $display['documentTypeID']){
				echo "<option value='" . $display['documentTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['documentTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

		?>
		</select>
		<span id='span_newDocumentType'><button type="button" class="btn btn-sm link" onclick="newDocumentType();"><?php echo _("add document type");?></button></span>
		</span>
		<span id='span_error_documentTypeID' class='error'></span>
		
		<label for="parentDocumentID" class="formText"><?php echo _("Parent:");?></label>
		<select name='parentDocumentID' id='parentDocumentID'>
			<option value=''></option>
		<?php

		$display = array();

		foreach($license->getDocuments() as $display) {
			if ($document->parentDocumentID == $display->documentID) {
				echo "<option value='" . $display->documentID . "' selected>" . $display->shortName . "</option>";
			}else if ($document->documentID != $display->documentID) {
				echo "<option value='" . $display->documentID . "'>" . $display->shortName . "</option>";
			}
		}

		foreach($license->getArchivedDocuments() as $display) {
			if ($document->parentDocumentID == $display->documentID) {
				echo "<option value='" . $display->documentID . "' selected>" . $display->shortName . "</option>";
			}else if ($document->documentID != $display->documentID) {
				echo "<option value='" . $display->documentID . "'>" . $display->shortName . "</option>";
			}
		}

		?>
		</select>
		
		<label for="shortName" class="formText"><?php echo _("Name:");?></label>
		<textarea name='shortName' id='shortName' rows='2' aria-describedby="span_error_shortName"><?php echo $document->shortName; ?></textarea>
		<span id='span_error_shortName' class='error'></span>

		<label for="upload_button" class="formText"><?php echo _("File:");?></label>
		<div class="form-group">
		<?php

		//if editing
		if ($documentID){
			echo "<div id='div_uploadFile'><div class='url'>" . $document->documentURL . "</div>";
			echo "<button type='button' class='btn link' onclick='replaceFile();'>"._("replace with new file")."</button>";
			echo "<input type='hidden' id='upload_button' name='upload_button' value='" . $document->documentURL . "'></div>";

		//if adding
		}else{
			echo "<div id='div_uploadFile'><input type='file' name='upload_button' id='upload_button' aria-describedby='div_file_message'></div>";
		}


		?>
		<span id='div_file_message' class="msg"></span>
		</div>
		
		<?php if (($document->parentDocumentID == "0") || ($document->parentDocumentID == "")){ ?>
		
		<p class="checkbox indent">
			<input type='checkbox' id='archiveInd' name='archiveInd' <?php echo $archiveChecked; ?> />
			<label for="archiveInd"><?php echo _("Archived");?></label>
		</p>
		
		<?php } ?>

		<p class="actions">
		<input type='button' value='<?php echo _("submit");?>' name='submitDocument' onclick='myDialogPOST("")' id='submitDocument' class='btn primary'>
	<!--	<input type='button' value='<?php echo _("submit");?>' name='submitDocument' id='submitDocument' onclick='myDialogPOST("ajax_processing.php?action=submitDocument")' class='submit-button'>  -->
		<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog('#uploadDocument')" class='btn secondary'></td>
		</p>
		</div>
		</div>

		<script type="text/javascript" src="js/forms/documentForm.js?random=<?php echo rand(); ?>"></script>

		<?php

        break;





	//form to prompt for date for archiving documents
	//Jan 2010, form no longer used, archive checkbox on document form instead
	//leaving in in case we revert
    case 'getArchiveDocumentForm':

		if (isset($_GET['documentID'])) $documentID = $_GET['documentID']; else $documentID = '';

		?>
		<div id='div_archiveDocumentForm'>
		<h2><?php echo _("Archive Document Date");?></h2>
		<span id='span_errors' class='error'></span>
		
		
		<input type='hidden' name='documentID' id='documentID' value='<?php echo $documentID; ?>' />
		<label for="expirationDate"><?php echo _("Archive Date:");?></label>
		<input class='date-pick' id='expirationDate' name='expirationDate' value='<?php echo format_date(date); ?>' />
		
		<p class="actions">
			<button type="submit" name='submitArchive' id='submitArchive'><?php echo _("Continue");?></button>
		</p>


		<script type="text/javascript" src="js/forms/documentArchiveForm.js?random=<?php echo rand(); ?>"></script>
		</div>

		<?php

       break;




	//form to add/edit sfx or other terms tool provider links
    case 'getSFXForm':

		//sfx provider id passed in for updates
		$licenseID = $_GET['licenseID'];
		if (isset($_GET['providerID'])) $sfxProviderID = $_GET['providerID']; else $sfxProviderID = '';

		$sfxProvider = new SFXProvider(new NamedArguments(array('primaryKey' => $sfxProviderID)));
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));

		?>
		<div id='div_sfxForm'>
		<input type='hidden' id='sfxProviderID' name='sfxProviderID' value='<?php echo $sfxProviderID; ?>'>

		<h2><?php echo _("Terms Tool Resource Link");?></h2>
		<span id='span_errors' class='error'></span>
		<div class="block-form">
		<p>
			<label for="documentID" class="formText"><?php echo _("For Document:");?></label>  
			<select name='documentID' id='documentID' aria-describedby="span_error_documentID">
			<option value=''></option>
			<?php

			$display = array();

			foreach($license->getDocuments() as $display) {
				if ($sfxProvider->documentID == $display->documentID) {
					echo "<option value='" . $display->documentID . "' selected>" . $display->shortName . "</option>";
				}else{
					echo "<option value='" . $display->documentID . "'>" . $display->shortName . "</option>";
				}
			}


			?>
			</select>
		</p>
		<p id='span_error_documentID' class='error'></p>
		<p>
			<label for="shortName" class="formText"><?php echo _("Terms Tool Resource:");?></label>  
			<input id='shortName' name='shortName' value='<?php echo $sfxProvider->shortName; ?>' aria-describedby="span_error_shortName" />
		</p>
		<p id='span_error_shortName' class='error'></p>	
		</div>
		<p class="actions">
			<input type='submit' value='<?php echo _("submit");?>' name='submitSFX' onclick='myDialogPOST("")' id='submitSFX' class='btn primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick='myCloseDialog("")' class='btn secondary'>
		</p>
		
		<script type="text/javascript" src="js/forms/sfxForm.js?random=<?php echo rand(); ?>"></script>
		</div>

		<?php

       break;




	//form to add/edit signatures
    case 'getSignatureForm':

		//signature passed in for updates
		$documentID = $_GET['documentID'];
		if (isset($_GET['signatureID'])) $signatureID = $_GET['signatureID']; else $signatureID = '';

		if ($signatureID == 'undefined') $signatureID = '';

		$document = new Document(new NamedArguments(array('primaryKey' => $documentID)));

		?>
		<div id='div_signatureForm'>
		<h2><?php echo _("Signatures");?></h2>
		<span id='span_errors' class='error'></span>
		
		<table class='table-border table-striped dataTable'>
		<thead>
		<tr>
		<th scope="col" id="header-name"><?php echo _("Signer Name");?></th>
		<th scope="col" id="header-date"><?php echo _("Date");?></th>
		<th scope="col" id="header-type"><?php echo _("Type");?></th>
		<th scope="col" id="header-update"><?php echo _("Update");?></th>
		</tr>
		</thead>
		<tbody>
		<?php

			if ($signatureID == ""){
				echo "<input type='hidden' name='signatureID' id='signatureID' value='' />";
			}

			$display = array();
			foreach ($document->getSignaturesForDisplay() as $display) {
				echo "<tr>";
				//used for in-line editing (since this is already a form, can't make another form to edit sigs!)
				if ($signatureID == $display['signatureID']){
					echo "<th scope='row' id='signerName-".$signatureID."'><input type='text' id='signerName' value=\"" . $display['signerName'] . "\" aria-labelledby='header-name' /></th>";
					echo "<td><input class='date-pick' id='signatureDate' name='signatureDate' value=\"" . format_date($display['signatureDate']) . "\"  aria-labelledby='header-date' /></td>";
					echo "<td><span id='span_signatureType'><select id='signatureTypeID' name='signatureTypeID'  aria-labelledby='header-type'>";

					$stdisplay = array();
					$signatureType = new SignatureType();

					foreach($signatureType->allAsArray() as $stdisplay) {
						if ($display['signatureTypeID'] == $stdisplay['signatureTypeID']){
							echo "<option value='" . $stdisplay['signatureTypeID'] . "' selected>" . $stdisplay['shortName'] . "</option>";
						}else{
							echo "<option value='" . $stdisplay['signatureTypeID'] . "'>" . $stdisplay['shortName'] . "</option>";
						}
					}

					echo "</select></span>";

					echo "</td>";
					echo "<td class='actions'><button type='button' class='btn' id='commitUpdate' name='commitUpdate' aria-describedby='signerName-".$signatureID."'>"._("commit update")."</button></td>";
					echo "<input type='hidden' name='signatureID' id='signatureID' value='" . $display['signatureID'] . "' />";


				}else{
					echo "<th scope='row' id='signerName-".$signatureID."'>" . $display['signerName'] . "</th>";
					echo "<td>" . format_date($display['signatureDate']) . "</td>";
					echo "<td>" . $display['signatureTypeName'] . "</td>";
					if ($signatureID){
						echo "<td></td>";
					}else{
						echo "<td class='actions'><button type='button' class='btn link' onclick='updateSignatureForm(\"" . $display['signatureID'] . "\");' aria-describedby='signerName-".$signatureID."'>"._("edit")."</button>";
						echo "<button type='button' class='btn link' onclick='removeSignature(\"" . $display['signatureID'] . "\");' aria-describedby='signerName-".$signatureID."'>"._("remove")."</button></td>";
					}
				}

				echo "</tr>";

			}


			if ($signatureID == ""){
				echo "<tr>";
				echo "<td><input type='text' id='signerName' aria-labelledby='header-name' /></td>";
				echo "<td><input class='date-pick' id='signatureDate' name='signatureDate' aria-labelledby='header-date' /></td>";
				echo "<td><span id='span_signatureType'><select id='signatureTypeID' name='signatureTypeID' aria-labelledby='header-type'>";
				$stdisplay = array();
				$signatureType = new SignatureType();

				foreach($signatureType->allAsArray() as $stdisplay) {
					echo "<option value='" . $stdisplay['signatureTypeID'] . "'>" . $stdisplay['shortName'] . "</option>";
				}

				echo "</select></span></td>";
				echo "<td class='actions'><button type='button' class='btn link' id='commitUpdate' name='commitUpdate'>"._("add")."</button></td>";
				echo "</tr>";
			}

		?>

		</table>
		</td>
		</tr>
		</table>
		<p class="actions">
			<button type="button" class="btn secondary" onclick='myCloseDialog("");  window.parent.updateDocuments();  window.parent.updateArchivedDocuments(); return false' class='cancel-button secondary'><?php echo _("Close");?></button>
			<input type="hidden" id='documentID' name='documentID' value='<?php echo $documentID; ?>'>
		</p>
		<script type="text/javascript" src="js/forms/signatureForm.js?random=<?php echo rand(); ?>"></script>
		</div>

		<?php

       break;


	//form to add/edit expressions
    case 'getExpressionForm':

		//expression ID sent in for updates
		if (isset($_GET['expressionID'])) $expressionID = $_GET['expressionID']; else $expressionID = '';

		$licenseID = $_GET['licenseID'];

		$expression = new Expression(new NamedArguments(array('primaryKey' => $expressionID)));
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));

		//get the expression type so we can determine the qualifiers to display
		$expressionTypeID = $expression->expressionTypeID;

		$expressionType = new ExpressionType();
		$expressionTypeArray = $expressionType->allAsArray();

		//if expression type id isn't set up, get the first one as a default
		if (!$expressionTypeID){
			$expressionTypeID = $expressionTypeArray[0]['expressionTypeID'];
		}

		$expressionType = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));

		//get qualifiers set up for this expression
		$sanitizedInstance = array();
		$instance = new Qualifier();
		$expressionQualifierProfileArray = array();
		foreach ($expression->getQualifiers() as $instance) {
			$expressionQualifierProfileArray[] = $instance->qualifierID;
		}


		//get all qualifiers for output in checkboxes
		$expressionQualifierArray = array();
		$expressionQualifierArray = $expressionType->getQualifiers();


		?>
		<div id='div_expressionForm' class="form-grid">
		<input type='hidden' id='expressionID' name='expressionID' value='<?php echo $expressionID; ?>'>

		
		<h2 class="headerText"><?php echo _("Expressions");?></h2>
		<span id='span_errors' class='error'></span>
		
		<label for="documentID" class="formText"><?php echo _("Document:");?></label>
		<select name='documentID' id='documentID'>
		<?php

		$display = array();

		foreach($license->getDocuments() as $display) {
			if ($expression->documentID == $display->documentID) {
				echo "<option value='" . $display->documentID . "' selected>" . $display->shortName . "</option>";
			}else{
				echo "<option value='" . $display->documentID . "'>" . $display->shortName . "</option>";
			}
		}


		?>
		</select>
		
		<label for="expressionTypeID" class="formText"><?php echo _("Expression Type:");?></label>
		<div class="form-group" id='span_expressionType'>
		<select name='expressionTypeID' id='expressionTypeID'>
		<?php

		$display = array();

		foreach($expressionTypeArray as $display) {
			if ($expression->expressionTypeID == $display['expressionTypeID']){
				echo "<option value='" . $display['expressionTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['expressionTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}


		?>
		</select>
		<p class="wide" id='span_newExpressionType'>
			<button type="button" class="btn link" onclick="newExpressionType();"><?php echo _("add expression type");?></button>
		</p>
	</div>

		<?php if (count($expressionQualifierArray) == 0) { ?>
			<fieldset class="subgrid">
			<legend><?php echo _("Qualifier:");?></legend>
			<div class="form-group" id='div_Qualifiers'>

			<?php
			if (is_array($expressionQualifierArray) && count($expressionQualifierArray) > 0) {
				echo '<ul class="unstyled">';
				//loop over all qualifiers available for this expression type
				foreach ($expressionQualifierArray as $expressionQualifierIns){
					$checked = '';
					if (in_array($expressionQualifierIns->qualifierID,$expressionQualifierProfileArray)){
						$checked = ' checked ';
					}
					echo "<li class='checkbox'><input class='check_Qualifiers' type='checkbox' name='qualifierID' id='" . $expressionQualifierIns->qualifierID . "' value='" . $expressionQualifierIns->qualifierID . "' ".$checked." /><label for='" . $expressionQualifierIns->qualifierID . "'>" . $expressionQualifierIns->shortName . "</label></li>";
				}
				echo '</ul>';
			}
			?>
			</fieldset>
		<?php } ?>

		<label for="documentText" class="formText"><?php echo _("Document Text:");?></label>
		<textarea name='documentText' id = 'documentText' rows='10'><?php echo $expression->documentText; ?></textarea>
	
	<p class="actions">
		<input type='submit' value='<?php echo _("submit");?>' name='submitExpression' onclick='myDialogPOST("")' id='submitExpression' class='submit-button primary'>
		<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog('#submitExpression')" class='cancel-button secondary'>
	</p>	
	</div>

		<script type="text/javascript" src="js/forms/expressionForm.js?random=<?php echo rand(); ?>"></script>

		<?php

        break;


	//form to add / edit expression notes (internal and display notes)
    case 'getExpressionNotesForm':

		$expressionID = $_GET['expressionID'];
		if (isset($_GET['expressionNoteID'])) $expressionNoteID = $_GET['expressionNoteID']; else $expressionNoteID = '';
		if ($expressionNoteID == 'undefined') $expressionNoteID = '';

		$expression = new Expression(new NamedArguments(array('primaryKey' => $expressionID)));
		$expressionType = new ExpressionType(new NamedArguments(array('primaryKey' => $expression->expressionTypeID)));

		$documentText = nl2br($expression->documentText);
		$noteType = $expressionType->noteType;


		$expressionNoteArray = $expression->getExpressionNotes();

		?>
		<div id='div_expressionNotesForm'>
		<input type='hidden' name='expressionID' id='expressionID' value='<?php echo $expressionID; ?>'>
		<h2><?php printf(_("%s Notes"), ucfirst($noteType));?></h2>
		<b><?php echo _("For Document Text:");?></b>  
		<p><?php echo $documentText; ?></p>
		<p id='span_errors' class='error'></p>

		<!-- TODO: remove table? -->
		<table class='dataTable'>
		<thead>
		<tr>
		<th>&nbsp;</th>
		<th><?php printf(_("%s Notes"), ucfirst($noteType));?></th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		</tr>
		</thead>
		<tbody>
		<?php
			if ($expressionNoteID == ""){
				echo "<input type='hidden' name='expressionNoteID' id='expressionNoteID' value='' />";
			}

			$rowCount = count($expressionNoteArray);
			$rowNumber=0;
			$expressionNote = new ExpressionNote();
			foreach ($expressionNoteArray as $expressionNote){
				$rowNumber++;
				echo "<tr>";

				if ($expressionNoteID == $expressionNote->expressionNoteID){
					echo "<td>&nbsp;</td>";
					echo "<td><textarea name='expressionNote' id = 'expressionNote' rows='4' aria-label='"._('Expression note')."'>" .  $expressionNote->note . "</textarea></td>";
					// TODO: check button action
					echo "<td class='actions'><button type='button' class='btn' onclick id='commitUpdate' name='commitUpdate'>"._("commit update")."</button></td>";
					echo "<input type='hidden' name='expressionNoteID' id='expressionNoteID' value='" . $expressionNoteID . "' />";
					echo "<input type='hidden' name='displayOrderSeqNumber' id='displayOrderSeqNumber' value='" . $expressionNote->displayOrderSeqNumber . "' />";
					echo "<td>&nbsp;</td>";
				}else{

					if ($expressionNoteID){
						echo "<td>&nbsp;</td>";
						echo "<td>" .  nl2br($expressionNote->note) . "</td>";
						echo "<td>&nbsp;</td>";
						echo "<td>&nbsp;</td>";

					}else{
						// TODO: make these rows drag and drop; replace up & down buttons with one button that has keyboard up/down arrow handlers
						//calculate which arrows to show for reordering
						if ($rowNumber == "1"){
							echo "<td class='reorder'><button type='button' class='btn' onclick='reorder(\"" . $expressionNote->expressionNoteID . "\", \"" . $expressionNote->displayOrderSeqNumber . "\",\"down\");'><img src='images/arrowdown.png' border=0></button></td>";
						}else if($rowNumber == $rowCount){
							echo "<td><button type='button' class='btn' onclick='reorder(\"" . $expressionNote->expressionNoteID . "\", \"" . $expressionNote->displayOrderSeqNumber . "\",\"up\");'><img src='images/arrowup.png' border=0></button></td>";
						}else{
							echo "<td><button type='button' class='btn' onclick='reorder(\"" . $expressionNote->expressionNoteID . "\", \"" . $expressionNote->displayOrderSeqNumber . "\",\"up\");'><img src='images/arrowup.png' border=0></button>&nbsp;<button type='button' class='btn' onclick='reorder(\"" . $expressionNote->expressionNoteID . "\", \"" . $expressionNote->displayOrderSeqNumber . "\",\"down\");'><img src='images/arrowdown.png' border=0></button></td>";
						}
						echo "<td>" .  nl2br($expressionNote->note) . "</td>";
						echo "<td><button type='button' class='btn' onclick='updateExpressionNoteForm(\"" . $expressionNote->expressionNoteID . "\");'>"._("edit")."</button></td>";
						echo "<td><button type='button' class='btn' onclick='removeExpressionNote(\"" . $expressionNote->expressionNoteID . "\");'>"._("remove")."</button></td>";
					}

				}


				echo "</tr>";

			}
			$rowNumber++;

			if ($expressionNoteID == ""){
				echo "<tr>";
				echo "<td>&nbsp;</td>";
				echo "<td><textarea name='expressionNote' id = 'expressionNote' rows='4' aria-label='"._('Epression note')."'></textarea></td>";
				echo "<td><button type='button' class='btn' onclick='addExpressionNote();'>"._("add")."</button></td>";
				echo "<td>&nbsp;</td>";
				echo "</tr>";
			}
		?>

		</tbody>
		</table>
		</td>
		</tr>
<!--		<tr><td style='width:100%;'><br /><br /><button type="button" class="btn" onclick='myCloseDialog('');  window.parent.<?php if ($_GET['org'] == "compare") { echo "updateSearch()"; } else { echo "updateExpressions()"; } ?>; return false' class='cancel-button'><?php echo _("Close");?></button></td></tr> -->
                <tr><td style='width:100%;'><br /><br /><button type="button" class="btn" onclick='myCloseDialog("");  window.parent.<?php if ($_GET['org'] == "compare") { echo "updateSearch()"; } else { echo "updateExpressions()"; } ?>; return false' class='cancel-button secondary'><?php echo _("Close");?></button></td></tr>

		</table>
		<input type="hidden" id='documentID' name='documentID' value='<?php echo $documentID; ?>'>
		<input type="hidden" id='org' name='org' value='<?php if (isset($_GET['org'])){ echo $_GET['org']; } ?>'>

		<script type="text/javascript" src="js/forms/expressionNotesForm.js?random=<?php echo rand(); ?>"></script>
		</div>

		<?php

       break;



	//form to add/edit attachment form
    case 'getAttachmentForm':

		//attachment ID sent in for updates
		if (isset($_GET['attachmentID'])) $attachmentID = $_GET['attachmentID']; else $attachmentID = '';

		$attachment = new Attachment(new NamedArguments(array('primaryKey' => $attachmentID)));

		if (($attachment->sentDate != '') && ($attachment->sentDate != "0000-00-00")) {
			$sentDate = format_date($attachment->sentDate);
		}else{
			$sentDate='';
		}


		?>
		<div id='div_attachmentForm' class="block-form">
		<form id='attachmentForm'>
		<input type='hidden' id='attachmentID' name='attachmentID' value='<?php echo $attachmentID; ?>'>
		<input type='hidden' id='licenseID' name='licenseID' value='<?php echo $_GET['licenseID']; ?>'>
		
		<h2><?php echo _("Attachments");?></h2>
		<span id='span_errors'></span>
		
		<label for="sentDate" class="formText"><?php echo _("Date:");?></label>
		<input class='date-pick' id='sentDate' name='sentDate' value='<?php echo $sentDate; ?>' />
		
		<label for="attachmentText" class="formText"><?php echo _("Details:");?></label>
		<textarea name='attachmentText' id = 'attachmentText' rows='10'><?php echo $attachment->attachmentText; ?></textarea>
		
		<label for="upload_attachment_button" class="formText"><?php echo _("Attachments:");?></label>
		<p id='div_file_message' class='error'></p>
		<p id='div_file_success' class='success'></p>
		<?php

		//if editing
		if ($attachmentID){
			$attachmentFile = new AttachmentFile();

			foreach ($attachment->getAttachmentFiles() as $attachmentFile){
				echo "<div id='div_existing_" . $attachmentFile->attachmentFileID . "'>" . $attachmentFile->attachmentURL . "  <button type='button' class='btn' onclick='removeExistingAttachment(\"" . $attachmentFile->attachmentFileID . "\");' class='smallLink'>"._("remove")."</button><br /></div>";
			}

			echo "<div id='div_uploadFile'><input type='file' name='upload_attachment_button' id='upload_attachment_button'></div><br />";

		//if adding
		}else{
			echo "<div id='div_uploadFile'><input type='file' name='upload_attachment_button' id='upload_attachment_button'></div><br />";
		}


		?>
		
	
		<p class="actions">
			<input type='submit' value='<?php echo _("submit");?>' name='submitAttachment' onclick='myDialogPOST("")' id='submitAttachment'class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog('');window.parent.updateAttachments();" class='cancel-button secondary'>
		</p>



		<script type="text/javascript" src="js/forms/attachmentForm.js?random=<?php echo rand(); ?>"></script>
		</form>
		</div>


		<?php

        break;


	//generic form for administering lookup tables on the admin page (these tables simply have an ID and shortName attributes)
	case 'getAdminUpdateForm':
		$updateID = $_GET['updateID'];


		$className = $_GET['tableName'];
		$instance = new $className(new NamedArguments(array('primaryKey' => $updateID)));

		?>
		<div id='div_updateForm'>
		
		<label for="updateVal"><?php echo _("Edit");?></label>
		<p id='span_errors' class='error'></p>
		
		<p>
		<?php
		echo "<input type='text' id='updateVal' name='updateVal' value='" . $instance->shortName . "' aria-describedby='span_errors'/><button type='submit' onclick='updateData(\"" . $className . "\", \"" . $updateID . "\");' onclick='myDialogPOST(\"\")' id='updateButton' class='submit-button primary'>"._("Edit")."</button>";
		?>


		</p>
		<p class="actions"><button type="button" class="btn" onclick='myCloseDialog(""); return false' class='cancel-button secondary'><?php echo _("Close");?></button></p>
		</div>


		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#updateVal').keyup(function(e) {

				   if(e.keyCode == 13) {
					   updateData("<?php echo $className; ?>", "<?php echo $updateID; ?>");
				   }
        	});

        </script>


		<?php

		break;



	//user form on the admin tab needs its own form since there are other attributes
	case 'getAdminUserUpdateForm':
		if (isset($_GET['loginID'])) $loginID = $_GET['loginID']; else $loginID = '';

		if ($loginID != ''){
			$update=_('Edit User');
			$updateUser = new User(new NamedArguments(array('primaryKey' => $loginID)));
		}else{
			$update=_('Add User');
		}

		$util = new Utility();

		?>
		<h2><?php echo $update ?></h2>
		<div id='div_updateForm' class="form-grid">
			<p id='span_errors' class='error'></p>
			
			<label for='loginID'><?php echo _("Login ID");?></label>
			<input type='text' id='loginID' name='loginID' value='<?php echo $loginID; ?>' />
		
			<label for='firstName'><?php echo _("First Name");?></label>
			<input type='text' id='firstName' name='firstName' value="<?php if (isset($updateUser)) echo $updateUser->firstName; ?>"  />
		
			<label for='lastName'><?php echo _("Last Name"); ?></label>
			<input type='text' id='lastName' name='lastName' value="<?php if (isset($updateUser)) echo $updateUser->lastName; ?>" />
		
			<label for='privilegeID'><?php echo _("Privilege"); ?></label>

			<select name='privilegeID' id='privilegeID'>
				<?php

				$display = array();
				$privilege = new Privilege();

				foreach($privilege->allAsArray() as $display) {
					if ($updateUser->privilegeID == $display['privilegeID']){
						echo "<option value='" . $display['privilegeID'] . "' selected>" . $display['shortName'] . "</option>";
					}else{
						echo "<option value='" . $display['privilegeID'] . "'>" . $display['shortName'] . "</option>";
					}
				}

				?>
			</select>
			<ul class="form-instructions">
				<li><?php echo _("Add/Edit users can add, edit, or remove licenses and associated fields"); ?></li>
				<li><?php echo _("Admin users have access to the Admin page and the SFX tab."); ?></li>
				<li><?php echo _("Restricted users do not have the ability to view documents"); ?></li>
				<li><?php echo _("View only users can view all license information, including the license pdf");?></li>
			</ul>			

			<?php
			//if not configured to use SFX, hide the Terms Tool Report
			if ($util->useTermsTool()) {
			?>
				<label for='emailAddressForTermsTool'><?php echo _("Terms Tool Email");?></label>
				<input type='email' id='emailAddressForTermsTool' name='emailAddressForTermsTool' value='<?php if (isset($updateUser)) echo $updateUser->emailAddressForTermsTool; ?>' aria-describedby="emailAddressForTermsToolInstructions" />
		
				<ul class="form-instructions" id="emailAddressForTermsToolInstructions">
					<li><?php echo _("Enter email address if you wish this user to receive email notifications when the terms tool box is checked on the Expressions tab.")?></li>
					<li><?php echo _("Leave this field blank if the user shouldn't receive emails.");?></li>
				</ul>
			<?php } else { 
				echo "<input type='hidden' id='emailAddressForTermsTool' name='emailAddressForTermsTool' value='' /><br />"; 
			}?>

			<p class="actions">
				<input type='submit' value='<?php echo $update; ?>' onclick='window.parent.submitUserData("<?php echo $loginID; ?>");myDialogPOST("");' class='submit-button primary'>
				<input type='button' value="<?php echo _("Close");?>" onclick="myCloseDialog()" id='update-user-cancel' class='cancel-button secondary'>
			</p>
		</div>

		<?php

		break;


	//expression types on admin.php screen - since expression types also have note type (internal/display)
	case 'getExpressionTypeForm':
		if (isset($_GET['expressionTypeID'])) $expressionTypeID = $_GET['expressionTypeID']; else $expressionTypeID = '';

		if ($expressionTypeID){
			$update=_('Edit Expression Type');
			$expressionType = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));
		}else{
			$update=_('Add Expression Type');
		}


		?>
		<h2><?php echo $update; ?></h2>
		<div id='div_updateForm' class="form-grid">
		<input type='hidden' name='expressionTypeID' id='expressionTypeID' value='<?php echo $expressionTypeID; ?>' />
		
		<p id='span_errors' class='error'></p>
			
		<label for='shortName'><?php echo _("Expression Type");?></label>
		<input type='text' id='shortName' name='shortName' value='<?php if (isset($expressionType)) echo $expressionType->shortName; ?>' />
		
		<label for='noteType'><?php echo _("Note Type");?></label>
		<select name='noteType' id='noteType'>
			<option value='Internal' <?php if ((isset($expressionType)) && ($expressionType->noteType == 'Internal')) echo "selected"; ?> ><?php echo _("Internal");?></option>
			<option value='Display' <?php if ((isset($expressionType)) && ($expressionType->noteType == 'Display')) echo "selected"; ?> ><?php echo _("Display");?></option>
		</select>

		<!-- TODO: i18n placeholders (asterisk) -->
		<p class='form-instructions'>* <?php echo _("Note type of display allows for terms tool use");?></p>
		
		<p class="actions">
			<input type='submit' value='<?php echo $update; ?>' onclick='window.parent.submitExpressionType();myDialogPOST("")' id='update-expression-type' class='submit-button primary'>
	<!--	<td><input type='button' value='<?php echo _("Close");?>' onclick="myCloseDialog(''); return false;" id='cancel-expression-type' class='cancel-button'></td> -->
	    <input type='button' value='<?php echo _("Close");?>' onclick="myCloseDialog('#newExpressionTypeForm')"; return false;" id='cancel-expression-type' class='cancel-button secondary'>
		</p>
		</div>


		<?php

		break;

	//Calendar Settings on admin.php screen - want it to be edited by users so not in the config
	case 'getCalendarSettingsForm':
		if (isset($_GET['calendarSettingsID'])) $calendarSettingsID = $_GET['calendarSettingsID']; else $calendarSettingsID = '';

		if ($calendarSettingsID){
			$update=_('Edit Calendar Settings');
			$calendarSettings = new CalendarSettings(new NamedArguments(array('primaryKey' => $calendarSettingsID)));
		}


		?>
		<div id='div_updateForm'>
		<input type='hidden' name='calendarSettingsID' id='calendarSettingsID' value='<?php echo $calendarSettingsID; ?>' />
		<h2><?php echo $update; ?></h2>
		<?php

		if (strtolower($calendarSettings->shortName) == strtolower('Resource Type(s)')) { ?>
      <label for='shortName'><?php echo _("Variable Name");?></label>
			<?php if (isset($calendarSettings)) echo $calendarSettings->shortName; ?>
			<label for='value'><?php echo _("Value");?></label>
			<select multiple name='value' id='value'>
			<?php

			$display = array();
			$resourceType = new ResourceType();

				foreach($resourceType->getAllResourceType() as $display) {
					if (in_array($display['resourceTypeID'], explode(",", $calendarSettings->value))) {
						echo "<option value='" . $display['resourceTypeID'] . "' selected>" . $display['shortName'] . "</option>";
					}else{
						echo "<option value='" . $display['resourceTypeID'] . "'>" . $display['shortName'] . "</option>";
					}
				}

			?>
			</select>

		<?php

		} elseif (strtolower($calendarSettings->shortName) == strtolower('Authorized Site(s)')) { ?>
            <b><?php echo _("Variable Name");?></b>
						<p><?php if (isset($calendarSettings)) echo $calendarSettings->shortName; ?></p>
						
			<label for='value'><?php echo _("Value");?></label>
			<select multiple name='value' id='value'>
			<?php

			$authorizedSite = new AuthorizedSite();
			$authorizedSitesArray = $authorizedSite->getAllAuthorizedSite();
            if ($authorizedSitesArray['authorizedSiteID']) {
                $authorizedSitesArray = array($authorizedSitesArray);
            }

				foreach($authorizedSitesArray as $display) {
					if (in_array($display['authorizedSiteID'], explode(",", $calendarSettings->value))) {
						echo "<option value='" . $display['authorizedSiteID'] . "' selected>" . $display['shortName'] . "</option>";
					}else{
						echo "<option value='" . $display['authorizedSiteID'] . "'>" . $display['shortName'] . "</option>";
					}
				}

			?>
			</select>


		<?php

		} else {

		?>
      <b><?php echo _("Variable Name");?></b>
			<p><?php if (isset($calendarSettings)) echo $calendarSettings->shortName; ?></p>
			
			<label for='value'><?php echo _("Value");?></label></td>
			<input type='text' id='value' name='value' value='<?php if (isset($calendarSettings)) echo $calendarSettings->value; ?>' />
			
		<?php

		}

		?>

		<p class="actions">
			<input type='submit' value='<?php echo $update; ?>' onclick='window.parent.submitCalendarSettings();myDialogPOST("")' class='submit-button primary'>
			<input type='button' value='<?php echo _("Close");?>' onclick="myCloseDialog(''); return false" class='cancel-button secondary'>
		</p>
		</div>


		<?php

		break;


	//qualifier on admin.php screen - since qualifiers also have expression types
	case 'getQualifierForm':
		if (isset($_GET['qualifierID'])) $qualifierID = $_GET['qualifierID']; else $qualifierID = '';

		if ($qualifierID){
			$update=_('Edit Qualifier');
			$qualifier = new Qualifier(new NamedArguments(array('primaryKey' => $qualifierID)));
		}else{
			$update=_('Add Qualifier');
		}


		?>
		<h2><?php echo $update; ?></h2>
		<div id='div_updateForm' class="form-grid">
		<input type='hidden' name='qualifierID' id='qualifierID' value='<?php echo $qualifierID; ?>' />
		
		<span id='span_errors' class='error'></span>
		
		<label for='expressionTypeID'><?php echo _("For Expression Type");?></label>
		<select name='expressionTypeID' id='expressionTypeID'>
		<?php

		$display = array();
		$expressionType = new ExpressionType();

		foreach($expressionType->allAsArray() as $display) {
			if ($qualifier->expressionTypeID == $display['expressionTypeID']){
				echo "<option value='" . $display['expressionTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['expressionTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

		?>
		</select>
		
		<label for='shortName'><?php echo _("Qualifier");?></label>
		<input type='text' id='shortName' name='shortName' value='<?php if (isset($qualifier)) echo $qualifier->shortName; ?>' />

		<p class="actions">
			<input type='submit' value='<?php echo $update; ?>' onclick='window.parent.submitQualifier();myDialogPOST("")' id='submitQualifier' class='submit-button primary'>
			<input type='button' value='<?php echo _("Close");?>' onclick="myCloseDialog(''); return false" class='cancel-button secondary'>
		</p>
		</div>

		<?php

		break;

  case 'getInProgressStatusesForm':
    $config = new Configuration();
    ?>
    <div id='div_updateInProgressStatusesForm' class="block-form">

      <h2 class='headerText'><?php echo _("Update In Progress Statuses"); ?></h2>
      <p id='span_errors' class="error"></p>

			<p>
				<label for="inProgressStatuses"><?php echo _('In Progress Statuses'); ?></label>
				<textarea name="in_progress_statuses" id="inProgressStatuses"><?php echo $config->settings->inProgressStatuses ?? ''; ?></textarea>
			</p>

			<p class="actions">
				<input type="submit" value="<?php echo _('Save'); ?>" onclick='javascript:window.parent.submitInProgressStatusesSettings();myDialogPOST("")' id="submitInProgressStatusesSettings" class="submit-button primary">
				<input type="button" value="<?php echo _("cancel");?>" onclick="myCloseDialog(''); return false" class="cancel-button secondary">
			</p>
    </div>

    <?php

    break;

  case 'getInProgressStatusesForm':
    $config = new Configuration();
    ?>
    <div id='div_updateInProgressStatusesForm' class="block-form">

            <h2 class='headerText'><?php echo _("Update In Progress Statuses"); ?></h2>
            <p id='span_errors'></p>
						
          <p>
						<label for="inProgressStatuses"><?php echo _('In Progress Statuses'); ?></label>
          	<textarea name="in_progress_statuses" id="inProgressStatuses"><?php echo $config->settings->inProgressStatuses ?? ''; ?></textarea>
					</p>

          <p class="actions">
						<input type="submit" value="<?php echo _('Save'); ?>" onclick='javascript:window.parent.submitInProgressStatusesSettings();myDialogPOST("")' id="submitInProgressStatusesSettings" class="submit-button primary">
          	<input type="button" value="<?php echo _("cancel");?>" onclick="myCloseDialog(''); return false" class="cancel-button secondary">
					</p>
    </div>

    <?php

    break;

    case 'getTermsToolSettingsForm':
        $config = new Configuration();
        ?>
				<h2><?php echo _("Update Terms Tool Settings"); ?></h2>
        <div id='div_updateTermsToolSettingsForm' class="form-grid">
            
					<p id='span_errors' class='error'></p>
					
					<label for="termsToolResolver"><?php echo _("Resolver"); ?></label>

					<select name="resolver" id="termsToolResolver">
							<?php foreach(['SFX','SerialsSolutions','EBSCO'] as $v): ?>
							<option
									value="<?php echo $v; ?>"
									<?php echo $config->terms->resolver == $v ? 'selected' : ''; ?>
									data-resolver="<?php echo $v; ?>"
							><?php echo $v; ?></option>
							<?php endforeach; ?>
					</select>
           
					<label for="termsToolOpenUrl"><?php echo _('Open URL'); ?></label>
          <input type="url" name="open_url" id="termsToolOpenUrl" value="<?php echo $config->terms->open_url; ?>">
                
					<label for="termsToolClientId"></label>
					<input type="text" name="client_identifier" id="termsToolClientId" value="<?php echo $config->terms->client_identifier; ?>">
          
					<label for="termsToolSID"></label>
					<input type="text" name="sid" id="termsToolSID" value="<?php echo $config->terms->sid; ?>">
					
				<p class="actions">
					<input type="submit" value="<?php echo _('Save'); ?>" onclick='window.parent.submitTermsToolSettings();myDialogPOST("")' id="submitTermsToolSettings" class="submit-button primary">
          <input type="button" value="<?php echo _("cancel");?>" onclick="myCloseDialog(''); return false" class="cancel-button secondary">
				</p>
				</div>

        <script type="text/javascript">
          $('#termsToolResolver').change(function(e) {
            var selected = $(this).val();
            var sidText = selected === 'EBSCO' ? '<?php echo _('Api Key'); ?>' : 'SID';
            var clientIdText = selected === 'EBSCO' ? '<?php echo _('Customer ID'); ?>' : '<?php echo _('Client ID'); ?>';
            $('tr[class*="tt-option"]').hide();
						// TODO: check display
            $('.tt-option-'+selected).css('display', 'table-row');
            $('label[for="termsToolSID"]').html(sidText);
            $('label[for="termsToolClientId"]').html(clientIdText);
          }).trigger('change');

        </script>

        <?php

        break;
	default:
			if (empty($action))
          return;
       printf(_("Action %s not set up!"), $action);
       break;


}



?>
