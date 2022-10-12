<?php
	$alertEmailAddressID = $_GET['alertEmailAddressID'];

	if ($alertEmailAddressID){
		$instance = new AlertEmailAddress(new NamedArguments(array('primaryKey' => $alertEmailAddressID)));
	}else{
		$instance = new AlertEmailAddress();
	}
?>
		<div id='div_updateForm'>

		<input type='hidden' id='editAlertEmailAddressID' value='<?php echo $alertEmailAddressID; ?>'>

		<div class='formTitle' style='width:245px;'><span class='headerText' style='margin-left:7px;'><?php if ($alertEmailAddressID){ echo _("Edit Alert Email Address"); } else { echo _("Add Alert Email Address"); } ?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class="surroundBox" style="width:250px;">
		<tr>
		<td>

			<table class='noBorder' style='width:200px; margin:10px;'>
			<td><input type='text' id='emailAddress' value='<?php echo $instance->emailAddress; ?>' style='width:190px;'/></td>
			</td>
			</tr>
			</table>

		</td>
		</tr>
		</table>
		<div class='smallDarkRedText' id='div_form_error'>&nbsp;</div>
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
		   $('#emailAddress').keyup(function(e) {
				   if(e.keyCode == 13) {
					   window.parent.submitAdminAlertEmail();
				   }
		});


		   $('#submitAddUpdate').click(function () {
			       window.parent.submitAdminAlertEmail();
		   });


	</script>

