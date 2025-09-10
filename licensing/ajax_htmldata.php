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
**************************************************************************************************************************
** ajax_htmldata.php formats display data for all pages - tables and search results
**
** when ajax_htmldata.php is called through ajax, 'action' parm is required to dictate which data will be returned
**
** this also displays the links to edit/delete data depending on user privileges
**
**************************************************************************************************************************
*/


include_once 'directory.php';
include_once 'user.php';


switch ($_GET['action']) {

	//this is the head to license info on license.php
	case 'getLicenseHead':
		$licenseID = $_GET['licenseID'];
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
		$consortium = new Consortium(new NamedArguments(array('primaryKey' => $license->consortiumID)));

		?>

<div class="header">
		<h2><?php echo $license->shortName; ?></h2>
		<?php

		if ($user->canEdit()){?>
			<button type="button" onclick='myDialog("ajax_forms.php?action=getLicenseForm&licenseID=<?php echo $licenseID; ?>",260,280)' class='thickbox btn addElement'><img src='images/edit.gif' alt="<?php echo _("edit license") ?>"></button>
			<button type="button" onclick='deleteLicense("<?php echo $licenseID; ?>");' class='btn addElement'><img src='images/cross.gif' alt="<?php echo _("remove license") ?>"></button> 
		<?php } ?>
</div>



		<div class="header">
		<?php

		//make sure they have org module installed before we give them a link to view the organization
		$config = new Configuration;

		if ($config->settings->organizationsModule == 'Y'){
			$util = new Utility();

			echo '<div class="header"><h3>' . $license->getOrganizationName() . "</h3>  <a href='" . $util->getOrganizationURL() . $license->organizationID  . "' " .  getTarget() . " class='addElement'><img src='images/edit.gif' alt='"._("edit organization")."'></a></div>";

			if ($license->consortiumID) {
				echo "<p>" . $license->getConsortiumName() . "</p>";
			}
		}else{
			echo $license->getOrganizationName();
			if ($license->consortiumID) {
				echo "<p>" . $license->getConsortiumName() . "</p>";
			}
		}

		?>
		</div>
		<p>
		<?php if ($user->canEdit()){ ?>
			<label for="statusID"><?php echo _("License Status:");?></label>
			<select id='statusID' name='statusID' onchange='javascript:updateStatus();'>
			<option value=''></option>
			<?php


			$display = array();
			$status = new Status();

			foreach($status->allAsArray as $display) {
				if ($license->statusID == $display['statusID']) {
					echo "<option value='" . $display['statusID'] . "' selected>" . $display['shortName'] . "</option>";
				}else{
					echo "<option value='" . $display['statusID'] . "'>" . $display['shortName'] . "</option>";
				}
			}

			?>
			</select>
			
			<p class="error" id='span_updateStatusResponse' name='span_updateStatusResponse'></p>
		<?php } ?>
		
		<?php


		break;


	//sfx display for the sfx tab on license.php
	case 'getAllSFXProviders':

		$licenseID = $_GET['licenseID'];

		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
		$document = new Document();
		$documentArray = $license->getDocuments();

		$rowCount=0;

		//loop through each document separately
		//note - this tab is only displayed for admin users so no validation is required
		foreach($documentArray as $document) {
			$sfxProvider = new SFXProvider();
			foreach($document->getSFXProviders as $sfxProvider) {
				$rowCount++;
				if ($rowCount == "1"){
				?>
						<table class='verticalFormTable table-striped table-border'>
						<thead>
						<tr>
						<th scope="col"><?php echo _("For Document");?></th>
						<th scope="col"><?php echo _("Resource");?></th>
						<th scope="col"><?php echo _("Actions");?></th>
						</tr>
						</thead>
						<tbody>
				<?php
				}
				echo "<tr>";
				echo "<th scope='row'>" . $document->shortName . "</th>";
				echo "<td>" . $sfxProvider->shortName . "</td>";
				echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getSFXForm&licenseID=" . $licenseID . "&providerID=" . $sfxProvider->sfxProviderID . "\",210,300)' class='thickbox' aria-label='".sprintf(_('Edit %s'), $sfxProvider->shortName)."'><img class='icon' src='images/edit.gif' /></a>";
				echo "<a href='javascript:deleteSFXProvider(\"" . $sfxProvider->sfxProviderID . "\");' aria-label='".sprintf(_('Remove %s'), $sfxProvider->shortName)."'><img src='images/cross.gif' /></a></td>";
				echo "</tr>";
			}


		//end loop over sfx provider records
		}
		?>
		</tbody>
		</table>

		<?php
		if ($rowCount == "0"){
			echo "<p>" . _("(none found)") . "</p>";
		}

		if ($user->canEdit()){
			 echo "<p><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getSFXForm&licenseID=" . $licenseID . "\", 210, 320)' class='thickbox' id='addSFXResource'>"._("Add Terms Tool Resource Link")."</a></p>";
			
		}

		break;



	//attachments display for attachments tab on license.php
	//note - this was originally called email logs since that's the main intent but we renamed to attachments so it could be general purpose
	case 'getAllAttachments':

		$licenseID = $_GET['licenseID'];

		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
		$attachment = new Attachment();
		$attachmentArray = $license->getAttachments();

		if (is_array($attachmentArray) && count($attachmentArray) > 0) {

		?>

		<table class='verticalFormTable table-striped table-border'>
		<thead>
		<tr>
		<th scope="col"><?php echo _("Date");?></th>
		<th scope="col"><?php echo _("Details");?></th>
		<th scope="col">&nbsp;</th>
		<?php if ($user->canEdit()){ ?>
			<th scope="col"><span class="visually-hidden"><?php echo _("Edit");?></span></th>
		<?php } ?>
		</tr>
		</thead>
		<tbody>
			<?php


			foreach($attachmentArray as $attachment) {
				if (($attachment->sentDate == "0000-00-00") || ($attachment->sentDate == "")) {
					$sentDate='';
				}else{
					$sentDate=format_date($attachment->sentDate);
				}
				$attachmentText = nl2br($attachment->attachmentText);

				echo "<tr>";
				echo "<th scope='row'>" . $sentDate . "</th>";
				// TODO: a11y: change to <details>/<summary>?
				echo "<td><div id='attachment_short_" . $attachment->attachmentID . "'>" . substr($attachmentText, 0,200);

				if (strlen($attachmentText) > 200){
					echo "...&nbsp;&nbsp;<a href='javascript:showFullAttachmentText(\"" . $attachment->attachmentID . "\");'>"._("more...")."</a>";
				}

				echo "</div>";
				echo "<div id='attachment_full_" . $attachment->attachmentID . "' style='display:none'>" . $attachmentText;
					echo "&nbsp;&nbsp;<a href='javascript:hideFullAttachmentText(\"" . $attachment->attachmentID . "\");'>"._("less...")."</a>";
				echo "</div>";

				echo "</td>";

				$attachmentFileArray=$attachment->getAttachmentFiles();
				$attachmentFile = new AttachmentFile();

				echo "<td>";


				if (count($attachmentFileArray) == 0){
					echo _("(none uploaded)")."<br />";
				}

				$i=1;
				foreach($attachmentFileArray as $attachmentFile) {
					echo "<a href='attachments/" . $attachmentFile->attachmentURL . "' ". getTarget() .">"._("view attachment ") . $i . "</a><br />";
					$i++;
				}
				echo "</td>";

				if ($user->canEdit()){
				  echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAttachmentForm&licenseID=" . $licenseID . "&attachmentID=" . $attachment->attachmentID . "\",400, 350)' class='thickbox' id='editAttachment'><img id='Edit'  class='AdminEditIcon' src='images/edit.gif' title= '"._("Edit")."' /></a>&nbsp;&nbsp;<a href='javascript:deleteAttachment(\"". $attachment->attachmentID . "\");'><img id='Remove' class='AdminRemoveIcon' src='images/cross.gif' title= '"._("Remove")."' /></a></td>";
				}

				echo "</tr>";

			}
			?>
		</tbody>
		</table>
		<?php
		}else{
			echo _("(none found)");
		}

		if ($user->canEdit()){
		  echo "<p><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAttachmentForm&licenseID=" . $licenseID . "\",400, 350)' class='thickbox' id='attachment'>"._("add attachment")."</a></p>";

		}

		break;


	//number of attachments, used to display on the tab so user knows whether to look on tab
	case 'getAttachmentsNumber':
		$licenseID = $_GET['licenseID'];
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));

		echo count($license->getAttachments());

		break;

	//license search - used on index.php
	case 'getSearchLicenses':

		$pageStart = intval($_GET['pageStart']);
		$numberOfRecords = intval($_GET['numberOfRecords']);
		$whereAdd = array();

		//get where statements together

		//if the org module is installed where clause must go to the org database
		$config = new Configuration;
		if ($config->settings->organizationsModule == 'Y'){
			//searches against org name and aliases
			if ($_GET['shortName']) $whereAdd[] = "(UPPER(L.shortName) LIKE UPPER('%" .  str_replace("'","''",$_GET['shortName']) . "%') OR UPPER(D.shortName) LIKE  UPPER('%" .  str_replace("'","''",$_GET['shortName']) . "%') OR UPPER(O.name) LIKE  UPPER('%" .  str_replace("'","''",$_GET['shortName']) . "%') OR UPPER(A.name) LIKE  UPPER('%" .  str_replace("'","''",$_GET['shortName']) . "%'))";
		}else{
			if ($_GET['shortName']) $whereAdd[] = "(UPPER(L.shortName) LIKE UPPER('%" .  str_replace("'","''",$_GET['shortName']) . "%') OR UPPER(D.shortName) LIKE  UPPER('%" .  str_replace("'","''",$_GET['shortName']) . "%') OR UPPER(O.shortName) LIKE  UPPER('%" .  str_replace("'","''",$_GET['shortName']) . "%'))";
		}

		if ($_GET['organizationID']){
			$whereAdd[] = "O.organizationID = '" . $_GET['organizationID'] . "'";
		}

		$consortiumID = $_GET['consortiumID'];
		if ($consortiumID == "0") {
			$whereAdd[] = " L.consortiumID IS NULL ";
		}else{
			if ($consortiumID <> "") $whereAdd[] = " L.consortiumID = '" . $consortiumID . "'";
		}

		if ($_GET['statusID']) $whereAdd[] = "S.statusID = '" . $_GET['statusID'] . "'";
		if ($_GET['documentTypeID']) $whereAdd[] = "D.documentTypeID = '" . $_GET['documentTypeID'] . "'";

		if ($_GET['expressionTypeID']) $whereAdd[] = "E.expressionTypeID = '" . $_GET['expressionTypeID'] . "'";
		if ($_GET['qualifierID']) $whereAdd[] = "E.expressionID IN (SELECT expressionID FROM ExpressionQualifierProfile WHERE  qualifierID = '" . $_GET['qualifierID'] . "')";

		if ($_GET['startWith']) $whereAdd[] = "TRIM(LEADING 'THE ' FROM UPPER(L.shortName)) LIKE UPPER('" . $_GET['startWith'] . "%')";



		$orderBy = $_GET['orderBy'];

		//get total number of records to print out and calculate page selectors
		$totalLicenseObj = new License();

		$totalRecords = $totalLicenseObj->searchCount($whereAdd);

		//reset pagestart to 1 - happens when a new search is run but it kept the old page start
		if ($totalRecords <= $pageStart){
			$pageStart=1;
		}

		$limit = ($pageStart-1) . ", " . $numberOfRecords;

		$licenseObj = new License();
		$licenseArray = array();
		$licenseArray = $licenseObj->search($whereAdd, $orderBy, $limit);
    $pagination = '';
		if ($totalRecords == 0){
			echo "<p><i>"._("Sorry, no licenses fit your query")."</i></p>";
			$i=0;
		}else{
		  //maximum number of pages to display on screen at one time
			$maxDisplay = 25;

			$thisPageNum = count($licenseArray) + $pageStart - 1;
			echo "<h2>" . sprintf(_("Displaying %1\$d to %2\$d of %3\$d license records"), $pageStart, $thisPageNum, $totalRecords) . "</h2>";
			
			//print out page selectors
			if ($totalRecords > $numberOfRecords){
				echo "<nav class='pagination' id='pagination-div' aria-label='"._('Records per page')."'><ul>";
				if ($pageStart == "1"){
					$pagination .= "<li class='first' aria-hidden='true'><span class='smallText'><i class='fa fa-backward'></i></span></li>";
				}else{
					$pagination .= "<li class='first'><a href='javascript:setPageStart(1);' class='smallLink' aria-label='" . sprintf(_('First page, page %d'), $i ? $i : 1) . "'><i class='fa fa-backward'></i></a></li>";
				}
        $page = floor($pageStart/$numberOfRecords) + 1;
        //now determine the starting page - we will display 3 prior to the currently selected page
				if ($page > 3){
					$startDisplayPage = $page - 3;
				}else{
					$startDisplayPage = 1;
				}

				$maxPages = floor($totalRecords / $numberOfRecords) + 1;

				//now determine last page we will go to - can't be more than maxDisplay
				$lastDisplayPage = $startDisplayPage + $maxDisplay;
				if ($lastDisplayPage > $maxPages){
					$lastDisplayPage = ceil($maxPages);
				}

				for ($i=$startDisplayPage; $i<=$lastDisplayPage; $i++){

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
				echo $pagination;
				echo "</ul></nav>";
			} else {
				echo "<div id='pagination-empty-div'></div>";
			}


			?>
			<table class='dataTable table-border table-striped'>
			<thead>
			<tr>
			<th scope="col"><span class="sortable"><?php echo _("Name");?><span class='arrows'><a href='javascript:setOrder("L.shortName","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by name, ascending'); ?>'></a><a href='javascript:setOrder("L.shortName","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by name, descending'); ?>'></a></span></span></th>
			<th scope="col"><span class="sortable"><?php echo _("Publisher / Provider");?><span class='arrows'><a href='javascript:setOrder("providerName","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by publisher, ascending'); ?>'></a><a href='javascript:setOrder("providerName","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by publisher, descending'); ?>'></a></span></span></th>
			<th scope="col"><span class="sortable"><?php echo _("Consortium");?><span class='arrows'><a href='javascript:setOrder("C.shortName","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by consortium, ascending'); ?>'></a><a href='javascript:setOrder("C.shortName","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by consortium, descending'); ?>'></a></span></span></th>
			<th scope="col"><span class="sortable"><?php echo _("Status");?><span class='arrows'><a href='javascript:setOrder("S.shortName","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by status ascending'); ?>'></a><a href='javascript:setOrder("S.shortName","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by status, descending'); ?>'></a></span></span></th>
			</tr>
			</thead>	
			<tbody>
			<?php
			foreach ($licenseArray as $license){
				echo "<tr>";
				echo "<th scope='row'><a href='license.php?licenseID=" . $license['licenseID'] . "'>" . $license['licenseName'] . "</a></th>";
				echo "<td>" . $license['providerName'] . "</td>";
				echo "<td>" . $license['consortiumName'] . "</td>";
				echo "<td>" . $license['status'] . "</td>";
				echo "</tr>";
			}

			?>
			</tbody>
			</table>

			<p id="records-per-page">
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
			</p>
			
			<?php

			//set everything in sessions to make form "sticky"
			$_SESSION['license_pageStart'] = $_GET['pageStart'];
			$_SESSION['license_numberOfRecords'] = $_GET['numberOfRecords'];
			$_SESSION['license_shortName'] = $_GET['shortName'];
			$_SESSION['license_organizationID'] = $_GET['organizationID'];
			$_SESSION['license_consortiumID'] = $_GET['consortiumID'];
			$_SESSION['license_statusID'] = $_GET['statusID'];
			$_SESSION['license_documentTypeID'] = $_GET['documentTypeID'];
			$_SESSION['license_startWith'] = $_GET['startWith'];
			$_SESSION['license_orderBy'] = $_GET['orderBy'];
			$_SESSION['license_expressionTypeID'] = $_GET['expressionTypeID'];
			$_SESSION['license_qualifierID'] = $_GET['qualifierID'];
		}

		break;


	//used for in progress page.
	//note that in the License class the statuses are hard-coded
	case 'getInProgressLicenses':
		try {
			?>
				<table class='dataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("Name");?></th>
				<th scope="col"><?php echo _("Publisher / Provider");?></th>
				<th scope="col"><?php echo _("Consortium");?></th>
				<th scope="col"><?php echo _("Status");?></th>
				</tr>
				</thead>
				</tbody>
				<?php

				$license=new License();
				$licenseArray = array();

				foreach ($license->getInProgressLicenses() as $licenseArray){
					echo "<tr>";
					echo "<th scope='row'><a href='license.php?licenseID=" . $licenseArray['licenseID'] . "'>" . $licenseArray['licenseName'] . "</a></th>";
					echo "<td>" . $licenseArray['providerName'] . "</td>";
					echo "<td>" . $licenseArray['consortiumName'] . "</td>";
					echo "<td>" . $licenseArray['status'] . "</td>";
					echo "</tr>";
				}

				?>
				</table>

				<?php
			}catch(Exception $e){
				echo "<p class='error'>"._("There was an error processing this request - please verify configuration.ini is set up for organizations correctly and the database and tables have been created.")."</p>";
			}

		break;


	//used for expression comparison tool (compare.php)
	case 'getComparisonList':

		$expressionTypeID = $_GET['expressionTypeID'];

		//populate array with the expression types that we are to display
		//if expression type is passed in, only that one
		if ($expressionTypeID != "") {
			$expressionTypeArray[] = $expressionTypeID;
		}else{
			$et = new ExpressionType();
			foreach($et->allAsArray() as $expressionType){
				$expressionTypeArray[] = $expressionType['expressionTypeID'];
			}

		}


		foreach($expressionTypeArray as $expressionTypeID){ {
			$expressionType = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));
			$etArray = $expressionType->getComparisonList($_GET['qualifierID']);

			if (is_array($etArray) && count($etArray) > 0) {

				echo "<h3>" . $expressionType->shortName . "</h3>";

				foreach($etArray as $expressionTypeArray){
					echo "<div class='exp-comparison'>";
					echo "\n<h4>" . $expressionTypeArray['document'] . "</h4>";


					if ($user->canEdit()){
						 echo "\n<ul class='inline unstyled comparison-links'><li><a href='license.php?licenseID=" . $expressionTypeArray['licenseID'] . "' ". getTarget() .">"._("view / edit license")."</a></li>";
						 echo "<li><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getExpressionNotesForm&org=compare&expressionID=" . $expressionTypeArray['expressionID'] . "\", 350,605)' class='thickbox' id='ExpressionNotes'>".sprintf(_("view / edit %s notes"), strtolower($expressionType->noteType))."</a></li>";
						 echo "<li><a href='documents/" . $expressionTypeArray['documentURL'] . "' ". getTarget() .">"._("view document")."</a></li></ul>";

					}else{
						echo "\n<ul class='inline unstyled comparison-links'><li><a href='license.php?licenseID=" . $expressionTypeArray['licenseID'] . "' ". getTarget() .">"._("view license")."</a></li>";
						echo "<a href='documents/" . $expressionTypeArray['documentURL'] . "' ". getTarget() .">"._("view document")."</a></li></ul>";
					}

					echo "<div class='doc-text'>";

					if ($expressionTypeArray['documentText']){
						echo "<h5>"._("Document Text:")."</h5>" . nl2br($expressionTypeArray['documentText']);
					}


					$expr_notes = "<p><b>" . ucfirst($expressionTypeArray['noteType']) . _(" Notes:")."  </b></p>";

					$expression = new Expression(new NamedArguments(array('primaryKey' => $expressionTypeArray['expressionID'])));
					$expressionNotes = $expression->getExpressionNotes();

					if  (count($expressionNotes) > "0"){
						echo "<ul class='moved'>";

						foreach($expressionNotes as $expressionNote){
							$expr_notes .=  "<li>";
							$expr_notes .= $expressionNote->note;
							$expr_notes .=  "</li>";
						}

						$expr_notes .= "</ul>";

						echo $expr_notes;
					}

					if ($user->canEdit()){
						echo "";
					}



					if ($expressionTypeArray['qualifiers']){
						echo "<p><b>"._("Qualifiers:")."</b></p>  " . $expressionTypeArray['qualifiers'];
					}

					echo "</div>";

				#end expressions loop
				}

			}
			}

		#end expression type loop
		}


		echo "</div>";
		break;





	//used for terms tool report (terms_report.php)
	case 'getTermsReport':

		$expressionTypeID = $_GET['expressionTypeID'];

		//populate array with the expression types that we are to display
		//if expression type is passed in, only that one
		if ($expressionTypeID != "") {
			$expressionTypeArray[] = $expressionTypeID;
		}else{
			$et = new ExpressionType();
			foreach($et->allAsArray() as $expressionType){
				if ($expressionType['noteType'] == 'Display'){
					$expressionTypeArray[] = $expressionType['expressionTypeID'];
				}
			}

		}


		foreach($expressionTypeArray as $expressionTypeID){


			$expressionType = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));
			$etArray = $expressionType->getTermsReport();
			echo "<h3>" . $expressionType->shortName . "</h3>";
			echo "<table class='dataTable table-border table-striped'>";


			if (is_array($etArray) && count($etArray) > 0) {
				?>
				<thead>
				<tr>
				<th scope="col"><?php echo _("License");?></th>
				<th scope="col"><?php printf(_("%s Notes"), ucfirst($expressionType->noteType));?></th>
				<th scope="col"><?php echo _("Document Text");?></th>
				</tr>
				</thead>
				<tbody>
				<?php

				foreach($etArray as $expressionTypeArray){
					
					echo "\n<tr><td colspan='3'><b>" . $expressionTypeArray['document'] . "</b>  <a href='license.php?licenseID=" . $expressionTypeArray['licenseID'] . "'>"._("view license")."</a></td></tr>";

					if ($expressionTypeArray['documentText']){
						$documentText = $expressionTypeArray['documentText'];
					}else{
						$documentText = _("(document text not entered)");
					}

					echo "\n<tr>";
					echo "<td>&nbsp;</td>";

					echo "<td>";

					$expr_notes = ucfirst($expressionTypeArray['noteType']) . _(" Notes:")."  <ul class='moved'>";

					$expression = new Expression(new NamedArguments(array('primaryKey' => $expressionTypeArray['expressionID'])));
					$expressionNotes = $expression->getExpressionNotes();

					foreach($expressionNotes as $expressionNote){
						$expr_notes .=  "<li>";
						$expr_notes .= $expressionNote->note;
						$expr_notes .=  "</li>";
					}

					$expr_notes .= "</ul>";

					if  (count($expressionNotes) > "0"){
						echo $expr_notes;
					}

					echo "</td>";
					echo "<td>";
					// TODO: use <details>/<summary>?
					echo "<div id='text_short_" . $expressionTypeArray['expressionID'] . "'>" . substr($documentText, 0,200);

					if (strlen($documentText) > 200){
						echo "...&nbsp;&nbsp;<a href='javascript:showFullDocumentText(\"" . $expressionTypeArray['expressionID'] . "\");'>"._("more...")."</a>";
					}

					echo "</div>";
					echo "<div id='text_full_" . $expressionTypeArray['expressionID'] . "' style='display:none'>" . $documentText;
					echo "&nbsp;&nbsp;<a href='javascript:hideFullDocumentText(\"" . $expressionTypeArray['expressionID'] . "\");'>"._("less...")."</a>";
					echo "</div>";


					echo "</td>";
					echo "</tr>";

				#end expressions loop
				}

			#end numrows if
			}else{
				echo "<tr><td colspan='3'>"._("(none for ") . $expressionTypeArray['shortName'] . ")</td></tr>";
			}

			echo "</table>";





		#end expression type loop
		}


		echo "</table>";
		break;






	//display table for all documents for the license on license.php
	case 'getAllDocuments':

		$licenseID = $_GET['licenseID'];
		if (isset($_GET['displayArchiveInd'])) $displayArchiveInd = $_GET['displayArchiveInd']; else $displayArchiveInd = '';
		if (isset($_GET['parentOrderBy'])) $parentOrderBy = $_GET['parentOrderBy'];
		if (isset($_GET['childOrderBy'])) $childOrderBy = $_GET['childOrderBy'];
		if (isset($_GET['parentArchivedOrderBy'])) $parentArchivedOrderBy = $_GET['parentArchivedOrderBy'];
		if (isset($_GET['childArchivedOrderBy'])) $childArchivedOrderBy = $_GET['childArchivedOrderBy'];

		//used to turn on/off display of archived documents
		if ($displayArchiveInd == 'undefined') $displayArchiveInd='';

		//used to turn on/off display of specific child documents
		if (isset($_GET['showChildrenDocumentID'])) $showChildrenDocumentID = $_GET['showChildrenDocumentID']; else $showChildrenDocumentID = '';
		if ($showChildrenDocumentID == 'undefined')	$showChildrenDocumentID='';


		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
		$document = new Document();
		//display archive not sent in for unarchived docs
		if ($displayArchiveInd == ''){
			$documentArray = $license->getDocumentsWithoutParents($parentOrderBy);
			$chJSFunction = "setChildOrder";

			$isArchive='N';
		}else if ($displayArchiveInd == '1'){
			$documentArray = $license->getArchivedDocumentsWithoutParents($parentArchivedOrderBy);
			if (is_array($documentArray) && count($documentArray) > 0) {
				echo "<b>"._("Archived Documents")."</b>  <i><a href='javascript:updateArchivedDocuments(2)'>"._("hide archives")."</a></i>";
			}

			$chJSFunction = "setChildArchivedOrder";
			$childOrderBy = $childArchivedOrderBy;

			$isArchive='Y';
		}else{
			$documentArray = $license->getArchivedDocumentsWithoutParents($parentArchivedOrderBy);
			$jsFunction = "setParentArchivedOrder";
			$chJSFunction = "setChildArchivedOrder";
			$childOrderBy = $childArchivedOrderBy;

			$isArchive='Y';
		}



		$numRows = count($documentArray);

		if (($numRows > 0) && ($displayArchiveInd != '2')){

		?>

		<table class='verticalFormTable table-striped table-border'>
			<thead>
		<tr>

		<?php if ($isArchive == 'N') { ?>
		<th scope="col"><span class='sortable'><?php echo _("Name");?><span class='arrows'><a href='javascript:setParentOrder("D.shortName","asc");'><img src='images/arrowup<?php if ($parentOrderBy == 'D.shortName asc') echo "_sel"; ?>.png'></a>&nbsp;<a href='javascript:setParentOrder("D.shortName","desc");'><img src='images/arrowdown<?php if ($parentOrderBy == 'D.shortName desc') echo "_sel"; ?>.png'></a></span></span></th>
		<th scope="col"><span class='sortable'><?php echo _("Type");?><span class='arrows'><a href='javascript:setParentOrder("DT.shortName","asc");'><img src='images/arrowup<?php if ($parentOrderBy == 'DT.shortName asc') echo "_sel"; ?>.png'></a>&nbsp;<a href='javascript:setParentOrder("DT.shortName","desc");'><img src='images/arrowdown<?php if ($parentOrderBy == 'DT.shortName desc') echo "_sel"; ?>.png'></a></span></span></th>
		<th scope="col"><span class='sortable'><?php echo _("Effective Date");?><span class='arrows'><a href='javascript:setParentOrder("D.effectiveDate","asc");'><img src='images/arrowup<?php if ($parentOrderBy == 'D.effectiveDate asc') echo "_sel"; ?>.png'></a>&nbsp;<a href='javascript:setParentOrder("D.effectiveDate","desc");'><img src='images/arrowdown<?php if ($parentOrderBy == 'D.effectiveDate desc') echo "_sel"; ?>.png'></a></span></span></th>
		<th scope="col"><span class='sortable'><?php echo _("Signatures");?><span class='arrows'><a href='javascript:setParentOrder("min(signatureDate) asc, min(signerName)","asc");'><img src='images/arrowup<?php if ($parentOrderBy == 'min(signatureDate) asc, min(signerName) asc') echo "_sel"; ?>.png'></a>&nbsp;<a href='javascript:setParentOrder("max(signatureDate) desc, max(signerName)","desc");'><img src='images/arrowdown<?php if ($parentOrderBy == 'max(signatureDate) desc, max(signerName) desc') echo "_sel"; ?>.png'></a></span></span></th>
		<?php }else{ ?>
		<th scope="col"><span class='sortable'><?php echo _("Name");?><span class='arrows'><a href='javascript:setParentArchivedOrder("D.shortName","asc");'><img src='images/arrowup<?php if ($parentArchivedOrderBy == 'D.shortName asc') echo "_sel"; ?>.png'></a>&nbsp;<a href='javascript:setParentArchivedOrder("D.shortName","desc");'><img src='images/arrowdown<?php if ($parentArchivedOrderBy == 'D.shortName desc') echo "_sel"; ?>.png'></a></span></span></th>
		<th scope="col"><span class='sortable'><?php echo _("Type");?><span class='arrows'><a href='javascript:setParentArchivedOrder("DT.shortName","asc");'><img src='images/arrowup<?php if ($parentArchivedOrderBy == 'DT.shortName asc') echo "_sel"; ?>.png'></a>&nbsp;<a href='javascript:setParentArchivedOrder("DT.shortName","desc");'><img src='images/arrowdown<?php if ($parentArchivedOrderBy == 'DT.shortName desc') echo "_sel"; ?>.png'></a></span></span></th>
		<th scope="col"><span class='sortable'><?php echo _("Effective Date");?><span class='arrows'><a href='javascript:setParentArchivedOrder("D.effectiveDate","asc");'><img src='images/arrowup<?php if ($parentArchivedOrderBy == 'D.effectiveDate asc') echo "_sel"; ?>.png'></a>&nbsp;<a href='javascript:setParentArchivedOrder("D.effectiveDate","desc");'><img src='images/arrowdown<?php if ($parentArchivedOrderBy == 'D.effectiveDate desc') echo "_sel"; ?>.png'></a></span></span></th>
		<th scope="col"><span class='sortable'><?php echo _("Signatures");?><span class='arrows'><a href='javascript:setParentArchivedOrder("min(signatureDate) asc, min(signerName)","asc");'><img src='images/arrowup<?php if ($parentArchivedOrderBy == 'min(signatureDate) asc, min(signerName) asc') echo "_sel"; ?>.png'></a>&nbsp;<a href='javascript:setParentArchivedOrder("max(signatureDate) desc, max(signerName)","desc");'><img src='images/arrowdown<?php if ($parentArchivedOrderBy == 'max(signatureDate) desc, max(signerName) desc') echo "_sel"; ?>.png'></a></span></span></th>
		<?php } ?>
		<th scope="col"><?php echo _("Attachments");?></th>
<?php if ($user->canEdit()){ ?>
		<th scope="col"><?php echo _("Expiration");?></th>
		<th scope="col"><?php echo _("Actions");?></th>
		<?php } ?>
		</tr>
</thead>
<tbody>
			<?php

			$numrows=0;
			foreach($documentArray as $document) {

				$documentType = new DocumentType(new NamedArguments(array('primaryKey' => $document->documentTypeID)));

				//determine coloring of the row
				if(($document->expirationDate != "0000-00-00") && ($document->expirationDate != "")){
					//$classAdd="class='archive'";
					$classAdd="";
				}else if ((strtoupper($documentType->shortName) == 'AGREEMENT') || (strpos(strtoupper($documentType->shortName),'AGREEMENT'))){
					$classAdd="class='agreement'";
				}else{
					$classAdd="";
				}
				$numrows++;

				if (($document->effectiveDate == "0000-00-00") || ($document->effectiveDate == "")){
					$displayEffectiveDate = '';
				}else{
					$displayEffectiveDate = format_date($document->effectiveDate);
				}

				if (($document->expirationDate != "0000-00-00") && ($document->expirationDate != "")){
					$displayExpirationDate = sprintf(_("archived on: %s"), format_date($document->expirationDate));
				}else{
					$displayExpirationDate = '';
				}


				echo "<tr>";
				echo "<th $classAdd scope='row'>" . $document->shortName . "</th>";
				echo "<td $classAdd>" . $documentType->shortName . "</td>";
				echo "<td $classAdd>" . $displayEffectiveDate . "</td>";
				echo "<td $classAdd>";

				$signature = array();
				$signatureArray = $document->getSignaturesForDisplay();
				if (is_array($signatureArray) && count($signatureArray) > 0) {
					echo "<dl class='dl-grid'>";

					foreach($signatureArray as $signature) {

						if (($signature['signatureDate'] != '') && ($signature['signatureDate'] != "0000-00-00")) {
							$signatureDate = format_date($signature['signatureDate']);
						}else{
							$signatureDate=_("(no date)");
						}

						echo "<dt>" . $signature['signerName'] . "</dt>";
						echo "<dd>" . $signatureDate . "</dd>";

					}
					echo "</dl>";
					if ($user->canEdit()){
					  echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getSignatureForm&documentID=" . $document->documentID . "\", 580, 800)' class='thickbox' id='signatureForm'>"._("add/view details")."</a>";

					}


				}else{
					echo _("(none found)")."&nbsp";
					if ($user->canEdit()){
						echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getSignatureForm&documentID=" . $document->documentID . "\", 580, 800)' class='thickbox' id='signatureForm'><img class='SignatureAddIcon' src='images/plus.gif' title= '"._("Add signatures")."' /></a>";
					}
				}

				echo "</td>";

				echo "<td $classAdd>";
				if (!$user->isRestricted()) {
					if ($document->documentURL != ""){
						echo "<a href='documents/" . $document->documentURL . "' " . getTarget() . ">"._("view document")."</a><br />";
					}else{
						echo _("(none uploaded)")."<br />";
					}
				}

				if (is_array($document->getExpressions) && count($document->getExpressions) > 0) {
					echo "<a href='javascript:showExpressionForDocument(" . $document->documentID . ");'>"._("view expressions")."</a>";
				}

				echo "</td>";

				if ($user->canEdit()){
					echo "<td class='numeric'>" . $displayExpirationDate . "</td>";
					echo "<td class='actions icon $classAdd'><a href='javascript:void(0)'  onclick='javascript:myDialog(\"ajax_forms.php?action=getUploadDocument&licenseID=" . $licenseID . "&documentID=" . $document->documentID . "\", 295,350)' class='thickbox' id='editDocument'><img id='Edit'  src='images/edit.gif' title= '"._("Edit")."' /></a>";
					echo "<a href='javascript:deleteDocument(\"" . $document->documentID . "\");'><img id='Remove' src='images/cross.gif' title= '"._("Remove")."' /></a></td>";

				}
				echo "</tr>";

				$numberOfChildren = $document->getNumberOfChildren();
				if ($numberOfChildren > 0) {
					//if display for this child is turned off
					if ((($showChildrenDocumentID) && ($showChildrenDocumentID != $document->documentID)) || !($showChildrenDocumentID)) {
						if ($displayArchiveInd == '1') {
							echo "<tr><td colspan='6'><i>".sprintf(_("This document has %d children document(s) not displayed."), $numberOfChildren)."  <a href='javascript:updateArchivedDocuments(\"\"," . $document->documentID . ")'>"._("show all documents for this parent")."</a></i></td></tr>";
						}else{
							echo "<tr><td colspan='6'><i>".sprintf(_("This document has %d children document(s) not displayed."), $numberOfChildren)."  <a href='javascript:updateDocuments(" . $document->documentID . ")'>"._("show all documents for this parent")."</a></i></td></tr>";
						}
					}else{
						if ($displayArchiveInd == '1') {
							echo "<tr><td colspan='6'><i>".sprintf(_("The following %d document(s) belong to %s."), $numberOfChildren, $document->shortName) . "  <a href='javascript:updateArchivedDocuments(\"\",\"\")'>"._("hide children documents for this parent")."</a></i></td></tr>";
						}else{
							echo "<tr><td colspan='6'><i>".sprintf(_("The following %d document(s) belong to %s."),$numberOfChildren, $document->shortName) . "  <a href='javascript:updateDocuments(\"\")'>"._("hide children documents for this parent")."</a></i></td></tr>";
						}

						?>
						<thead>
						<tr>
						<?php if ($isArchive == 'N') { ?>
						<th scope="col"><span class='sortable'><?php echo _("Name");?><span class='arrows'><a href='javascript:setChildOrder("D.shortName","asc");'><img src='images/arrowup<?php if ($childOrderBy == 'D.shortName asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildOrder("D.shortName","desc");'><img src='images/arrowdown<?php if ($childOrderBy == 'D.shortName desc') echo "_sel"; ?>.gif'></a></span></span></th>
						<th scope="col"><span class='sortable'><?php echo _("Type");?><span class='arrows'><a href='javascript:setChildOrder("DT.shortName","asc");'><img src='images/arrowup<?php if ($childOrderBy == 'DT.shortName asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildOrder("DT.shortName","desc");'><img src='images/arrowdown<?php if ($childOrderBy == 'DT.shortName desc') echo "_sel"; ?>.gif'></a></span></span></th>
						<th scope="col"><span class='sortable'><?php echo _("Effective Date");?><span class='arrows'><a href='javascript:setChildOrder("D.effectiveDate","asc");'><img src='images/arrowup<?php if ($childOrderBy == 'D.effectiveDate asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildOrder("D.effectiveDate","desc");'><img src='images/arrowdown<?php if ($childOrderBy == 'D.effectiveDate desc') echo "_sel"; ?>.gif'></a></span></span></th>
						<th scope="col"><span class='sortable'><?php echo _("Signatures");?><span class='arrows'><a href='javascript:setChildOrder("min(signatureDate) asc, min(signerName)","asc");'><img src='images/arrowup<?php if ($childOrderBy == 'min(signatureDate) asc, min(signerName) asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildOrder("max(signatureDate) desc, max(signerName)","desc");'><img src='images/arrowdown<?php if ($childOrderBy == 'max(signatureDate) desc, max(signerName) desc') echo "_sel"; ?>.gif'></a></span></span></th>
						<?php }else{ ?>
						<th scope="col"><span class='sortable'><?php echo _("Name");?><span class='arrows'><a href='javascript:setChildArchivedOrder("D.shortName","asc");'><img src='images/arrowup<?php if ($childArchivedOrderBy == 'D.shortName asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildArchivedOrder("D.shortName","desc");'><img src='images/arrowdown<?php if ($childArchivedOrderBy == 'D.shortName desc') echo "_sel"; ?>.gif'></a></span></span></th>
						<th scope="col"><span class='sortable'><?php echo _("Type");?><span class='arrows'><a href='javascript:setChildArchivedOrder("DT.shortName","asc");'><img src='images/arrowup<?php if ($childArchivedOrderBy == 'DT.shortName asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildArchivedOrder("DT.shortName","desc");'><img src='images/arrowdown<?php if ($childArchivedOrderBy == 'DT.shortName desc') echo "_sel"; ?>.gif'></a></span></span></th>
						<th scope="col"><span class='sortable'><?php echo _("Effective Date");?><span class='arrows'><a href='javascript:setChildArchivedOrder("D.effectiveDate","asc");'><img src='images/arrowup<?php if ($childArchivedOrderBy == 'D.effectiveDate asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildArchivedOrder("D.effectiveDate","desc");'><img src='images/arrowdown<?php if ($childArchivedOrderBy == 'D.effectiveDate desc') echo "_sel"; ?>.gif'></a></span></span></th>
						<th scope="col"><span class='sortable'><?php echo _("Signatures");?><span class='arrows'><a href='javascript:setChildArchivedOrder("min(signatureDate) asc, min(signerName)","asc");'><img src='images/arrowup<?php if ($childArchivedOrderBy == 'min(signatureDate) asc, min(signerName) asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildArchivedOrder("max(signatureDate) desc, max(signerName)","desc");'><img src='images/arrowdown<?php if ($childArchivedOrderBy == 'max(signatureDate) desc, max(signerName) desc') echo "_sel"; ?>.gif'></a></span></span></th>
						<?php } ?>
						<th scope="col"><?php echo _("Attachments");?></th>
						<?php if ($user->canEdit()){ ?>
							<th scope="col"><span class="visually-hidden"><?php echo _("Edit");?></span></th>
						<?php } ?>
						</tr>
						</thead>
						<tbody>

						<?php
						$childrenDocumentArray = $document->getChildrenDocuments($childOrderBy);
						foreach($childrenDocumentArray as $childDocument) {

							$documentType = new DocumentType(new NamedArguments(array('primaryKey' => $childDocument->documentTypeID)));


							if (($childDocument->effectiveDate == "0000-00-00") || ($childDocument->effectiveDate == "")){
								$displayEffectiveDate = '';
							}else{
								$displayEffectiveDate = format_date($childDocument->effectiveDate);
							}

							if ((($childDocument->expirationDate == "0000-00-00") || ($childDocument->expirationDate == "")) && ($user->canEdit())){
								$displayExpirationDate = "<a href='javascript:archiveDocument(" . $childDocument->documentID . ");'>"._("archive document")."</a>";
							}else{
								$displayExpirationDate = sprintf(_("archived on: %s"), format_date($childDocument->expirationDate));
							}


							echo "<tr>";
							echo "<th scope='row'>" . $childDocument->shortName . "</th>";
							echo "<td>" . $documentType->shortName . "</td>";
							echo "<td>" . $displayEffectiveDate . "</td>";
							echo "<td>";

							$signature = array();
							$signatureArray = $childDocument->getSignaturesForDisplay();
							// TODO: eliminate nested tables
							if (is_array($signatureArray) && count($signatureArray) > 0) {
								echo "<table class='noBorderTable'>";


								foreach($signatureArray as $signature) {
									if (($signature['signatureDate'] != '') && ($signature['signatureDate'] != "0000-00-00")) {
										$signatureDate = format_date($signature['signatureDate']);
									}else{
										$signatureDate=_("(no date)");
									}

									echo "<tr>";
									echo "<td $classAdd>" . $signature['signerName'] . "</td>";
									echo "<td $classAdd>" . $signatureDate . "</td>";
									echo "</tr>";

								}
								echo "</table>";
								if ($user->canEdit()){
									echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getSignatureForm&height=270&width=460&modal=true&documentID=" . $childDocument->documentID . "\",300,500)' class='thickbox' id='signatureForm'>"._("add/view details")."</a>";
								}


							}else{
								echo "<p>". _("(none found)")."</p>";
								if ($user->canEdit()){
									echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getSignatureForm&height=170&width=460&modal=true&documentID=" . $childDocument->documentID . "\"200,500)' class='thickbox' id='signatureForm'>"._("add signatures")."</a>";
								}
							}

							echo "</td>";

							echo "<td $classAdd>";
							if (!$user->isRestricted) {
								if ($childDocument->documentURL != ""){
									echo "<a href='documents/" . $childDocument->documentURL . "' " . getTarget() . ">"._("view document")."</a><br />";
								}else{
									echo _("(none uploaded)")."<br />";
								}							}

							if (is_array($childDocument->getExpressions) && count($childDocument->getExpressions) > 0) {
								echo "<a href='javascript:showExpressionForDocument(" . $childDocument->documentID . ");'>"._("view expressions")."</a>";
							}

							echo "</td>";

							if ($user->canEdit()){
								echo "<td $classAdd><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getUploadDocument&height=285&width=305&modal=true&licenseID=" . $licenseID . "&documentID=" . $childDocument->documentID . "\",320,350)' class='thickbox' id='editDocument'>"._("edit document")."</a><br /><a href='javascript:deleteDocument(\"" . $childDocument->documentID . "\");'>"._("remove document")."</a>";
								//echo "<br />" . $displayExpirationDate . "</td>";
							}
							echo "</tr>";

							$numberOfChildren = $childDocument->getNumberOfChildren;

							if ($numberOfChildren > 0){
								if ($displayArchiveInd == '1') {
									echo "<tr><td colspan='6'><i>"._("The following ") . $numberOfChildren . _(" document(s) belong to ") . $childDocument->shortName . ".</i></td></tr>";
								}else{
									echo "<tr><td colspan='6'><i>"._("The following ") . $numberOfChildren . _(" document(s) belong to ") . $childDocument->shortName . ".</i></td></tr>";
								}
							}

						//end loop over child document records
						}

						echo "<tr><td colspan='6'>&nbsp;</td></tr>";
					//end display child if
					}
				//end number of children if
				}


			//end loop over document records
			}
			?>
		</tbody>
		</table>

		<?php
		}else{
			if ($displayArchiveInd == ""){
				echo _("(none found)");
			}else if (($displayArchiveInd == "1") || ($numRows == "0")){
				//echo "(no archived documents found)";
			}else{
				echo "<i>" . $numRows . _(" archive(s) available.")."  <a href='javascript:updateArchivedDocuments(1)'>"._("show archives")."</a></i><br /><br />";
			}
		}


		if (($user->canEdit()) && ($displayArchiveInd != "")){
			echo "<a href='javascript:void(0)' id='uploadDocument' onclick='javascript:myDialog(\"ajax_forms.php?action=getUploadDocument&licenseID=" . $licenseID ."\",280,800)'>"._("upload new document")."</a>";
		}


		break;





	//display for expressions tab on license.php
	case 'getAllExpressions':

		$licenseID = $_GET['licenseID'];
		$documentID = $_GET['documentID'];



		//if 'view expressions' link is clicked on a specific document, we're just displaying expressions for that document
		//otherwise we're displaying expressions for all un-archived documents
		if ($documentID != ''){
			$document = new Document(new NamedArguments(array('primaryKey' => $documentID)));
			$documentArray = $document->getDocumentsForExpressionDisplay();
		}else{
			$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
			$documentArray = $license->getAllDocumentsForExpressionDisplay();
		}

		$util = new Utility();



		$numRows = count($documentArray);

		if ($numRows > 0){
			$documentObj = new Document();

			//documents are the outside loop, then we find expressions for each document in the loop
			foreach($documentArray as $documentObj) {

			?>

				<b><?php echo _("For Document:");?>  </b><?php echo $documentObj->shortName; ?>

				<table class='verticalFormTable table-striped table-border'>
					<thead>
				<tr>
				<th scope="col"><?php echo _("Type");?></th>
				<th scope="col"><?php echo _("Document Text");?></th>
				<th scope="col"><?php echo _("Qualifier");?></th>
				<?php if ($user->canEdit()){ ?>
					<th scope="col"><span class="visually-hidden"><?php echo _("Type");?></span></th>
				<?php } ?>
				</tr>
				</thead>
				<tbody>
				<?php



				$expressionArray = $documentObj->getExpressionsForDisplay();
				$i = 1;
				foreach($expressionArray as $expressionIns) {
					$expression = new Expression(new NamedArguments(array('primaryKey' => $expressionIns['expressionID'])));


					//get qualifiers set up for this expression
					$sanitizedInstance = array();
					$instance = new Qualifier();
					$qualifierArray = array();
					foreach ($expression->getQualifiers() as $instance) {
						$qualifierArray[]=$instance->shortName;
					}
					?>
					<tr>
					<td class='alt'>
						<!-- TODO: eliminate nested tables -->
						<table class='noBorderTable'>
						<tr>
						<td class='alt'><label for="<?php echo "productionUseInd_" . $expressionIns['expressionID']; ?>"><?php echo $expressionIns['expressionTypeName']; ?></label>
						<?php
						//if not configured to use the terms tool, hide the production use in terms tool checkbox/display
						if ((strtoupper($expressionIns['noteType']) == 'DISPLAY') && ($util->useTermsTool())){
							if ($user->isAdmin()) {
								// TODO: eliminate tables and inline styles
								if ($expressionIns['productionUseInd'] == "1"){
									echo "</td><td class='alt' style='float: right;text-align:right;'><input type='checkbox' id='productionUseInd_" . $expressionIns['expressionID'] . "' name='productionUseInd_" . $expressionIns['expressionID'] . "' onclick='javascript:changeProdUse(" . $expressionIns['expressionID'] . ")' checked></td>";
								}else{
									echo "</td><td class='alt' style='float: right;text-align:right;'><input type='checkbox' id='productionUseInd_" . $expressionIns['expressionID'] . "' name='productionUseInd_" . $expressionIns['expressionID'] . "' onclick='javascript:changeProdUse(" . $expressionIns['expressionID'] . ")'></td>";
								}
							}else{
								if ($expressionIns['productionUseInd'] == "1"){
									echo "<br /><br /><i>"._("used in terms tool")."</i></td>";
								}
							}

						}
						?>
						</tr>
						</table>
						<span id='span_prod_use_<?php echo $expressionIns['expressionID']; ?>' class='error'></span>
					</td>

					<?php
					echo "<td class='alt'>" . nl2br($expressionIns['documentText']) . "</td>";
					echo "<td class='alt'>";

					if (is_array($qualifierArray) && count($qualifierArray) > 0) {
						echo implode("<br />", $qualifierArray);
					}

					echo "</td>";

					if ($user->canEdit()){
      						echo "<td class='alt actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getExpressionForm&licenseID=" . $licenseID . "&expressionID=" . $expressionIns['expressionID'] . "\",420,375)' class='thickbox'><img id='Edit' src='images/edit.gif' title= '"._("Edit")."' /></a>&nbsp;&nbsp;<a href='javascript:deleteExpression(" . $expressionIns['expressionID'] . ");'><img id='Remove' class='removeIcon' src='images/cross.gif' title= '"._("Remove")."' /></a></td>";
					}

					echo "</tr>";

					if ($user->canEdit()){
						echo "<tr><td class='alt'>&nbsp;</td><td colspan='4' class='alt'>" . ucfirst($expressionIns['noteType']) . _(" Notes:")."  <ul class='moved'>";
					}else{
						echo "<tr><td class='alt'>&nbsp;</td><td colspan='2' class='alt'>" . ucfirst($expressionIns['noteType']) . _(" Notes:")."  <ul class='moved'>";
					}

					$expressionNoteArray = $expression->getExpressionNotes();

					$rowcount=0;
					foreach($expressionNoteArray as $expressionNoteObj) {
						echo "<li>";
						echo nl2br($expressionNoteObj->note);
						echo "</li>";
						$rowcount++;
					}

					if  ($rowcount == "0"){ echo _("(none)"); }

					echo "</ul>";

					//link to view/edit display notes
					if ($user->canEdit()){
 echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getExpressionNotesForm&expressionID=" . $expressionIns['expressionID'] . "\", 330,605)' class='thickbox' id='ExpressionNotes'>"._("add/view ") . lcfirst($expressionIns['noteType']) . _(" notes")."</a>";

					}
					echo "</td>";
					echo "</tr>";
					if ($user->canEdit()){
						echo "<tr><td colspan='4'>&nbsp;</td></tr>";
					}else{
						echo "<tr><td colspan='3'>&nbsp;</td></tr>";
					}


				}
				?>
			</tbody>
			</table>

			<?php
			}

		}else{
			echo _("(none found)");
		}

		if ($user->canEdit()){
		 	echo "<br /><br /><a href='javascript:void(0)' id='expression' onclick='javascript:myDialog(\"ajax_forms.php?action=getExpressionForm&licenseID=" . $licenseID ."\",420,375)'>"._("add expression")."</a>";
		}







		break;




	//generic admin data (lookup table) display - all tables have ID and shortName so we can simplify retrieving this data
	case 'getAdminList':
		$className = $_GET['tableName'];
		$instance = new $className();

		$resultArray = $instance->allAsArray();

		if (is_array($resultArray) && count($resultArray) > 0) {
			?>
			<table class='dataTable table-border table-striped'>
				<?php
				$i = 0;
				foreach($resultArray as $result){
					$i++;
					echo "<tr>";
					echo "<th scope='col' id='shortName-".$i."'>" . $result['shortName'] . "</th>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUpdateForm&tableName=" . $className . "&updateID=" . $result[lcfirst($className) . 'ID'] . "\",225,450)' class='thickbox' aria-label='".sprintf(_("Edit %s"),  $result['shortName'])."'><img src='images/edit.gif' /></a>";
					echo "<a href='javascript:deleteData(\"" . $className . "\",\"" . $result[lcfirst($className) . 'ID'] . "\")' aria-label='".sprintf(_("Remove %s"),  $result['shortName'])."'><img src='images/cross.gif' /></a></td>";
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)");
		}

		break;


	//display user info for admin screen
	case 'getAdminUserList':

		$instanceArray = array();
		$user = new User();
		$tempArray = array();
		$util = new Utility();

		if (is_array($user->allAsArray()) && count($user->allAsArray()) > 0) {

			?>
			<table class='dataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("Login ID");?></th>
				<th scope="col"><?php echo _("First Name");?></th>
				<th scope="col"><?php echo _("Last Name");?></th>
				<th scope="col"><?php echo _("Privilege");?>

				</th>
				<?php
				//if not configured to use terms tool, hide the Terms Tool Update Email
				if ($util->useTermsTool()){
					echo "<th scope='col'>"._("Terms Tool Update Email")."</th>";
				}
				?>
				<th scope="col"><?php echo _("Actions");?>
				</tr>
				</thead>
				</tbody>
				<?php

				foreach($user->allAsArray() as $instance) {
					$privilege = new Privilege(new NamedArguments(array('primaryKey' => $instance['privilegeID'])));
					
					echo "<tr>";
					echo "<th scope='row'>" . $instance['loginID'] . "</th>";
					echo "<td>" . $instance['firstName'] . "</td>";
					echo "<td>" . $instance['lastName'] . "</td>";
					echo "<td>" . $privilege->shortName . "</td>";
					//if not configured to use SFX, hide the Terms Tool Update Email
					if ($util->useTermsTool()){
						echo "<td>" . $instance['emailAddressForTermsTool'] . "</td>";
					}
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm&loginID=" . $instance['loginID'] . "\", 225,600)' class='thickbox' aria-label='".sprintf(_("edit %s %s"), $instance['firstName'], $instance['lastName'])."'><img src='images/edit.gif' /></a>";
					echo "<a href='javascript:deleteUser(\"" . $instance['loginID'] . "\")' aria-label='".sprintf(_("remove %s %s"), $instance['firstName'], $instance['lastName'])."'><img src='images/cross.gif' /></a></td>";
					echo "</tr>";
				}

				?>
			</tbody>
			</table>
			<?php

		}else{
			echo _("(none found)");
		}

		break;






	//display expression type list for admin screen - needs its own display because of note type
	case 'getExpressionTypeList':

		$instanceArray = array();
		$expressionType = new ExpressionType();
		$tempArray = array();

		foreach ($expressionType->allAsArray() as $tempArray) {
			array_push($instanceArray, $tempArray);
		}



		if (is_array($instanceArray) && count($instanceArray) > 0) {

			?>
			<table class='dataTable table-border table-striped'>
			<thead>
				<tr>
				<th scope="col"><?php echo _("Expression Type");?></th>
				<th scope="col"><?php echo _("Note Type");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
			</tr>
			</thead>
			</tbody>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<th scope='row'>" . $instance['shortName'] . "</th>";
					echo "<td>" . $instance['noteType'] . "</td>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getExpressionTypeForm&expressionTypeID=" . $instance['expressionTypeID'] . "\", 225,350)' class='thickbox' aria-label= '".sprintf(_("Edit %s"), $instance['shortName'])."'><img src='images/edit.gif' /></a>";
					echo "<a href='javascript:deleteExpressionType(\"" . $instance['expressionTypeID'] . "\")' aria-label= '".sprintf(_("Edit %s"), $instance['shortName'])."'><img src='images/cross.gif' /></a></td>";
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)");
		}

		break;

	//display expression type list for admin screen - needs its own display because of note type
	case 'getCalendarSettingsList':

		$instanceArray = array();
		$calendarSettings = new CalendarSettings();
		$tempArray = array();

		foreach ($calendarSettings->allAsArray() as $tempArray) {
			array_push($instanceArray, $tempArray);
		}

		if (is_array($instanceArray) && count($instanceArray) > 0) {

			?>
			<table class='dataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("Setting");?></th>
				<th scope="col"><?php echo _("Value");?></th>
				<th scope="col">&nbsp;</th>
				</tr>
				</thead>
				</tbody>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<th scope='row'>" . $instance['shortName'] . "</th>";
					echo "<td>";
						if (strtolower($instance['shortName']) == strtolower('Authorized Site(s)')) {
							$display = array();
							$authorizedSite = new AuthorizedSite();
							$siteCount = 0;
                            $authorizedSitesArray = $authorizedSite->getAllAuthorizedSite();
                            if ($authorizedSitesArray['authorizedSiteID']) {
                                $authorizedSitesArray = array($authorizedSitesArray);
                            }

								foreach($authorizedSitesArray as $display) {
									if (in_array($display['authorizedSiteID'], explode(",", $instance['value']))) {
										if ($siteCount > 0) {
											echo ", ";
										}
										echo $display['shortName'];
										$siteCount = $siteCount + 1;
									}
								}
						} elseif (strtolower($instance['shortName']) == strtolower('Resource Type(s)')) {
							$display = array();
							$resourceType = new ResourceType();
							$siteCount = 0;
								foreach($resourceType->getAllResourceType() as $display) {

								if(isset($display['resourceTypeID'])){
									if (in_array($display['resourceTypeID'], explode(",", $instance['value']))) {
										if ($siteCount > 0) {
											echo ", ";
										}
										echo $display['shortName'];
										$siteCount = $siteCount + 1;
									}
								}

								}
						} else {
							echo $instance['value'];
						}
					echo "</td>";


					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getCalendarSettingsForm&calendarSettingsID=" . $instance['calendarSettingsID'] . "\",225, 375)' class='thickbox' aria-label= '".sprintf(_("Edit %s"), $instance['shortName'])."'><img src='images/edit.gif' /></a></td>";
					echo "</tr>";
				}

				?>
				</tbody>
			</table>
			<?php

		}else{
			echo _("(none found)");
		}

		break;

  case 'getInProgressStatuses':
    $config = new Configuration();
    $statuses = $config->settings->inProgressStatuses ?? null;
    if ($statuses) {
      $output = explode(',', $statuses);
      if (is_array($output)) {
        echo '<ul class="list-unstyled">';
        foreach($output as $v) {
          echo sprintf('<li>%s</li>', $v);
        }
        echo '<ul>';
      }
    }
    break;
		
    case 'getTermsToolSettings':
        $config = new Configuration();
        $output = array('Resolver' => $config->terms->resolver);
        switch($config->terms->resolver) {
            case 'SFX':
                $output['Open URL'] = $config->terms->open_url;
                $output['SID'] = $config->terms->sid;
                break;
            case 'SerialsSolutions':
                $output['Client ID'] = $config->terms->client_identifier;
                break;
            case 'EBSCO':
                $output['Customer ID'] = $config->terms->client_identifier;
                $output['Api Key'] = $config->terms->sid;
                break;
            default:
                break;
        }
        echo '<table class="dataTable table-striped table-border">';
				/*
				echo '<thead><tr>';
				foreach(array_keys($output) as $header) {
					echo sprintf('<th scope="col">%s</th>', _($header));
				}
				echo '</tr></thead><tbody>';
				/**/
        foreach($output as $k => $v) {
            echo sprintf('<tr><th scope="row">%s</th><td>%s</td></tr>', _($k), $v);
        }
        echo '<table>';
        break;


	//display qualifier list for admin screen - needs its own display because of expression type
	case 'getQualifierList':



		$expressionType = new ExpressionType();


		?>
		<table class='dataTable table-striped table-border'>
			<thead>
			<tr>
			<th scope="col"><?php echo _("For Expression Type");?></th>
			<th scope="col"><?php echo _("Qualifier");?></th>
			<th scope="col"><?php echo _("Actions");?></th>
			</tr>
			</thead>
			<tbody>
			<?php

			foreach($expressionType->all() as $expressionTypeObj) {
				$i = 0; //counter to display expression type first time only
				foreach ($expressionTypeObj->getQualifiers() as $qualifier){
					if ($i == 0) $displayET = $expressionTypeObj->shortName; else $displayET = '&nbsp;';
					echo "<tr>";
					echo "<th scope='row'>" . $displayET . "</th>";
					echo "<td>" . $qualifier->shortName . "</td>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getQualifierForm&qualifierID=" . $qualifier->qualifierID . "\",225,350)' class='thickbox' aria-label= '".sprintf(_("Edit %s"), $qualifier->shortName)."'><img src='images/edit.gif' /></a>";
					echo "<a href='javascript:deleteQualifier(\"" . $qualifier->qualifierID . "\")' aria-label= '".sprintf(_("Remove %s"), $qualifier->shortName)."'><img src='images/cross.gif' /></td>";
					echo "</tr>";
					$i++;
				}

			}

			?>
			</tbody>
		</table>
		<?php

		break;



	//display qualifier dropdown - for the search (index.php)
	case 'getQualifierDropdownHTML':


		if (isset($_GET['expressionTypeID'])){
			$selectedValue = '';
			$reset = '';

			if (isset($_GET['page'])){

				if(isset($_SESSION['license_qualifierID'])){
					$selectedValue = $_SESSION['license_qualifierID'];
				}


				$reset = $_GET['reset'];
			}

			$expressionTypeID = $_GET['expressionTypeID'];
			$expressionType = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));

			$qualifierArray = array();

			$qualifierArray = $expressionType->getQualifiers();

			if (count($qualifierArray) > 0 ) {
				if (!isset($_GET['page'])) echo "<label for='qualifierID'>"._("Limit by Qualifier:")."</label>";
			?>
				<select name='qualifierID' id='qualifierID' onchange='javsacript:updateSearch();'>
				<option value='' <?php if ((!$selectedValue) || ($reset == 'Y')) echo "selected"; ?> ></option>
				<?php

				foreach($qualifierArray as $qualifier) {
					if (($selectedValue == $qualifier->qualifierID) && ($reset != 'Y')){
						echo "<option value='" . $qualifier->qualifierID . "' selected>" . $qualifier->shortName . "</option>\n";
					}else{
						echo "<option value='" . $qualifier->qualifierID . "'>" . $qualifier->shortName . "</option>\n";
					}

				}

				?>
				</select>

			<?php
			}

		}

		break;









	//display qualifier dropdown - for the expression form
	case 'getQualifierCheckboxHTML':

		if (isset($_GET['expressionTypeID'])){
			$expressionTypeID = $_GET['expressionTypeID'];
			$expressionType = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));

			$qualifierArray = array();

			$qualifierArray = $expressionType->getQualifiers();

			// TODO: eliminate nested tables
			$i=0;
			if (is_array($qualifierArray) && count($qualifierArray) > 0) {
				echo "<table class='table-striped table-border'>";
				//loop over all qualifiers available for this expression type
				foreach ($qualifierArray as $expressionQualifierIns){
					$i++;
					if(($i % 2)==1){
						echo "<tr>\n";
					}
					echo "<td><label><input class='check_Qualifiers' type='checkbox' name='" . $expressionQualifierIns->qualifierID . "' id='" . $expressionQualifierIns->qualifierID . "' value='" . $expressionQualifierIns->qualifierID . "' />   " . $expressionQualifierIns->shortName . "</label></td>\n";

					if(($i % 2)==0){
						echo "</tr>\n";
					}
				}

				if(($i % 2)==1){
					echo "<td>&nbsp;</td></tr>\n";
				}

				echo "</table>";
			}


		}

		break;

	case 'getRightPanel':
		$licenseID = $_GET['licenseID'];
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
		$config = new Configuration;
		//get resources (already returned in array)
		$resourceArray = $license->getResourceArray();
		$resourcesExist = count($resourceArray) > 0;
		$resourcesModuleExists = $config->settings->resourcesModule == 'Y';
		$feedbackEmail = $config->settings->feedbackEmailAddress;
		$feedbackEmailExists = $feedbackEmail != '';
		if(($resourcesExist && $resourcesModuleExists) || $feedbackEmailExists){
			?>
			<aside id="links" class="helpfulLinks">
				<div id='div_fullRightPanel' class='rightPanel'>
					<h3 id="side-menu-title"><?php echo _("Helpful Links"); ?></h3>
					<?php if($resourcesExist) { 
						//First we need to de-deduplicate the resources Array. It can be duplicated if there are multiple orders of a single resource.
						$deDupedList = [];
						foreach($resourceArray as $resource){$deDupedList[$resource['resourceID']] = $resource['resource'];}
						?>
						<h4><?php echo _("Resources Module");?></h4>
						<ul class="unstyled">
							<?php foreach($deDupedList as $id=>$resourceName){
								$url = $util->getResourceURL();
								$target = getTarget();
								echo "<li><a href='{$url}{$id}' {$target} class='helpfulLink'>{$resourceName}</a></li>";
							} ?>
						</ul>
					<?php } ?>
					<?php if($resourcesExist && $feedbackEmailExists){echo "<hr>";} ?>
					<?php if($feedbackEmailExists) { ?>
						<p>
							<?php 
								echo "<a href='mailto:{$feedbackEmail}?subject={$license->shortName} (License ID: {$licenseID})' class='helpfulLink'>"; 
									echo _("Send feedback on this license");
								echo "</a>";
							?>
						</p>
					<?php } ?>
				</div>
			</aside>


			<?php 
		}

		break;

	default:
			if (empty($action))
            return;
       printf(_("Action %s not set up!"), $action);
       break;


}



?>
