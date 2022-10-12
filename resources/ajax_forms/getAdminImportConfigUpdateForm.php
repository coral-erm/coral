<?php
	$updateID = $_GET['updateID'];

	if ($updateID){
		$instance = new ImportConfig(new NamedArguments(array('primaryKey' => $updateID)));
		$orgMappingInstance = new OrgNameMapping();
		$orgMappings=$orgMappingInstance->getOrgNameMappingByImportConfigID($updateID);
	}else{
		$instance = new ImportConfig();
		$orgMappingInstance = new OrgNameMapping();
		$orgMappings=array();
	}

	$configuration=json_decode($instance->configuration,true);
?>
<div id='div_updateForm' style='height: 700px;'>
	<input type='hidden' id='importConfigID' value='<?php echo $updateID; ?>'>
	<div class='formTitle' style='min-width:1000px;'>
		<span class='headerText' style='margin-left:7px;'>
			<?php
				if ($updateID) {
					echo _("Edit Import Configuration");
				} else {
					echo _("Add Import Configuration");
				}
			?>
		</span>
	</div>
	<span class='smallDarkRedText' id='span_errors'></span>
	<div id='config-name'>
		<?php echo _("Configuration Name");?><input id='shortName' value='<?php echo $instance->shortName;?>'>
	</div>
	<div class='ic-content'>
		<p id="ic-instructions"><?php echo _("For each of the resource fields please input the number of the column in your CSV file that corresponds to the resource field. For example, if your import file has a second column called Name that corresponds to the Resource Title, then you would input 2 for the value for the Resource Title field. For columns with multiple values that are character-delimited, indicate the delimiter using the If delimited, delimited by field. For fields with values across multiple columns, add additional sets using the +Add another links. Use the Dedupe on this column option for ISBN/ISSN sets to ignore any duplicate values that might occur across those columns. The Alias Types, Note Types, and Organization Roles that you can assign to your mapped columns can be configured on the Admin page.");?></p>
		<?php include 'getImportConfigForm.php';?>
	</div>
	<br />
	<table class='noBorderTable' style='width:125px;'>
		<tr>
			<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' id ='submitAddUpdate' class='submit-button'></td>
			<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button'></td>
		</tr>
	</table>
</div>
<script type="text/javascript">
   $('#submitAddUpdate').click(function () {
		submitImportConfigData();
   });
</script>
