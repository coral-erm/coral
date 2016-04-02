<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
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

include_once 'directory.php';


//set referring page
CoralSession::set('ref_script', $currentPage);

$pageTitle=_('My Queue');
include 'templates/header.php';



?>


	<table class='headerTable'>
	<tr>
	<td style='margin:0;padding:0;text-align:left;'>
		<table style='width:100%; margin:0 0 11px 0;padding:0;'>
		<tr style='vertical-align:top'>
		<td>
		<span class="headerText"><?php echo _("My Queue");?></span>
		<br />
		</td>
		</tr>
		</table>


		<table style='width:890px; text-align:left; vertical-align:top;'>
		<tr>
		<td style='width:170px;vertical-align:top;'>
			<table class='queueMenuTable' style='width:170px;'>
				<tr><td><div class='queueMenuLink'><a href='javascript:void(0);' id='OutstandingTasks'><?php echo _("Outstanding Tasks");?></a></div><span class='span_OutstandingTasksNumber smallGreyText' style='clear:right; margin-left:10px;'></span></td></tr>
				<tr><td><div class='queueMenuLink'><a href='javascript:void(0);' id='SavedRequests'><?php echo _("Saved Requests");?></a></div><span class='span_SavedRequestsNumber smallGreyText' style='clear:right; margin-left:10px;'></span></td></tr>
				<tr><td><div class='queueMenuLink'><a href='javascript:void(0);' id='SubmittedRequests'><?php echo _("Submitted Requests");?></a></div><span class='span_SubmittedRequestsNumber smallGreyText' style='clear:right; margin-left:10px;'></span></td></tr>

			</table>
		</td>
		<td class='queueRightPanel' style='width:720px;margin:0;'>
			<div id='div_QueueContent'>
			<img src = "images/circle.gif" /><?php echo _("Loading...");?>
			</div>
			<div style='margin-top:5px;' class='darkRedText' id='div_error'></div>

		</td>
		</tr>
		</table>



	</td>
	</tr>
	</table>


	<br />
	<br />

	<script type="text/javascript" src="js/queue.js"></script>

<?php
include 'templates/footer.php';
?>


