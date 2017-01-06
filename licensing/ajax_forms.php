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
		<table class="thickboxTable">
		<tr>
		<td colspan='2' id='license-form-title'><span class='headerText'><?php echo _("License");?></span><br /></td>
		</tr>

		<tr>
		<td colspan='2' class='below-title'><label for="shortName" class="formText"><?php echo _("License Name:");?></label>  <span id='span_error_licenseShortName' class='errorText'></span><br /><textarea name='licenseShortName' id = 'licenseShortName' cols='38' rows='2'><?php echo $license->shortName; ?></textarea></td>
		</tr>


		<tr>
		<td colspan='2'><label for="licenseOrganizationID" class="formText"><?php echo _("Publisher / Provider:");?></label>  <span id='span_error_organizationName' class='errorText'></span><br />
		<input type='textbox' id='organizationName' name='organizationName' value="<?php echo $organizationName; ?>" style='width:100%;' />
		<input type='hidden' id='licenseOrganizationID' name='licenseOrganizationID' value='<?php echo $license->organizationID; ?>'>
		<span id='span_error_organizationNameResult' class='errorText'></span>
		<br />
		</td>
		</tr>


		<tr>
		<td colspan='2'><label for="consortiumID" class="formText"><?php echo _("Consortium:");?></label><br />
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
			echo "<span style='color:red'>"._("There was an error processing this request - please verify configuration.ini is set up for organizations correctly and the database and tables have been created.")."</span>";
		}
		?>
		</span>

		<?php
		$config = new Configuration;

		//if the org module is not installed allow to add consortium from this screen
		if (($config->settings->organizationsModule == 'N') || (!$config->settings->organizationsModule)){
		?>
		<br />
		<span id='span_newConsortium'><a href="javascript:newConsortium();"><?php echo _("add consortium");?></a></span>
		<?php } ?>

		</td>
		</tr>

		<tr style="vertical-align:middle;">
		<td style="width:60px;"><input type='button' value='<?php echo _("submit");?>' name='submitLicense' id ='submitLicense' class='submit-button'></td>
		<td><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove()" class='cancel-button'></td>
		</tr>
		</table>



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
		<input type='hidden' id='licenseID' name='licenseID' value='<?php echo $licenseID; ?>'>
		<input type='hidden' id='documentID' name='documentID' value='<?php echo $documentID; ?>'>
		<table class="thickboxTable" style="width:310px;">
		<tr>
		<td colspan='2'><span class='headerText'><?php echo _("Document Upload");?></span><br /><span id='span_errors'></span><br /></td>
		</tr>
		<tr>
		<td style='text-align:right;vertical-align:top;'><label for="effectiveDate" class="formText"><?php echo _("Effective Date:");?></label><br /><span id='span_error_effectiveDate' class='errorText'></span></td>
		<td>
		<input class='date-pick' id='effectiveDate' name='effectiveDate' style='width:80px' value='<?php echo $effectiveDate; ?>' />
		</td>
		</tr>

		<tr>
		<td style='text-align:right;vertical-align:top;'><label for="documentType" class="formText"><?php echo _("Document Type:");?></label><br /><span id='span_error_documentTypeID' class='errorText'></span></td>
		<td>
		<span id='span_documentType'>
		<select name='documentTypeID' id='documentTypeID' style='width:185px;'>
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
		</span>
		<br />
		<span id='span_newDocumentType'><a href="javascript:newDocumentType();"><?php echo _("add document type");?></a></span>
		<br />
		</td>
		</tr>



		<tr>
		<td style='text-align:right;vertical-align:top;'><label for="documentType" class="formText"><?php echo _("Parent:");?></label></td>
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

		<tr>
		<td style='text-align:right;vertical-align:top;'><label for="shortName" class="formText"><?php echo _("Name:");?></label><br /><span id='span_error_shortName' class='errorText'></span></td>
		<td>
		<textarea name='shortName' id = 'shortName' cols='28' rows='2' style='width:185px;'><?php echo $document->shortName; ?></textarea>
		</td>
		</tr>
		<tr>
		<td style='text-align:right;vertical-align:top;'><label for="uploadDocument" class="formText"><?php echo _("File:");?></label></td>
		<td>
		<?php

		//if editing
		if ($documentID){
			echo "<div id='div_uploadFile'>" . $document->documentURL . "<br /><a href='javascript:replaceFile();'>"._("replace with new file")."</a>";
			echo "<input type='hidden' id='upload_button' name='upload_button' value='" . $document->documentURL . "'></div>";

		//if adding
		}else{
			echo "<div id='div_uploadFile'><input type='file' name='upload_button' id='upload_button'></div>";
		}


		?>
		<span id='div_file_message'></span>
		</td>
		</tr>

		<?php if (($document->parentDocumentID == "0") || ($document->parentDocumentID == "")){ ?>
		<tr>
		<td style='text-align:right;vertical-align:top;'><label for="archiveInd" class="formText"><?php echo _("Archived:");?></label></td>
		<td><input type='checkbox' id='archiveInd' name='archiveInd' <?php echo $archiveChecked; ?> />
		</td>
		</tr>
		<?php } ?>

		<tr style="vertical-align:middle;">
		<td style="width:60px;"><input type='button' value='<?php echo _("submit");?>' name='submitDocument' id='submitDocument' class='submit-button'></td>
		<td><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove()" class='cancel-button'></td>
		</tr>
		</table>
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
		<table class="thickboxTable" style="width:200px;">
		<tr>
		<td><span class='headerText'><?php echo _("Archive Document Date");?></span><br /><br /><span id='span_errors'></span></td>
		</tr>
		<tr>
		<td>
		<input type='hidden' name='documentID' id='documentID' value='<?php echo $documentID; ?>' />
		<?php echo _("Archive Date:");?>  <input class='date-pick' id='expirationDate' name='expirationDate' style='width:80px' value='<?php echo format_date(date); ?>' />
		</td>
		</tr>
		<tr><td style='text-align:center;width:100%;'><br /><br /><a href='javascript:void(0)' name='submitArchive' id='submitArchive'><?php echo _("Continue");?></a></td></tr>
		</table>


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

		<table class="thickboxTable" style="width:240px;">
		<tr>
		<td colspan='2'><span class='headerText'><?php echo _("Terms Tool Resource Link");?></span><br /><span id='span_errors'></span><br /></td>
		</tr>


		<tr>
		<td colspan='2'><label for="documentID" class="formText"><?php echo _("For Document:");?></label>  <span id='span_error_documentID' class='errorText'></span><br />
		<select name='documentID' id='documentID' style='width:200px;'>
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
		</td>
		</tr>

		<tr>
		<td>
		<label for="shortName" class="formText"><?php echo _("Terms Tool Resource:");?></label>  <span id='span_error_shortName' class='errorText'></span><br />
		<input id='shortName' name='shortName' style='width:190px' value='<?php echo $sfxProvider->shortName; ?>' />
		</td>
		</tr>
		
		<tr>
		<td style="width:60px;"><input type='button' value='<?php echo _("submit");?>' name='submitSFX' id='submitSFX' class='submit-button'></td>
		<td><input type='button' value='<?php echo _("cancel");?>' onclick="window.parent.tb_remove()" class='cancel-button'></td>
		</tr>

		</table>


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
		<table class="thickboxTable" style="background-image:url('images/tbtitle.gif');width:100%;">
		<tr>
            <td><span class='headerText'><?php echo _("Signatures");?></span><br /><span id='span_errors' style='color:#F00;'></span></td>
		</tr>
		<tr>

		<table class='dataTable' style='width:448px;margin-left:2px;'>
		<tr>
		<th><?php echo _("Signer Name");?></th>
		<th><?php echo _("Date");?></th>
		<th><?php echo _("Type");?></th>
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
					echo "<td><input type='textbox' id='signerName' value=\"" . $display['signerName'] . "\" style='width:118px;' /></td>";
					echo "<td><input class='date-pick' id='signatureDate' name='signatureDate' style='width:80px' value=\"" . format_date($display['signatureDate']) . "\" /></td>";
					echo "<td><span id='span_signatureType'><select id='signatureTypeID' name='signatureTypeID'>";

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
					echo "<td><a href='javascript:void(0)' id='commitUpdate' name='commitUpdate'>"._("commit update")."</a></td>";
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
						echo "<td><a href='javascript:updateSignatureForm(\"" . $display['signatureID'] . "\");'>"._("edit")."</a></td>";
						echo "<td><a href='javascript:removeSignature(\"" . $display['signatureID'] . "\");'>"._("remove")."</a></td>";
					}
				}

				echo "</tr>";

			}


			if ($signatureID == ""){
				echo "<tr>";
				echo "<td><input type='text' id='signerName' style='width:118px;' /></td>";
				echo "<td><input class='date-pick' id='signatureDate' name='signatureDate' style='width:80px' /></td>";
				echo "<td><span id='span_signatureType'><select id='signatureTypeID' name='signatureTypeID'>";
				$stdisplay = array();
				$signatureType = new SignatureType();

				foreach($signatureType->allAsArray() as $stdisplay) {
					echo "<option value='" . $stdisplay['signatureTypeID'] . "'>" . $stdisplay['shortName'] . "</option>";
				}

				echo "</select></span></td>";
				echo "<td><a href='javascript:void(0);' id='commitUpdate' name='commitUpdate'>"._("add")."</a></td>";
				echo "<td>&nbsp;</td>";
				echo "</tr>";
			}

		?>

		</table>
		</td>
		</tr>
		<tr><td style='text-align:center;width:100%;'><br /><br /><a href='#' onclick='window.parent.tb_remove();  window.parent.updateDocuments();  window.parent.updateArchivedDocuments(); return false' class='cancel-button'><?php echo _("Close");?></a></td></tr>
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

		<table class="thickboxTable" style="width:340px;">
		<tr>
		<td colspan='2'><span class='headerText'><?php echo _("Expressions");?></span><br /><span id='span_errors'></span><br /></td>
		</tr>


		<tr>
		<td colspan='2'><label for="documentID" class="formText"><?php echo _("Document:");?></label><br />
		<select name='documentID' id='documentID' style='width:280px;'>
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
		</select><br />
		</td>
		</tr>


		<tr>
		<td colspan='2'><label for="expressionTypeID" class="formText"><?php echo _("Expression Type:");?></label><br />
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
		</span>&nbsp;&nbsp;
		<span id='span_newExpressionType'><a href="javascript:newExpressionType();"><?php echo _("add expression");?> type</a></span>

		</td>
		</tr>


		<tr id='tr_Qualifiers' <?php if (count($expressionQualifierArray) == 0) echo "style='display:none;'"; ?> >
		<td colspan='2'><label for="qualifierID" class="formText"><?php echo _("Qualifier:");?></label><br />
		<div id='div_Qualifiers'>

		<table>
		<?php
		$i=0;
		if (count($expressionQualifierArray) > 0){
			//loop over all qualifiers available for this expression type
			foreach ($expressionQualifierArray as $expressionQualifierIns){
				$i++;
				if(($i % 2)==1){
					echo "<tr>\n";
				}
				if (in_array($expressionQualifierIns->qualifierID,$expressionQualifierProfileArray)){
					echo "<td><input class='check_Qualifiers' type='checkbox' name='" . $expressionQualifierIns->qualifierID . "' id='" . $expressionQualifierIns->qualifierID . "' value='" . $expressionQualifierIns->qualifierID . "' checked />   " . $expressionQualifierIns->shortName . "</td>\n";
				}else{
					echo "<td><input class='check_Qualifiers' type='checkbox' name='" . $expressionQualifierIns->qualifierID . "' id='" . $expressionQualifierIns->qualifierID . "' value='" . $expressionQualifierIns->qualifierID . "' />   " . $expressionQualifierIns->shortName . "</td>\n";
				}
				if(($i % 2)==0){
					echo "</tr>\n";
				}
			}

			if(($i % 2)==1){
				echo "<td>&nbsp;</td></tr>\n";
			}
		}
		?>
		</table>


		</div>
		</td>
		</tr>

		<tr>
		<td colspan='2'><label for="documentText" class="formText"><?php echo _("Document Text:");?></label><br /><textarea name='documentText' id = 'documentText' cols='48' rows='10'><?php echo $expression->documentText; ?></textarea></td>
		</tr>

		<tr style="vertical-align:middle;">
		<td style="width:60px;"><input type='button' value='<?php echo _("submit");?>' name='submitExpression' id='submitExpression' class='submit-button'></td>
		<td><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove()" class='cancel-button'></td>
		</tr>
		</table>
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
		<table class="thickboxTable" style="width:420px;">
		<tr>
		<td><span class='headerText'><?php echo ucfirst($noteType); ?> <?php echo _("Notes");?></span><br />
		<b><?php echo _("For Document Text:");?></b>  <?php echo $documentText; ?><br />
		<span id='span_errors' style='color:#F00;'></span>
		<br /><br /></td>
		</tr>
		<tr>
		<td>

		<table class='dataTable' style='width:420px;'>
		<tr>
		<th style='width:19px;'>&nbsp;</th>
		<th><b><?php echo ucfirst($noteType); ?> <?php echo _("Notes");?></b></th>
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
					echo "<td><textarea name='expressionNote' id = 'expressionNote' cols='50' rows='4'>" .  $expressionNote->note . "</textarea></td>";
					echo "<td><a href='javascript:void(0)' id='commitUpdate' name='commitUpdate'>"._("commit update")."</a></td>";
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
						echo "<td><a href='javascript:updateExpressionNoteForm(\"" . $expressionNote->expressionNoteID . "\");'>"._("edit")."</a></td>";
						echo "<td><a href='javascript:removeExpressionNote(\"" . $expressionNote->expressionNoteID . "\");'>"._("remove")."</a></td>";
					}

				}


				echo "</tr>";

			}
			$rowNumber++;

			if ($expressionNoteID == ""){
				echo "<tr>";
				echo "<td>&nbsp;</td>";
				echo "<td><textarea name='expressionNote' id = 'expressionNote' cols='50' rows='4'></textarea></td>";
				echo "<td><a href='javascript:addExpressionNote();'>"._("add")."</a></td>";
				echo "<td>&nbsp;</td>";
				echo "</tr>";
			}
		?>


		</table>
		</td>
		</tr>
		<tr><td style='width:100%;'><br /><br /><a href='#' onclick='window.parent.tb_remove();  window.parent.<?php if ($_GET['org'] == "compare") { echo "updateSearch()"; } else { echo "updateExpressions()"; } ?>; return false' class='cancel-button'><?php echo _("Close");?></a></td></tr>
		</table>
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
		<table class="thickboxTable" style="width:300px;">
		<tr>
		<td colspan='2'><span class='headerText'><?php echo _("Attachments");?></span><br /><span id='span_errors'></span><br /></td>
		</tr>

		<tr>
		<td colspan='2'><label for="sentDate" class="formText"><?php echo _("Date:");?></label><br />

		<input class='date-pick' id='sentDate' name='sentDate' style='width:80px' value='<?php echo $sentDate; ?>' />

		</tr>

		<tr>
		<td colspan='2'><label for="attachmentText" class="formText"><?php echo _("Details:");?></label><br /><textarea name='attachmentText' id = 'attachmentText' cols='45' rows='10'><?php echo $attachment->attachmentText; ?></textarea></td>

		</td>

		</tr>
		<tr>
		<td colspan='2' style="width:300px;"><label for="upload_attachment_button" class="formText"><?php echo _("Attachments:");?></label><span id='div_file_message'></span>
		<br /><span id='div_file_success'></span>
		<?php

		//if editing
		if ($attachmentID){
			$attachmentFile = new AttachmentFile();

			foreach ($attachment->getAttachmentFiles() as $attachmentFile){
				echo "<div id='div_existing_" . $attachmentFile->attachmentFileID . "'>" . $attachmentFile->attachmentURL . "  <a href='javascript:removeExistingAttachment(\"" . $attachmentFile->attachmentFileID . "\");' class='smallLink'>"._("remove")."</a><br /></div>";
			}

			echo "<div id='div_uploadFile'><input type='file' name='upload_attachment_button' id='upload_attachment_button'></div><br />";

		//if adding
		}else{
			echo "<div id='div_uploadFile'><input type='file' name='upload_attachment_button' id='upload_attachment_button'></div><br />";
		}


		?>
		</td>
		</tr>

		<tr style="vertical-align:middle;">
		<td style="width:60px;"><input type='button' value='<?php echo _("submit");?>' name='submitAttachment' id='submitAttachment'class='submit-button'></td>
		<td><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove();window.parent.updateAttachments();" class='cancel-button'></td>
		</tr>
		</table>



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
		<table class="thickboxTable" style="width:400px;">
		<tr>
		<td colspan='3'><span class='headerText'><?php echo _("Update");?></span><br /><span id='span_errors' style='color:#F00;'></span><br /></td>
		</tr>
		<tr>
		<td>
		<?php
		echo "<input type='text' id='updateVal' name='updateVal' value='" . $instance->shortName . "' style='width:190px;'/></td><td><a href='javascript:updateData(\"" . $className . "\", \"" . $updateID . "\");' id='updateButton' class='submit-button'>"._("update")."</a>";
		?>


		</td>
		<td><a href='#' onclick='window.parent.tb_remove(); return false' class='cancel-button'><?php echo _("close");?></a></td>
		</tr>
		</table>
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
			$update=_('Update');
			$updateUser = new User(new NamedArguments(array('primaryKey' => $loginID)));
		}else{
			$update=_('Add New');
		}

		$util = new Utility();

		?>
		<div id='div_updateForm'>
		<table class="thickboxTable" style="width:285px;padding:2px;">
		<tr><td colspan='3'><span class='headerText'><?php echo $update.' '. _("User"); ?></span><br /><span id='span_errors' style='color:#F00;'></span><br /></td></tr>
            <tr><td colspan='2' style='width:135px;'><label for='loginID'><b><?php echo _("Login ID");?></b></label></td><td><input type='text' id='loginID' name='loginID' value='<?php echo $loginID; ?>' style='width:140px;' /></td></tr>
            <tr><td colspan='2'><label for='firstName'><b><?php echo _("First Name");?></b></label></td><td><input type='text' id='firstName' name='firstName' value="<?php if (isset($updateUser)) echo $updateUser->firstName; ?>" style='width:140px;' /></td></tr>
            <tr><td colspan='2'><label for='lastName'><b><?php echo _("Last Name"); ?></b></label></td><td><input type='text' id='lastName' name='lastName' value="<?php if (isset($updateUser)) echo $updateUser->lastName; ?>" style='width:140px;' /></td></tr>
            <tr><td><label for='privilegeID'><b><?php echo _("Privilege"); ?></b></label></td>
		<td>
				<fieldset id="fieldsetPrivilege">
				<a title = "<?php echo _("Add/Edit users can add, edit, or remove licenses and associated fields")."<br /><br />"._("Admin users have access to the Admin page and the SFX tab.")."<br /><br />"._("Restricted users do not have the ability to view documents")."<br /><br />"._("View only users can view all license information, including the license pdf");?>" href=""><img src='images/help.gif'></a>
				</fieldset>

				<div id="footnote_priv" style='display:none;'><?php echo _("Add/Edit users can add, edit, or remove licenses and associated fields")."<br /><br />"._("Admin users have access to the Admin page and the SFX tab.")."<br /><br />"._("Restricted users do not have the ability to view documents")."<br /><br />"._("View only users can view all license information, including the license pdf");?></div>

		</td>
		<td>
		<select name='privilegeID' id='privilegeID' style='width:145px'>
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
		</td>
		</tr>

		<?php
		//if not configured to use SFX, hide the Terms Tool Report
		if ($util->useTermsTool()) {
		?>
            <tr><td><label for='emailAddressForTermsTool'><b><?php echo _("Terms Tool Email");?></b></label></td>
		<td>
				<fieldset id="fieldsetEmail">
				<a title = "<?php echo _("Enter email address if you wish this user to receive email notifications when the terms tool box is checked on the Expressions tab.")."<br /><br />"._("Leave this field blank if the user shouldn't receive emails.");?>" href=""><img src='images/help.gif'></a>
				</fieldset>

		</td>
		<td><input type='text' id='emailAddressForTermsTool' name='emailAddressForTermsTool' value='<?php if (isset($updateUser)) echo $updateUser->emailAddressForTermsTool; ?>' style='width:140px;' /></td>
		</tr>

		<?php } else { echo "<input type='hidden' id='emailAddressForTermsTool' name='emailAddressForTermsTool' value='' /><br />"; }?>

		<tr style="vertical-align:middle;">
		<td colspan='2' style="width:60px;"><input type='button' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitUserData("<?php echo $loginID; ?>");' class='submit-button'></td>
		<td><input type='button' value="<?php echo _("cancel");?>" onclick="window.parent.tb_remove(); return false" id='update-user-cancel' class='cancel-button'></td>
		</tr>

		</table>

		</div>


		<script type="text/javascript" src="js/forms/adminUserForm.js?random=<?php echo rand(); ?>"></script>
		<?php

		break;


	//expression types on admin.php screen - since expression types also have note type (internal/display)
	case 'getExpressionTypeForm':
		if (isset($_GET['expressionTypeID'])) $expressionTypeID = $_GET['expressionTypeID']; else $expressionTypeID = '';

		if ($expressionTypeID){
			$update=_('Update');
			$expressionType = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));
		}else{
			$update=_('Add New');
		}


		?>
		<div id='div_updateForm'>
		<input type='hidden' name='expressionTypeID' id='expressionTypeID' value='<?php echo $expressionTypeID; ?>' />
		<table class="thickboxTable" style="width:260px;padding:2px;">
		<tr><td colspan='2'><span class='headerText'><?php echo $update.' '. _("Expression Type");?></span><br /><span id='span_errors' style='color:#F00;'></span><br /></td></tr>
            <tr><td><label for='shortName'><b><?php echo _("Expression Type");?></b></label></td><td><input type='text' id='shortName' name='shortName' value='<?php if (isset($expressionType)) echo $expressionType->shortName; ?>' style='width:130px;'/></td></tr>
            <tr><td><label for='noteType'><b><?php echo _("Note Type");?></b></label></td>
		<td>
		<select name='noteType' id='noteType' style='width:135px'>
		<option value='Internal' <?php if ((isset($expressionType)) && ($expressionType->noteType == 'Internal')) echo "selected"; ?> ><?php echo _("Internal");?></option>
		<option value='Display' <?php if ((isset($expressionType)) && ($expressionType->noteType == 'Display')) echo "selected"; ?> ><?php echo _("Display");?></option>
		</select>
		</td>
		</tr>

		<tr><td colspan='2'><span class='smallText'>* <?php echo _("Note type of display allows for terms tool use");?></span></td></tr>


		<tr>
		<td style="width:60px;"><input type='button' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitExpressionType();' id='update-expression-type' class='submit-button'></td>
		<td><input type='button' value='<?php echo _("cancel");?>' onclick="window.parent.tb_remove(); return false;" id='cancel-expression-type' class='cancel-button'></td>
		</tr>
		</table>
		</div>


		<?php

		break;

	//Calendar Settings on admin.php screen - want it to be edited by users so not in the config
	case 'getCalendarSettingsForm':
		if (isset($_GET['calendarSettingsID'])) $calendarSettingsID = $_GET['calendarSettingsID']; else $calendarSettingsID = '';

		if ($calendarSettingsID){
			$update=_('Edit');
			$calendarSettings = new CalendarSettings(new NamedArguments(array('primaryKey' => $calendarSettingsID)));
		}


		?>
		<div id='div_updateForm'>
		<input type='hidden' name='calendarSettingsID' id='calendarSettingsID' value='<?php echo $calendarSettingsID; ?>' />
		<table class="thickboxTable" style="width:260px;padding:2px;">
		<tr><td colspan='2'><span class='headerText'><?php echo $update.' '._("Calendar Settings"); ?></span><br /><br /></td></tr>

		<?php 
		
		if (strtolower($calendarSettings->shortName) == strtolower('Resource Type(s)')) { ?>
            <tr><td><label for='shortName'><b><?php echo _("Variable Name");?></b></label></td><td><?php if (isset($calendarSettings)) echo $calendarSettings->shortName; ?></td></tr>
			<tr>
			
                <td><label for='value'><b><?php echo _("Value");?></b></label></td>
			<td>
			
			
			<select multiple name='value' id='value' style='width:155px'>
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
			</td>			
			
			</tr>

		<?php 
		
		} elseif (strtolower($calendarSettings->shortName) == strtolower('Authorized Site(s)')) { ?>
            <tr><td><label for='shortName'><b><?php echo _("Variable Name");?></b></label></td><td><?php if (isset($calendarSettings)) echo $calendarSettings->shortName; ?></td></tr>
			<tr>
			
                <td><label for='value'><b><?php echo _("Value");?></b></label></td>
			<td>
			<select multiple name='value' id='value' style='width:155px'>
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
			</td>			
			
			</tr>

		<?php 
		
		} else { 
		
		?>
            <tr><td><label for='shortName'><b><?php echo _("Variable Name");?></b></label></td><td><?php if (isset($calendarSettings)) echo $calendarSettings->shortName; ?></td></tr>
            <tr><td><label for='value'><b><?php echo _("Value");?></b></label></td>
			<td><input type='text' id='value' name='value' value='<?php if (isset($calendarSettings)) echo $calendarSettings->value; ?>' style='width:130px;'/></td>
			</tr>
		
		<?php 
		
		} 
		
		?>
		

		
		
		<tr>
		<td style="width:60px;"><input type='button' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitCalendarSettings();' class='submit-button'></td>
		<td><input type='button' value='<?php echo _("cancel");?>' onclick="window.parent.tb_remove(); return false" class='cancel-button'></td>
		</tr>
		</table>
		</div>


		<?php

		break;


	//qualifier on admin.php screen - since qualifiers also have expression types
	case 'getQualifierForm':
		if (isset($_GET['qualifierID'])) $qualifierID = $_GET['qualifierID']; else $qualifierID = '';

		if ($qualifierID){
			$update=_('Update');
			$qualifier = new Qualifier(new NamedArguments(array('primaryKey' => $qualifierID)));
		}else{
			$update=_('Add New');
		}


		?>
		<div id='div_updateForm'>
		<input type='hidden' name='qualifierID' id='qualifierID' value='<?php echo $qualifierID; ?>' />
		<table class="thickboxTable" style="width:290px;padding:2px;">
		<tr><td colspan='2'><span class='headerText'><?php echo $update.' '. _("Qualifier"); ?></span><br /><span id='span_errors' style='color:#F00;'></span><br /></td></tr>

            <tr><td><label for='expressionTypeID'><b><?php echo _("For Expression Type");?></b></label></td>
		<td>
		<select name='expressionTypeID' id='expressionTypeID' style='width:155px'>
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
		</td>
		</tr>

            <tr><td><label for='shortName'><b><?php echo _("Qualifier");?></b></label></td><td><input type='text' id='shortName' name='shortName' value='<?php if (isset($qualifier)) echo $qualifier->shortName; ?>' style='width:150px;'/></td></tr>

		<tr>
		<td style="width:60px;"><input type='button' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitQualifier();' id='submitQualifier' class='submit-button'></td>
		<td><input type='button' value='<?php echo _("cancel");?>' onclick="window.parent.tb_remove(); return false" class='cancel-button'></td>
		</tr>
		</table>
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
       echo _("Action ") . $action . _(" not set up!");
       break;


}



?>
