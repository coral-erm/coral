<?php
		$className = $_GET['className'];
		$updateID = $_GET['updateID'];

		if ($updateID){
			$instance = new $className(new NamedArguments(array('primaryKey' => $updateID)));
		}else{
			$instance = new $className();
		}
?>
		<div id='div_updateForm'>

		<input type='hidden' id='editClassName' value='<?php echo $className; ?>'>
		<input type='hidden' id='editUpdateID' value='<?php echo $updateID; ?>'>
		<!-- TODO: i18n placeholders -->
		<div class='formTitle'><span class='headerText' id="updateValueLabel"><?php if ($updateID){ echo _("Edit ") . preg_replace("/[A-Z]/", " \\0" , $className); } else { echo _("Add ") . preg_replace("/[A-Z]/", " \\0" , $className); } ?></span></div>

		<span class='error' id='span_errors'></span>

			<p>
				<input type="text" id="updateVal" value="<?php echo $instance->shortName; ?>" aria-labelledby="updateValueLabel"/>
			</p>
		
			
			<p class="actions">

				<input type='submit' value='<?php echo _("submit");?>' name='submitGeneralSubjectForm' id ='submitGeneralSubjectForm' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button secondary'>
			</p>


		</form>
		</div>

		<script type="text/javascript" src="js/forms/generalSubjectForm.js?random=<?php echo rand(); ?>"></script>
