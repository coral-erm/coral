<?php
	$updateID = $_GET['updateID'];

	if ($updateID){
		$instance = new Fund(new NamedArguments(array('primaryKey' => $updateID)));
	}else{
		$instance = new Fund();
	}

?>
<form id='div_updateForm' class="form-grid">
	<input type='hidden' id='fundID' value='<?php echo $updateID; ?>'>

	<h2 class='headerText'><?php if ($updateID){ echo _("Edit Fund"); } else { echo _("Add Fund"); } ?></h2>
	<span class='error' id='span_errors'></span>

	<label for="fundCode"><?php echo _("Code");?></label>
	<input type='text' id='fundCode' value='<?php echo $instance->fundCode; ?>' />
		
	<label for="shortName"><?php echo _("Name");?></label>
	<input type='text' id='shortName' value='<?php echo $instance->shortName; ?>' />

	<p class="checkbox">
		<input type='checkbox' id='archivedUpdate' <?php if ($instance->archived) echo 'checked'; ?> />
		<label for='archivedUpdate'><?php echo _("Archived") ?></label>
	</p>
	
	<p class="actions">
		<input type='submit' value='<?php echo _("submit");?>' onclick="window.parent.submitFundData()" id ='submitAddUpdate' class='submit-button primary'>
		<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
	</p>

</form>
