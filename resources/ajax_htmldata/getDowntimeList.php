<?php
$resourceID = $_GET['resourceID'];
$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
$archivedFlag = (!empty($_GET['archived']) && $_GET['archived'] == 1) ? true:false;

$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));
$util = new Utility();


//shared html template for organization and resource downtimes
function generateDowntimeHTML($downtime,$associatedEntities=null) {

	$html = "
	<div class=\"downtime\">";

	$html .= "
	  	<dl>
	  		<dt>" . _("Type:") . "</dt>
	  		<dd>{$downtime->shortName}</dd>

	  		<dt>" . _("Downtime Start:") . "</dt>
	  		<dd>{$downtime->startDate}</dd>
	  		<dt>" . _("Downtime Resolved:") . "</dt>
	  		<dd>";
	if ($downtime->endDate != null) {
		$html .= $downtime->endDate;
	} else {
		$html .= "<a class=\"thickbox\" href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getResolveDowntimeForm&height=363&width=345&modal=true&downtimeID={$downtime->downtimeID}\",400,400)'>Resolve</a>";
	}
	$html .= '</dd>';

	if($downtime->subjectText) {
		$html .= "
	  		<dt>" . _("Linked issue:") . "</dt>
	  		<dd>{$downtime->subjectText}</dd>";
	}

	if ($downtime->note) {
		$html .= "
	  		<dt>" . _("Note:") . "</dt>
	  		<dd>{$downtime->note}</dd>";
	}

	$html .= "
		</dl>
	</div>";
	
	return $html;
}

//display any organization level downtimes for the resource
$organizationArray = $resource->getOrganizationArray();

if (is_array($organizationArray) && count($organizationArray) > 0) {
	echo '<h2 class="headerText">' . _("Organizational") . '</h2>';

	$downtimedOrgs = array();
	foreach ($organizationArray as $orgData) {
		if (!in_array($orgData['organizationID'],$downtimedOrgs)) {
			$organization = new Organization(new NamedArguments(array('primaryKey' => $orgData['organizationID'])));

			$orgDowntimes = $organization->getDowntime($archivedFlag);

			if(count($orgDowntimes) > 0) {
				foreach ($orgDowntimes as $downtime) {
					echo generateDowntimeHTML($downtime);
				}
			} else {
				echo "<p>" . _("There are no organization level downtimes.") . "</p>";
			}

			$orgDowntimes = null;
			$downtimedOrgs[] = $orgData['organizationID'];
		}
	}
}

//display any resource level downtimes for the resource (shows any other resources associated with the downtime, too)
$resourceDowntimes = $resourceAcquisition->getDowntime($archivedFlag);
echo '<h2 class="headerText">' . _("Resources") . '</h2>';
if(count($resourceDowntimes) > 0) {
	foreach ($resourceDowntimes as $downtime) {
		echo generateDowntimeHTML($downtime);
	}
} else {
	echo "<p>" . _("There are no order level downtimes.") . "</p>";
}
?>
