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
		<form id='attachmentForm'>
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>
		<input type='hidden' name='editResourceAcquisitionID' id='editResourceAcquisitionID' value='<?php echo isset($resourceAcquisitionID) ? $resourceAcquisitionID : $attachment->resourceAcquisitionID; ?>'>
		<input type='hidden' name='editAttachmentID' id='editAttachmentID' value='<?php echo $attachmentID; ?>'>

		<div class='formTitle' style='width:345px;'><span class='headerText' style='margin-left:7px;'><?php if ($attachmentID){ echo _("Edit Attachment"); } else { echo _("Add Attachment"); } ?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class="surroundBox" style="width:350px;">
		<tr>
		<td>

			<table class='noBorder' style='width:310px; margin:10px 15px;'>

			<tr>
			<td style='vertical-align:top;text-align:left;'><label for='shortName'><b><?php echo _("Name:");?></b></label></td>
			<td>
			<input type='text' class='changeInput' id='shortName' name='shortName' value = '<?php echo $attachment->shortName; ?>' style='width:230px' /><span id='span_error_shortName' class='smallDarkRedText'></span>
			</td>
			</tr>

			<tr>

			<td style='vertical-align:top;text-align:left;border:0px;'><label for='attachmentTypeID'><b><?php echo _("Type:");?></b></label></td>
			<td style='vertical-align:top;text-align:left;border:0px;'>

			<select name='attachmentTypeID' id='attachmentTypeID'>
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
			<span id='span_error_attachmentTypeID' class='smallDarkRedText'></span>
			</td>
			</tr>

			<tr>
			<td style='text-align:left;vertical-align:top;'><label for="uploadAttachment"><b><?php echo _("File:");?></b></label></td>
			<td>
			<?php

			//if editing
			if ($attachmentID){
				echo "<div id='div_uploadFile'>" . $attachment->attachmentURL . "<br /><a href='javascript:replaceFile();'>"._("replace with new file")."</a>";
				echo "<input type='hidden' id='upload_button' name='upload_button' value='" . $attachment->attachmentURL . "'></div>";

			//if adding
			}else{
				echo "<div id='div_uploadFile'><input type='file' name='upload_button' id='upload_button' /></div>";
			}


			?>
			<span id='div_file_message'></span>
			</td>
			</tr>

			<tr>
			<td style='vertical-align:top;text-align:left;'><label for='descriptionText'><b><?php echo _("Details:");?></b></label></td>
			<td><textarea rows='5' class='changeInput' id='descriptionText' name='descriptionText' style='width:230px'><?php echo $attachment->descriptionText; ?></textarea></td>
			</td>
			</tr>
			</table>

		</td>
		</tr>
		</table>

		<br />
		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitAttachmentForm' id ='submitAttachmentForm' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button'></td>
			</tr>
		</table>


		</form>
		</div>

		<script type="text/javascript" src="js/forms/attachmentForm.js?random=<?php echo rand(); ?>"></script>

