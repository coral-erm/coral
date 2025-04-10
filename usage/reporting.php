<?php
$pageTitle = _('Report Options');
include 'templates/header.php';

?>
<script type="text/javascript" src="js/reporting.js"></script>

<main id="main-content">
	<article>
	<h2><?php echo _("Publisher / Platform Reporting Administrative Update");?></h2>
	<h3><?php echo _("Directions:"); ?></h3>
	<p><?php echo _("Mark the checkboxes to add / remove specific Platforms or Publishers to the default report list."); ?></p>
	<p><?php echo _("Click 'edit report display name' to change the display name in the reporting system for specific Platforms or Publishers."); ?></p>
<?php


$platformObj = new Platform();
$platform = new Platform();
$platformArray = array();

$platformArray = $platformObj->all();

if (is_array($platformArray) && count($platformArray) > 0) {
	// TODO: i18n placeholders
	echo _("Available") . "<br />" . _("As Default") . "<br />" . _("Report");

	foreach($platformArray as $platform) {
		if ($platform->reportDropDownInd == '1') { $reportDropDownInd = 'checked';}else{$reportDropDownInd = '';}

		echo "<div id = 'div_platform_" . $platform->platformID . "'>";
		echo "<input type='checkbox' id='chk_platform_" . $platform->platformID  . "' onclick='javascript:updatePlatformDropDown(" . $platform->platformID  . ");' $reportDropDownInd>";
		echo "<label for='chk_platform_" . $platform->platformID  . "'>" . $platform->name . "</label>";
		if ($platform->reportDisplayName)  echo " (<i>" . $platform->reportDisplayName . "</i>) ";
		echo "<button type='button' onclick='myDialog(\"ajax_forms.php?action=getReportDisplayForm&height=122&width=248&type=platform&updateID=" . $platform->platformID . "&modal=true\",125,250)' class='thickbox btn link'>" . _("edit report display name") . "</button>";
		echo "</div>";
		echo "<p class='error' id='span_platform_" . $platform->platformID . "_response'></p>";


		echo "<button type='button' class='link' onclick=\"showPublisherList('" . $platform->platformID . "');\"><img src='images/arrowright.gif' alt='show publisher list' name='image_" . $platform->platformID . "' id='image_" . $platform->platformID . "'></button>";
		echo "<button type='button' class='link' onclick=\"showPublisherList('" . $platform->platformID . "');\" name='link_" . $platform->platformID . "' id='link_" . $platform->platformID . "'>" . _("show publisher list") . "</button>";

		echo "\n<div id='div_" . $platform->platformID . "' style='display:none;'>";

		//$Publisher_result = mysqli_query($platformObj->getDatabase(), "select Publisher.name Publisher, publisherPlatformID, Publisher.publisherID, pp.reportDisplayName reportPublisher, pp.reportDropDownInd reportPublisherDropDownInd from Publisher_Platform pp, Publisher where pp.publisherID = Publisher.publisherID and platformID = '" . $row['platformID'] . "' order by 1,2;");

		$publisherPlatform = new PublisherPlatform();
		echo "<table class='table-border'>
			<tbody>";
		foreach($platform->getPublisherPlatforms() as $publisherPlatform) {
			$publisher = new Publisher(new NamedArguments(array('primaryKey' => $publisherPlatform->publisherID)));

			if ($publisherPlatform->reportDropDownInd == '1') { $reportDropDownInd = 'checked';}else{$reportDropDownInd = '';}

			echo "<tr id='div_publisher_" . $publisherPlatform->publisherPlatformID . "'><th scope='row'>";
			echo "<td><input type='checkbox' id='chk_publisher_" . $publisherPlatform->publisherPlatformID  . "' onclick='javascript:updatePublisherDropDown(" . $publisherPlatform->publisherPlatformID  . ");' $reportDropDownInd></td>";
			echo "<td><label for=id='chk_publisher_" . $publisherPlatform->publisherPlatformID  . "'>" . $publisher->name . '</label>';
			if ($publisherPlatform->reportDisplayName)  
				echo " (<i>" . $publisherPlatform->reportDisplayName . "</i>) ";
			echo "</td>";
			echo "<td class='actions'><button type='button' onclick='myDialog(\"ajax_forms.php?action=getReportDisplayForm&height=122&width=248&type=publisher&updateID=" . $publisherPlatform->publisherPlatformID . "&modal=true\",125,250)' class='thickbox btn link'>" . _("edit report display name") . "</button>";
			echo "</td></tr>";
			echo "<tr><td rowspan='3' id='span_publisher_" . $publisherPlatform->publisherPlatformID . "_response' class='error'></td></tr>";
		}

		echo "</tbody></table></div>";

	}

}else{
	echo "<p><i>" . _("No publishers / platforms found.") . "</i></p>";
}

?>
</article>
</main>
<?php include 'templates/footer.php'; ?>
</body>
</html>