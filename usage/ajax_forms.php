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
** ajax_processing.php contains processing (adds/updates/deletes) on data sent using ajax from forms and other pages
**
** when ajax_processing.php is called through ajax, 'action' parm is required to dictate which form will be returned
**
**************************************************************************************************************************
*/


include "common.php";
include_once 'directory.php';

$action = $_REQUEST['action'];

switch ($action) {


	//log email addresses (on admin page)
	case 'getLogEmailAddressForm':

		if (isset($_GET['logEmailAddressID']) && ($_GET['logEmailAddressID'] != '')){
			$logEmailAddressID = $_GET['logEmailAddressID'];
			$addUpdate = _('Edit Email Address');
			$logEmailAddress = new LogEmailAddress(new NamedArguments(array('primaryKey' => $_GET['logEmailAddressID'])));
		}else{
			$logEmailAddressID = '';
			$addUpdate = _('Add Email Address');
			$logEmailAddress = new LogEmailAddress();
		}




		?>
		<div id='div_updateForm'>
		<input type='hidden' id='updateLogEmailAddressID' name='updateLogEmailAddressID' value='<?php echo $logEmailAddressID; ?>'>
		<p id='span_errors' class='error'></p>

		<p>
			<label for="emailAddress"><?php echo $addUpdate; ?></label>
			<input type='text' id='emailAddress' name='emailAddress' value='<?php if (isset($_GET['logEmailAddressID']) && ($_GET['logEmailAddressID'] != '')) echo $logEmailAddress->emailAddress; ?>'>
		</p>
		<p>
			<button type='button' onclick='doSubmitLogEmailAddress();' id='addButton' class='submit-button primary'><?php echo ($addUpdate); ?></button>
		</p>
		<p>
			<button type='button' onclick='myCloseDialog(); return false' id='closeButton' class='cancel-button secondary'><?php echo _("Close");?></button>
		</p>
		
		</div>

		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#emailAddress').keyup(function(e) {

				   if(e.keyCode == 13) {
					   doSubmitLogEmailAddress();
				   }
		   });

		</script>


		<?php

		break;






	//prompt for running sushi, posts to sushi.php
	case 'getSushiRunForm':

		if ((isset($_GET['sushiServiceID'])) && ($_GET['sushiServiceID'] != '')){
			$sushiServiceID = $_GET['sushiServiceID'];
 			$sushiService = new SushiService(new NamedArguments(array('primaryKey' => $sushiServiceID)));

 			$sushiService->setDefaultImportDates();

			?>
			<div id='div_sushiRunForm'>
			<form name="input" action="sushi.php" method="post">
			<input type='hidden' id='sushiServiceID' name='sushiServiceID' value='<?php echo $sushiServiceID; ?>'>
			<h3><?php printf(_("SUSHI Service for %s"), $sushiService->getServiceProvider);?></h3>
			<p id='span_errors' class='error'></p>

			<p><?php echo _("Optional Parameters");?></p>
			<div class="form-grid">
				
				<label for='startDate'><?php echo _("Start Date:");?></label>
				<!-- TODO: i18n placeholder date format -->
				<input type='text' class='date-pick' id='startDate' name='startDate' value="<?php echo $sushiService->startDate; ?>" placeholder="<?php echo _("(yyyy-mm-dd)");?>" aria-describedby="span_error_startDate" />
				<p id='span_error_startDate' class='error'></p>
				
				<label for='endDate'><?php echo _("End Date:");?></label>
				<!-- TODO: i18n placeholder date format -->
				<input type='text' class='date-pick' id='endDate' name='endDate' value="<?php echo $sushiService->endDate; ?>" placeholder="<?php echo _("(yyyy-mm-dd)");?>" aria-describedby="span_error_endDate" />
				<p id='span_error_endDate' class='error'></p>

				<p class="checkbox">
					<input type='checkbox' id='overwritePlatform' name='overwritePlatform' value='1' checked />
					<label for="overwritePlatform"><?php echo _("Ensure platform name stays CORAL's Platform Name: ") . $sushiService->getServiceProvider;?></label>
				</p>

				<p class="actions">
					<input type='submit' value='<?php echo _("submit for processing");?>' name='submitSushiRun' id ='submitSushiRun' class='submit-button primary'>
					<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
				</p>
			</div>
			</form>
			</div>

			<?php

		}else{
			echo _("No Sushi Service passed in!");
		}

		break;




	case 'getOutlierForm':


		if (isset($_GET['outlierID']) && ($_GET['outlierID'] != '')){
			$outlierID = $_GET['outlierID'];
			$outlier = new Outlier(new NamedArguments(array('primaryKey' => $_GET['outlierID'])));
		}


		?>
		<form onsubmit='window.parent.updateOutlier()'>
			<div id='div_updateForm' class="form-grid">
				<input type='hidden' id='updateOutlierID' name='updateOutlierID' value='<?php echo $outlierID; ?>'>
				<h2 class='headerText'><?php printf(_("Edit Outlier - <b>Level %s</b>"), $outlier->outlierLevel); ?></h2>
				
				<label for='overageCount'><?php echo _("Count Over");?></label>
				<input type='text' id='overageCount' name='overageCount' value="<?php echo $outlier->overageCount; ?>" aria-describedby='span_error_overageCount' />
				<span id='span_error_overageCount' class='error'></span>
			
				<label for='overagePercent'><?php echo _("% Over prior 12 months");?></label>
				<input type='text' id='overagePercent' name='overagePercent' value="<?php echo $outlier->overagePercent; ?>" aria-describedby='span_error_overagePercent' />
				<span id='span_error_overagePercent' class='error'></span>

				<p class="actions">
					<input type='submit' value='<?php echo _("Edit");?>' class='submit-button primary'>
					<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
				</p>
			</div>
		</form>
		<?php

		break;




	//reporting display name (on reporting page)
	case 'getReportDisplayForm':
		if (isset($_GET['updateID'])) $updateID = $_GET['updateID']; else $updateID = '';

		if ($_GET['type'] == 'platform'){
			$obj = new Platform(new NamedArguments(array('primaryKey' => $updateID)));
		}else{
			$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $updateID)));
		}


		?>
		<form onsubmit="updateReportDisplayName()">
			<div id='div_updateForm' class="block-form">
				<input type='hidden' id='updateID' name='updateID' value='<?php echo $updateID; ?>'>
				<input type='hidden' id='type' name='type' value='<?php echo $_GET['type']; ?>'>
				
				<h2 class='headerText' id="reportDisplayNameLabel"><?php echo _("Edit Report Display Name");?></h2>
				<p id='span_errors' class='error'></p>
				
				<label for="reportDisplayName"><?php echo _("Report Display Name");?></label>
				<input type='text' id='reportDisplayName' name='reportDisplayName' value='<?php echo $obj->reportDisplayName ?>' />
				
				<p class="actions">
					<input type="submit" class='submit-button primary' value="<?php echo _("Update") ?>" />
					<input type="button" onclick='myCloseDialog()' class='cancel-button secondary' value="<?php echo _("Close");?>" />
				</p>
			</div>
		</form>
		<?php

		break;


	case 'getPlatformNoteForm':
		if (isset($_GET['platformNoteID'])) $platformNoteID = $_GET['platformNoteID']; else $platformNoteID = '';
		if (isset($_GET['platformID'])) $platformID = $_GET['platformID'];


		if ($platformNoteID) $addUpdate = _('Edit Interface Notes'); 
		else $addUpdate = _('Add Interface Notes');

		if ($platformNoteID){
			$platformNote = new PlatformNote(new NamedArguments(array('primaryKey' => $platformNoteID)));

			$platformID = $platformNote->platformID;

			if ($platformNote->counterCompliantInd == '1'){
				$counterCompliant = 'checked';
				$notCounterCompliant = '';
			}elseif ($platformNote->counterCompliantInd == '0'){
				$notCounterCompliant = 'checked';
				$counterCompliant = '';
			}else{
				$notCounterCompliant = '';
				$counterCompliant = '';
			}

			if (($platformNote->endYear == '0') || ($platformNote->endYear =='')) $endYear = ''; else $endYear = $platformNote->endYear;
			$startYear = $platformNote->startYear;
			$noteText = $platformNote->noteText;

		}else{
			$platformNote = new PlatformNote();
			$notCounterCompliant = '';
			$counterCompliant = '';
			$startYear = '';
			$endYear = '';
			$noteText = '';
		}



		?>
		<div id='div_updateForm'>
			<input type='hidden' id='editPlatformNoteID' name='editPlatformNoteID' value='<?php echo $platformNoteID; ?>'>
			<input type='hidden' id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		
			<h2 class='headerText'><?php echo $addUpdate;?></h2>
			<p id='span_errors' class='error'></p>
		
			<div class="form-grid">
				<label for='startYear'><?php echo _("Start Year:");?></label>
				<input type='text' id='startYear' name='startYear' value="<?php echo $platformNote->startYear; ?>" aria-describedby="span_error_startYear" />
				<p id='span_error_startYear' class='error'></p>
			
				<label for='endYear'><?php echo _("End Year:");?></label>
				<input type='text' id='endYear' name='endYear' value="<?php echo $endYear; ?>" aria-describedby="span_error_endYear" />
					<p id='span_error_endYear' class='error'></p>
			
				<fieldset class="subgrid">
					<legend><?php echo _("Counter Compliant?");?></legend>
					<p class="form-group form-inline checkbox">
						<label><input type='radio' name='counterCompliantInd' value='1' <?php echo $counterCompliant; ?> /> <?php echo _("Yes"); ?></label>
						<label><input type='radio' name='counterCompliantInd' value='0' <?php echo $notCounterCompliant; ?> /> <?php echo _("No"); ?></label>
					</p>
				</fieldset>
			
				<label for='noteText'><?php echo _("Interface Notes:");?></label>
				<textarea rows='4' id='noteText' name='noteText'><?php echo $noteText; ?></textarea>
			
				
			<p class="actions">
				<input type='submit' value='<?php echo _("submit");?>' name='submitPlatformNoteForm' id ='submitPlatformNoteForm' class='submit-button primary'></td>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" id='interface-cancel' class='cancel-button secondary'></td>
			</p>

			</div>

		</div>

		<script type="text/javascript" src="js/forms/platformNoteSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

		break;








	case 'getPublisherNoteForm':
		if (isset($_GET['publisherPlatformNoteID'])) $publisherPlatformNoteID = $_GET['publisherPlatformNoteID']; else $publisherPlatformNoteID = '';
		if (isset($_GET['publisherPlatformID'])) $publisherPlatformID = $_GET['publisherPlatformID'];

		if ($publisherPlatformNoteID){
			$addUpdate = _('Edit Publisher Notes');

			$publisherPlatformNote = new PublisherPlatformNote(new NamedArguments(array('primaryKey' => $publisherPlatformNoteID)));

			$publisherPlatformID = $publisherPlatformNote->publisherPlatformID;

			if (($publisherPlatformNote->endYear == '0') || ($publisherPlatformNote->endYear =='')) $endYear = ''; else $endYear = $publisherPlatformNote->endYear;

		}else{
			$addUpdate = _('Add Publisher Notes');
			$publisherPlatformNote = new PublisherPlatformNote();
		}


		?>
		<div id='div_updateForm'>
			<input type='hidden' id='editPublisherPlatformNoteID' name='editPublisherPlatformNoteID' value='<?php echo $publisherPlatformNoteID; ?>'>
			<input type='hidden' id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>
		
			<h2 class='headerText'><?php echo $addUpdate ?></h2>
			<p id='span_errors' class='error'></p>
			
			<div class="form-grid">
				<label for='startYear'><?php echo _("Start Year:");?></label>
				<input type='text' id='startYear' name='startYear' value="<?php echo $publisherPlatformNote->startYear; ?>" aria-describedby="span_error_startYear" />
				<span id='span_error_startYear' class='error'></span>
			
				<label for='endYear'><?php echo _("End Year:");?></label>
				<input type='text' id='endYear' name='endYear' value="<?php echo $endYear; ?>" aria-describedby="span_error_endYear" />
				<p id='span_error_endYear' class='error'></p>
				
				<label for='noteText'><?php echo _("Publisher Notes:");?></label>
				<textarea rows='4' id='noteText' name='noteText'><?php echo $publisherPlatformNote->noteText; ?></textarea>

				<p class='actions'>
					<input type='submit' value='<?php echo _("submit");?>' name='submitPublisherNoteForm' id ='submitPublisherNoteForm' class='submit-button primary'>
					<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
				</p>

			</div>

		</div>

		<script type="text/javascript" src="js/forms/publisherNoteSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

		break;





	case 'getLoginForm':
		if (isset($_GET['externalLoginID'])) $externalLoginID = $_GET['externalLoginID']; else $externalLoginID = '';
		if (isset($_GET['platformID'])) $platformID = $_GET['platformID']; else $platformID = '';
		if (isset($_GET['publisherPlatformID'])) $publisherPlatformID = $_GET['publisherPlatformID']; else $publisherPlatformID = '';

		if ($externalLoginID){
			$addUpdate = _('Edit Login');
			$externalLogin = new ExternalLogin(new NamedArguments(array('primaryKey' => $externalLoginID)));

			$publisherPlatformID = $externalLogin->publisherPlatformID;
			$platformID = $externalLogin->platformID;
		}else{
			$addUpdate = _('Add Login');
			$externalLogin = new ExternalLogin();
		}

		?>
		<div id='div_updateForm'>
		<input type='hidden' id='editExternalLoginID' name='editExternalLoginID' value='<?php echo $externalLoginID; ?>'>
		<input type='hidden' id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		<input type='hidden' id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>
		
		<h2 class='headerText'><?php echo $addUpdate;?></h2>
		<p id='span_errors' error="error"></p>
		
		<label for='username'><?php echo _("Username:");?></label>
		<input type='text' id='username' name='username' value="<?php if ($externalLoginID) echo $externalLogin->username; ?>" aria-describedby="span_error_loginID" />
		<p id='span_error_loginID' class='error'></p>
		
		<label for='password'><?php echo _("Password:");?></label>
		<input type='text' id='password' name='password' value="<?php if ($externalLoginID) echo $externalLogin->password; ?>" aria-describedby="span_error_password" />
		<p id='span_error_password' class='error'></p>
	
		<label for='loginURL'><?php echo _("URL:");?></label>
		<input type='text' id='loginURL' name='loginURL' value="<?php if ($externalLoginID) echo $externalLogin->loginURL; ?>" aria-describedby="span_error_url" />
		<p id='span_error_url' class='error'></p>
		
		<label for='noteText'><?php echo _("Login Notes:");?></label>
		<textarea rows='4' id='noteText' name='noteText'><?php if ($externalLoginID) echo $externalLogin->noteText; ?></textarea>
	
		<p class="actions">
			<input type='button' value='<?php echo _("submit");?>' name='submitExternalLoginForm' id ='submitExternalLoginForm' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>

		</div>
	</div>

		<script type="text/javascript" src="js/forms/externalLoginSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

		break;


	//sushi service information
	case 'getSushiForm':
		$sushiServiceID = $_GET['sushiServiceID'];
		$platformID = $_GET['platformID'];


		if ($sushiServiceID){
			$addUpdate = _('Edit SUSHI Connection');
			$sushiService = new SushiService(new NamedArguments(array('primaryKey' => $sushiServiceID)));

		}else{
			$addUpdate = _('Add SUSHI Connection');
			$sushiService = new SushiService();
		}

		?>
		<div id='div_updateForm'>
		<input type='hidden' id='editSushiServiceID' name='editSushiServiceID' value='<?php echo $sushiServiceID; ?>'>
		<input type='hidden' id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		
		<h2 class='headerText'><?php echo $addUpdate;?></h2>
		<p id='span_errors' class='error'></p>
		
		<div class="form-grid">

		<label for='serviceURL'><?php echo _("Service/Endpoint URL:");?></label>
		<input type='url' id='serviceURL' name='serviceURL' value="<?php if ($sushiServiceID) echo $sushiService->serviceURL; ?>" aria-describedby="span_error_serviceURL" />

		<p class="form-text"><?php echo _(" - if using COUNTER's WSDL or Release 5");?></p>
		<p id='span_error_serviceURL' class="error"></p>
			
		<label for='wsdlURL'><?php echo _(" - or - WSDL URL:");?></label>
		<input type='url' id='wsdlURL' name='wsdlURL' value="<?php if ($sushiServiceID) echo $sushiService->wsdlURL; ?>" />

		<p class="form-text"><?php echo _(" - if not using COUNTER's WSDL (not applicable for Release 5)");?></p>
		
		<label for='reportLayouts'><?php echo _("Report Type(s):");?></label>
		<div class="form-group">
		<input type='text' id='reportLayouts' name='reportLayouts' value="<?php if ($sushiServiceID) echo $sushiService->reportLayouts; ?>" aria-describedby="span_error_reportLayouts" />
		<p class="form-text"><?php echo _("separate report types with semi-colon, e.g. JR1;BR1");?></p>
		</div>
		
		<p id='span_error_reportLayouts' class='error'></p>
		
		<label for='releaseNumber'><?php echo _("COUNTER Release:");?></label>
		<select id='releaseNumber' name='releaseNumber'>
			<option value='4' <?php if ($sushiService->releaseNumber == "4"){ echo "selected"; } ?>>4</option>
      <option value='5' <?php if ($sushiService->releaseNumber == "5"){ echo "selected"; } ?>>5</option>
		</select>
		
		<label for='requestorID'><?php echo _("Requestor ID:");?></label>
		<input type='text' id='requestorID' name='requestorID' value="<?php if ($sushiServiceID) echo $sushiService->requestorID; ?>" />
		
		<label for='apiKey'><?php echo _("API Key:");?></label>
		<div class="form-group">
			<input type='text' id='apiKey' name='apiKey' value="<?php if ($sushiServiceID) echo $sushiService->apiKey; ?>" />
    	<p class="form-text"><?php echo _("for Release 5, some vendors use an API Key for authentication.");?></p>
		</div>
		
		<label for='customerID'><?php echo _("Customer ID:");?></label>
		<input type='text' id='customerID' name='customerID' value="<?php if ($sushiServiceID) echo $sushiService->customerID; ?>" />
		
		<label for='platform'><?php echo _("Platform:");?></label>
		<div class="form-group">
			<input type='text' id='platform' name='platform' value="<?php if ($sushiServiceID) echo $sushiService->platform; ?>" />
			<p class="error"><?php echo _("(optional)") . " " . _("only needed when required by vendor");?></p>
		</div>
		
		<label for='security'><?php echo _("Security Type:");?></label>
		<div class="form-group">
			<input type='text' id='security' name='security' value="<?php if ($sushiServiceID) echo $sushiService->security; ?>" aria-describedby="span_error_security" />
			<p class="form-text"><?php echo _("(optional)");?><br /><?php echo _("can be: HTTP Basic, WSSE Authentication");?></p>
			<span id='span_error_security' class='error'></span>
		</div>
		
		<label for='login'><?php echo _("Login:");?></label>
		<div class="form-group">
			<input type='text' id='login' name='login' value="<?php if ($sushiServiceID) echo $sushiService->login; ?>" aria-describedby="span_error_login" />
			<!-- TODO: i18n placeholders -->
			<p class="form-text"><?php echo _("(optional)") . "<br /> - " . _("only needed for HTTP or WSSE Authentication");?></p>
			<p id='span_error_login' class='error'></p>
		</div>
		
		<label for='password'><?php echo _("Password:");?></label>
		<div class="form-group">
			<input type='text' id='password' name='password' value="<?php if ($sushiServiceID) echo $sushiService->password; ?>" aria-describedby="span_error_password" />
			<!-- TODO: i18n placeholders -->
			<p class="form-text"><?php echo _("(optional)") . "<br /> - " . _("only needed for HTTP or WSSE Authentication");?></p>
			<p id='span_error_password' class='error'></p>
		</div>
		
		<label for='serviceDayOfMonth'><?php echo _("Service Day:");?></label>
		<div class="form-group">
			<input type='text' id='serviceDayOfMonth' name='serviceDayOfMonth' value="<?php if ($sushiServiceID) echo $sushiService->serviceDayOfMonth; ?>" aria-describedby="span_error_serviceDay" />
			<!-- TODO: i18n placeholders -->			
			<p class="form-text"><?php echo _("(optional)") . "<br /> - " . _("number indicating the day of month the service should run") . "<br />" . _("(e.g. 27 will run 27th of every month)");?></p>
			<p id='span_error_serviceDay' class='error'></p>
		</div>
		
		<label for='noteText'><?php echo _("Sushi Notes:");?></label>
		<textarea rows='4' id='noteText' name='noteText'><?php if ($sushiServiceID) echo $sushiService->noteText; ?></textarea>
	
		<p class='actions'>
			<input type='submit' value='submit' name='submitSushiForm' id ='submitSushiForm' class='submit-button primary'>
			<input type='button' value='cancel' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>

		</div>

		</div>

		<script type="text/javascript" src="js/forms/sushiSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

		break;


	//form to edit associated organizations
    case 'getOrganizationForm':

		$publisherPlatformID = $_GET['publisherPlatformID'];
		$platformID = $_GET['platformID'];

		if (isset($_GET['publisherPlatformID']) && ($_GET['publisherPlatformID'] != '')){
			$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));
		}else{
			$obj = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));
		}

		if ($obj->organizationID) $organizationName = $obj->getOrganizationName; else $organizationName = '';

		?>
		<div id='div_organizationsForm'>
		<form id='organizationsForm'>
		<input type='hidden' id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>
		<input type='hidden' id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		
		<h2 class='headerText'><?php echo _("Link Associated Organization");?></h2>
		
		<div class="form-grid">
		
			<label for="organizationID" class="formText"><?php echo _("Organization:");?></label>  
			
			<input type='text' id='organizationName' name='organizationName' value="<?php echo $organizationName; ?>" />
			<input type='hidden' id='organizationID' name='organizationID' value='<?php echo $obj->organizationID; ?>'>
			<p id='span_error_organizationName' class='error'></p>
			<p id='span_error_organizationNameResult' class='error'></p>
		
		<p class="actions">	
			<input type='submit' value='<?php echo _("submit");?>' name='submitOrganization' id ='submitOrganization' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>
	</div>

		<script type="text/javascript" src="js/forms/organizationForm.js?random=<?php echo rand(); ?>"></script>
		</form>
		</div>


		<?php

        break;





	case 'getMonthlyOutlierForm':
		if (isset($_GET['platformID'])) $platformID = $_GET['platformID']; else $platformID = '';
 		if (isset($_GET['publisherPlatformID'])) $publisherPlatformID = $_GET['publisherPlatformID'];

		$archiveInd = $_GET['archiveInd'];
		$year = $_GET['year'];
		$month = $_GET['month'];


		$statsArray = array();
		if ($publisherPlatformID) {
			$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $publisherPlatformID)));
			$publisher = new Publisher(new NamedArguments(array('primaryKey' => $publisherPlatform->publisherID)));
			$platform = new Platform(new NamedArguments(array('primaryKey' => $publisherPlatform->platformID)));
			$nameDisplay = $publisher->name . " / " . $platform->name;

			$statsArray = $publisherPlatform->getMonthlyOutliers($archiveInd, $year, $month);
		}else{
			$platform = new Platform(new NamedArguments(array('primaryKey' => $platformID)));
			$nameDisplay = $platform->name;

			$statsArray = $platform->getMonthlyOutliers($archiveInd, $year, $month);
		}

		$totalRows = count($statsArray);

		?>

		<div id='div_outlierForm'>
		
		<h2 class='headerText'><?php echo $nameDisplay; ?></h2>
		
		
		<table class='dataTable table-border table-striped'>

		<?php

			if ($totalRows == 0){
				echo "<tr><td>" . _("None currently") . "</td></tr>";
			}else{
				foreach($statsArray as $monthlyStat){
					echo "<tr>";
					echo "<th scope='row'>" . $monthlyStat['Title']. "<p id='span_error_overrideUsageCount_" . $monthlyStat['monthlyUsageSummaryID'] . "' class='error'></p></th>";
					echo "<td class='center' style='background-color:" . $monthlyStat['color'] . "'>" . $monthlyStat['usageCount'] . "</td>";
					echo "<td><input type='text' name = 'overrideUsageCount_" . $monthlyStat['monthlyUsageSummaryID'] . "' id = 'overrideUsageCount_" . $monthlyStat['monthlyUsageSummaryID'] . "' value='" . $monthlyStat['overrideUsageCount'] . "' aria-label='".sprintf(_('Override usage count for %s'), $monthlyStat['Title'])."'></td>";
					echo "<td><a href=\"javascript:updateOverride('" . $monthlyStat['monthlyUsageSummaryID'] . "');\" style='font-size:100%;'>" . _("edit override") . "</a><br /><a href=\"javascript:ignoreOutlier('" . $monthlyStat['monthlyUsageSummaryID'] . "');\" style='font-size:100%;'>" . _("ignore outlier") . "</a></td>";
					echo "</tr>";
				}
			}

		?>

		</table>
		<p class="actions">
		<a href='#' onclick='window.parent.updateFullStatsDetails(); myCloseDialog(); return false'><?php echo _("Close");?></a></td></tr>
		</p>
		<input type="hidden" id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		<input type="hidden" id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>
		<input type="hidden" id='archiveInd' name='archiveInd' value='<?php echo $archiveInd; ?>'>
		<input type="hidden" id='year' name='year' value='<?php echo $year; ?>'>
		<input type="hidden" id='month' name='month' value='<?php echo $month; ?>'>

		<script type="text/javascript" src="js/forms/outlierSubmitForm.js?random=<?php echo rand(); ?>"></script>
		</div>


		<?php

		break;





	case 'getYearlyOverrideForm':
		if (isset($_GET['platformID'])) $platformID = $_GET['platformID']; else $platformID = '';
 		if (isset($_GET['publisherPlatformID'])) $publisherPlatformID = $_GET['publisherPlatformID'];

		$archiveInd = $_GET['archiveInd'];
		$year = $_GET['year'];


		$statsArray = array();
		if ($publisherPlatformID) {
			$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $publisherPlatformID)));
			$publisher = new Publisher(new NamedArguments(array('primaryKey' => $publisherPlatform->publisherID)));
			$platform = new Platform(new NamedArguments(array('primaryKey' => $publisherPlatform->platformID)));
			$nameDisplay = $publisher->name . " / " . $platform->name;

			$statsArray = $publisherPlatform->getYearlyOverrides($archiveInd, $year);
		}else{
			$platform = new Platform(new NamedArguments(array('primaryKey' => $platformID)));
			$nameDisplay = $platform->name;

			$statsArray = $platform->getYearlyOverrides($archiveInd, $year);
		}

		$totalRows = count($statsArray);


		?>

		<div id='div_overrideForm'>
		
		<h2 class='headerText'><?php echo $nameDisplay; ?></h2>
		<p><?php echo _("(showing only titles for which there were outliers during the year)");?></p>
		
		
		<table class='dataTable table-border table-striped'>

		<?php

			if ($totalRows == 0){
				echo "<tr><td>" . _("None currently") . "</td></tr>";
			}else{
				foreach($statsArray as $yearlyStat){
				?>
					<tr>
					<th scope='row'><?php echo $yearlyStat['Title']; ?></th>
					<td><?php echo _("Total");?></td>
					<td><?php echo $yearlyStat['totalCount']; ?></td>
					<td><input name="overrideTotalCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" id="overrideTotalCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" type="text"value="<?php echo $yearlyStat['overrideTotalCount']; ?>" size="6" maxlength="6" /></td>
					<td><a href="javascript:updateYTDOverride('<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>', 'overrideTotalCount')"><?php echo _("edit");?></a></td>
					</tr>
					<tr>
					<th><span id="span_error_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>_response" class='error'></span></th>
					<td><?php echo _("PDF");?></td>
					<td><?php echo $yearlyStat['ytdPDFCount']; ?></td>
					<td><input name="overridePDFCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" id="overridePDFCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" type="text" value="<?php echo $yearlyStat['overridePDFCount']; ?>" size="6" maxlength="6" aria-label='<?php sprintf(_('Override PDF count for %s'), $monthlyStat['Title']); ?>'/></td>
					<td><a href="javascript:updateYTDOverride('<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>', 'overridePDFCount')"><?php echo _("edit");?></a></td>
					</tr>
					<tr>
					<td>&nbsp;</td>
					<td><?php echo _('HTML'); ?></td>
					<td><?php echo $yearlyStat['ytdHTMLCount']; ?></td>
					<td><input name="overrideHTMLCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" id="overrideHTMLCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" type="text"value="<?php echo $yearlyStat['overrideHTMLCount']; ?>" size="6" maxlength="6" aria-label='<?php sprintf(_('Override HTML count for %s'), $monthlyStat['Title']); ?>'/></td>
					<td><a href="javascript:updateYTDOverride('<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>', 'overrideHTMLCount')"><?php echo _("edit");?></a></td>
					</tr>
				<?php

				}
			}

		?>

		</table>
		<p class="actions">
			<a href='#' onclick='myCloseDialog(); return false'><?php echo _("Close");?></a>
		</p>
		<input type="hidden" id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		<input type="hidden" id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>
		<input type="hidden" id='archiveInd' name='archiveInd' value='<?php echo $archiveInd; ?>'>
		<input type="hidden" id='year' name='year' value='<?php echo $year; ?>'>

		<script type="text/javascript" src="js/forms/overrideSubmitForm.js?random=<?php echo rand(); ?>"></script>
		</div>


		<?php

		break;




	//Add Platforms for sushi
	case 'getAddPlatformForm':


		?>
		<div id='div_addPlatformForm' class="block-form">
			<h2 class='headerText'><?php echo _("Add New Platform for SUSHI Connection");?></h2>
			
			<label for='platformName'><?php echo _("Platform Name");?></label>
			<input type='text' id='platformName' name='platformName' value="" />
			<p id='span_error_Platform' class='error'></p>
			
			<p class="actions">
				<input type='submit' value='<?php echo _("submit");?>' name='submitPlatformForm' id ='submitPlatformForm' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" id='cancel-button' class='cancel-button secondary'>
			</p>
		</div>

		<script type="text/javascript" src="js/forms/platformSubmitForm.js?random=<?php echo rand(); ?>"></script>


		<?php

		break;






	//Add Identifiers
	case 'getAddIdentifierForm':
		if (isset($_GET['platformID'])) $platformID = $_GET['platformID']; else $platformID = '';
		if (isset($_GET['publisherPlatformID'])) $publisherPlatformID = $_GET['publisherPlatformID'];
		if (isset($_GET['titleID'])) $titleID = $_GET['titleID'];


		?>
		<div id='div_addIdentifierForm' class="block-form">
			
			<h2 class='headerText'><?php echo _("Add Identifier");?></h2>
			<label for='identifierType'><?php echo _("Identifier Type");?></label>
			
			<select id='identifierType' name='identifierType'>
				<option value='ISSN'><?php echo _("ISSN");?></option>
				<option value='eISSN'><?php echo _("eISSN");?></option>
				<option value='ISBN'><?php echo _("ISBN");?></option>
				<option value='eISBN'><?php echo _("eISBN");?></option>
				<option value='doi'><?php echo _("DOI");?></option>
				<option value='pi'><?php echo _("Proprietary ID");?></option>
			</select>
			
			<label for='identifier'><?php echo _("Identifier");?></label>
			<input type='text' id='identifier' name='identifier' value="" />
			<p id='span_error_Identifier' class='error'></p>

			<p class="actions">
				<input type='submit' value='<?php echo _("submit");?>' name='submitIdentifierForm' id ='submitIdentifierForm' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
			</p>
		</div>

		<input type="hidden" id='titleID' name='titleID' value='<?php echo $titleID; ?>'>
		<input type="hidden" id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		<input type="hidden" id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>

		<script type="text/javascript" src="js/forms/identifierSubmitForm.js?random=<?php echo rand(); ?>"></script>


		<?php

		break;




	//Related Titles (this form is display only)
	case 'getRelatedTitlesForm':
 		if (isset($_GET['titleID'])) $titleID = $_GET['titleID'];

		$title = new Title(new NamedArguments(array('primaryKey' => $titleID)));

		?>
		<div id='div_relatedTitles' class="block-form">
		<h2 class='headerText'><?php echo _("Associated Titles and Identifiers");?></h2>
		
		<?php
			$relatedTitle = new Title();
			foreach($title->getRelatedTitles as $relatedTitle) {

				echo "<h2 class='headerText'>" . $relatedTitle->title . "</h2>";

				foreach($relatedTitle->getIdentifiers as $relatedTitleIdentifier) {
					$displayIdentifier = substr($relatedTitleIdentifier->identifier,0,4) . "-" . substr($relatedTitleIdentifier->identifier,4,4);

					echo "<dl class='dl-grid'>";
					echo "<dt>" . $relatedTitleIdentifier->identifierType . "</dt>";
					echo "<dd>" . $displayIdentifier . "</dd>";
					echo "</dl>";

				}


			}
		?>

		<p><a href='#' onclick='myCloseDialog(); return false' class='cancel-button secondary'><?php echo _("Close");?></a>
		</p>

		</div>


		<?php

		break;








	//user form on the admin tab needs its own form since there are other attributes
	case 'getAdminUserUpdateForm':
		if (isset($_GET['loginID'])) $loginID = $_GET['loginID']; else $loginID = '';

		if ($loginID != ''){
			$update=_('Edit User');
			$updateUser = new User(new NamedArguments(array('primaryKey' => $loginID)));
		}else{
			$update=_('Add User');
		}


		?>
		<div id='div_updateForm'>
			<h2 class='headerText'><?php echo $update; ?></h2>
			<div class="form-grid">
				
				<label for='loginID'><?php echo _("Login ID");?></label>
				<input type='text' id='loginID' name='loginID' value='<?php echo $loginID; ?>' />
				
				<label for='firstName'><?php echo _("First Name");?></label>
				<input type='text' id='firstName' name='firstName' value="<?php if (isset($updateUser)) echo $updateUser->firstName; ?>" />
				
				<label for='lastName'><?php echo _("Last Name");?></label>
				<input type='text' id='lastName' name='lastName' value="<?php if (isset($updateUser)) echo $updateUser->lastName; ?>" />
			
				<label for='privilegeID'><?php echo _("Privilege");?></label>
				<div class="form-group">
					<p class="form-text">
						<?php echo _("Add/Edit users have access to everything except the Admin tab and admin users have access to everything");?>
					</p>
					<select name='privilegeID' id='privilegeID'>
						<?php



						$display = array();
						$privilege = new Privilege();

						foreach($privilege->allAsArray() as $display) {
							if ($updateUser->privilegeID == $display['privilegeID']){
								echo "<option value='" . $display['privilegeID'] . "' selected>" . $display['shortName'] . "</option>";
							}else{
								echo "<option value='" . $display['privilegeID'] . "'>" . $display['shortName'] . "</option>";
							}
						}

						?>
						</select>
					</div>
					<p class="actions">
						<input type='submit' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitUserData("<?php echo $loginID; ?>");' class='submit-button primary'>
						<input type='button' value='<?php echo _("Close");?>' onclick="myCloseDialog(); return false" id='update-user-cancel' class='cancel-button secondary'>
					</p>

			</div>
		</div>


		<script type="text/javascript" src="js/forms/adminUserForm.js?random=<?php echo rand(); ?>"></script>
		<?php

		break;

  case 'getUpdatePlatformForm':
    $platformID = $_GET['platformID'];
    $obj = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));

    ?>
    <div id='div_updateForm' class="block-form">
      <input type='hidden' id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
      
			<label for='platformName'><?php echo _("Platform Name:");?></label>
			<input type='text' id='platformName' name='platformName' value="<?php echo $obj->name; ?>" />
      
			<p class="actions">
				<input type='submit' value='<?php echo _("submit");?>' name='updatePlatformFrom' id ='updatePlatformForm' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" id='cancel-button' class='cancel-button secondary'>
			</p>

			<p class="warning">
				<?php echo _("If you change the platform name, any existing COUNTER reports will continue to use the original platform name. Before changing the platform name, make sure that you have no SUSHI reports for this platform in the outstanding import queue."); ?>
			</p>
    </div>

    <script type="text/javascript" src="js/forms/platformUpdateForm.js?random=<?php echo rand(); ?>"></script>

    <?php

    break;






	default:
		if (empty($_REQUEST['function']))
			return;
		printf(_("Function %s not set up!"), $_REQUEST['function']);
		break;


}



?>
