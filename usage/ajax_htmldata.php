<?php
/*
**************************************************************************************************************************
** CORAL Usage Statistics Module
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
**************************************************************************************************************************
** ajax_htmldata.php formats display data for all pages - tables and search results
**
** when ajax_htmldata.php is called through ajax, 'action' parm is required to dictate which data will be returned
**
**************************************************************************************************************************
*/

include_once 'directory.php';
include "common.php";

$action = $_REQUEST['action'];
$classAdd = "";

switch ($action) {



    case 'getImportDetails':

		if (isset($_GET['publisherPlatformID']) && ($_GET['publisherPlatformID'] != '')){
	    	$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));
	    	$platform = new Platform(new NamedArguments(array('primaryKey' => $publisherPlatform->platformID)));
	    }else{
	    	$platform = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));
	    }

		?>

		<h3><?php echo _("Import History for ") . $platform->name; ?></h3>

		<div id="div_imports">

		<?php


		$importLogArray = array();
		$importLogArray = $platform->getImportLogs();
		$importLog = new ImportLog();

		if (is_array($importLogArray) && count($importLogArray) > 0) {

			echo "<table class='dataTable table-border table-striped'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th scope='col'>" . _("Import Date") . "</th>";
			echo "<th scope='col'>" . _("Last Updated By") . "</th>";
			echo "<th scope='col'>" . _("Import Summary") . "</th>";
			echo "<th scope='col'>" . _("Log") . "</th>";
			echo "<th scope='col'>" . _("Archive") . "</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";

			$i=0;

			foreach($importLogArray as $importLog) {
				echo "<tr>";
				echo "<td>" . format_date($importLog->importDateTime) . "</td>";
				echo "<td>" . $importLog->loginID . "</td>";
				echo "<td>" . nl2br($importLog->details) . "</td>";
				echo "<td><a href='" . $importLog->logFileURL . "'>" . _("view log") . "</a></td>";
				echo "<td><a href='" . $importLog->archiveFileURL . "'>" . _("view archive") . "</a></td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";
		}else{
			echo _("(no imports found)");

		}

		echo "</div>";
		break;

    case 'getLoginDetails':

		if (isset($_GET['publisherPlatformID']) && ($_GET['publisherPlatformID'] != '')){
	    	$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));

			?>

			<h3><?php echo _("Publisher Logins");?></h3>

			<div id="div_logins">

			<?php


			$externalLoginArray = array();
			$externalLoginArray = $publisherPlatform->getExternalLogins();
			$externalLogin = new ExternalLogin();


			if (is_array($externalLoginArray) && count($externalLoginArray) > 0) {
			?>

			<table class='verticalFormTable table-border table-striped'>
			<thead>
			<tr>
			<th scope="col"><?php echo _("Interface Login");?></th>
			<th scope="col"><?php echo _("Password");?></th>
			<th scope="col"><?php echo _("URL");?></th>
			<th scope="col"><?php echo _("Login Notes");?></th>
			<th scope="col"><?php echo _("Actions");?></th>
			</tr>
			</thead>
			<tbody>
			<?php

			foreach($externalLoginArray as $externalLogin) {
				echo "<tr>";
				echo "<th scope='row'>" . $externalLogin->username . "</th>";
				echo "<td>" . $externalLogin->password . "</td>";
				echo "<td>" . $externalLogin->loginURL . "</td>";
				echo "<td>" . $externalLogin->noteText . "</td>";
				echo "<td class='actions'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getLoginForm&externalLoginID=" . $externalLogin->externalLoginID . "&height=250&width=425&modal=true\",250,425)' class='thickbox' style='font-size:100%;'>" . _("edit") . "</a><br /><a href='javascript:deleteExternalLogin(" . $externalLogin->externalLoginID . ");' style='font-size:100%;'>" . _("remove") . "</a></td>";
				echo "</tr>";

			}

			?>
			</tbody>
			</table>

			<?php
			}else{
				echo _("(none found)");
			}
			?>


			</div>
		<p>
			<a href='javascript:void(0)' onlcick='myDialog("ajax_forms.php?action=getLoginForm&publisherPlatformID=<?php echo $publisherPlatform->publisherPlatformID; ?>&height=250&width=425&modal=true",250,425)' class='thickbox' id='uploadDocument'><?php echo _("add new login");?></a>
		</p>

		<?php
		//Platform record
		}else{
			$platform = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));

			?>


			<h3><?php echo _("Interface Logins");?></h3>

			<div id="div_logins">

			<?php


			$externalLoginArray = array();
			$externalLoginArray = $platform->getExternalLogins();
			$externalLogin = new ExternalLogin();


			if (is_array($externalLoginArray) && count($externalLoginArray) > 0) {
			?>

			<table class='verticalFormTable table-border table-striped'>
			<thead>
			<tr>
			<th scope="col"><?php echo _("Interface Login");?></th>
			<th scope="col"><?php echo _("Password");?></th>
			<th scope="col"><?php echo _("URL");?></th>
			<th scope="col"><?php echo _("Login Notes");?></th>
			<th scope="col"><?php echo _("Actions");?></th>
			</tr>
			</thead>
			<tbody>

			<?php

			foreach($externalLoginArray as $externalLogin) {
				echo "<tr>";
				echo "<th scope='row'>" . $externalLogin->username . "</th>";
				echo "<td>" . $externalLogin->password . "</td>";
				echo "<td>" . $externalLogin->loginURL . "</td>";
				echo "<td>" . $externalLogin->noteText . "</td>";
				echo "<td><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getLoginForm&externalLoginID=" . $externalLogin->externalLoginID . "&height=250&width=425&modal=true\",250,425)' class='thickbox' style='font-size:100%;'>" . _("edit") . "</a><br /><a href='javascript:deleteExternalLogin(" . $externalLogin->externalLoginID . ");' style='font-size:100%;'>" . _("remove") . "</a></td>";
				echo "</tr>";

			}

			?>
			</tbody>
			</table>

			<?php
			}else{
				echo _("(none found)");
			}
			?>


			</div>

			<p>
			<a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getLoginForm&platformID=<?php echo $platform->platformID;?>&height=250&width=325&modal=true",250,425)' class='thickbox' id='uploadDocument'><?php echo _("add new login");?></a>
			</p>	
		<?php
		}

    	$config = new Configuration();
    	$util = new Utility();

		//both publishers and platforms will have organizations lookup
		if ($config->settings->organizationsModule == 'Y'){
			echo "<h3>" . _("Organization Accounts") . "</h3>";

			if (isset($_GET['publisherPlatformID']) && ($_GET['publisherPlatformID'] != '')){
				$publisherPlatformID = $_GET['publisherPlatformID'];
				$platformID = '';
				$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));
			}else{
				$publisherPlatformID = '';
				$platformID = $_GET['platformID'];
				$obj = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));
			}


			//if this publisher platform is already set up with an organization
			if (($obj->organizationID != '') && ($obj->organizationID != 0)){

				$externalLoginArray = $obj->getOrganizationExternalLogins();

				if (is_array($externalLoginArray) && count($externalLoginArray) > 0) {
					?>
					<?php echo _("For ");?><?php echo $obj->getOrganizationName() . "&nbsp;&nbsp;<a href='" . $util->getOrganizationURL() . $obj->organizationID . "' " . getTarget() . ">" . _("view organization") . "</a>";?>
					<table class='verticalFormTable table-border table-striped'>
					<thead>
					<tr>
					<th><?php echo _("Login Type");?></th>
					<th><?php echo _("Username");?></th>
					<th><?php echo _("Password");?></th>
					<th><?php echo _("URL");?></th>
					<th><?php echo _("Notes");?></th>
					</tr>
					</thead>
					<tbody>

					<?php
					foreach ($externalLoginArray as $externalLogin){
						echo "<tr>";
						echo "<td>" . $externalLogin['externalLoginType'] . "</td>";
						echo "<td>" . $externalLogin['username'] . "</td>";
						echo "<td>" . $externalLogin['password'] . "</td>";
						echo "<td>" . $externalLogin['loginURL'] . "</td>";
						echo "<td>" . $externalLogin['noteText'] . "</td>";
						echo "</tr>";

					}
					echo "</tbody>";
					echo "</table>";

				}else{
					echo "<i>" . _("No login information stored for ") . $obj->getOrganizationName . "</i>&nbsp;&nbsp;<a href='" . $util->getOrganizationURL() . $obj->organizationID . "' " . getTarget() . ">" . _("view organization") . "</a>";
				}

				?>
				<p>
				<a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getOrganizationForm&platformID=<?php echo $platformID; ?>&publisherPlatformID=<?php echo $publisherPlatformID; ?>&height=150&width=285&modal=true",150,300)' class='thickbox'><?php echo _("change associated organization");?></a>
				</p>
				<?php

			//display form for adding organizations
			}else{
				?>

					<p>
					<a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getOrganizationForm&platformID=<?php echo $platformID; ?>&publisherPlatformID=<?php echo $publisherPlatformID; ?>&height=150&width=285&modal=true",150,300)' class='thickbox'><?php echo _("link to associated organization");?></a>
					</p>

				<?php
			}


			//additionally, display any login records belonging to publishers below this platform
			if (isset($_GET['platformID']) && ($_GET['platformID'] != '')){


				$pubArray = array();
				foreach ($platform->getPublisherPlatforms() as $publisherPlatform){
					$orgArray = $publisherPlatform->getOrganizationExternalLogins();
					$externalLoginArray = $publisherPlatform->getExternalLogins();

					if ((is_array($orgArray) && count($orgArray) > 0) || (is_array($externalLoginArray) && count($externalLoginArray) > 0)){
						$pub = new Publisher(new NamedArguments(array('primaryKey' => $publisherPlatform->publisherID)));
						$pubArray[$publisherPlatform->publisherID] = $pub->name;
					}

				}

				if (is_array($pubArray) && count($pubArray) > 0) {
					echo "<br />";
					echo _("Login Credentials are also available for the following publishers:") . "<br />";

					foreach ($pubArray as $pubID => $pubName){
						echo "<a href='publisherPlatform.php?publisherPlatformID=" . $pubID . "'>" . $pubName . "</a><br />";
					}

				}


			}

		}

		echo "<br /><br /><br />";

		//Notes
		if (isset($_GET['publisherPlatformID']) && ($_GET['publisherPlatformID'] != '')){
	    	$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));

			?>

			<h3><?php echo _("Publisher Notes");?></h3>

			<div id="div_noteText">

			<?php

			$publisherPlatformNoteArray = array();
			$publisherPlatformNoteArray = $publisherPlatform->getPublisherPlatformNotes();
			$publisherPlatformNote = new PublisherPlatformNote();

			if (is_array($publisherPlatformNoteArray) && count($publisherPlatformNoteArray) > 0) {
			?>

			<table class='verticalFormTable table-border table-striped'>
			<thead>
			<tr>
			<th scope="col"><?php echo _("Start Year");?></th>
			<th scope="col"><?php echo _("End Year");?></th>
			<th scope="col"><?php echo _("Notes");?></th>
			<th scope="col"><?php echo _("Actions");?></th>
			</tr>
			</thead>
			<tbody>

			<?php

			foreach($publisherPlatformNoteArray as $publisherPlatformNote) {
				if (($publisherPlatformNote->endYear == '0') || ($publisherPlatformNote->endYear =='')) $endYear = _('Present'); else $endYear = $publisherPlatformNote->endYear;

				echo "<tr>";
				echo "<td>" . $publisherPlatformNote->startYear . "</td>";
				echo "<td>" . $endYear . "</td>";
				echo "<td>" . $publisherPlatformNote->noteText . "</td>";
				echo "<td><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getPublisherNoteForm&publisherPlatformNoteID=" . $publisherPlatformNote->publisherPlatformNoteID . "&height=225&width=313&modal=true\",250,425)' class='thickbox' style='font-size:100%;'>" . _("edit") . "</a><br /><a href='javascript:deletePublisherNote(" . $publisherPlatformNote->publisherPlatformNoteID . ");' style='font-size:100%;'>" . _("remove") . "</a></td>";
				echo "</tr>";

			}

			?>
			</tbody>
			</table>

			<?php }else{ echo _("(none found)"); } ?>
			</div>

			<br />

			<a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getPublisherNoteForm&publisherPlatformNoteID=&publisherPlatformID=<?php echo $publisherPlatform->publisherPlatformID; ?>&height=225&width=313&modal=true",250,425)' class='thickbox' id='uploadDocument'><?php echo _("add new publisher notes");?></a>


			<br />
			<br />

		<?php
		//Platform record
		}else{
			$platform = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));

			?>

			<h3><?php echo _("Interface Notes");?></h3>

			<div id="div_interfaces">

			<?php



			$platformNoteArray = array();
			$platformNoteArray = $platform->getPlatformNotes();
			$platformNote = new PlatformNote();

			if (is_array($platformNoteArray) && count($platformNoteArray) > 0) {

			?>

			<table class='verticalFormTable table-border table-striped'>
			<thead>
			<tr>
			<th scope="col"><?php echo _("Start Year");?></th>
			<th scope="col"><?php echo _("End Year");?></th>
			<th scope="col"><?php echo _("Counter") . '<br />' . _("Compliant?");?></th>
			<th scope="col"><?php echo _("Interface Notes");?></th>
			<th scope="col"><?php echo _("Actions");?></th>
			</tr>
			</thead>
			<tbody>

			<?php

			foreach($platformNoteArray as $platformNote) {
				if ($platformNote->counterCompliantInd == "1") {
					$counterCompliantInd = 'Y';
				}elseif ($platformNote->counterCompliantInd == "0"){
					$counterCompliantInd = 'N';
				}else{
					$counterCompliantInd = '';
				}
				if (($platformNote->endYear == '0') || ($platformNote->endYear =='')) $endYear = 'Present'; else $endYear = $platformNote->endYear;


				echo "<tr>";
				echo "<td>" . $platformNote->startYear . "</td>";
				echo "<td>" . $endYear . "</td>";
				echo "<td>" . $counterCompliantInd . "</td>";
				echo "<td>" . $platformNote->noteText . "</td>";
				echo "<td><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getPlatformNoteForm&platformNoteID=" . $platformNote->platformNoteID . "&height=255&width=408&modal=true\",260,410)' class='thickbox' style='font-size:100%;'>" . _("edit") . "</a><br /><a href='javascript:deletePlatformNote(" . $platformNote->platformNoteID . ");' style='font-size:100%;'>" . _("remove") . "</a></td>";
				echo "</tr>";

			}

			?>
			</tbody>
			</table>

			<?php }else{ echo _("(none found)"); } ?>
			</div>

			<p>

			<a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getPlatformNoteForm&platformNoteID=&platformID=<?php echo $platform->platformID; ?>&height=255&width=408&modal=true",260,410)' class='thickbox' id='addInterface'><?php echo _("add new interface note");?></a>

		</p>
		<?php
		}

        break;



	case 'getSushiDetails':

		$publisherPlatformID = $_GET['publisherPlatformID'];
		$platformID = $_GET['platformID'];

		if ($platformID){
			$sushiService = new SushiService();
			$sushiService->getByPlatformID($platformID);
		}else{

			$sushiService = new SushiService();
			$sushiService->getByPublisherPlatformID($publisherPlatformID);
		}

		echo "<h3>" . _("SUSHI Connection") . "</h3>";
		// TODO: a11y: eliminate tables (use dl.dl-grid?)
		if (($sushiService->platformID != '') || ($sushiService->publisherPlatformID != '')){
			echo "<table class='verticalFormTable table-border table-striped'>";
			echo "<tr><td>" . _("Service URL") . "</td><td>" . $sushiService->serviceURL . "</td></tr>";
			echo "<tr><td>" . _("WSDL URL") . "</td><td>" . $sushiService->wsdlURL . "</td></tr>";
			echo "<tr><td>" . _("COUNTER Release") . "</td><td>" . $sushiService->releaseNumber . "</td></tr>";
			echo "<tr><td>" . _("Report Layouts") . "</td><td>" . $sushiService->reportLayouts . "</td></tr>";
			echo "<tr><td>" . _("Requestor ID") . "</td><td>" . $sushiService->requestorID . "</td></tr>";
      echo "<tr><td>" . _("API Key") . "</td><td>" . $sushiService->apiKey . "</td></tr>";
			echo "<tr><td>" . _("Customer ID") . "</td><td>" . $sushiService->customerID . "</td></tr>";
			echo "<tr><td>" . _("Platform") . "</td><td>" . $sushiService->platform . "</td></tr>";
			echo "<tr><td>" . _("Security") . "</td><td>" . $sushiService->security . "</td></tr>";
			echo "<tr><td>" . _("Login") . "</td><td>" . $sushiService->login . "</td></tr>";
			echo "<tr><td>" . _("Password") . "</td><td>" . $sushiService->password . "</td></tr>";
			echo "<tr><td>" . _("Service Day") . "</td><td>" . $sushiService->serviceDayOfMonth . _(" (day of month)") . "</td></tr>";
			echo "<tr><td>" . _("Notes") . "</td><td>" . $sushiService->noteText . "</td></tr>";
			echo "</table>";
			echo "<br /><br /><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getSushiForm&sushiServiceID=" . $sushiService->sushiServiceID . "&platformID=" . $platformID . "&height=530&width=518&modal=true\",530, 520)' class='thickbox'>" . _("Edit SUSHI Connection Info") . "</a><br />";
			echo "<br /><div id='div_test_service'><a href='javascript:testService(" . $sushiService->sushiServiceID . ")'>" . _("Test SUSHI Connection") . "</a></div><br />";
		}else{
			echo "\n" . _("(none found)") . "<br /><br /><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getSushiForm&sushiServiceID=&platformID=" . $platformID . "&height=530&width=518&modal=true\",530,520)' class='thickbox'>" . _("Add SUSHI Connection") . "</a><br />";

		}


		echo "<br /><br /><img src='images/help.gif' style='float:left;'>&nbsp;&nbsp;";
		echo _("Visit the ") . "<a href='https://registry.projectcounter.org' " . getTarget() . ">" . _("COUNTER Registry") . "</a>" . _(" for information about adding your provider.");

        break;


	break;


    case 'getFullStatsDetails':
		//determine config settings for outlier usage
		$config = new Configuration();

		if (isset($_GET['publisherPlatformID']) && ($_GET['publisherPlatformID'] != '')){
			$publisherPlatformID = $_GET['publisherPlatformID'];
      $deleteParam = "publisherPlatformID=$publisherPlatformID";
			$platformID = '';
		}else{
			$platformID = $_GET['platformID'];
      $deleteParam = "platformID=$platformID";
			$publisherPlatformID = '';
		}

		$statsArray = array();
		if ($publisherPlatformID){
			$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $publisherPlatformID)));
			$statsArray = $publisherPlatform->statOverview();
		}else{
			$platform = new Platform(new NamedArguments(array('primaryKey' => $platformID)));
			$statsArray = $platform->statOverview();
		}
    echo "<h3>" . _("Statistics Management") . "</h3>";

		if (count($statsArray) == 0) {
       echo _("(none found)");
      exit;
    }

    echo "<a href=\"deletePublisherPlatformConfirmation.php?$deleteParam&statsOnly=true\" class=\"end\">". _('Delete All Stats') ."</a>";

		$nested = array();

		foreach($statsArray as $stat) {
      if(array_key_exists($stat['layoutID'], $nested)) {
        if (array_key_exists($stat['year'], $nested[$stat['layoutID']]['statsByYear'])) {
          $nested[$stat['layoutID']]['statsByYear'][$stat['year']][] = $stat;
        } else {
          $nested[$stat['layoutID']]['statsByYear'][$stat['year']] = array($stat);
        }
      } else {
        $nested[$stat['layoutID']] = array(
          'name' => $stat['layoutName'],
          'statsByYear' => array(
            $stat['year'] => array($stat)
          )
        );
      }
    }

		foreach($nested as $layoutID => $report) {
      echo "<div class='bigBlueText'>" ._($report['name']). "</div>";
      foreach($report['statsByYear'] as $year => $months) {
        echo "<div class='boldBlueText'>$year</div>";
        uasort($months, function($a, $b) {
          if ($a['month'] == $b['month']) {
            return 0;
          }
          return ($a['month'] < $b['month']) ? -1 : 1;
        });
        echo "<table class='verticalFormTable'>";
        echo "<tr>";
        echo "<th colspan='2'><a ". getTarget() ." href='spreadsheet.php?publisherPlatformID=" .  $publisherPlatformID . "&platformID=" . $platformID . "&year=" . $year . "&layoutID=" . $layoutID . "' style='font-size:110%;'>" . _("View Stats") . "</a></td>";
        echo "</tr>";
        foreach($months as $month) {
					// TODO: i18n placeholders
          echo "<tr>";
          echo "<td>" . numberToMonth($month['month']) . " " . $year . "</td>";
          echo "<td><a href=\"javascript:deleteMonth('" . $month['layoutID'] . "','" . $month['month'] . "','" . $year . "','" . $month['archiveInd'] . "', '" . $publisherPlatformID . "', '" . $platformID . "')\" style='font-size:100%;'>" . _("delete entire month") . "</a>";
          //print out prompt for outliers if outlierID is > 0
          if ($month['outlierID'] > 0){
            echo "&nbsp;&nbsp;<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getMonthlyOutlierForm&publisherPlatformID=" . $publisherPlatformID . "&platformID=" . $platformID . "&archiveInd=" . $month['archiveInd'] . "&month=" . $month['month'] . "&year=" . $month['year'] . "&resourceType=" . $month['resourceType'] . "&height=340&width=415&modal=true\",340,420)' class='thickbox' style='font-size:100%;'>" . _("view outliers for this month") . "</a>";
          }
          echo "</td></tr>";
        }
        echo '</table>';
      }
    }

		break;



	case 'getTitleSpreadsheets':
		if (isset($_GET['publisherPlatformID']) && ($_GET['publisherPlatformID'] != '')){
			$publisherPlatformID = $_GET['publisherPlatformID'];
			$platformID = '';
			$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));
		}else{
			$platformID = $_GET['platformID'];
			$publisherPlatformID = '';
			$obj = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));
		}

    echo '<div>';
    echo "<h3>" . _("Titles") . "</h3>";

		foreach(array('Platform', 'Database', 'Journal', 'Book', 'Item') as $type) {
		  echo '<h4>' . _($type . 's'). '</h4>';
      $titles = $obj->getTitles($type);
      $count = count($titles);
      if ($count <= 0) {
        echo _("(none found)");
      } else {
        $plural = $count > 1 ? 's' : '';
        echo "<div><a href='titles_spreadsheet.php?publisherPlatformID=$publisherPlatformID&platformID=$platformID&resourceType=$type' " . getTarget() . ">"
          . _("View $type Spreadsheet")
          . " ($count " . _("$type$plural") . ")"
          . "</a></div>";
      }
    }
		echo "</div>";

		break;

    case 'getTitleDetails':
		$titleArray = array();

		if (isset($_GET['publisherPlatformID']) && ($_GET['publisherPlatformID'] != '')){
			$publisherPlatformID = $_GET['publisherPlatformID'];
			$platformID = '';
			$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));
		}else{
			$platformID = $_GET['platformID'];
			$publisherPlatformID = '';
			$obj = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));
		}

		$journalTitleArray = $obj->getJournalTitles;
		$bookTitleArray = $obj->getBookTitles;
		$databaseTitleArray = $obj->getDatabaseTitles;

		if ((count($journalTitleArray) == '0') && (count($bookTitleArray) == '0') && (count($databaseTitleArray) == '0')){
			echo "<h3>" . _("Titles") . "</h3>" . _("(none found)");
		}


		/////////////////////////////////
		// JOURNAL
		/////////////////////////////////
		$titleArray = $journalTitleArray;

		//determine config settings for link resolver
		$config = new Configuration();
		$baseURL = $config->settings->baseURL;

		if (count($titleArray) >0 ){
			?>
			<h3><?php echo _("Journals - Associated Titles and ISSNs");?></h3>
<!-- TODO: table styling -->
			<table class='verticalFormTable table-border table-striped'>
			<thead>
			<tr>
				<th scope="col"><?php echo _("Title");?></th>
				<th scope="col"><?php echo _("DOI");?></th>
				<th scope="col"><?php echo _("ISSN");?></th>
				<th scope="col"><?php echo _("eISSN");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
			</tr>
			</thead>
			<tbody>
			

			<?php
			foreach($titleArray as $title) {

				echo "\n<tr>";
				echo "\n<th scope='row'>" . $title['title'] . "</th>";

				//get the first Identifier to use for the terms tool lookup
				$doi = $title['doi'];
				$issn = $title['issn'];
				$eissn = $title['eissn'];

				echo "\n<td>" . $doi . "</td>";
				echo "\n<td>" . $issn . "</td>";
				echo "\n<td>" . $eissn . "</td>";


				if ((($issn) || ($eissn)) && ($baseURL)){
					if (($issn) && !($eissn)){
						$urlAdd = "&rft.issn=" . $issn;
					}else if (($issn) && ($eissn)){
						$urlAdd = "&rft.issn=" . $issn . "&rft.eissn=" . $eissn;
					}else{
						$urlAdd = "&rft.eissn=" . $eissn;
					}


					$resolverURL = $config->settings->baseURL;

					//check if there is already a ? in the URL so that we don't add another when appending the parms
					if (strpos($resolverURL, "?") > 0){
						$resolverURL .= "&";
					}else{
						$resolverURL .= "?";
					}

					$resolverURL .= $urlAdd;
					echo "\n<td><span style='float:left;'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getRelatedTitlesForm&titleID=" . $title['titleID'] . "&height=240&width=258&modal=true\",240,260)' class='thickbox'>" . _("view related titles") . "</a><br /><a href='" . $resolverURL  . "' " . getTarget() . ">" . _("view in link resolver") . "</a></span></td>";
				}else{
					echo "\n<td>&nbsp;</td>";
				}



				echo "</tr>";

			#end Title loop
			}
			echo "</tbody>";
			echo "</table>";
		}


		/////////////////////////////////
		// BOOKS
		/////////////////////////////////
		$titleArray = array();

		$titleArray = $bookTitleArray;

		//determine config settings for link resolver
		$baseURL = $config->settings->baseURL;

		if (count($titleArray) >0 ){
			?>
			<h3><?php echo _("Books - Associated Titles and ISBNs");?></h3>
			<table class='verticalFormTable table-border table-striped'>
			<thead>
			<tr>
				<th scope="col"><?php echo _("Title");?></th>
				<th scope="col"><?php echo _("DOI");?></th>
				<th scope="col"><?php echo _("ISBN");?></th>
				<th scope="col"><?php echo _("ISSN");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
			</tr>
			</thead>
			<tbody>

			<?php
			foreach($titleArray as $title) {

				echo "\n<tr>";

				echo "\n<td>" . $title['title'] . "</td>";

				//get the first Identifier to use for the terms tool lookup
				$doi = $title['doi'];
				$isbn = $title['isbn'];
				$issn = $title['issn'];

				echo "\n<td>" . $doi . "</td>";
				echo "\n<td>" . $isbn . "</td>";
				echo "\n<td>" . $issn . "</td>";


				if ((($isbn) || ($eisbn)) && ($baseURL)){
					if (($isbn) && !($eisbn)){
						$urlAdd = "&rft.isbn=" . $isbn;
					}else if (($isbn) && ($issn)){
						$urlAdd = "&rft.isbn=" . $isbn . "&rft.eisbn=" . $eisbn;
					}else{
						$urlAdd = "&rft.eisbn=" . $eisbn;
					}


					$resolverURL = $config->settings->baseURL;

					//check if there is already a ? in the URL so that we don't add another when appending the parms
					if (strpos($resolverURL, "?") > 0){
						$resolverURL .= "&";
					}else{
						$resolverURL .= "?";
					}

					$resolverURL .= $urlAdd;

					echo "\n<td><span style='float:left;'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getRelatedTitlesForm&titleID=" . $title['titleID'] . "&height=240&width=258&modal=true\",240,260)' class='thickbox'>" . _("view related titles") . "</a><br /><a href='" . $resolverURL  . "' " . getTarget() . ">" . _("view in link resolver") . "</a></span></td>";
				}else{
					echo "\n<td>&nbsp;</td>";
				}

				echo "</tr>";

			#end Title loop
			}
			echo "</tbody>";
			echo "</table>";
			echo "<br /><br />";
		}




		/////////////////////////////////
		// DATABASE
		/////////////////////////////////
		$titleArray = array();

		$titleArray = $databaseTitleArray;

		if (is_array($titleArray) && count($titleArray) > 0) {
			?>
			<h3><?php echo _("Database Titles");?></h3>
			<ul class="unstyled">
			<?php
			foreach($titleArray as $title) {
				echo "\n<li>" . $title['title'] . "</li>";
			#end Title loop
			}
			echo "</ul>";
		}



		break;



    case 'getLogEmailAddressTable':

		$logEmailAddress = array();
		$logEmailAddresses = new LogEmailAddress();

		echo "<h3>" . _("Current Email Addresses") . "</h3>";
		echo "<table class='dataTable table-striped table-border'>";
		echo "<thead>";
		echo "<tr>";
		echo "<th scope='col'>" . _("Email") . "</th>";
		echo "<th scope='col'>" . _("Actions") . "</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";

		foreach($logEmailAddresses->allAsArray as $logEmailAddress) {
			echo "<tr><td>" . $logEmailAddress['emailAddress'] . "</td>";
			echo "<td class='actions'><a  href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getLogEmailAddressForm&height=122&width=248&logEmailAddressID=" . $logEmailAddress['logEmailAddressID'] . "&modal=true\",150,250)' class='thickbox'><img id='Edit'  src='images/edit.gif' title= '"._("Edit")."' /></a>";
			echo "<a href='javascript:deleteLogEmailAddress(" . $logEmailAddress['logEmailAddressID'] . ");'><img id='Remove'  src='images/cross.gif' title= '"._("Delete")."' /></a></td></tr>";
		}
		echo "</tbody>";
		echo "</table>";

        break;



    case 'getOutlierTable':

		//determine config settings for outlier usage
		$config = new Configuration();

		if ($config->settings->useOutliers == "Y"){

			$outlier = array();
			$outliers = new Outlier();

			echo "<h3>" . _("Current Outlier Parameters") . "</h3>";

			foreach($outliers->allAsArray as $outlier) {
				printf(_("Level: %s %d over plus %d\% over - displayed %s"), $outlier['outlierLevel'], $outlier['overageCount'], $outlier['overagePercent'], $outlier['color']);
				echo "<button type='button' onclick='myDialog(\"ajax_forms.php?action=getOutlierForm&height=162&width=308&outlierID=" . $outlier['outlierID'] . "&modal=true\",170,310)' class='thickbox btn btn-sm link'>" . _("edit") . "</button>";
			}
		}else{
			echo _("Outliers are currently disabled in the configuration file.  Contact your technical support to enable them.");

		}

        break;




    case 'getMonthlyOutlierStatsTable':
		$publisherPlatformID = $_GET['publisherPlatformID'];
		$platformID = $_GET['platformID'];
		$archiveInd = $_GET['archiveInd'];
		$year = $_GET['year'];
		$month = $_GET['month'];

		$statsArray = array();
		if ($publisherPlatformID) {
			$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $publisherPlatformID)));
			$statsArray = $publisherPlatform->getMonthlyOutliers($archiveInd, $year, $month);
		}else{
			$platform = new Platform(new NamedArguments(array('primaryKey' => $platformID)));
			$statsArray = $platform->getMonthlyOutliers($archiveInd, $year, $month);
		}



		$totalRows = count($statsArray);

		if ($totalRows == 0){
			echo "<p>" . _("None currently") . "</p>";
		}else{
			echo "<table class='table-border table-striped'><tbody>";
			foreach($statsArray as $monthlyStat){
				echo "<tr><th scope='row'>" . $monthlyStat['Title']. "</th>";
				echo "<td style='background-color:" . $monthlyStat['color'] . "'>" . $monthlyStat['usageCount'] . "</td>";
				echo "<td><input type='text' name='overrideUsageCount_" . $monthlyStat['monthlyUsageSummaryID'] . "' id = 'overrideUsageCount_" . $monthlyStat['monthlyUsageSummaryID'] . "' value='" . $monthlyStat['overrideUsageCount'] . "' aria-label='".sprintf(_('Override usage count for %s'), $monthlyStat['Title'])."'></td>";
				echo "<td class='actions'><button type='button' class='link' onclick=\"updateOverride('" . $monthlyStat['monthlyUsageSummaryID'] . "');\">" . _("update override") . "</button>";
				echo "<button type='button' class='link' onclick=\"ignoreOutlier('" . $monthlyStat['monthlyUsageSummaryID'] . "');\">" . _("ignore outlier") . "</button></td>";
				echo "</tr>";
			}
			echo "</tbody></table>";
		}


        break;




    case 'getYearlyOverrideStatsTable':

		$publisherPlatformID  = $_GET['publisherPlatformID'];
		$platformID  = $_GET['platformID'];
		$archiveInd  = $_GET['archiveInd'];
		$year  = $_GET['year'];

		$statsArray = array();
		if ($publisherPlatformID) {
			$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $publisherPlatformID)));
			$statsArray = $publisherPlatform->getYearlyOverrides($archiveInd, $year);
		}else{
			$platform = new Platform(new NamedArguments(array('primaryKey' => $platformID)));
			$statsArray = $platform->getYearlyOverrides($archiveInd, $year);
		}



		?>

		<table class="table-border">
			<tbody>
		<?php

		foreach($statsArray as $yearlyStat){
		?>
			<tr>
			<th scope="row"><?php echo $yearly_stat['Title']; ?></th>
			<th><?php echo _("Total");?><th>
			<td class="numeric"><?php echo $yearly_stat['totalCount']; ?></td>
			<td class="actions">
				<input name="overrideTotalCount_<?php echo $yearly_stat['yearlyUsageSummaryID']; ?>" 
				id="overrideTotalCount_<?php echo $yearly_stat['yearlyUsageSummaryID']; ?>" 
				type="text" value="<?php echo $yearly_stat['overrideTotalCount']; ?>" 
				size="6" maxlength="6" aria-label="<?php echo _('Override Total Count') ?>">
			</td>
			<td class="actions"><button type="button" class="link" onclick="updateYTDOverride('<?php echo $yearly_stat['yearlyUsageSummaryID']; ?>', 'overrideTotalCount')"><?php echo _("update");?></button></td>
			</tr>
			<tr>
			<th scope="row" colspan="2"><?php echo _("PDF");?><th>
			<td class="numeric"><?php echo $yearly_stat['ytdPDFCount']; ?></td>
			<td class="actions">
				<input name="overridePDFCount_<?php echo $yearly_stat['yearlyUsageSummaryID']; ?>" 
					id="overridePDFCount_<?php echo $yearly_stat['yearlyUsageSummaryID']; ?>" 
					type="text" value="<?php echo $yearly_stat['overridePDFCount']; ?>" 
					size="6" maxlength="6" aria-label="<?php echo _('Override PDF Count') ?>">
			</td>
			<td class="actions"><button type="button" class="link" onclick="updateYTDOverride('<?php echo $yearly_stat['yearlyUsageSummaryID']; ?>', 'overridePDFCount')"><?php echo _("update");?></button></td>
			</tr>
			<tr>
			<th scope="row" colspan="2"><?php echo _('HTML'); ?><td>
			<td class="numeric"><?php echo $yearly_stat['ytdHTMLCount']; ?></td>
			<td class="actions">
				<input name="overrideHTMLCount_<?php echo $yearly_stat['yearlyUsageSummaryID']; ?>" 
				id="overrideHTMLCount_<?php echo $yearly_stat['yearlyUsageSummaryID']; ?>" 
				type="text" value="<?php echo $yearly_stat['overrideHTMLCount']; ?>" 
				size="6" maxlength="6" aria-label="<?php echo _('Override HTML Count') ?>"></td>
			<td class="actions"><button type="button" class="link" onclick="updateYTDOverride('<?php echo $yearly_stat['yearlyUsageSummaryID']; ?>', 'overrideHTMLCount')"><?php echo _("update");?></button></td>
			</tr>
		<?php

		}

		?>
			</tbody>
		</table>

		<?php


        break;





    case 'getPlatformReportDisplay':
    	$platform = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));

		if ($platform->reportDropDownInd == '1') { $reportDropDownInd = 'checked';}else{$reportDropDownInd = '';}

		echo "<input type='checkbox' id='chk_Platform_" . $platform->platformID  . "' onclick='updatePlatformDropDown(" . $platform->platformID  . ");' $reportDropDownInd>";
		echo "<label for='chk_Platform_" . $platform->platformID  . "'>" . $platform->name . "</label>";

		if ($platform->reportDisplayName)  echo " (<i>" . $platform->reportDisplayName . "</i>) ";
		echo "<button type='button' class='link' onclick='myDialog(\"ajax_forms.php?action=getReportDisplayForm&height=122&width=248&type=Platform&updateID=" . $platform->platformID . "&modal=true\",130,250)' class='thickbox'>" . _("edit report display name") . "</button>";



        break;





    case 'getPublisherReportDisplay':
    	$publisherPlatformID = $_GET['publisherPlatformID'];

    	$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));
    	$publisher = new Publisher(new NamedArguments(array('primaryKey' => $publisherPlatform->publisherID)));

		$result = mysqli_query($publisherPlatform->getDatabase(), "select distinct pp.publisherPlatformID, Publisher.name Publisher, pp.reportDisplayName reportPublisher, pp.reportDropDownInd from Publisher_Platform pp, Publisher where pp.publisherID = Publisher.publisherID and pp.publisherPlatformID = '" . $publisherPlatformID . "';");

		if ($publisherPlatform->reportDropDownInd == '1') { $reportDropDownInd = 'checked';}else{$reportDropDownInd = '';}

		echo "<table class='table-border'>
			<tbody>
			  <tr>
					<td>
						<input type='checkbox' id='chk_Publisher_" . $publisherPlatform->publisherPlatformID  . "' onclick='javascript:updatePublisherDropDown(" . $publisherPlatform->publisherPlatformID  . ");' $reportDropDownInd>
					</td>";


		echo "<td>" . $publisher->name;
		if ($publisherPlatform->reportDisplayName)  echo " (<i>" . $publisherPlatform->reportDisplayName . "</i>) ";
		echo "<button type='button' onclick='myDialog(\"ajax_forms.php?action=getReportDisplayForm&height=122&width=248&type=Publisher&updateID=" . $publisherPlatform->publisherPlatformID . "&modal=true\",125,250)' class='thickbox btn link'>" . _("edit report display name") . "</button>";
		echo "</td></tr></tbody></table>";


        break;





    case 'getImportTable':


		$pageStart = $_GET['pageStart'];
		$numberOfRecords = 20;
		$limit = $pageStart-1 . ", " . $numberOfRecords;

		$importLog = new ImportLog();

		$totalRecords = count($importLog->getImportLogRecords(''));


		$importLogArray = $importLog->getImportLogRecords($limit);

		$recordCount = count($importLogArray);

		if ($totalRecords == 0){
			echo "<p><i>" . _("No imports found.") . "</i></p>";

		}else{
			$pagination = '';
			$thisPageNum = $recordCount + $pageStart - 1;
			echo "<h2 class='display-title'>" . sprintf(_("Displaying %1\$d to %2\$d of %3\$d records"), $pageStart, $thisPageNum, $totalRecords) . "</h2>";
			
			//print out page selectors
			if ($totalRecords > $numberOfRecords){
				$pagination .= "<nav class='pagination' aria-label='"._('Pages of Import Records')."'>";
				$pagination .= "<ul>";
				if ($pageStart == "1"){
					$pagination .= "<li class='first' aria-hidden='true'><span class='smallText'><i class='fa fa-backward'></i></span></li>";
				}else{
					$pagination .= "<li class='first'><a href='javascript:setPageStart(1);' class='smallLink' aria-label='" . sprintf(_('First page, page %d'), $i ? $i : 1) . "'><i class='fa fa-backward'></i></a></li>";
				}

				for ($i=1; $i<($totalRecords/$numberOfRecords)+1; $i++){

					$nextPageStarts = ($i-1) * $numberOfRecords + 1;
					if ($nextPageStarts == "0") $nextPageStarts = 1;


					if ($pageStart == $nextPageStarts){
						$pagination .= "<li aria-current='page'><span class='smallText'>" . $i . "</span></li>";
					}else{
						$pagination .= "<li><a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink' aria-label='" . sprintf(_('Page %d'), $i) . "'>" . $i . "</a></li>";
					}
				}

				if ($pageStart == $nextPageStarts){
					$pagination .= "<li class='last' aria-hidden='true'><span class='smallText'><i class='fa fa-forward'></i></span></li>";
				}else{
					$pagination .= "<li class='last'><a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink' aria-label='" . sprintf(_('Last page, page %d'), $i - 1) . "'><i class='fa fa-forward'></i></a></li>";
				}
				$pagination .= "</ul>";
				$pagination .= "</nav>";
			}

			echo $pagination;

			echo "<table class='dataTable table-border table-striped'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th scope='col'>" . _("Import Date") . "</th>";
			echo "<th scope='col'>" . _("Last Updated By") . "</th>";
			echo "<th scope='col'>" . _("Import Summary") . "</th>";
			echo "<th scope='col'>" . _("Log") . "</th>";
			echo "<th scope='col'>" . _("Archive") . "</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";

			$i=0;

			foreach($importLogArray as $importLog) {
				echo "<tr>";
				echo "<th scope='row'>" . format_date($importLog['dateTime'], "%m/%e/%y %I:%i %p") . "</th>";
				echo "<td>" . $importLog['loginID'] . "</td>";
				echo "<td>" . nl2br($importLog['details']) . "</td>";
				echo "<td class='actions'><a href='" . $importLog['logFileURL'] . "'>" . _("view log") . "</a></td>";
				echo "<td class='actions'><a href='" . $importLog['archiveFileURL'] . "'>" . _("view archive") . "</a></td>";
				echo "</tr>";
			}


			?>
			</tbody>
			</table>

			<?php
			echo $pagination;
		}

		break;




	//display sushi outstanding approval queue
	case 'getOutstandingSushiImports':

		$sushiImport = new ImportLog();

		$sushiArray = array();
		$sushiArray = $sushiImport->getSushiImports();

		if (is_array($sushiArray) && count($sushiArray) > 0) {
			echo "<table class='dataTable table-striped'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th scope='col'>" . _("Platform/Publisher") . "</th>";
			echo "<th scope='col'>" . _("Import Run Date") . "</th>";
			echo "<th scope='col'>" . _("Details") . "</th>";
			echo "<th scope='col'>" . _("Process") . "</th>";
			echo "<th scope='col'>" . _("Delete") . "</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";

			foreach($sushiArray as $sushi) {

				$imp = new ImportLog(new NamedArguments(array('primaryKey' => $sushi['importLogID'])));
				$platforms = $imp->getPlatforms();

				foreach ($platforms as $platform){
					if ($platform['platformID'] > 0){
						$urlstring = "platformID=" . $platform['platformID'];
						$obj = new Platform(new NamedArguments(array('primaryKey' => $platform['platformID'])));
					}else{
						$urlstring = "publisherPlatformID=" . $sushi['publisherPlatformID'];
						$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $sushi['publisherPlatformID'])));
					}
				}

				echo "<tr>";
				echo "<th><a href='publisherPlatform.php?" . $urlstring . "'>" . $obj->name . "</a></th>";
				echo "<td>" . format_date($sushi['importDateTime']) . "</td>";
				echo "<td>" . nl2br($sushi['details']) . "</td>";
				echo "<td class='actions'><a href='uploadConfirmation.php?importLogID=" . $sushi['importLogID'] . "'>" . _("view to process") . "</a></td>";
				echo "<td class='actions'><a href='javascript:deleteImportLog(" . $sushi['importLogID'] . ")'>" . _("delete import") . "</a></td>";
				echo "</tr>";
			}
			echo "</table>";
		}else{
			echo _("<p>(no outstanding imports found)</p>");

		}

		break;




	//display sushi outstanding approval queue
	case 'getFailedSushiImports':

		$sushiService = new SushiService();

		$sushiArray = array();
		$sushiArray = $sushiService->failedImports();

		if (is_array($sushiArray) && count($sushiArray) > 0) {
			echo "<table class='dataTable table-striped'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th scope='col'>" . _("Platform/Publisher") . "</th>";
			echo "<th scope='col'>" . _("Latest Run") . "</th>";
			echo "<th scope='col'>" . _("Latest Status") . "</th>";
			echo "<th scope='col'>" . _("Run") . "</th>";
			echo "<th scope='col'>" . _("Test") . "</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";

			foreach($sushiArray as $sushi) {

				if ($sushi['platformID'] > 0){
					$urlstring = "platformID=" . $sushi['platformID'];
					$obj = new Platform(new NamedArguments(array('primaryKey' => $sushi['platformID'])));
				}else{
					$urlstring = "publisherPlatformID=" . $sushi['publisherPlatformID'];
					$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $sushi['publisherPlatformID'])));
				}

				if ($obj->getImportLogs[0]){
					$lastImportObj = $obj->getImportLogs[0];
					$lastImportDate = format_date($lastImportObj->importDateTime);
					$lastImportDetails = nl2br($lastImportObj->details);
					$logFileURL = $lastImportObj->logFileURL;
				}


				echo "<tr>";
				echo "<th scope='row'><a href='publisherPlatform.php?" . $urlstring . "'>" . $obj->name . "</a></th>";
				echo "<td>" . $lastImportDate . "</td>";
				echo "<td>" . $lastImportDetails . "<br /><a href='" . $logFileURL . "'>" . _("view full log") . "</a></td>";
				echo "<td class='actions'><button type='button' onclick='myDialog(\"ajax_forms.php?action=getSushiRunForm&sushiServiceID=" . $sushi['sushiServiceID'] . "&height=216&width=348&modal=true\",220,350)' class='thickbox link'>" . _("run now") . "</button></td>";
				echo "<td class='actions'><a href='publisherPlatform.php?" . $urlstring . "&showTab=sushi'>" . _("change/test connection") . "</a></td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";


		}else{
			echo _("(no failed imports found)");

		}


		break;



	//display sushi outstanding approval queue
	case 'getAllSushiServices':

		$sushiService = new SushiService();

		$sushiArray = array();
		$sushiArray = $sushiService->allServices();

		if (is_array($sushiArray) && count($sushiArray) > 0) {
			echo "<table class='dataTable table-striped'>";
			echo "<thead>";
			echo "<tr>";
			echo "<th scope='col'>" . _("Platform/Publisher") . "</th>";
			echo "<th scope='col'>" . _("Report(s)") . "</th>";
			echo "<th scope='col'>" . _("Next Run") . "</th>";
			echo "<th scope='col'>" . _("Latest Run") . "</th>";
			echo "<th scope='col'>" . _("Latest Status") . "</th>";
			echo "<th scope='col'>" . _("Run") . "</th>";
			echo "<th scope='col'>" . _("Test") . "</th>";
			echo "</tr>";
			echo "</thead>";
			echo "<tbody>";

			foreach($sushiArray as $sushi) {

				if ($sushi['platformID'] > 0){
					$urlstring = "platformID=" . $sushi['platformID'];
					$obj = new Platform(new NamedArguments(array('primaryKey' => $sushi['platformID'])));
				}else{
					$urlstring = "publisherPlatformID=" . $sushi['publisherPlatformID'];
					$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $sushi['publisherPlatformID'])));
				}

				if (isset($obj->getImportLogs[0])){
					$lastImportObj = $obj->getImportLogs[0];
					$lastImportDate = format_date($lastImportObj->importDateTime);
					$lastImportDetails = nl2br($lastImportObj->details);
				}else{
					$lastImportDate="";
					$lastImportDetails = "";
				}


				echo "<tr>";
				echo "<th scope='row'><a href='publisherPlatform.php?" . $urlstring . "'>" . $obj->name . "</a></th>";
				echo "<td>" . $sushi['releaseNumber'] . ":" . $sushi['reportLayouts'] . "</td>";
				echo "<td>" . format_date($sushi['next_import']) . "</td>";
				echo "<td>" . format_date($lastImportDate) . "</td>";
				echo "<td>" . $lastImportDetails . "</td>";
				echo "<td class='actions'><button type='button' onclick='myDialog(\"ajax_forms.php?action=getSushiRunForm&sushiServiceID=" . $sushi['sushiServiceID'] . "&height=216&width=348&modal=true\",220,350)' class='thickbox link'>" . _("run now") . "</button></td>";
				echo "<td class='actions'><a href='publisherPlatform.php?" . $urlstring . "&showTab=sushi'>" . _("change/test connection") . "</a></td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";


		}else{
			echo _("<p>(no sushi services set up)</p>");

		}


		break;



	//display user info for admin screen
	case 'getAdminUserList':

		$instanceArray = array();
		$user = new User();
		$tempArray = array();
		$config = new Configuration();

		if (is_array($user->allAsArray()) && count($user->allAsArray()) > 0) {

			?>
			<table class='dataTable table-border table-striped'>
				<thead>
				<tr>
				<th><?php echo _("Login ID");?></th>
				<th><?php echo _("First Name");?></th>
				<th><?php echo _("Last Name");?></th>
				<th><?php echo _("Privilege");?></th>
				<th><?php echo _("Actions");?></th>
				</thead>
				<tbody>
				<?php

				foreach($user->allAsArray() as $instance) {
					$privilege = new Privilege(new NamedArguments(array('primaryKey' => $instance['privilegeID'])));

					echo "<tr>";
					echo "<th scope='row'>" . $instance['loginID'] . "</th>";
					echo "<td>" . $instance['firstName'] . "</td>";
					echo "<td>" . $instance['lastName'] . "</td>";
					echo "<td>" . $privilege->shortName . "</td>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm&loginID=" . $instance['loginID'] . "&height=196&width=248&modal=true\",200,450)' class='thickbox' id='expression'><img id='Edit'  src='images/edit.gif' title= '"._("Edit")."' /></a>";
					echo "<a href='javascript:deleteUser(\"" . $instance['loginID'] . "\")'><img id='Remove' src='images/cross.gif' title= '"._("Remove")."' /></a></td>";
					echo "</tr>";
				}

				?>
				</tbody>
			</table>
			<?php

		}else{
			echo _("<p>(none found)</p>");
		}

		break;




	//display platform search on front page
	case 'getSearch':

		$pageStart = $_GET['pageStart'];
		$numberOfRecords = $_GET['numberOfRecords'];
		$whereAdd = array();

		//get where statements together (and escape single quotes)
		if ($_GET['searchName']) $whereAdd[] = "(UPPER(P.name) LIKE UPPER('%" . str_replace("'","''",$_GET['searchName']) . "%') OR UPPER(Publisher.name) LIKE UPPER('%" . str_replace("'","''",$_GET['searchName']) . "%') OR UPPER(P.reportDisplayName) LIKE UPPER('%" . str_replace("'","''",$_GET['searchName']) . "%'))";

		if ($_GET['startWith']) $whereAdd[] = "TRIM(LEADING 'THE ' FROM UPPER(P.name)) LIKE UPPER('" . $_GET['startWith'] . "%')";


		$orderBy = $_GET['orderBy'];
		$limit = ($pageStart-1) . ", " . $numberOfRecords;

		//get total number of records to print out and calculate page selectors
		$totalPObj = new Platform();
		$totalRecords = count($totalPObj->search($whereAdd, $orderBy, ""));

		//reset pagestart to 1 - happens when a new search is run but it kept the old page start
		if ($totalRecords < $pageStart){
			$pageStart=1;
		}

		$limit = ($pageStart-1) . ", " . $numberOfRecords;

		$platformObj = new Platform();
		$platformArray = array();
		$platformArray = $platformObj->search($whereAdd, $orderBy, $limit);

		if (count($platformArray) == 0){
			echo "<p><i>" . _("Sorry, no platforms or publishers fit your query") . "</i></p>";
			$i=0;
		}else{
			$thisPageNum = count($platformArray) + $pageStart - 1;
			echo "<h2>" . sprintf(_("Displaying %1\$d to %2\$d of %3\$d platform records"), $pageStart, $thisPageNum, $totalRecords) . "</h2>";
			echo "<nav class='pagination' aria-label='"._('Records per page')."'><ul>";

								//print out page selectors
								if ($totalRecords > $numberOfRecords){
										if ($pageStart == "1"){
													echo "<li class='first'><span class='smallerText'><i class='fa fa-backward'></i></span></li>";
										}else{
													echo "<li class='first'><a href='javascript:setPageStart(1);' class='smallLink' aria-label='"._("first page")."'><i class='fa fa-backward'></i></a></li>";
										}

				//don't want to print out too many page selectors!!
				$maxDisplay=41;
				if ((($totalRecords/$numberOfRecords)+1) < $maxDisplay){
					$maxDisplay = ($totalRecords/$numberOfRecords)+1;
				}

				for ($i=1; $i<$maxDisplay; $i++){

					$nextPageStarts = ($i-1) * $numberOfRecords + 1;
					if ($nextPageStarts == "0") $nextPageStarts = 1;


					if ($pageStart == $nextPageStarts){
						echo "<li aria-current='page'><span class='smallerText'>" . $i . "</span></li>";
					}else{
						echo "<li><a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink'>" . $i . "</a></li>";
					}
				}

										if ($pageStart == $nextPageStarts){
													echo "<li class='last'><span class='smallerText'><i class='fa fa-forward'></i></span></li>";
										}else{
													echo "<li class='last'><a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink'  aria-label='"._("last page")."'><i class='fa fa-forward'></i></a></li>";
										}
								}

			?>
			<table class='dataTable table-border table-striped'>
			<thead>
			<tr>
				<th scope="col"><span class='sortable'><?php echo _("Platform Name");?><span class='arrows'><a href='javascript:setOrder("P.name","asc");' aria-label='<?php echo _('Sort by name, ascending'); ?>'><img src='images/arrowup.png'></a><a href='javascript:setOrder("P.name","desc");' aria-label='<?php echo _('Sort by name, descending'); ?>'><img src='images/arrowdown.png'></a></span></span></th>
				<th scope="col"><span class='sortable'><?php echo _("Publishers");?><span class='arrows'><a href='javascript:setOrder("publishers","asc");' aria-label='<?php echo _('Sort by publisher, ascending'); ?>'><img src='images/arrowup.png'></a><a href='javascript:setOrder("publishers","desc");' aria-label='<?php echo _('Sort by publisher, descending'); ?>'><img src='images/arrowdown.png'></a></span></span></th>
				<th scope="col"><span class='sortable'><?php echo _("Next Run");?><span class='arrows'><a href='javascript:setOrder("serviceDayOfMonth","asc");' aria-label='<?php echo _('Sort by next run date, ascending'); ?>'><img src='images/arrowup.png'></a><a href='javascript:setOrder("serviceDayOfMonth","desc");' aria-label='<?php echo _('Sort by next run date, descending'); ?>'><img src='images/arrowdown.png'></a></span></span></th>
				<th scope="col"><span class='sortable'><?php echo _("Latest Run");?><span class='arrows'><a href='javascript:setOrder("importDateTime","asc");' aria-label='<?php echo _('Sort by latest import date, ascending'); ?>'><img src='images/arrowup.png'></a><a href='javascript:setOrder("importDateTime","desc");' aria-label='<?php echo _('Sort by latest import date, descending'); ?>'><img src='images/arrowdown.png'></a></span></span></th>
				<th scope="col"><span class='sortable'><?php echo _("Latest Status");?><span class='arrows'><a href='javascript:setOrder("details","asc");' aria-label='<?php echo _('Sort by status, ascending'); ?>'><img src='images/arrowup.png'></a><a href='javascript:setOrder("details","desc");' aria-label='<?php echo _('Sort by status, descending'); ?>'><img src='images/arrowdown.png'></a></span></span></th>
			</tr>
			</thead>
			<tbody>
			<?php

			foreach ($platformArray as $platform){
				echo "<tr>";
				echo "<th scope='col'><a href='publisherPlatform.php?platformID=" . $platform['platformID'] . "'>" . $platform['name'] . "</a></th>";
				echo "<td>";
          $getPublishers = new Platform(new NamedArguments(array('primaryKey' => $platform['platformID'])));
          $publisherPlatformArray = $getPublishers->getPublisherPlatforms();
					if (count($publisherPlatformArray) == 0){
						echo _("(none found)");
					}else{

					 	if (count($publisherPlatformArray) > 5){
							echo "<a href=\"javascript:showPublisherList('" . $platform['platformID'] . "');\"><img src='images/arrowright.gif' style='border:0px' alt='" . _("show publisher list") . "' id='image_" . $platform['platformID'] . "'></a>&nbsp;<a href=\"javascript:showPublisherList('" . $platform['platformID'] . "');\" id='link_" . $platform['platformID'] . "'>" . _("show publisher list") . "</a><br />";
							echo "<div id='div_" . $platform['platformID'] . "' style='display:none;width:300px;margin-left:5px'>";

							foreach($publisherPlatformArray as $publisherPlatform){
								echo "<a href='publisherPlatform.php?publisherPlatformID=" . $publisherPlatform->publisherPlatformID . "'>" . $publisherPlatform->reportDisplayName . "</a><br />\n";
							}

							echo "</div>";
						}else{
							foreach($publisherPlatformArray as $publisherPlatform){
								echo "<a href='publisherPlatform.php?publisherPlatformID=" . $publisherPlatform->publisherPlatformID . "'>" . $publisherPlatform->reportDisplayName . "</a><br />\n";
							}
						}
					}
				echo "</td>";


				echo "<td class='numeric'>" . format_date($platform['next_import']) . "</td>";
				echo "<td class='numeric'>" . format_date($platform['last_import']) . "</td>";
				echo "<td>" . ImportLog::shortStatusFromDetails($platform['details']) . "</td>";
				echo "</tr>";
			}

			?>
			</tbody>
			</table>

			<nav class="pagination" aria-label="<?php echo _('Records per page'); ?>">
			<ul>
			<?php
			//print out page selectors
			if ($totalRecords > $numberOfRecords){
				if ($pageStart == "1"){
					echo "<li class='first'><span class='smallerText'><i class='fa fa-backward'></i></span></li>";
				}else{
					echo "<li class='first'><a href='javascript:setPageStart(1);' class='smallLink' aria-label='"._("first page")."'><i class='fa fa-backward'></i></a></li>";
				}

				$maxDisplay=41;
				if ((($totalRecords/$numberOfRecords)+1) < $maxDisplay){
					$maxDisplay = ($totalRecords/$numberOfRecords)+1;
				}

				for ($i=1; $i<$maxDisplay; $i++){

					$nextPageStarts = ($i-1) * $numberOfRecords + 1;
					if ($nextPageStarts == "0") $nextPageStarts = 1;


					if ($pageStart == $nextPageStarts){
						echo "<li aria-current='page'><span class='smallerText'>" . $i . "</span></li>";
					}else{
						echo "<li><a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink'>" . $i . "</a></li>";
					}
				}

				if ($pageStart == $nextPageStarts){
					echo "<li class='last'><span class='smallerText'><i class='fa fa-forward'></i></span></li>";
				}else{
					echo "<li class='last'><a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink' aria-label='"._("last page")."'><i class='fa fa-forward'></i></a></li>";
				}
			}
			?>
			</ul>
			</nav>
			<select id='numberOfRecords' name='numberOfRecords' onchange='javascript:setNumberOfRecords();'>
				<?php
				for ($i=5; $i<=50; $i=$i+5){
					if ($i == $numberOfRecords){
						echo "<option value='" . $i . "' selected>" . $i . "</option>";
					}else{
						echo "<option value='" . $i . "'>" . $i . "</option>";
					}
				}
				?>
			</select>
			<label for="numberOfRecords"><?php echo _("records per page");?></label>
			
			

			<?php
		}

		//set everything in sessions to make form "sticky"
		$_SESSION['plat_pageStart'] = $_GET['pageStart'];
		$_SESSION['plat_numberOfRecords'] = $_GET['numberOfRecords'];
		$_SESSION['plat_searchName'] = $_GET['searchName'];
		$_SESSION['plat_startWith'] = $_GET['startWith'];
		$_SESSION['plat_orderBy'] = $_GET['orderBy'];

		break;


	default:
		if (empty($_REQUEST['function']))
			return;
		printf(_("Function %s not set up!"), $_REQUEST['function']);
		break;


}



?>
