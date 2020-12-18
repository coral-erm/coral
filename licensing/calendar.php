<?php
/*
**************************************************************************************************************************
** CORAL Licensing Module v. 1.0
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
** This page was originally intended as a standalone add-on.  After interest this was added to the Licensing module
** but it was not retrofitted to more tightly integrate into the Licensing module.
*/
include_once 'directory.php';
$pageTitle=_('Home');
include 'templates/header.php';
//used for creating a "sticky form" for back buttons
//except we don't want it to retain if they press the 'index' button
//check what referring script is
	if (isset($_SESSION['ref_script']) && ($_SESSION['ref_script'] != "license.php")){
		$reset='Y';
	}else{
		$reset='N';
	}
$_SESSION['ref_script']=$currentPage;
//below includes search options in left pane only - the results are refreshed through ajax and placed in div searchResults
//print header
$pageTitle=_('Calendar');
$config = new Configuration;
$host = $config->database->host;
$username = $config->database->username;
$password = $config->database->password;
$license_databaseName = $config->database->name;
$resource_databaseName = $config->settings->resourcesDatabaseName;
$link = mysqli_connect($host, $username, $password) or die(_("Could not connect to host."));
mysqli_select_db($link, $license_databaseName) or die(_("Could not find License database."));
mysqli_select_db($link, $resource_databaseName) or die(_("Could not find Resource database."));
$display = array();
$calendarSettings = new CalendarSettings();
try{
	$calendarSettingsArray = $calendarSettings->allAsArray();
}catch(Exception $e){
	echo "<span style='color:red'>"._("There was an error with the CalendarSettings Table please verify the table has been created.")."</span>";
	exit;
}

$startDateName = "subscriptionStartDate";
$endDateName = "subscriptionEndDate";

	foreach($calendarSettingsArray as $display) {
		$config_error = TRUE;
		if (strtolower($display['shortName']) == strtolower('Days After Subscription End')) {
			if (strlen($display['value'])>0) {
				$daybefore = $display['value'];
				$config_error = FALSE;
			}
		} elseif (strtolower($display['shortName']) == strtolower('Days Before Subscription End')) {
			if (strlen($display['value'])>0) {
				$dayafter = $display['value'];
				$config_error = FALSE;
			}
		} elseif (strtolower($display['shortName']) == strtolower('Resource Type(s)')) {
			if (strlen($display['value'])>0) {
				$resourceType = $display['value'];
				$config_error = FALSE;
			}
		} elseif (strtolower($display['shortName']) == strtolower('Authorized Site(s)')) {
			if (strlen($display['value'])>0) {
				$authorizedSiteID = preg_split("/[\s,]+/", $display['value']);
				$config_error = FALSE;
			}
		}
	}

	// Validate the config settings
	if ($config_error) {
		echo "<span style='color:red'>"._("There was an error with the CalendarSettings Configuration.")."</span>";
		exit;
	}

	$query = "
	SELECT DATE_FORMAT(`$resource_databaseName`.`ResourceAcquisition`.`$endDateName`, '%Y') AS `year`,
	DATE_FORMAT(`$resource_databaseName`.`ResourceAcquisition`.`$endDateName`, '%M') AS `month`,
	DATE_FORMAT(`$resource_databaseName`.`ResourceAcquisition`.`$endDateName`, '%y-%m-%d') AS `sortdate`,
	DATE_FORMAT(`$resource_databaseName`.`ResourceAcquisition`.`$endDateName`, '%m/%d/%Y') AS `$endDateName`,
	`$resource_databaseName`.`ResourceAcquisition`.`resourceID`, `$resource_databaseName`.`Resource`.`titleText`,
	`$license_databaseName`.`License`.`shortName`,
	`$license_databaseName`.`License`.`licenseID`, `$resource_databaseName`.`ResourceType`.`shortName` AS resourceTypeName, `$resource_databaseName`.`ResourceType`.`resourceTypeID`
	FROM `$resource_databaseName`.`Resource`
  LEFT JOIN `$resource_databaseName`.`ResourceAcquisition` ON (`$resource_databaseName`.`Resource`.`resourceID` = `$resource_databaseName`.`ResourceAcquisition`.`resourceID`)
	LEFT JOIN `$resource_databaseName`.`ResourceLicenseLink` ON (`$resource_databaseName`.`ResourceAcquisition`.`resourceAcquisitionID` = `$resource_databaseName`.`ResourceLicenseLink`.`resourceAcquisitionID`)
	LEFT JOIN `$license_databaseName`.`License` ON (`ResourceLicenseLink`.`licenseID` = `$license_databaseName`.`License`.`licenseID`)
	INNER JOIN `$resource_databaseName`.`ResourceType` ON (`$resource_databaseName`.`Resource`.`resourceTypeID` = `$resource_databaseName`.`ResourceType`.`resourceTypeID`)
	WHERE
	`$resource_databaseName`.`Resource`.`archiveDate` IS NULL AND
	`$resource_databaseName`.`ResourceAcquisition`.`$endDateName` IS NOT NULL AND
	`$resource_databaseName`.`ResourceAcquisition`.`$endDateName` <> '00/00/0000' AND
	`$resource_databaseName`.`ResourceAcquisition`.`$endDateName` BETWEEN (CURDATE() - INTERVAL " . $daybefore . " DAY) AND (CURDATE() + INTERVAL " . $dayafter . " DAY) ";
	if ($resourceType) {
		$query = $query . " AND `$resource_databaseName`.`Resource`.`resourceTypeID` IN ( ". $resourceType . " ) ";
	}
