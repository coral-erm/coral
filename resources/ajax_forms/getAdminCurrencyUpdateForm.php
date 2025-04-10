<?php
	$updateID = $_GET['updateID'];

	if ($updateID){
		$instance = new Currency(new NamedArguments(array('primaryKey' => $updateID)));
	}else{
		$instance = new Currency();
	}
?>
		<div id='div_updateForm'>

		<input type='hidden' id='editCurrencyCode' value='<?php echo $updateID; ?>'>

		<div class='formTitle'><h2 class='headerText'><?php if ($updateID){ echo _("Edit Currency"); } else { echo _("Add Currency"); } ?></h2></div>

		<span class='error' id='span_errors'></span>

	<div class="form-grid">	
		<label for="currencyCode"><?php echo _("Code");?></label>
		<input type='text' id='currencyCode' value='<?php echo $instance->currencyCode; ?>' />
	
		<label for="shortName"><?php echo _("Name");?></label>
		<input type='text' id='shortName' value='<?php echo $instance->shortName; ?>' />
	
		<p class="actions">
				<input type='submit' value='<?php echo _("submit");?>' id ='submitAddUpdate' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button secondary'>
			</p>
			

		</form>
		</div>

		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#currencyCode').keyup(function(e) {
				   if(e.keyCode == 13) {
					   window.parent.submitCurrencyData();
				   }
		});

		   $('#shortName').keyup(function(e) {
				   if(e.keyCode == 13) {
					   window.parent.submitCurrencyData();
				   }
		});

		   $('#submitAddUpdate').click(function () {
			       window.parent.submitCurrencyData();
		   });


	</script>

