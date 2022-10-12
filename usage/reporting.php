<?php
$pageTitle = _('Report Options');
include 'templates/header.php';

?>
<script type="text/javascript" src="js/reporting.js"></script>

<table class="headerTable">
<tr><td>
<div class="headerText" style='margin-bottom:9px;'><?php echo _("Publisher / Platform Reporting Administrative Update");?></div>

<b><?php echo _("Directions:") . "</b> " . _("Mark the checkboxes to add / remove specific Platforms or Publishers to the default report list.") . "<br />" . _("Click 'edit report display name' to change the display name in the reporting system for specific Platforms or Publishers.");?>
<br /><br />
<?php


$platformObj = new Platform();
$platform = new Platform();
$platformArray = array();

$platformArray = $platformObj->all();

if (count($platformArray) > 0){

	echo _("Available") . "<br />" . _("As Default") . "<br />" . _("Report");

	echo "<div style='line-height:130%;margin-top:15px;'>";

	foreach($platformArray as $platform) {
		if ($platform->reportDropDownInd == '1') { $reportDropDownInd = 'checked';}else{$reportDropDownInd = '';}

		echo "<div id = 'div_platform_" . $platform->platformID . "'>";
		echo "<input type='checkbox' id='chk_platform_" . $platform->platformID  . "' onclick='javascript:updatePlatformDropDown(" . $platform->platformID  . ");' $reportDropDownInd>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;<span class='PlatformText'>" . $platform->name . "</span>";
		if ($platform->reportDisplayName)  echo "&nbsp;&nbsp;(<i>" . $platform->reportDisplayName . "</i>)";
		echo "&nbsp;&nbsp;<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getReportDisplayForm&height=122&width=248&type=platform&updateID=" . $platform->platformID . "&modal=true\",125,250)' class='thickbox'>" . _("edit report display name") . "</a><br />";
		echo "</div>";
		echo "<span id='span_platform_" . $platform->platformID . "_response' style='color:red'></span>";


		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:showPublisherList('" . $platform->platformID . "');\"><img src='images/arrowright.gif' style='border:0px' alt='show publisher list' name='image_" . $platform->platformID . "' id='image_" . $platform->platformID . "'></a>&nbsp;<a href=\"javascript:showPublisherList('" . $platform->platformID . "');\" name='link_" . $platform->platformID . "' id='link_" . $platform->platformID . "'>" . _("show publisher list") . "</a><br />";

		echo "\n<div id='div_" . $platform->platformID . "' style='display:none;max-width:600px;margin-left:50px;'>";

		//$Publisher_result = mysqli_query($platformObj->getDatabase(), "select Publisher.name Publisher, publisherPlatformID, Publisher.publisherID, pp.reportDisplayName reportPublisher, pp.reportDropDownInd reportPublisherDropDownInd from Publisher_Platform pp, Publisher where pp.publisherID = Publisher.publisherID and platformID = '" . $row['platformID'] . "' order by 1,2;");

		$publisherPlatform = new PublisherPlatform();
		foreach($platform->getPublisherPlatforms() as $publisherPlatform) {
			$publisher = new Publisher(new NamedArguments(array('primaryKey' => $publisherPlatform->publisherID)));

			if ($publisherPlatform->reportDropDownInd == '1') { $reportDropDownInd = 'checked';}else{$reportDropDownInd = '';}

			echo "<div id = 'div_publisher_" . $publisherPlatform->publisherPlatformID . "'>";
			echo "<table><tr valign='top'><td><input type='checkbox' id='chk_publisher_" . $publisherPlatform->publisherPlatformID  . "' onclick='javascript:updatePublisherDropDown(" . $publisherPlatform->publisherPlatformID  . ");' $reportDropDownInd></td>";


			echo "<td>" . $publisher->name;
			if ($publisherPlatform->reportDisplayName)  echo "&nbsp;&nbsp;(<i>" . $publisherPlatform->reportDisplayName . "</i>)";
			echo "&nbsp;&nbsp;<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getReportDisplayForm&height=122&width=248&type=publisher&updateID=" . $publisherPlatform->publisherPlatformID . "&modal=true\",125,250)' class='thickbox'>" . _("edit report display name") . "</a></td></tr></table>";
			echo "</div>";
			echo "<span id='span_publisher_" . $publisherPlatform->publisherPlatformID . "_response' style='color:red'></span>";
		}

		echo "</div>";
		echo "<br />";

	}

	echo "</div>";
}else{
	echo "<i>" . _("No publishers / platforms found.") . "</i>";
}




?>

</td>
</tr>
</table>

<?php include 'templates/footer.php'; ?>
