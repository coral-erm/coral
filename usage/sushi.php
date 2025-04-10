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

<main id="main-content">
	<article>
			<h2><?php echo _("SUSHI Administration");?></h2>
			
			<button type="button" onclick='myDialog("ajax_forms.php?action=getAddPlatformForm&height=500&width=800&modal=true",500,800)' class='thickbox primary' id='uploadDocument'><?php echo _("Add new platform for SUSHI");?></button>

			<div id="div_run_feedback"><?php
				if (isset($_SESSION['sushi_log'])) {
						echo "<h3>" . _("Sushi Output Log:") . "</h3>";
						echo "<p>" . $_SESSION['sushi_log'] . "</p>";
						unset($_SESSION['sushi_log']);
				} ?>
			</div>
			<h3><?php echo _("Outstanding Import Queue");?></h3>
			<p id='span_outstanding_feedback'></p>
			<div id="div_OutstandingSushiImports"></div>

			<h3><?php echo _("Last Failed SUSHI Imports");?></h3>
			<p id='span_failed_feedback'></p>
			<div id="div_FailedSushiImports"></div>

			<h3><?php echo _("All SUSHI Services");?></h3>
			<p id='span_upcoming_feedback'></p>
			<div id="div_AllSushiServices"></div>
	</article>
</main>

<?php
  //print footer
  include 'templates/footer.php';
?>
<script type="text/javascript" src="js/sushi.js"></script>
</body>
</html>