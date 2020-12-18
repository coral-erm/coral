<?php

/*
**************************************************************************************************************************
** CORAL Usage Statistics v. 1.1
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
*/


session_start();

include_once 'directory.php';

//this a SUSHI Service ID has been passed in, it needs to be run
if ((isset($_POST['sushiServiceID'])) and ($_POST['sushiServiceID'] > 0)) {
  $sushiServiceID = $_POST['sushiServiceID'];
  $sushiService = new SushiService(new NamedArguments(array('primaryKey' => $sushiServiceID)));

  $sushiService->setImportDates($_POST['startDate'], $_POST['endDate']);

  //try to run!
  try {
    $logText = $sushiService->runAll($_POST['overwritePlatform']);
  } catch (Exception $e) {
    $logText = $e->getMessage();
  }

  $_SESSION['sushi_log'] = nl2br($logText);
  header('Location: '.$_SERVER['PHP_SELF']);
  exit;
}

//print header
$pageTitle=_('SUSHI Import');
include 'templates/header.php';
?>

	<script type="text/javascript" src="js/sushi.js"></script>

	<table class="headerTable" style="background-image:url('images/header.gif');background-repeat:no-repeat;">
		<tr><td>
			<table style='width:897px;'>
			<tr style='vertical-align:top'>
			<td><span class="headerText"><?php echo _("SUSHI Administration");?></span><br /></td>
			<td style='text-align:right;'>&nbsp;</td>
			</tr>
			</table>


			<a href='ajax_forms.php?action=getAddPlatformForm&height=150&width=325&modal=true' class='thickbox' id='uploadDocument'><?php echo _("Add new platform for SUSHI");?></a>

			<br /><br /><div id="div_run_feedback"><?php

if (isset($_SESSION['sushi_log'])) {
    echo "<div class='headerText'>" . _("Sushi Output Log:") . "</div>" . $_SESSION['sushi_log'] . "<br /><br />";
    unset($_SESSION['sushi_log']);
}

?></div><br />
			<div class="headerText" style='margin-bottom:9px;'><?php echo _("Outstanding Import Queue");?>&nbsp;&nbsp;&nbsp;<span id='span_outstanding_feedback'></span></div>
			<div id="div_OutstandingSushiImports"></div>

			<br /><br /><br />
			<div class="headerText" style='margin-bottom:9px;'><?php echo _("Last Failed SUSHI Imports");?>&nbsp;&nbsp;&nbsp;<span id='span_failed_feedback'></span></div>
			<div id="div_FailedSushiImports"></div>

			<br /><br /><br />
			<div class="headerText" style='margin-bottom:9px;'><?php echo _("All SUSHI Services");?>&nbsp;&nbsp;&nbsp;<span id='span_upcoming_feedback'></span></div>
			<div id="div_AllSushiServices"></div>



		</td></tr>
	</table>



<?php
  //print footer
  include 'templates/footer.php';

?>
