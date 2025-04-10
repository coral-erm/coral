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

				<div class='formTitle'>
					<h2 class='headerText'><?php if ($resourceNoteID){ echo _("Edit Note"); } else { echo _("Add Note"); } ?></h2>
				</div>

				<span class='error' id='span_errors'></span>

				<div class="form-grid">
					<label for='noteTypeID'><?php echo _("Note Type:");?></label>
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

					<label for='noteText'><?php echo _("Notes:");?></label>
					<textarea rows='5' id='noteText' name='noteText'><?php echo $resourceNote->noteText; ?></textarea>
					<p class='error' id='span_error_noteText'></p>

					<p class="actions">
						<input type='submit' value='<?php echo _("submit");?>' name='submitResourceNoteForm' id ='submitResourceNoteForm' class='submit-button primary'>
						<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
					</p>
				</div>
			</form>
		</div>

		<script type="text/javascript" src="js/forms/resourceNoteForm.js?random=<?php echo rand(); ?>"></script>

