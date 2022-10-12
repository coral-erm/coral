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

		<div class='formTitle' style='width:245px;'><span class='headerText' style='margin-left:7px;'><?php if ($updateID){ echo _("Edit Currency"); } else { echo _("Add Currency"); } ?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class="surroundBox" style="width:250px;">
		<tr>
		<td>

			<table class='noBorder' style='width:200px; margin:10px;'>
			<tr>
			<td><?php echo _("Code");?></td><td><input type='text' id='currencyCode' value='<?php echo $instance->currencyCode; ?>' style='width:150px;'/></td>
			</tr>
			<tr>
			<td><?php echo _("Name");?></td><td><input type='text' id='shortName' value='<?php echo $instance->shortName; ?>' style='width:150px;'/></td>
			</tr>
			</table>

		</td>
		</tr>
		</table>

		<br />
		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' id ='submitAddUpdate' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button'></td>
			</tr>
		</table>


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