$query = $query . "ORDER BY `sortdate`, `$resource_databaseName`.`Resource`.`titleText`";
$result = mysqli_query($link, $query) or die(_("Bad Query Failure: ".mysqli_error($link)));
?>

<div style='text-align:left;'>
	<table class="headerTable" style="background-image:url('images/header.gif');background-repeat:no-repeat;">
		<tr style='vertical-align:top;'>
			<td>
				<b><?php echo _("Upcoming License Renewals");?></b>
			</td>
		</tr>
	</table>

	<div id="searchResults">
		<table style="width: 100%;" class="dataTable">
			<tbody>
			<?php
				$mYear = "";
				$mMonth = "";
				$month_html = "";
				$year_html = "";

				$displayYear = FALSE;
				$displayMonth = FALSE;

				$i = -1;
				while ($row = mysqli_fetch_assoc($result)) {
					$query2 = "SELECT
					  `$resource_databaseName`.`Resource`.`resourceID`,
					  `$resource_databaseName`.`AuthorizedSite`.`shortName`,
					  `$resource_databaseName`.`AuthorizedSite`.`authorizedSiteID`
					FROM
					  `$resource_databaseName`.`Resource`
                      INNER JOIN `$resource_databaseName`.`ResourceAcquisition`
                        ON (`$resource_databaseName`.`ResourceAcquisition`.`resourceID` = `$resource_databaseName`.`Resource`.`resourceID`)
					  INNER JOIN `$resource_databaseName`.`ResourceAuthorizedSiteLink` ON (`$resource_databaseName`.`ResourceAcquisition`.`resourceAcquisitionID` = `$resource_databaseName`.`ResourceAuthorizedSiteLink`.`resourceAcquisitionID`)
					  INNER JOIN `$resource_databaseName`.`AuthorizedSite` ON (`$resource_databaseName`.`ResourceAuthorizedSiteLink`.`authorizedSiteID` = `$resource_databaseName`.`AuthorizedSite`.`authorizedSiteID`)
					WHERE
					  `$resource_databaseName`.`Resource`.`resourceID` = " . $row["resourceID"] .
					  " order by `$resource_databaseName`.`AuthorizedSite`.`shortName`";
					$result2 = mysqli_query($link, $query2) or die(_("Bad Query2 Failure"));

					$i = $i + 1;
					$html = "";
						if ($mYear != $row["year"])  {
							$mYear = $row["year"];

							$year_html = "";
							$year_html = $year_html . "<tr>";
							$year_html = $year_html . "<th colspan='2'>
													<table class='noBorderTable'>
														<tbody>
															<tr>
																<td>" . $mYear . "</td>
															</tr>
														</tbody>
													</table>
												</th>";
							$year_html = $year_html . "</tr>";
							$displayYear = TRUE;
						}

						if ($mMonth != $row["month"]) {
							$mMonth = $row["month"];

							$month_html = "";
							$month_html = $month_html . "<tr>";
							$month_html = $month_html . "<th colspan='2'>
													<table class='noBorderTable'>
														<tbody>
															<tr>
																<td>&nbsp;&nbsp;&nbsp;" . $mMonth . "</td>
															</tr>
														</tbody>
													</table>
												</th>";
							$month_html = $month_html . "</tr>";
							$displayMonth = TRUE;
						}

					$html = $html . "<tr>";

						if ($i % 2 == 0) {
							$alt = "alt";
						} else {
							$alt = "";
						}
					$date1 = new DateTime(date("m/d/y"));
					$date2 = new DateTime($row["subscriptionEndDate"]);
					$interval = $date1->diff($date2);
					$num_days = ((($interval->y) * 365) + (($interval->m) * 30) + ($interval->d));

					$html = $html . "<td  colspan='2' class='$alt'>";

					$html = $html . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='../resources/resource.php?resourceID=" . $row["resourceID"] . "'><b>". $row["titleText"] . "</b></a>";
					$html = $html . "&nbsp;&nbsp;[License: ";
						if (is_null($row["licenseID"])) {
							$html = $html . "<i>"._("No associated licenses available.")."</i>";
						} else {
							$html = $html . "<a href='license.php?licenseID=" . $row["licenseID"] . "'>". $row["shortName"] . "</a>";
						}
					$html = $html . " ] - " . $row["resourceTypeName"] . " ";
                    if ($interval->invert) {
                        $html = $html . "- <strong style='color:red'>"._("Expired ").$num_days._(" days ago")."</strong>";
                    } else {
					    $html = $html . _("- Expires in ");

						if ($date1 > $date2) {
							$html = $html . "<span style='color:red'>(" . $num_days . _(" days)")."</span>";
						} else {
							$html = $html . $num_days . _(" days ");
						}
					}
					$k = 0;
					$siteID = array();

						while ($row2 = mysqli_fetch_assoc($result2)) {
							if ($k == 0) {
								$html = $html . "</td></tr>";
								$html = $html . "<tr>
									<td class='$alt'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td class='$alt'>"._("Participants:  ");
							} else {
								$html = $html . ", ";
							}

							$html = $html . $row2["shortName"];
							array_push( $siteID, $row2["authorizedSiteID"] );
							$k = $k + 1;
						}

					$arr3 = array_intersect($authorizedSiteID, $siteID);
					$html = $html . "</td>";
					$html = $html . "</tr>";

					if (count($arr3) > 0) {
						if ($displayYear) {
							echo $year_html;
							$displayYear = FALSE;
						}
						if ($displayMonth) {
							echo $month_html;
							$displayMonth = FALSE;
						}
						echo $html;
					}

				}

			?>
			</tbody>
		</table>
	</div>
</div>
<br />

<?php
  //print footer
  include 'templates/footer.php';
?>
