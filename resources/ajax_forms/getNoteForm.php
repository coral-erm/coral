<?php
	$entityID = isset($_GET['entityID']) ? $_GET['entityID'] : null;
	if (isset($_GET['resourceNoteID'])) $resourceNoteID = $_GET['resourceNoteID']; else $resourceNoteID = '';
		if (isset($_GET['tab'])) $tabName = $_GET['tab']; else $tabName = '';
	$resourceNote = new ResourceNote(new NamedArguments(array('primaryKey' => $resourceNoteID)));

		//get all note types for output in drop down
		$noteTypeArray = array();
		$noteTypeObj = new NoteType();
		$noteTypeArray = $noteTypeObj->allAsArrayForDD();
?>
		<div id='div_noteForm'>
		<form id='noteForm'>
		<input type='hidden' name='editEntityID' id='editEntityID' value='<?php echo $entityID; ?>'>
		<input type='hidden' name='editResourceNoteID' id='editResourceNoteID' value='<?php echo $resourceNoteID; ?>'>
		<input type='hidden' name='tab' id='tab' value='<?php echo $tabName; ?>'>

		<div class='formTitle' style='width:395px;'><span class='headerText' style='margin-left:7px;'><?php if ($resourceNoteID){ echo _("Edit Note"); } else { echo _("Add Note"); } ?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class="surroundBox" style="width:400px;">
		<tr>
		<td>

			<table class='noBorder' style='width:360px; margin:10px 15px;'>
			<tr>
			<td style='vertical-align:top;text-align:left;border:0px;'><label for='noteTypeID'><b><?php echo _("Note Type:");?></b></label></td>
			<td style='vertical-align:top;text-align:left;border:0px;'>

			<select name='noteTypeID' id='noteTypeID'>
			<option value=''></option>
			<?php
			foreach ($noteTypeArray as $noteType){
				if (!(trim(strval($noteType['noteTypeID'])) != trim(strval($resourceNote->noteTypeID)))){
					echo "<option value='" . $noteType['noteTypeID'] . "' selected>" . $noteType['shortName'] . "</option>\n";
				}else{
					echo "<option value='" . $noteType['noteTypeID'] . "'>" . $noteType['shortName'] . "</option>\n";
				}
			}
			?>
			</select>

			</td>
			</tr>

			<tr>
			<td style='vertical-align:top;text-align:left;'><label for='noteText'><b><?php echo _("Notes:");?></b></label></td>
			<td><textarea rows='5' id='noteText' name='noteText' style='width:270px'><?php echo $resourceNote->noteText; ?></textarea><span class='smallDarkRedText' id='span_error_noteText'></span></td>
			</td>
			</tr>
			</table>

		</td>
		</tr>
		</table>

		<br />
		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitResourceNoteForm' id ='submitResourceNoteForm' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button'></td>
			</tr>
		</table>


		</form>
		</div>

		<script type="text/javascript" src="js/forms/resourceNoteForm.js?random=<?php echo rand(); ?>"></script>

