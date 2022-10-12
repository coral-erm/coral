<?php
	$resourceID = $_GET['resourceID'];
    $resourceAcquisitionID = $_GET['resourceAcquisitionID'];
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
    $resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

	$util = new Utility();
	$getIssuesFormData = "action=getIssuesList&resourceID=".$resourceID . "&resourceAcquisitionID=" . $resourceAcquisitionID;
	$getDowntimeFormData = "action=getDowntimeList&resourceID=".$resourceID . "&resourceAcquisitionID=" . $resourceAcquisitionID;
	$exportIssuesUrl = "export_issues.php?resourceID={$resourceID}&resourceAcquisitionID=" . $resourceAcquisitionID;
	$exportDowntimesUrl = "export_downtimes.php?resourceID={$resourceID}&resourceAcquisitionID=" . $resourceAcquisitionID;


?>

	<table id="issueTable" class='linedFormTable issueTabTable'>
		<tr>
			<th><?php echo _("Issues/Problems");?></th>
		</tr>
		<tr>
			<td><a id="createIssueBtn" class="thickbox" href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getNewIssueForm&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",500,600)'><?php echo _("report new issue");?></a></td>
		</tr>
		<tr>
			<td>
				<!-- <a href="<?php echo $getIssuesFormData; ?>" class="issuesBtn" id="openIssuesBtn"><?php echo _("view open issues");?></a>  -->
				<a href='javascript:void(0);' onclick='javascript:myDialog("action=getIssuesList&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",500,500)' class="issuesBtn" id="openIssuesBtn"><?php echo _("view open issues");?></a> 
				<a target="_blank" href="<?php echo $exportIssuesUrl;?>"><img src="images/xls.gif" /></a>
				<div class="issueList" id="openIssues" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<td>
				<a href="<?php echo $getIssuesFormData."&archived=1"; ?>" class="issuesBtn" id="archivedIssuesBtn"><?php echo _("view archived issues");?></a> 
				<a target="_blank" href="<?php echo $exportIssuesUrl;?>&archived=1"><img src="images/xls.gif" /></a>
				<div class="issueList" id="archivedIssues"></div>
			</td>
		</tr>
	</table>

	<table id="downTimeTable" class='linedFormTable issueTabTable'>
		<tr>
			<th><?php echo _("Downtime");?></th>
		</tr>
		<tr>
			<td><a id="createDowntimeBtn" class="thickbox" href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getNewDowntimeForm&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",300,400)'><?php echo _("report new Downtime");?></a></td>
		</tr>
		<tr>
			<td>
				<a href="<?php echo $getDowntimeFormData; ?>" class="downtimeBtn" id="openDowntimeBtn"><?php echo _("view current/upcoming downtime");?></a> 
				<a target="_blank" href="<?php echo $exportDowntimesUrl;?>"><img src="images/xls.gif" /></a>
				<div class="downtimeList" id="currentDowntime" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<td>
				<a href="<?php echo $getDowntimeFormData."&archived=1"; ?>" class="downtimeBtn" id="archiveddowntimeBtn"><?php echo _("view archived downtime");?></a> 
				<a target="_blank" href="<?php echo $exportDowntimesUrl;?>&archived=1"><img src="images/xls.gif" /></a>
				<div class="downtimeList" id="archivedDowntime"></div>
			</td>
		</tr>
	</table>

