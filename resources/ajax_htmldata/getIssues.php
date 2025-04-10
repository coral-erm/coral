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
<div class="header">

<h3><?php echo _("Issues/Problems");?></h3>
<span class="addElement">
	<a id="createIssueBtn" class="thickbox" href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getNewIssueForm&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",500,600)'><?php echo _("report new issue");?></a>
</span>
</div>

<!-- TODO: eliminate tables -->
	<table id="issueTable" class='linedFormTable issueTabTable'>
		<tr>
			<td>
				<a href='javascript:void(0);' onclick='javascript:myDialog("ajax_htmldata.php?<?php echo $getIssuesFormData; ?>",500,500)' class="issuesBtn" id="openIssuesBtn"><?php echo _("view open issues");?></a>
				<a <?php echo getTarget(); ?> href="<?php echo $exportIssuesUrl;?>"><img src="images/xls.gif" alt="<?php echo _('Export'); ?>" /></a>
				<div class="issueList" id="openIssues" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<td>
				<a href='javascript:void(0);' onclick='javascript:myDialog("ajax_htmldata.php?archived=1&<?php echo $getIssuesFormData; ?>",500,500)' class="issuesBtn" id="archivedIssuesBtn"><?php echo _("view archived issues");?></a>
				<a <?php echo getTarget(); ?> href="<?php echo $exportIssuesUrl;?>&archived=1"><img src="images/xls.gif" alt="<?php echo('Export'); ?>" /></a>
				<div class="issueList" id="archivedIssues"></div>
			</td>
		</tr>
	</table>
	<div class="header">
		<h3><?php echo _("Downtime");?></h3>
		<span class="addElement">
			<a id="createDowntimeBtn" class="thickbox" href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getNewDowntimeForm&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",300,400)'><?php echo _("report new Downtime");?></a>
		</span>
	</div>

	<table id="downTimeTable" class='linedFormTable issueTabTable'>
			<td>
				<button type="button" onclick='myDialog("ajax_htmldata.php?<?php echo $getDowntimeFormData; ?>",500,500)' class="downtimeBtn btn link" id="openDowntimeBtn"><?php echo _("view current/upcoming downtime");?></button>
				<a <?php echo getTarget(); ?> href="<?php echo $exportDowntimesUrl;?>"><img src="images/xls.gif" alt="<?php echo _('Export'); ?>" /></a>
				<div class="downtimeList" id="currentDowntime" style="display:none;"></div>
			</td>
		</tr>
		<tr>
			<td>
				<button type="button" onclick='myDialog("ajax_htmldata.php?archived=1&<?php echo $getDowntimeFormData; ?>",500,500)' class="downtimeBtn btn link" id="archiveddowntimeBtn"><?php echo _("view archived downtime");?></button>
				<a <?php echo getTarget(); ?> href="<?php echo $exportDowntimesUrl;?>&archived=1"><img src="images/xls.gif" alt="<?php echo _('Export'); ?>" /></a>
				<div class="downtimeList" id="archivedDowntime"></div>
			</td>
		</tr>
	</table>
