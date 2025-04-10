<?php
$util = new utility();

$resourceID = $_GET["resourceID"];
$resourceAcquisitionID = $_GET["resourceAcquisitionID"];

$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

$organizationArray = $resource->getOrganizationArray();
$organizationData = $organizationArray[0];

$config = new Configuration();
//the issues feature currently support org or resource contacts, but not both
$moduleFilter = ($config->settings->organizationsModule == 'Y') ? 'organizations':'resources';
$contactsArray = $resourceAcquisition->getUnarchivedContacts($moduleFilter);
if ($organizationData['organizationID']) {
	$organizationResourcesArray = $resource->getSiblingResourcesArray($organizationData['organizationID']);
?>

<form id='newIssueForm' class="form-grid">
	<input type="hidden" id="sourceOrganizationID" name="sourceOrganizationID" value="<?php echo $organizationData['organizationID'];?>" />
	<input type="hidden" name="sourceResourceID" value="<?php echo $resourceID;?>" />
	<input type="hidden" name="sourceResourceAcquisitionID" value="<?php echo $resourceAcquisitionID;?>" />
	
	<h2 class="headerText"><?php echo _("Report New Problem");?></h2>
	<p class='required wide'><?php echo _("* required fields");?></p>
	
	<label for="org"><?php echo _("Organization:");?><span class='required'>*</span></label>
	<output id="org"><?php echo $organizationData['organization']; ?></output>
	<span id='span_error_organizationId' class='error'></span>
	
	<label for="contactIDs"><?php echo _("Contact:");?> <span class='required'>*</span></label>
	<div class="form-group">
	<select multiple type='text' id='contactIDs' name='contactIDs[]' aria-describedby='span_error_contactName'>
<?php

	foreach ($contactsArray as $contact) {
		if (!empty($contact['name'])) {
			echo "		<option value=\"{$contact['contactID']}\">{$contact['name']}</option>";
		} else {
			echo "		<option value=\"{$contact['contactID']}\">{$contact['emailAddress']}</option>";
		}
	}

?>
		</select>
		<span id='span_error_contactName' class='error'></span>
			
		<?php
		if ($config->settings->organizationsModule == 'Y') {
		?>
			<input type="hidden" name="orgModuleUrl" id="orgModuleUrl" value="<?php echo $util->getCoralUrl();?>organizations/" />
			<a id="getCreateContactForm" href="#"><?php echo _("add contact");?></a>
			<div id="inlineContact"></div>
						<script>
				    $("#getCreateContactForm").on("click",function(e) {
				        e.preventDefault();
				        $(this).fadeOut(250, function() {
				            getInlineContactForm();
				        });
				    });
				</script>
		<?php
		}
		?>
		</div>

		<label for="currentEmails"><?php echo _("Current CCs:");?> </label>
		<output id="currentEmails"></output>
		
		<label for="inputEmail"><?php echo _("CC:");?></label>
		<div class="form-group">
			<input type="text" id="inputEmail" name="inputEmail" aria-describedby="span_error_contactIDs" />
			
			<input type="button" id="addEmail" name="addEmail" value="<?php echo _('Add');?>" />

			<p class="checkbox">
			<input type='checkbox' id='ccCreator' name='ccCreator' class='changeInput' aria-describedby='span_error_ccCreator' />
			<label for="ccCreator"><?php echo _("CC myself");?></label>
			<span id='span_error_ccCreator' class='error error'></span>
		</p>	
		</div>
		
		
				
		<input type="hidden" id='ccEmails' name='ccEmails' value='' class='changeInput' />
		<span id='span_error_contactIDs' class='error'></span>
		
		<label for="subjectText"><?php echo _("Subject:");?> <span class='required'>*</span></label>
		<input type='text' id='subjectText' name='issue[subjectText]' value='' class='changeInput' aria-describedby='span_error_subjectText' />
		
		<label for="bodyText"><?php echo _("Body:");?> <span class='required'>*</span></label>
		<textarea id='bodyText' name='issue[bodyText]' value='' aria-describedby="span_error_bodyText"></textarea>
		<span id='span_error_bodyText' class='error'></span>
		
		<fieldset class="subgrid">
			<legend><?php echo _("Applies to:");?> <span class='required'>*</span></legend>
			<div class="form-group">
				<ul class="unstyled checkbox">
					<li>
						<input type="checkbox" class="issueResources entityArray" name="resourceIDs[]" value="<?php echo $resourceID;?>" checked /> 
						<label for="thisResources"><?php printf(_("Applies only to %s"), $resource->titleText);?></label>
					</li>
					<li>
						<input type="checkbox" class="issueResources entityArray" name="organizationID" id="organizationID" value="<?php echo $organizationData['organizationID'];?>" /> 
						<label for="allResources"><?php printf(_("Applies to all %s resources"), $organizationData['organization']);?></label>
					</li>
					<li>
						<input type="checkbox" class="issueResources" id="otherResources" />
						<label for="otherResources"> <?php printf(_("Applies to selected %s resources"), $organizationData['organization']);?></label>
					</li>		
				</ul>
				<select multiple id="resourceIDs" name="resourceIDs[]" aria-label="<?php echo _('Select resources') ?>" aria-describedby="span_error_entities">
				<?php
					if (!empty($organizationResourcesArray)) {
						foreach ($organizationResourcesArray as $resource) {
							echo "		<option class=\"entityArray\" value=\"{$resource['resourceID']}\">{$resource['titleText']}</option>";
						}
					}
				?>
				</select>
				<span id='span_error_entities' class='error'></span>
			</div>
		</fieldset>

		<label for="interval"><?php echo _("Send me a reminder every");?></label>
		<select name="issue[reminderInterval]" id="interval">
			<?php for ($i = 1; $i <= 31; $i++) echo "<option".(($i==7) ? ' selected':'').">" . sprintf(_('%d days'), $i) . "</option>"; ?>
		</select>

	<p class='actions'>
		<input type='submit' value='<?php echo _("submit");?>' name='submitNewIssue' id='submitNewIssue' class='submit-button primary'>
		<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
	</p>

</form>

<?php
} else {
	echo '<p>' . _("Opening an issue requires a resource to be associated with an organization.") . '</p>';
	echo '<input type="button" value="' . _("cancel") . '" onclick="myCloseDialog();">';
}
?>


