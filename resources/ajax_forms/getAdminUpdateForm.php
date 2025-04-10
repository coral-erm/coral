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
		<div class='formTitle'><h2 class='headerText' id='updateValueLabel'><?php if ($updateID){ printf(_("Edit %s"), trim(preg_replace("/[A-Z]/", " \\0" , $className))); } else { printf(_("Add %s"), trim(preg_replace("/[A-Z]/", " \\0" , $className))); } ?></h2></div>

		<span class='error' id='span_errors'></span>

		<p>
			<input type='text' id='updateVal' value='<?php echo $instance->shortName; ?>' aria-labelledby='updateValueLabel'/>
		</p>
		
			<?php
				if($className == 'ResourceType' && ($config->settings->usageModule == 'Y')){
					if($instance->includeStats == 1){$stats = 'checked';}else{$stats='';}
					echo "<p class='checkbox'><label for='stats'>"._("Show stats button?")."</label>";
					echo "<input type='checkbox' id='stats' ".$stats." /></p>";
				}
			?>
			
		<p class='actions'>
			<input type='submit' value='<?php echo _("submit");?>' id ='submitAddUpdate' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button secondary'>
		</p>


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

