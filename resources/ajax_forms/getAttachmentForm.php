<?php
	$resourceID = $_GET['resourceID'];
	$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
	if (isset($_GET['attachmentID'])) $attachmentID = $_GET['attachmentID']; else $attachmentID = '';
	$attachment = new Attachment(new NamedArguments(array('primaryKey' => $attachmentID)));

		//get all attachment types for output in drop down
		$attachmentTypeArray = array();
		$attachmentTypeObj = new AttachmentType();
		$attachmentTypeArray = $attachmentTypeObj->allAsArray();
?>
		<div id='div_attachmentForm'>
		<form id='attachmentForm' class="form-grid">
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>
		<input type='hidden' name='editResourceAcquisitionID' id='editResourceAcquisitionID' value='<?php echo isset($resourceAcquisitionID) ? $resourceAcquisitionID : $attachment->resourceAcquisitionID; ?>'>
		<input type='hidden' name='editAttachmentID' id='editAttachmentID' value='<?php echo $attachmentID; ?>'>

		<h2 class='headerText'><?php if ($attachmentID){ echo _("Edit Attachment"); } else { echo _("Add Attachment"); } ?></h2>

		<span class='error' id='span_errors'></span>


			<label for='shortName'><?php echo _("Name:");?></label>
			<input type='text' class='changeInput' id='shortName' name='shortName' value = '<?php echo $attachment->shortName; ?>' />
			<span id='span_error_shortName' class='error'></span>


			<label for='attachmentTypeID'><?php echo _("Type:");?></label>
			<select name='attachmentTypeID' id='attachmentTypeID' aria-describedby='span_error_attachmentTypeID'>
			<option value=''></option>
			<?php
			foreach ($attachmentTypeArray as $attachmentType){
				if (!(trim(strval($attachmentType['attachmentTypeID'])) != trim(strval($attachment->attachmentTypeID)))){
					echo "<option value='" . $attachmentType['attachmentTypeID'] . "' selected>" . $attachmentType['shortName'] . "</option>\n";
				}else{
					echo "<option value='" . $attachmentType['attachmentTypeID'] . "'>" . $attachmentType['shortName'] . "</option>\n";
				}
			}
			?>
			</select>
			<span id='span_error_attachmentTypeID' class='error'></span>
			
			<label for="upload_button"><?php echo _("File:");?></label>
			<div class="form-group">
			<?php

			//if editing
			if ($attachmentID){
				echo "<div id='div_uploadFile'>" . $attachment->attachmentURL . "<br /><button type='button' class='btn' onclick='replaceFile();'>"._("replace with new file")."</button>";
				echo "<input type='hidden' id='upload_button' name='upload_button' value='" . $attachment->attachmentURL . "'></div>";

			//if adding
			}else{
				echo "<div id='div_uploadFile'><input type='file' name='upload_button' id='upload_button' /></div>";
			}


			?>
			<span id='div_file_message'></span>
			</div>

			<label for='descriptionText'><?php echo _("Details:");?></label>
			<textarea rows='5' class='changeInput' id='descriptionText' name='descriptionText'><?php echo $attachment->descriptionText; ?></textarea>
		
			<p class="actions">
				<input type='button' value='<?php echo _("submit");?>' name='submitAttachmentForm' id ='submitAttachmentForm' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>


		</form>
		</div>

		<script type="text/javascript" src="js/forms/attachmentForm.js?random=<?php echo rand(); ?>"></script>

