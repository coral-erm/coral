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
** ajax_forms.php contains all forms that are displayed using thickbox
**
** when ajax_forms.php is called through ajax, 'action' parm is required to dictate which form will be returned
**
** each form should have a corresponding javascript file located in /js/forms/
**************************************************************************************************************************
*/

include_once 'directory.php';
include_once 'user.php';

// TODO: finish eliminating nested tables and fixing headers
// TODO: submit-button / primary; cancel-button / secondary; fix submit button types

switch ($_GET['action']) {

	//form to edit license record
		case 'getLicenseForm':
		if (isset($_GET['licenseID'])) {
			$licenseID = $_GET['licenseID'];
		} else {
			$licenseID = '';
		}
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
		if ($licenseID) {
			$organizationName = $license->getOrganizationName;
		} else {
			$organizationName = '';
		}
		//a new note can be added along with the initial document creation, but not when we're editing a document
		if (!$licenseID) {
	 		$note = new DocumentNote(new NamedArguments(array('primaryKeyName'=>'documentNoteID')));
	 		$documentNoteType = new DocumentNoteType(new NamedArguments(array('primaryKeyName'=>'documentNoteTypeID')));
		}
?>
		<div id='div_licenseForm'>
			<form id='licenseForm'>
				<input type='hidden' id='editLicenseID' name='editLicenseID' value='<?php echo $licenseID; ?>'>
				<input type='hidden' id='editLicenseForm' name='editLicenseForm' value='Y'>
				<h2 id='headerText' class='headerText'><?php if ($licenseID) echo _("Edit Document"); else echo _("New Document")?></h2>
				
				<div class="form-grid">
					<label for="licenseShortName" class="formText"><?php echo _("Name:");?></label>
					<input type='text' id = 'licenseShortName' value="<?php echo $license->shortName; ?>" aria-describedby="span_error_licenseShortName">
					<p id='span_error_licenseShortName' class='error'></p>

					<label for="licenseDescription" class="formText"><?php echo _("Description:");?></label>
					<textarea name='licenseDescription' id = 'licenseDescription' rows='2' aria-describedby="span_error_licenseDescription"><?php echo $license->description; ?></textarea>
					<p id='span_error_licenseDescription' class='error'></p>

					<input type='hidden' id='licenseOrganizationID' name='licenseOrganizationID' value='<?php echo '0'; ?>'>
					<input type='hidden' id='organizationName' name='organizationName' value='<?php echo 'Default Internal'; ?>'>

<?php
		//if not editing
		if (!$licenseID){
?>
				<label for="docTypeID" class="formText"><?php echo _("Type:");?></label>
				<span id='span_documentType' class="form-group">
					<select name='docTypeID' id='docTypeID' aria-describedby="span_error_documentTypeID">
						<?php
						$display = array();
						$documentType = new DocumentType();

						foreach($documentType->allAsArray() as $display) {
							if ($license->typeID == $display['documentTypeID']){
								echo "				<option value='" . $display['documentTypeID'] . "' selected>" . $display['shortName'] . "</option>";
							}else{
								echo "				<option value='" . $display['documentTypeID'] . "'>" . $display['shortName'] . "</option>";
							}
						}
						?>
					</select>
					<span id='span_newDocumentType'><button type="button" class="btn btn-sm link" onclick="newDocumentType();"><?php echo _("add document type");?></button></span>
					<p id='span_error_documentTypeID' class='error'></p>
				</span>
				
				<label for="revisionDate" class="formText"><?php echo _("Last Document Revision:");?></label>
				<input class="date-pick" type='input' id='revisionDate' name='revisionDate' value="<?php echo format_date(date('m/d/Y'));?>" />
				
					<?php
							//if editing
							} else {
					?>
					<input type='hidden' id='docTypeID' name='docTypeID' value='<?php echo $license->typeID; ?>'>
					<?php
							}
					?>

				<fieldset>
					<legend><?php echo _("Categories:");?></legend>
					<div id='span_consortium' class="form-group">
					<?php
						try{
							$consortiaArray = array();
							$consortiaArray = $license->getConsortiumList();
							$display = array();

							$licenseconsortiumids = $license->getConsortiumsByLicense();

							foreach($consortiaArray as $display) {
								if (is_array($licenseconsortiumids) && in_array($display['consortiumID'],$licenseconsortiumids)) {
									echo "<label><input type='checkbox' name='consortiumID' value='" . $display['consortiumID'] . "' checked>" . $display['name'] . "</label>";
								}else{
									echo "<label><input type='checkbox' name='consortiumID' value='" . $display['consortiumID'] . "'>" . $display['name'] . "</label>";
								}
							}
							}catch(Exception $e){
								echo "				<span class='error'>" . _("There was an error processing this request - please verify configuration.ini is set up for organizations correctly and the database and tables have been created.") . "</span>";
							}
						?>
						<p id='span_error_licenseConsortiumID' class='error'></p>
						<span id='span_newConsortium'><button type="button" class="btn btn-sm link" onclick="newConsortium();"><?php echo _("add category");?></button></span>
					</div>
				</fieldset>
<?php
		//if editing
		if ($licenseID) {
			// No Editing of file from Main page
			//echo "<div id='div_uploadFile'>" . $document->documentURL . "<br /><a href='javascript:replaceFile();'>replace with new file</a>";
			//echo "<input type='hidden' id='upload_button' name='upload_button' value='" . $document->documentURL . "'></div>";
		} else {
?>
					
		<label for="upload_button" class="formText"><?php echo _("File:");?></label>
<?php
			echo "			<span id='div_uploadFile'><input type='file' name='upload_button' id='upload_button' aria-describedby='div_file_message span_error_licenseuploadDocument'></span>";
	}
?>
		<p id='div_file_message' class='wide indent'></p>
		<p id='span_error_licenseuploadDocument' class='error'></p>

		<p class="checkbox indent">
			<input type='checkbox' id='archiveInd' name='archiveInd' value='1' />
			<label for="archiveInd"><?php echo _("Archived");?></label>
		</p>
<?php
		//only show the new note option if we're creating a new document
		if (!$licenseID) {
?>
	<fieldset class="subgrid">				
		<legend class="wide"><?php echo _("Add Optional Note");?></legend>			
		<p id='span_errors' class="error"></p>
		
		<label for="noteBody" class="formText"><?php echo _("Note:");?></label>
		<textarea name='note[body]' id = 'noteBody' rows='5'></textarea>
			
		<label for="noteDocumentNoteTypeID" class="formText"><?php echo _("Note Type:");?></label>
		<p id='span_noteType' class="form-group">
			<?php
				echo '						<select id="noteDocumentNoteTypeID" name="note[documentNoteTypeID]">';
				foreach($documentNoteType->allAsArray() as $display) {
					echo "						<option value='" . $display['documentNoteTypeID'] . "'>" . $display['shortName'] . "</option>";
				}

				echo '						</select>';
			?>
			<span id='span_newNoteType'>
				<button type="button" class="btn btn-sm link" onclick="newNoteType();"><?php echo _("add note type");?></button>
			</span>
		</p>
							
	</fieldset>
</div>
<?php
		}
?>
				<p class="actions">
					<input type='submit' value='<?php echo _("submit");?>' name='submitLicense' id ='submitLicense' class='btn primary'>
					<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='btn secondary'>
				</p>

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

		if (is_array($license->getDocumentsWithoutParents('documentID',$documentID)) && count($license->getDocumentsWithoutParents('documentID',$documentID)) > 0) {
			$blockArchiveCheck = 'disabled';
		} else {
			$blockArchiveCheck = '';
		}

		//if effective date isn't set, set it to today's date
		if (($document->effectiveDate == "0000-00-00") || ($document->effectiveDate == "")){
			$effectiveDate = format_date(date("m/d/Y"));
		}else{
			$effectiveDate=format_date($document->effectiveDate);
		}
		//if revision date isn't set, set it to today's date
		if (($document->revisionDate == "0000-00-00") || ($document->revisionDate == "")){
			$revisionDate = format_date(date("m/d/Y"));
		} else {
			$revisionDate = format_date($document->revisionDate);
		}

		if (($document->expirationDate) && ($document->expirationDate != '0000-00-00')){
			$archiveChecked = 'checked';
		}else{
			$archiveChecked = '';
		}

 		?>
		<div id='div_uploadDoc'>
		<form id="uploadDoc" action="ajax_processing.php?action=submitDocument" method="POST" enctype="multipart/form-data">
		<input type='hidden' id='licenseID' name='licenseID' value='<?php echo $licenseID; ?>'>
		<input type='hidden' id='documentID' name='documentID' value='<?php echo $documentID; ?>'>
		<h2><?php echo _("Document Upload");?></h2>
		<p class='error' id='span_errors'></p>

		<div class="block-form">
		
		<label for="revisionDate" class="formText"><?php echo _("Last Document Revision:");?></label>
		<p id='span_error_revisionDate' class='error'></p>
		
		<input type='hidden' id="effectiveDate" name='effectiveDate' value='<?php echo $effectiveDate; ?>' />
		<input class='date-pick' id='revisionDate' name='revisionDate' value='<?php echo $revisionDate; ?>' aria-describedby='span_error_revisionDate' />
		
		<label for="docTypeID" class="formText"><?php echo _("Document Type:");?></label>
		<p id='span_error_documentTypeID' class='error'></p>
		
		<p id='span_documentType'>
		<select name='docTypeID' id='docTypeID' aria-describedby='span_error_documentTypeID'>
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
		</p>
		
		<p id='span_newDocumentType'><a href="javascript:newDocumentType();"><?php echo _("add document type");?></a></p>
		

<!--

		<tr>
		<td style='text-align:right;vertical-align:top;'><label for="documentType" class="formText">Parent:</label></td>
		<td>
		<div>
		<select name='parentDocumentID' id='parentDocumentID' style='width:185px;'>
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
		</div>
		</td>
		</tr>

-->

		
		<label for="shortName" class="formText"><?php echo _("Name:");?></label>
		<p id='span_error_shortName' class='error'></p>

		<textarea name='shortName' id = 'shortName' rows='2'><?php echo $document->shortName; ?></textarea>

		<label for="upload_button" class="formText"><?php echo _("File:");?></label>

		<?php

		//if editing
		if ($documentID){
			echo "<p id='div_uploadFile'>" . $document->documentURL . "<p><a href='javascript:replaceFile();'>" . _("replace with new file") . "</a></p>";
			echo "<input type='hidden' id='upload_button' name='upload_button' value='" . $document->documentURL . "'></p>";

		//if adding
		}else{
			echo "<p id='div_uploadFile'><input type='file' name='upload_button' id='upload_button' aria-describedby='div_file_message'></p>";
		}


		?>
		<p id='div_file_message' class='wide indent'></p>
		
		
		<?php if (($document->parentDocumentID == "0") || ($document->parentDocumentID == "")){ ?>
			<p class="checkbox indent">
			<label for="archiveInd" class="formText" id="archiveDummyLabel"><?php echo _("Archived");?></label>
				<?php
				if ($_GET['isArchived'] == 1) {
				?>
					<input type='checkbox' name='archiveDummy' checked="checked" disabled="disabled" aria-labelledby="archiveDummyLabel" />
					<input type="hidden" id="archiveInd" name="archiveInd" value="1" />
				<?php
				} else {
				?>
					<input type='checkbox' id='archiveInd' name='archiveInd' <?php echo $archiveChecked; ?> <?php echo $blockArchiveCheck; ?> />
				<?php
				}
				?>
			</p>
		<?php } ?>

		<p class="actions">
			<input type='submit' value='<?php echo _("submit");?>' name='submitDocument' id='submitDocument' class='btn primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='btn secondary'>
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
		<p class='error' id='span_errors'></p>
		
		<div class="block-form">
			<input type='hidden' name='documentID' id='documentID' value='<?php echo $documentID; ?>' />
			<p>
				<label for="expirationDate"><?php echo _("Archive Date:");?></label>
				<input class='date-pick' id='expirationDate' name='expirationDate' value='<?php echo format_date(date); ?>' />
			</p>
			<p class="actions">
				<a href='javascript:void(0)' name='submitArchive' id='submitArchive' class='submit-button'><?php echo _("Continue");?></a>
			</p>
				
		</div>
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
		<p class='error' id='span_errors'></p>
		
		<label for="documentID" class="formText"><?php echo _("For Document:");?></label>  
		<p id='span_error_documentID' class='error'></p>
		<select name='documentID' id='documentID'>
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
		
		
		<label for="shortName" class="formText"><?php echo _("Terms Tool Resource:");?></label>  
		<p id='span_error_shortName' class='error'></p>
		<input id='shortName' name='shortName' value='<?php echo $sfxProvider->shortName; ?>' />
		
		<p class="actions">
			<input type='submit' value='<?php echo _("submit");?>' name='submitSFX' id='submitSFX' class='btn primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='btn secondary'>
		</p>

		</div>


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
		
		<table class='dataTable table-border table-striped'>
					<tr>
						<th id="sigNameLabel"><?php echo _("Signer Name");?></th>
						<th id="sigDateLabel"><?php echo _("Date");?></th>
						<th id="sigTypeLabel"><?php echo _("Type");?></th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>

		<?php

			if ($signatureID == ""){
				echo "<input type='hidden' name='signatureID' id='signatureID' value='' />";
			}

			$display = array();
			foreach ($document->getSignaturesForDisplay() as $display) {
				echo "<tr>";

				//used for in-line editing (since this is already a form, can't make another form to edit sigs!)
				if ($signatureID == $display['signatureID']){
					echo "<td><input type='text' id='signerName' value=\"" . $display['signerName'] . "\" aria-labelledby='sigNameLabel' /></td>";
					echo "<td><input class='date-pick' id='signatureDate' name='signatureDate' aria-labelledby='sigDateLabel' value=\"" . format_date($display['signatureDate']) . "\" /></td>";
					echo "<td><span id='span_signatureType'><select id='signatureTypeID' name='signatureTypeID' aria-labelledby='sigTypeLabel'>";

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
					echo "<td><a href='javascript:void(0)' id='commitUpdate' name='commitUpdate'>" . _("commit update") . "</a></td>";
					echo "<input type='hidden' name='signatureID' id='signatureID' value='" . $display['signatureID'] . "' />";
					echo "<td>&nbsp;</td>";


				}else{
					echo "<td>" . $display['signerName'] . "</td>";
					echo "<td>" . format_date($display['signatureDate']) . "</td>";
					echo "<td>" . $display['signatureTypeName'] . "</td>";
					if ($signatureID){
						echo "<td>&nbsp;</td>";
						echo "<td>&nbsp;</td>";
					}else{
						echo "<td><a href='javascript:updateSignatureForm(\"" . $display['signatureID'] . "\");'>" . _("edit") . "</a></td>";
						echo "<td><a href='javascript:removeSignature(\"" . $display['signatureID'] . "\");'>" . _("remove") . "</a></td>";
					}
				}

				echo "</tr>";

			}

			if ($signatureID == ""){
				echo "<tr>";
				echo "<td><input type='text' id='signerName' aria-labelledby='sigNameLabel' /></td>";
				echo "<td><input class='date-pick' id='signatureDate' name='signatureDate' aria-labelledby='sigDateLabel' /></td>";
				echo "<td><span id='span_signatureType'><select id='signatureTypeID' name='signatureTypeID' aria-labelledby='sigTypeLabel'>";
				$stdisplay = array();
				$signatureType = new SignatureType();

				foreach($signatureType->allAsArray() as $stdisplay) {
					echo "<option value='" . $stdisplay['signatureTypeID'] . "'>" . $stdisplay['shortName'] . "</option>";
				}

				echo "</select></span></td>";
				echo "<td><a href='javascript:void(0);' id='commitUpdate' name='commitUpdate'>" . _("add") . "</a></td>";
				echo "<td>&nbsp;</td>";
				echo "</tr>";
			}

		?>

		</table>
		</td>
		</tr>
		<tr><td style='text-align:center;width:100%;'><br /><br /><a href='#' onclick='window.parent.updateDocuments();  window.parent.updateArchivedDocuments(); myCloseDialog(); return false' class='cancel-button secondary'><?php echo _("Close");?></a></td></tr>
		</table>
		<input type="hidden" id='documentID' name='documentID' value='<?php echo $documentID; ?>'>

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
		<div id='div_expressionForm'>
		<input type='hidden' id='expressionID' name='expressionID' value='<?php echo $expressionID; ?>'>

		<h2 class='headerText'><?php echo _("Expressions");?></h2>
		<p id='span_errors'></p>
		
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
		<span id='span_expressionType'>
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
		</span>
		
		<p id='span_newExpressionType'><a href="javascript:newExpressionType();"><?php echo _("add expression type");?></a></p>

		
		<div id='tr_Qualifiers' <?php if (count($expressionQualifierArray) == 0) echo "style='display:none;'"; ?>>
		
		<label for="qualifierID" class="formText"><?php echo _("Qualifier:");?></label>
		<div id='div_Qualifiers'>

		<ul class="unstyled">
		<?php
		if (is_array($expressionQualifierArray) && count($expressionQualifierArray) > 0) {
			//loop over all qualifiers available for this expression type
			foreach ($expressionQualifierArray as $expressionQualifierIns){
				
				if (in_array($expressionQualifierIns->qualifierID,$expressionQualifierProfileArray)){
					echo "<li><label><input class='check_Qualifiers' type='checkbox' name='" . $expressionQualifierIns->qualifierID . "' id='" . $expressionQualifierIns->qualifierID . "' value='" . $expressionQualifierIns->qualifierID . "' checked />   " . $expressionQualifierIns->shortName . "</label></li>\n";
				}else{
					echo "<li><label><input class='check_Qualifiers' type='checkbox' name='" . $expressionQualifierIns->qualifierID . "' id='" . $expressionQualifierIns->qualifierID . "' value='" . $expressionQualifierIns->qualifierID . "' />   " . $expressionQualifierIns->shortName . "</label></li>\n";
				}
			}
		}
		?>
		</ul>


		</div>

		</div>

		
		<label for="documentText" class="formText"><?php echo _("Document Text:");?></label>
		<textarea name='documentText' id = 'documentText' rows='10'><?php echo $expression->documentText; ?></textarea>
		
		<p class="actions">
			<input type='submit' value='<?php echo _("submit");?>' name='submitExpression' id='submitExpression' class='btn primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='btn secondary'>

		</p>
		</div>
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
		
		<h2 class='headerText'><?php echo ucfirst($noteType) . ' ' . _("Notes");?></h2>
		<p><b><?php echo _("For Document Text:");?></b>  <?php echo $documentText; ?></p>
		
		
<!-- TODO: eliminate tables -->
		<table class='dataTable'>
		<tr>
		<th style='width:19px;'>&nbsp;</th>
		<th id='noteLabel'><h3><?php printf(_("%s Notes"), ucfirst($noteType));?></h3></th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		</tr>

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
					echo "<td><textarea name='expressionNote' id = 'expressionNote' cols='50' rows='4' aria-labelledby='noteLabel'>" .  $expressionNote->note . "</textarea></td>";
					echo "<td><a href='javascript:void(0)' id='commitUpdate' name='commitUpdate'>" . _("commit update") . "</a></td>";
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
						//calculate which arrows to show for reordering
						if ($rowNumber == "1"){
							echo "<td style='text-align:right;'><a href='javascript:reorder(\"" . $expressionNote->expressionNoteID . "\", \"" . $expressionNote->displayOrderSeqNumber . "\",\"down\");'><img src='images/arrowdown.png' border=0></a></td>";
						}else if($rowNumber == $rowCount){
							echo "<td><a href='javascript:reorder(\"" . $expressionNote->expressionNoteID . "\", \"" . $expressionNote->displayOrderSeqNumber . "\",\"up\");'><img src='images/arrowup.png' border=0></a></td>";
						}else{
							echo "<td><a href='javascript:reorder(\"" . $expressionNote->expressionNoteID . "\", \"" . $expressionNote->displayOrderSeqNumber . "\",\"up\");'><img src='images/arrowup.png' border=0></a>&nbsp;<a href='javascript:reorder(\"" . $expressionNote->expressionNoteID . "\", \"" . $expressionNote->displayOrderSeqNumber . "\",\"down\");'><img src='images/arrowdown.png' border=0></a></td>";
						}
						echo "<td>" .  nl2br($expressionNote->note) . "</td>";
						echo "<td><a href='javascript:updateExpressionNoteForm(\"" . $expressionNote->expressionNoteID . "\");'>" . _("edit") . "</a></td>";
						echo "<td><a href='javascript:removeExpressionNote(\"" . $expressionNote->expressionNoteID . "\");'>" . _("remove") . "</a></td>";
					}

				}


				echo "</tr>";

			}
			$rowNumber++;

			if ($expressionNoteID == ""){
				echo "<tr>";
				echo "<td>&nbsp;</td>";
				echo "<td><textarea name='expressionNote' id = 'expressionNote' cols='50' rows='4' aria-labelledby='noteLabel'></textarea></td>";
				echo "<td><a href='javascript:addExpressionNote();'>" . _("add") . "</a></td>";
				echo "<td>&nbsp;</td>";
				echo "</tr>";
			}
		?>


		</table>
		<p class="actions">
			<a href='#' onclick='myCloseDialog();  window.parent.<?php if ($_GET['org'] == "compare") { echo "updateSearch()"; } else { echo "updateExpressions()"; } ?>; return false' class='btn secondary'><?php echo _("Close");?></a>
		</p>
		
		<input type="hidden" id='documentID' name='documentID' value='<?php echo $documentID; ?>'>
		<input type="hidden" id='org' name='org' value='<?php echo $_GET['org']; ?>'>

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
		<div id='div_attachmentForm'>
		<form id='attachmentForm'>
		<input type='hidden' id='attachmentID' name='attachmentID' value='<?php echo $attachmentID; ?>'>
		<input type='hidden' id='licenseID' name='licenseID' value='<?php echo $_GET['licenseID']; ?>'>
		
		<h2 class='headerText'><?php echo _("Attachments");?></h2>
		<p id='span_errors'></p>
		
		<div class="block-form">
		<label for="sentDate" class="formText"><?php echo _("Date:");?></label>
		<input class='date-pick' id='sentDate' name='sentDate' value='<?php echo $sentDate; ?>' />

		<label for="attachmentText" class="formText"><?php echo _("Details:");?></label>
		<textarea name='attachmentText' id = 'attachmentText' rows='10'><?php echo $attachment->attachmentText; ?></textarea>
	
		<label for="upload_attachment_button" class="formText"><?php echo _("Attachments:");?></label>
		<p id='div_file_message' class="error"></p>
		<p id='div_file_success' class="success"></p>
		<?php

		//if editing
		if ($attachmentID){
			$attachmentFile = new AttachmentFile();

			foreach ($attachment->getAttachmentFiles() as $attachmentFile){
				echo "<p id='div_existing_" . $attachmentFile->attachmentFileID . "'>" . $attachmentFile->attachmentURL . "  <a href='javascript:removeExistingAttachment(\"" . $attachmentFile->attachmentFileID . "\");' class='small'>" . _("remove") . "</a></p>";
			}

			echo "<p id='div_uploadFile'><input type='file' name='upload_attachment_button' id='upload_attachment_button' aria-describedby='div_file_message div_file_success'></p>";

		//if adding
		}else{
			echo "<p id='div_uploadFile'><input type='file' name='upload_attachment_button' id='upload_attachment_button' aria-describedby='div_file_message div_file_success'></p>";
		}


		?>
		
		
		<p class="actions">
			<input type='submit' value='<?php echo _("submit");?>' name='submitAttachment' id='submitAttachment' class='btn primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="window.parent.updateAttachments(); myCloseDialog()" class='btn secondary'>
		</p>
		
		</div>

		<script type="text/javascript" src="js/forms/attachmentForm.js?random=<?php echo rand(); ?>"></script>
		</form>
		</div>


		<?php

        break;

	//form to add/edit notes
    case 'getNoteForm':
		//note ID sent in for updates
		if (isset($_GET['documentNoteID'])) {
			 $documentNoteID = $_GET['documentNoteID'];
		} else {
			 $documentNoteID = '';
		}

		$note = new DocumentNote(new NamedArguments(array('primaryKey' => $documentNoteID)));
		$documentNoteType = new DocumentNoteType(new NamedArguments(array('primaryKeyName'=>'documentNoteTypeID')));
		$license = new License(new NamedArguments(array('primaryKey'=>$_GET['licenseID'])));
		$documents = $license->getAllDocumentNamesAsIndexedArray();
		?>
		<div id='div_noteForm'>
		<form id='noteForm'>
		<input type='hidden' id='documentNoteID' name='documentNoteID' value='<?php echo $documentNoteID; ?>'>
		<input type='hidden' id='licenseID' name='licenseID' value='<?php echo $_GET['licenseID']; ?>'>
		
		<h2 class='headerText'><?php echo _("Notes");?></h2>
		<p id='span_errors'></p>
		
		<div class="block-form">
		<label for="notebody" class="formText"><?php echo _("Note:");?></label>
		<textarea name='notebody' id = 'notebody' rows='10'><?php echo $note->body; ?></textarea>
	
		<label for="documentNoteTypeID" class="formText"><?php echo _("Note Type:");?></label>
		<p id='span_noteType'>

