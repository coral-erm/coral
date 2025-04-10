<?php
$util = new utility();

$organizationID = isset($_GET["organizationID"]) ? $_GET["organizationID"] : NULL;

$resourceID = isset($_GET["resourceID"]) ? $_GET["resourceID"] : NULL;
$issueID = isset($_GET['issueID']) ? $_GET['issueID'] : NULL;
$resourceAcquisitionID = isset($_GET['resourceAcquisitionID']) ? $_GET['resourceAcquisitionID'] : NULL;

$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

$isOrgDowntime = false;
if ($organizationID) {
	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));
	$issues = $organization->getIssues();
	$isOrgDowntime = true;
} else {
	$issues = $resourceAcquisition->getIssues();

	$organizationArray = $resource->getOrganizationArray();
	$organizationData = $organizationArray[0];

	if ($organizationData['organizationID']) {
		$organizationID = $organizationData['organizationID'];

		$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));

		$orgIssues = $organization->getIssues();

		foreach ($orgIssues as $issue) {
			array_push($issues, $issue);
		}
		$organizationResourcesArray = $resource->getSiblingResourcesArray($organizationID);
	}
}

//our $organizationID could have come from the $_GET or through the resource
if ($organizationID) {
	$downtimeObj = new Downtime();
	$downtimeTypeNames = $downtimeObj->getDowntimeTypesArray();

?>

<form id='newDowntimeForm' class="form-grid">
<?php
if ($isOrgDowntime) {
	echo '<input type="hidden" name="sourceOrganizationID" value="'.$organizationID.'" />';
} else {
	echo '<input type="hidden" name="sourceResourceID" value="'.$resourceID.'" />';
	echo '<input type="hidden" name="sourceResourceAcquisitionID" value="'.$resourceAcquisitionID.'" />';
}
?>

			<h2 class="headerText"><?php echo _("Resource Downtime Report");?></h2>

			<h3 class="wide"><?php echo _("Downtime Start:");?></h3>
			
				
				<label for="startDate"><?php echo _("Date");?></label>
				<div class="form-group">
					<input class="date-pick" type="text" name="startDate" id="startDate" aria-describedby="span_error_startDate" placeholder='mm/dd/yyyy' />
					<span id='span_error_startDate' class='error addDowntimeError'></span>
				</div>
				
				<fieldset class="subgrid">
					<legend><?php echo _("Time");?></legend>
					<div class="form-group">
						<?php
						echo buildTimeForm("startTime");
						?>
						<span id='span_error_startDate' class='error addDowntimeError'></span>
					</div>
				</fieldset>

			<h3 class="wide"><?php echo _("Downtime Resolution:");?></h3>
			
			<label for="endDate"><?php echo _("Date");?></label>
			<div class="form-group">
				<input class="date-pick" type="text" name="endDate" id="endDate" aria-describedby="span_error_endDate" placeholder='mm/dd/yyyy' />
				<span id='span_error_endDate' class='error addDowntimeError'></span>
			</div>
			
			<fieldset class="subgrid">
				<legend><?php echo _("Time");?></legend>
				<div class="form-group">
					<?php
					echo buildTimeForm("endTime");
					?>
					<span id='span_error_endDate' class='error addDowntimeError'></span>
				</div>
			</fieldset>
		
			<label for="downtimeType"><?php echo _("Problem Type:");?></label>
			<select class="downtimeType" name="downtimeType" id="downtimeType">
			<?php
				foreach ($downtimeTypeNames as $downtimeType) {
					echo "<option value=" . (isset($downtimeType['downtimeTypeID']) ? $downtimeType['downtimeTypeID'] : '') . ">" . (isset($downtimeType['shortName']) ? $downtimeType['shortName'] : '') . "</option>";
				}
			?>
			</select>
			
			
		<?php
		if ($issues) {
		?>
			<label for="issueID"><?php echo _("Link to open issue:");?></label>
			<select class="issueID" name="issueID" id="issueID">
				<option value=""><?php echo _('none'); ?></option>
				<?php
					foreach ($issues as $issue) {
						echo "<option".(($issueID == $issue->issueID) ? ' selected':'')." value=".$issue->issueID.">".$issue->subjectText."</option>";
					}
				?>
				</select>

<?php
}
?>
		
		<label for="note"><?php echo _("Note:");?></label>
		<textarea name="note" id="note"></textarea>
		
		<p class="actions">
			<input type='submit' value='<?php echo _("submit");?>' name='submitNewDowntime' id='submitNewDowntime' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog();" class='submit-button secondary'>
		</p>

</form>

<?php
} else {
	echo '<p>' . _("Creating downtime requires an organization or a resource to be associated with an organization.") . '</p>';
	echo '<input type="button" value="' . _("cancel") . '" onclick="myCloseDialog();">';
}
?>


