<?php

/*
 * *************************************************************************************************************************
 * * CORAL Usage Statistics Reporting Module v. 1.0
 * *
 * * Copyright (c) 2010 University of Notre Dame
 * *
 * * This file is part of CORAL.
 * *
 * * CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * *
 * * CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * *
 * * You should have received a copy of the GNU General Public License along with CORAL. If not, see <http://www.gnu.org/licenses/>.
 * *
 * *************************************************************************************************************************
 */
session_start();
require 'minify.php';
ob_start('minify_output');
include_once 'directory.php';

// print header
$pageTitle = 'Home';
include 'templates/header.php';

?>
<main id="main-content">
	<article>
	<form name="reportlist" method="post" onsubmit=" return daterange_onsubmit()" action="report.php">
		
	
					<div id='div_report'>
						<label for="reportID">
							<?php echo _("Select Report");?>
						</label>
						<select name='reportID' id='reportID' class='opt'>
							<option value=''></option>
<?php
// get all reports for output in drop down

$db = DBService::getInstance();
foreach ( $db->query("SELECT reportID, reportName FROM Report ORDER BY 2, 1")->fetchRows(MYSQLI_ASSOC) as $report ){
	echo "<option value='" . $report['reportID'] . "' ";
	if (isset($report['reportID']) && isset($_GET['reportID']) && $report['reportID'] === $_GET['reportID']){
		echo 'selected';
	}
	echo ">" . $report['reportName'] . "</option>";
}
unset($db);
?>
						</select>
					</div>
					<div id='div_parm'>
<?php
if (isset($_GET['reportID']))
{
	$reportID = $_GET['reportID'];
}
else if (isset($_SESSION['reportID']))
{
	$reportID = $_SESSION['reportID'];
	unset($_SESSION['reportID']);
}

if (isset($reportID))
{
	$report = ReportFactory::makeReport($reportID);
	Parameter::$ajax_parmValues = array();
	foreach ( $report->getParameters() as $parm )
	{
		$parm->form();
	}
	Parameter::$ajax_parmValues = null;
}
else
{
	echo "<br />";
}

?>
					</div>
					<input type='hidden' name='rprt_output' value='web'/>
					<p class="actions">
						<input type="submit" value="<?php echo _("Submit");?>" name="submitbutton" id="submitbutton" class="submit-button primary" />
						<input type="button" value="<?php echo _("Reset");?>" name="resetbutton" id="resetbutton" onclick="clearParms();" class="cancel-button secondary" />
					</p>
	</article>
</main>

<?php
// print footer
include 'templates/footer.php';
ob_end_flush();
?>
<script src="js/index.js"></script>
</body>
</html>