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
include_once 'directory.php';

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="public">
	<title>CORAL Usage Statistics Reporting - FAQ</title>
	<link rel="stylesheet" href="css/style.css" type="text/css"
		media="screen" />
	<link rel="stylesheet" href="css/thickbox.css" type="text/css"
		media="screen" />
	<link rel="stylesheet" href="../css/datePicker.css" type="text/css"
		media="screen" />
	<link rel="stylesheet" href="../css/jquery.autocomplete.css"
		type="text/css" media="screen" />
	<link rel="stylesheet" href="css/jquery.tooltip.css" type="text/css"
		media="screen" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link  rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="../js/plugins/datejs-patched-for-i18n.js"></script>
	<script type="text/javascript" src="../js/plugins/jquery.datePicker-patched-for-i18n.js"></script>
	<script type="text/javascript" src="../js/plugins/jquery.autocomplete.js"></script>
	<script type="text/javascript" src="../js/plugins/jquery.tooltip.js"></script>
	<script type="text/javascript" src="js/common.js"></script>

</head>
<body>


<?php
$type = $_GET['type'];

if ($type === 'report'){

	$report = ReportFactory::makeReport($_GET['value']);

	?>
<br />
	<center>
		<table width='400'>
			<tr>
				<td>
					<h2><?php echo $report->name; ?></h2>
					<h3><?php echo _("Frequently Asked Questions");?></h3>
					<b><?php echo _("Q. Why isn't the HTML number double the PDF number for interfaces that automatically download HTML?");?></b>
					<br />
					<?php echo _("A. Frequently these sites do NOT automatically download HTML from the Table of Contents browse interface, so even platforms such as ScienceDirect occasionally have higher PDF than HTML counts.");?>
					<br /><br />
					<b><?php echo _("Q. I thought COUNTER standards prevented double-counting of article downloads.");?></b>
					<br />
					<?php echo _("A. COUNTER does require that duplicate clicks on HTML or PDF within a short period of time be counted once. But COUNTER specifically does not deny double count of different formats--HTML and PDF. Because some publishers automatically choose HTML for users, and because many users prefer to save and/or print the PDF version, this interface significantly inflates total article usage.");?>
					<br /><br />
					<b><?php echo _("Q. Why do some Highwire Press publishers have high HTML ratios to PDFs, but some appear to have a very low ratio?");?></b>
					<br />
					<?php echo _("A. Some publishers have automatic HTML display on Highwire, and some do not. This is because the publisher is able to indicate a preferred linking page through the DOI registry. Because this platform includes multiple publishers, the interface impact is not consistent.");?>
					<br /><br />
				</td>
			</tr>
		</table>
	</center>
<?php
}else{
	echo _('Invalid type!!');
}

?>


</body>
</html>
