<?php
	$updateID = $_GET['updateID'];

	if ($updateID){
		$instance = new Fund(new NamedArguments(array('primaryKey' => $updateID)));
	}else{
		$instance = new Fund();
	}

?>
		<div id='div_updateForm'>

		<input type='hidden' id='fundID' value='<?php echo $updateID; ?>'>

		<div class='formTitle' style='width:245px;'><span class='headerText' style='margin-left:7px;'><?php if ($updateID){ echo _("Edit Fund"); } else { echo _("Add Fund"); } ?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class="surroundBox" style="width:250px;">
		<tr>
		<td>

			<table class='noBorder' style='width:200px; margin:10px;'>
			<tr>
			<td><?php echo _("Code");?></td><td><input type='text' id='fundCode' value='<?php echo $instance->fundCode; ?>' style='width:150px;'/></td>
			</tr>
			<tr>
			<td><?php echo _("Name");?></td><td><input type='text' id='shortName' value='<?php echo $instance->shortName; ?>' style='width:150px;'/></td>
			</tr>
			<?php	{
								if($instance->archived == 1){$archive = 'checked';}else{$archive='';}
								echo "<tr><td>" . _("Archived") . "</td>";
								echo "<td><input type='checkbox' id='archivedUpdate' ".$archive." /></td></tr>";
					}
			?>
			</table>

		</td>
		</tr>
		</table>

		<br />
		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' id ='submitAddUpdate' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="window.parent.tb_remove(); return false;" class='cancel-button'></td>
			</tr>
		</table>


		</div>

		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#fundCode').keyup(function(e) {
				   if(e.keyCode == 13) {
					   window.parent.submitFundData();
				   }
		});

		   $('#shortName').keyup(function(e) {
				   if(e.keyCode == 13) {
					   window.parent.submitFundData();
				   }
		});

		   $('#submitAddUpdate').click(function () {
			       window.parent.submitFundData();
		   });


	</script>
