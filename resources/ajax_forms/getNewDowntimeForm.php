<?php
$util = new utility();

$organizationID = $_GET["organizationID"];

$resourceID = $_GET["resourceID"];
$issueID = $_GET['issueID'];

$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

$isOrgDowntime = false;
if ($organizationID) {
	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));
	$issues = $organization->getIssues();
	$isOrgDowntime = true;
} else {
	$issues = $resource->getIssues();

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

<form id='newDowntimeForm'>
<?php
if ($isOrgDowntime) {
	echo '<input type="hidden" name="sourceOrganizationID" value="'.$organizationID.'" />';
} else {
	echo '<input type="hidden" name="sourceResourceID" value="'.$resourceID.'" />';
}
?>
	<table class="thickboxTable" style="width:98%;background-image:url('images/title.gif');background-repeat:no-repeat;">
		<tr>
			<td colspan="2">
				<h1><?php echo _("Resource Downtime Report");?></h1>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Downtime Start:");?></label></td>
			<td>
				<div>
					<div><i><?php echo _("Date");?></i></div>
					<input class="date-pick" type="text" name="startDate" id="startDate" />
					<span id='span_error_startDate' class='smallDarkRedText addDowntimeError'></span>
				</div>
				<div style="clear:both;">
					<div><i><?php echo _("Time");?></i></div>
<?php
echo buildTimeForm("startTime");
?>
					<span id='span_error_startDate' class='smallDarkRedText addDowntimeError'></span>
				</div>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Downtime Resolution:");?></label></td>
			<td>
				<div>
					<div><i><?php echo _("Date");?></i></div>
					<input class="date-pick" type="text" name="endDate" id="endDate" />
					<span id='span_error_endDate' class='smallDarkRedText addDowntimeError'></span>
				</div>
				<div style="clear:both;">
					<div><i><?php echo _("Time");?></i></div>
<?php
echo buildTimeForm("endTime");
?>
					<span id='span_error_endDate' class='smallDarkRedText addDowntimeError'></span>
				</div>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Problem Type:");?></label></td>
			<td>
				<select class="downtimeType" name="downtimeType">
<?php
			foreach ($downtimeTypeNames as $downtimeType) {
				echo "<option value=".$downtimeType["downtimeTypeID"].">".$downtimeType["shortName"]."</option>";
			}
?>
				</select>
			</td>
		</tr>
		<tr>
<?php
if ($issues) {
?>
			<td><label><?php echo _("Link to open issue:");?></label></td>
			<td>
				<select class="issueID" name="issueID">
					<option value="">none</option>
<?php
			foreach ($issues as $issue) {
				echo "<option".(($issueID == $issue->issueID) ? ' selected':'')." value=".$issue->issueID.">".$issue->subjectText."</option>";
			}
?>
				</select>
			</td>
		</tr>
<?php
}
?>
		<tr>
			<td><label><?php echo _("Note:");?></label></td>
			<td>
				<textarea name="note"></textarea>
			</td>
		</tr>
	</table>

	<table class='noBorderTable' style='width:125px;'>
		<tr>
			<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitNewDowntime' id='submitNewDowntime' class='submit-button'></td>
			<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove();" class='submit-button'></td>
		</tr>
	</table>

</form>

<?php
} else {
	echo '<p>' . _("Creating downtime requires an organization or a resource to be associated with an organization.") . '</p>';
	echo '<input type="button" value="' . _("cancel") . '" onclick="tb_remove();">';
}
?>


