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

$util = new Utility();
$config = new Configuration();


// find sushi services which need to be run today
$day = date("j");
$sushiServices = new SushiService();
$sushiServicesArray = $sushiServices->getByDayOfMonth($day);
$emailLog = sprintf(_("<h2>%d SUSHI runs found for day: %s</h2>"), count($sushiServicesArray), $day);

foreach ($sushiServicesArray as $sushiService){
	$sushiService->setImportDates();
	$emailLog .= "<h3>" . $sushiService->getServiceProvider() . "</h3>";

 	//try to run!
	try {
		$emailLog .= nl2br($sushiService->runAll($_POST['overwritePlatform']));
	} catch (Exception $e) {
		$emailLog .= nl2br($e->getMessage());
	}

}


//if more than one run, send email
if (is_array($sushiServicesArray) && count($sushiServicesArray) > 0) {
	$emailLog .= sprintf(_("<p>Log in to <a href='%s'>Sushi Administration</a> for more information.</p>"), $util->getPageURL() . 'sushi.php');

	//send email to email addresses listed in DB
	$logEmailAddress = new LogEmailAddress();
	$emailAddresses = array();

	foreach ($logEmailAddress->allAsArray() as $emailAddress){
		$emailAddresses[] = $emailAddress['emailAddress'];
	}

	if (is_array($emailAddresses) && count($emailAddresses) > 0) {
		$email = new Email();
		$email->to 			= implode(", ", $emailAddresses);
		$email->subject		= sprintf(_("SUSHI Scheduled run log for %s - %d runs"), format_date(date), count($sushiServicesArray));
		$email->message		= $emailLog;


		if ($email->send()) {
			printf(_("Run complete. Log has been emailed to %s"), implode(", ", $emailAddresses));
		}else{
			printf(_("Email to %s failed!"),  implode(", ", $emailAddresses));
		}
	}

}else{
	echo _("Nothing to see here!  (no sushi scheduled today)");
}


echo "<br /><br />" . $emailLog;

?>




