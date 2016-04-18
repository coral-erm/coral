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

<center>
<form name="reportlist" method="post" onsubmit=" return daterange_onsubmit()" action="report.php">

<table class='noborder' cellpadding="0" cellspacing="0" style="width: 699px; text-align: left;">
	<tr>
		<td class="noborder" style="background-image: url('images/reportstitle.gif'); background-repeat: no-repeat; text-align: right;">
			<span style="border: none; outline: none; -moz-outline-style: none; float: left;">
			<img src='images/transparent.gif' style='width: 450px; height: 100px; border: none' alt=''/></span>
			<div style='margin-right: 5px; margin-top: 35px; text-align: right;'>
			<span style='float: right; font-size: 110%; color: #526972'>&nbsp;</span>
			</div>
		</td>
	</tr>
	<tr>
		<td class="fullborder"><br /> <br />
			<div id='div_report'>
				<label for="reportID">Select Report</label> 
				<select name='reportID' id='reportID' class='opt'>
					<option value=''></option>
						<?php
						// get all reports for output in drop down

						$db = new DBService();
						foreach ( $db->query("SELECT reportID, reportName FROM Report ORDER BY 2, 1")->fetchRows(MYSQLI_ASSOC) as $report ) {
							echo "<option value='" . $report['reportID'] . "' ";
							if (isset($_REQUEST['reportID']) && $report['reportID'] === $_REQUEST['reportID']) {
								echo 'selected="selected"';
							}
							echo ">{$report['reportName']}</option>";
						}
						unset($db);
						?>
				</select>
			</div>

			<div id='div_parm'>
			<?php

				if (isset($_GET['reportID'])) {
					$reportID = $_GET['reportID'];
				} else if (isset($_SESSION['reportID'])) {
					$reportID = $_SESSION['reportID'];
					unset($_SESSION['reportID']);
				}

				if (isset($reportID)) {
					$report = ReportFactory::makeReport($reportID);
					Parameter::$ajax_parmValues = array();
					foreach ( $report->getParameters() as $parm ) {
						$parm->form();
					}
					Parameter::$ajax_parmValues = null;
				} else {
					echo "<br />";
				}

			?>

			</div>
			<input type='hidden' name='rprt_output' value='web'/>
			<br /><br />
			<input type="submit" value="Submit" name="submitbutton" id="submitbutton"/>
			<input type="button" value="Reset" name="resetbutton" id="resetbutton" onclick="javascript:clearParms();"/>
		</td>
	</tr>
</table>

</form>
</center>
<br />
<br />
<br />

<script type="text/javascript" src="js/index.js"></script>

<?php
// print footer
include 'templates/footer.php';
ob_end_flush();
?>
