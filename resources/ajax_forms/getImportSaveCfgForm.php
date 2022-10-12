<?php
	$configName = (($_GET["configName"]=="__NEW")?"":$_GET["configName"]);
?>
		<div id='div_updateForm'>

		<div class='formTitle' style='width:245px;'><span class='headerText' style='margin-left:7px;'><?php echo _("Save Import Configuration");?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class="surroundBox" style="width:250px;">
		<tr>
		<td>

			<table class='noBorder' style='width:200px; margin:10px;'>
			<tr>
			<td><?php echo _("Name:");?></td><td><input type='text' id='saveConfigName' value='<?php echo $configName;?>' style='width:250px;'/></td>
			</tr>
			</table>

		</td>
		</tr>
		</table>

		<br />
		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("save");?>' id ='submitAddUpdate' onclick='saveConfiguration();' class='save-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel")?>' onclick="myCloseDialog(); return false;" class='cancel-button'></td>
			</tr>
		</table>


		</form>
		</div>