<?php
		echo '			<select id="documentNoteTypeID" name="documentNoteTypeID">';
		foreach($documentNoteType->allAsArray() as $display) {
			if ($note->documentNoteTypeID == $display['documentNoteTypeID']){
				echo "		<option value='" . $display['documentNoteTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "		<option value='" . $display['documentNoteTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}
		echo '			</select>';
?>
					</p>
					
					<p id='span_newNoteType'><a href="javascript:newNoteType();"><?php echo _("add note type");?></a></p>
					
		<label for="documentID" class="formText"><?php echo _("Document:");?></label>
<?php
		echo "<select id='documentID' name='documentID'>
			<option value='0'>" . _("All Documents") . "</option>";
		foreach($documents as $display) {
			if ($note->documentID == $display['documentID']){
				echo "	<option value='" . $display['documentID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "	<option value='" . $display['documentID'] . "'>" . $display['shortName'] . "</option>";
			}
		}
		echo '		</select>';
?>
	
	
			<p class="actions">
				<input type='submit' value='<?php echo _("submit");?>' name='submitNote' id='submitNote' class='btn primary' />
				<input type='button' value='<?php echo _("cancel");?>' onclick="window.parent.updateNotes();myCloseDialog();" class='btn secondary'>
			</p>
		</div>



		<script type="text/javascript" src="js/forms/noteForm.js?random=<?php echo rand(); ?>"></script>
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
		<div id='div_updateForm' class="form-grid">
		
		<h2 class='headerText'><?php echo _("Edit");?></h2>
		<p id='span_errors' class='error'></p>
		
		<?php
		echo "<input type='text' id='updateVal' name='updateVal' value='" . $instance->shortName . "' aria-label='"._('Name')."' /> <a href='javascript:updateData(\"" . $className . "\", \"" . $updateID . "\");' id='updateButton' class='link' aria-description='".$instance->shortName."'>" . _("Edit") . "</a>";
		?>


		<p class="actions"><a href='#' onclick='myCloseDialog(); return false' id='closeButton' class='btn secondary'><?php echo _("Close");?></a></p>

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
		<div id='div_updateForm' class="block-form">
			<h2><?php echo $update;?></h2>
			<p id='span_errors' class='error'></p>
			
			<p>
				<label for='loginID'><?php echo _("Login ID");?></label>
				<input type='text' id='loginID' name='loginID' value='<?php echo $loginID; ?>' />
			</p>
			<p>
				<label for='firstName'><?php echo _("First Name");?></label>
				<input type='text' id='firstName' name='firstName' value="<?php if (isset($updateUser)) echo $updateUser->firstName; ?>" />
			</p>
			<p>
				<label for='lastName'><?php echo _("Last Name");?></label>
				<input type='text' id='lastName' name='lastName' value="<?php if (isset($updateUser)) echo $updateUser->lastName; ?>" />
			</p>
			<p>
				<label for='privilegeID'><?php echo _("Privilege");?></label>
				
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
	</p>
	<ul>
					<li><?php echo _("Add/Edit users can add, edit, or remove licenses and associated fields");?></li>
					<li><?php echo _("Admin users have access to the Admin page and the SFX tab.");?><li>
					<li><?php echo _("View only users can view all license information, including the license pdf");?></li>
				</ul>

		<?php
		//if not configured to use SFX, hide the Terms Tool Report
		if ($util->useTermsTool()) {
		?>
			<p>
				<label for='emailAddressForTermsTool'><?php echo _("Terms Tool Email");?></label>
				<input type='text' id='emailAddressForTermsTool' name='emailAddressForTermsTool' value='<?php if (isset($updateUser)) echo $updateUser->emailAddressForTermsTool; ?>' aria-describedby="emailAddressForTermsToolInstructions"/>
			</p>
			<ul class="form-instructions" id="emailAddressForTermsToolInstructions">
				<li><?php echo _("Enter email address if you wish this user to receive email notifications when the terms tool box is checked on the Expressions tab."); ?></li>
				<li><?php echo _("Leave this field blank if the user shouldn't receive emails."); ?></li>
			</ul>

		<?php } else { echo "<input type='hidden' id='emailAddressForTermsTool' name='emailAddressForTermsTool' value='' />"; }?>

		<p class="actions">
		<input type='submit' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitUserData("<?php echo $loginID; ?>");' class='btn primary'>
	
		<input type='button' value='<?php echo _("Close");?>' onclick="myCloseDialog(); return false" class='btn secondary'></td>
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
		<div id='div_updateForm' class="block-form">
		<input type='hidden' name='expressionTypeID' id='expressionTypeID' value='<?php echo $expressionTypeID; ?>' />
		
		<h2 class='headerText'><?php echo $update;?></h2>
		
		<label for='shortName'><?php echo _("Expression Type");?></label>
		<input type='text' id='shortName' name='shortName' value='<?php if (isset($expressionType)) echo $expressionType->shortName; ?>'/>
	
		<label for='noteType'><?php echo _("Note Type");?></label>
		
		<select name='noteType' id='noteType'>
		<option value='Internal' <?php if ((isset($expressionType)) && ($expressionType->noteType == 'Internal')) echo "selected"; ?>><?php echo _("Internal");?></option>
		<option value='Display' <?php if ((isset($expressionType)) && ($expressionType->noteType == 'Display')) echo "selected"; ?>><?php echo _("Display");?></option>
		</select>
		
		<p class='form-text'><?php echo _("* Note type of display allows for terms tool use");?></p>
	
		
		<p class="actions">
			<input type='submit' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitExpressionType();' class='btn primary'>
			<input type='button' value='<?php echo _("close");?>' onclick="myCloseDialog(); return false" class='btn secondary'>
		</p>
		
		</div>


		<?php

		break;




	//qualifier on admin.php screen - since qualifiers also have expression types
	case 'getQualifierForm':
		if (isset($_GET['qualifierID'])) $qualifierID = $_GET['qualifierID']; else $qualifierID = '';

		if ($qualifierID){
			$update=_('Edit');
			$qualifier = new Qualifier(new NamedArguments(array('primaryKey' => $qualifierID)));
		}else{
			$update=_('Add');
		}


		?>
		<div id='div_updateForm' class="block-form">
		<input type='hidden' name='qualifierID' id='qualifierID' value='<?php echo $qualifierID; ?>' />
	
		<h2 class='headerText'><?php echo $update . _(" Qualifier");?></h2>
		
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
			<input type='submit' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitQualifier();' id='submitQualifier' class='btn primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false" class='btn secondary'>
		</p>
		</div>


		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#shortName').keyup(function(e) {

				   if(e.keyCode == 13) {
					   submitQualifier();
				   }
        	});

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
