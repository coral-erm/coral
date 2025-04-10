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
<div id='div_updateForm'>
	<input type='hidden' id='importConfigID' value='<?php echo $updateID; ?>'>
	<div class='formTitle'>
		<h2 class='headerText'>
			<?php
				if ($updateID) {
					echo _("Edit Import Configuration");
				} else {
					echo _("Add Import Configuration");
				}
			?>
		</h2>
	</div>
	<span class='error' id='span_errors'></span>
	<p id='config-name' class="block-form">
		<label for="shortName"><?php echo _("Configuration Name");?></label>
		<input id='shortName' value='<?php echo $instance->shortName;?>'>
	</p>
	<div class='ic-content'>
		<p id="ic-instructions"><?php echo _("For each of the resource fields please input the number of the column in your CSV file that corresponds to the resource field. For example, if your import file has a second column called Name that corresponds to the Resource Title, then you would input 2 for the value for the Resource Title field. For columns with multiple values that are character-delimited, indicate the delimiter using the If delimited, delimited by field. For fields with values across multiple columns, add additional sets using the +Add another links. Use the Dedupe on this column option for ISBN/ISSN sets to ignore any duplicate values that might occur across those columns. The Alias Types, Note Types, and Organization Roles that you can assign to your mapped columns can be configured on the Admin page.");?></p>
		<?php include 'getImportConfigForm.php';?>
	</div>
	<br />
	<p class="actions">
		<input type='submit' value='<?php echo _("submit");?>' id ='submitAddUpdate' class='submit-button primary'>
		<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button secondary'>
	</p>
</div>
<script type="text/javascript">
   $('#submitAddUpdate').click(function () {
		submitImportConfigData();
   });
</script>
