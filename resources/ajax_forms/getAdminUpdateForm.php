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

		<div class='formTitle' style='width:245px;'><span class='headerText' style='margin-left:7px;'><?php if ($updateID){ echo _("Edit ") . _(trim(preg_replace("/[A-Z]/", " \\0" , $className))); } else { echo _("Add ") . _(trim(preg_replace("/[A-Z]/", " \\0" , $className))); } ?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class="surroundBox" style="width:250px;">
		<tr>
		<td>

			<table class='noBorder' style='width:200px; margin:10px;'>
			<tr>
			<td><input type='text' id='updateVal' value='<?php echo $instance->shortName; ?>' style='width:190px;'/></td>
			</tr>
			<?php
				if($className == 'ResourceType' && ($config->settings->usageModule == 'Y')){
					if($instance->includeStats == 1){$stats = 'checked';}else{$stats='';}
					echo "<tr><td><label for='stats'>"._("Show stats button?")."</label>";
					echo "<input type='checkbox' id='stats' ".$stats." /></td></tr>";
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


		</form>
		</div>

		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#updateVal').keyup(function(e) {
				   if(e.keyCode == 13) {
					   window.parent.submitData();
				   }
		});


		   $('#submitAddUpdate').click(function () {
			       window.parent.submitData();
		   });


	</script>

