<?php
$resourceID = $_GET['resourceID'];
$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
$archivedFlag = (!empty($_GET['archived']) && $_GET['archived'] == 1) ? true:false;

$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));
$util = new Utility();


//shared html template for organization and resource issues
function generateIssueHTML($issue,$associatedEntities=null) {
	$html = "<div id='openIssues'>
	<div class=\"issue\">";
	if (!$issue->dateClosed) {
		$html .= "
		<a class=\"thickbox action closeIssueBtn\" href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getCloseIssueForm&issueID={$issue->issueID}&height=120&width=345&modal=true\",150,400)'>" . _("close") . "</a>";
		if ($associatedEntities && $associatedEntities[0]['entityType']==1) {
			$html .= "<a class=\"thickbox action\" href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getNewDowntimeForm&organizationID={$associatedEntities[0]['id']}&issueID={$issue->issueID}&height=200&width=390&modal=true\",25,420)'>" . _("downtime") . "</a>";
		} else {
			$html .= "<a class=\"thickbox action\" href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getNewDowntimeForm&resourceID={$GLOBALS['resourceID']}&issueID={$issue->issueID}&height=200&width=390&modal=true\",250,420)'>" . _("downtime") . "</a>";
		}
	}
	$html .= "
	  	<dl>
	  		<dt>" . _("Date reported:") . "</dt>
	  		<dd>{$issue->dateCreated}</dd>";
	if ($issue->dateClosed) {

		$html .= "<dt>" . _("Date closed:") . "</dt>
	  		<dd>{$issue->dateClosed}</dd>
	  		<dt>Resolution</dt>
	  		<dd>{$issue->resolutionText}</dd>";
	  	}

	$html .= "<dt>" . _("Contact(s):") . "</dt>
	  		<dd>";
	$contacts = $issue->getContacts();
	if ($contacts) {
		$html .= "<ul class='unstyled'>";
		foreach($contacts as $contact) {
			if (!empty($contact['name'])) {
				$html .= "<li><a href=\"mailto:".urlencode($contact['emailAddress'])."?Subject=RE: {$issue->subjectText}\">{$contact['name']}</a></li>";
			} else {
				$html .= "<li><a href=\"mailto:".urlencode($contact['emailAddress'])."?Subject=RE: {$issue->subjectText}\">{$contact['emailAddress']}</a></li>";
			}
		}
		$html .= "</ul>";
	}


	$html .= "	</dd>
	  		<dt>" . _("Applies to:") . "</dt>
	  		<dd>";
	if ($associatedEntities) {
		$temp ='';
		foreach ($associatedEntities as $entity) {
			$temp .= " {$entity['name']},";
		}
		$html .= rtrim($temp,',');
	}

	$html .= "</dd>
        <dt>" . _("Subject:") . "</dt>
        <dd>{$issue->subjectText}</dd>
        <dt>" . _("Body:") . "</dt>
        <dd>{$issue->bodyText}</dd>
        </dl>
    </div></div>";
	return $html;
}

//display any organization level issues for the resource
$organizationArray = $resource->getOrganizationArray();

if (is_array($organizationArray) && count($organizationArray) > 0) {
	echo '<h2 class="headerText">' . _("Organizational") . '</h2>';

	$issuedOrgs = array();
	foreach ($organizationArray as $orgData) {
		if (!in_array($orgData['organizationID'],$issuedOrgs)) {
			$organization = new Organization(new NamedArguments(array('primaryKey' => $orgData['organizationID'])));
			$orgIssues = $organization->getIssues($archivedFlag);

			if(count($orgIssues) > 0) {
				foreach ($orgIssues as $issue) {
					echo generateIssueHTML($issue,array(array("name"=>$orgData['organization'],"id"=>$orgData['organizationID'],"entityType"=>1)));
				}
			}

			$orgIssues = null;
			$issuedOrgs[] = $orgData['organizationID'];
		}
	}
	if (count($issuedOrgs) < 1) {
		echo "<p>" . _("There are no organization level issues.") . "</p>";
	}
}

//display any resource level issues for the resource (shows any other resources associated with the issue, too)
$resourceIssues = $resourceAcquisition->getIssues($archivedFlag);
echo '<h2 class="headerText">' . _("Resources") . '</h2>';
if(count($resourceIssues) > 0) {
	foreach ($resourceIssues as $issue) {
		$associatedEntities = array();
		if ($associatedResources = $issue->getAssociatedResources()) {
			foreach ($associatedResources as $resource) {
				$associatedEntities[] = array("name"=>$resource->titleText,"id"=>$resource->resourceID,"entityType"=>2);
			}
		}
		echo generateIssueHTML($issue,$associatedEntities);
	}
} else {
	echo "<p>" . _("There are no order level issues.") . "</p>";
}
?>
