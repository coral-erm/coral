<?php

/*
**************************************************************************************************************************
** CORAL Management Module v. 1.0
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
//		$consortium = new Consortium(new NamedArguments(array('primaryKey' => $license->consortiumID)));
		?>
<!-- TODO: eliminate tables? -->
		<table class="headerTable">
		<tr><th scope="col">
		<?php echo $license->shortName; ?>

		<?php

		if ($user->canEdit()){?>
			<a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getLicenseForm&licenseID=<?php echo $licenseID; ?>&height=350&width=300&modal=true",350,300)' class='thickbox'><?php echo _("edit");?></a>  |  <a href='javascript:deleteLicense("<?php echo $licenseID; ?>");'><?php echo _("remove");?></a>
		<?php }

		echo "<div style='margin-bottom:8px;'>";

		//make sure they have org module installed before we give them a link to view the organization
		$config = new Configuration;

		if ($config->settings->organizationsModule == 'Y') {
			$util = new Utility();

			echo "<div>" . _("Description: ") . $license->description() . "</div>";
			echo _("Categories:") . "<br />";

			if ($licenseconsortiumids = $license->getConsortiumsByLicense()) {
				echo '<ul>';
				foreach ($licenseconsortiumids as $cid) {
					echo "<li>{$license->getConsortiumName($cid)}</li>";
				}
				echo '</ul>';
			} elseif ($license->consortiumID) {
				echo "<br />" . $license->getConsortiumName();
			}
		}else{
			echo "<div>" . _("Description: ") . $license->description() . "</div>";
			echo _("Categories:") . "<br />";
			if ($licenseconsortiumids = $license->getConsortiumsByLicense()) {
				echo '<ul>';
				foreach ($licenseconsortiumids as $cid) {
					echo "<li>{$license->getConsortiumName($cid)}</li>";
				}
				echo '</ul>';
			} elseif ($license->consortiumID) {
				echo "<br />" . $license->getConsortiumName();
			}
		}
//		echo "Category:  " . $license->getConsortiumName();
		echo "<br />" . _("Creation Date: ") . format_date($license->createDate())." ({$license->createLoginID})";
		echo "<br />" . _("Last Update: ") . format_date($license->statusDate())." ({$license->statusLoginID})";

		?>
		</div>
		</th>
		<td class='end'>
		<?php if ($user->canEdit() && 1 == 2){ // supress ?>
			<label for="statusID"><?php echo _("License Status:");?></label><br />
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
			<br />
			<span class='error' id='span_updateStatusResponse' name='span_updateStatusResponse'></span>
		<?php } ?>
		<br />
		</td></tr>
		</table>

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
				<!-- TODO: eliminate tables? -->
						<table class='verticalFormTable table-border table-striped'>
						<thead>
						<tr>
						<th><?php echo _("For Document");?></th>
						<th><?php echo _("Resource");?></th>
						<th><?php echo _("Actions");?></th>
						</tr>
						</thead>
						<tbody>
				<?php
				}
				echo "<tr>";
				echo "<td>" . $document->shortName . "</td>";
				echo "<td>" . $sfxProvider->shortName . "</td>";
				echo "<td class='actions'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getSFXForm&height=178&width=260&modal=true&licenseID=" . $licenseID . "&providerID=" . $sfxProvider->sfxProviderID . "\",200,300)' class='thickbox' id='editSFXProvider'>" . _("edit") . "</a>";
				echo "<a href='javascript:deleteSFXProvider(\"" . $sfxProvider->sfxProviderID . "\");'>" . _("remove") . "</a></td>";
				echo "</tr>";
			}


		//end loop over sfx provider records
		}
		?>
		</tbody>
		</table>

		<?php
		if ($rowCount == "0"){
			echo _("(none found)");

		}

		if ($user->canEdit()){
			echo "<p><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getSFXForm&licenseID=" . $licenseID . "&height=178&width=260&modal=true\",200,300)' class='thickbox' id='addSFXResource'>" . _("add new terms tool resource link") . "</a></p>";
		}

		break;


	case 'getAllNotes':
		$licenseID = $_GET['licenseID'];
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));
		$notes = $license->getNotes();
?>
		<h4><?php echo _("Notes");?></h4>
<?php
		if ($user->canEdit()){
			echo "<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getNoteForm&licenseID=" . $licenseID . "&height=380&width=305&modal=true\",400,310)' class='thickbox' id='note'>" . _("add note") . "</a><br /><br />";
		}
		if (is_array($notes) && count($notes) > 0){
			$documentNoteTypes = new DocumentNoteType(new NamedArguments(array('primaryKeyName'=>'documentNoteTypeID')));
			$notetypes = $documentNoteTypes->allAsIndexedArray();
			$documents = $license->getAllDocumentNamesAsIndexedArray();
		?>
		<table class='verticalFormTable table-border table-striped'>
		<tr>
		<th scope="col"><?php echo _("Date");?></th>
		<th scope="col"><?php echo _("Note");?></th>
		<th scope="col"><?php echo _("Document");?></th>
		<th scope="col"><?php echo _("Note Type");?></th>
		<?php if ($user->canEdit()){ ?>
			<th class="actions"><?php echo _("Actions");?></th>
		<?php } ?>
		</tr>

			<?php


			foreach($notes as $note) {
				if (($note->createDate == "0000-00-00") || ($note->createDate == "")) {
					$createDate='';
				}else{
					$createDate=format_date($note->createDate);
				}
				$noteText = nl2br($note->body);

				echo "<tr>";
				echo "<td>" . $createDate . "</td>";
				echo "<td><div id='note_short_" . $note->noteID . "'>" . substr($noteText, 0,200);

				if (strlen($noteText) > 200){
					echo "...&nbsp;&nbsp;<a href='javascript:showFullNoteText(\"" . $note->noteID . "\");'>" . _("more") . "...</a>";
				}

				echo "</div>";
				echo "<div id='note_full_" . $note->noteID . "' style='display:none'>" . $noteText;
					echo "&nbsp;&nbsp;<a href='javascript:hideFullNoteText(\"" . $note->noteID . "\");'>" . _("less") . "...</a>";
				echo "</div>";
				echo "</td>
					  <td>{$documents[$note->documentID]['shortName']}</td>
					  <td>{$notetypes[$note->documentNoteTypeID]['shortName']}</td>";


				if ($user->canEdit()){
					echo "<td class='actions'><div class='addIconTab'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getNoteForm&height=398&width=305&modal=true&licenseID=" . $licenseID . "&documentNoteID=" . $note->documentNoteID . "\",400,310)' class='thickbox' id='editNote'><img src='images/edit.gif' title= '"._("Edit")."' /></a>&nbsp;&nbsp;&nbsp;<a href='javascript:deleteNote(\"". $note->documentNoteID . "\");'><img id='Remove' src='images/cross.gif' title= '"._("Remove")."' /></a></div></td>";
				}

				echo "</tr>";

			}
			?>

		</table>
		<?php
		}else{
			echo _("(none found)");
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
		<!-- TODO: a11y: eliminate tables -->
		<table class='verticalFormTable table-border table-striped'>
		<thead>
		<tr>
		<th scope="col"><?php echo _("Date");?></th>
		<th scope="col"><?php echo _("Details");?></th>
		<th>&nbsp;</th>
		<?php if ($user->canEdit()){ ?>
			<th class="actions"><?php echo _("Actions");?></th>
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
				echo "<td>" . $sentDate . "</td>";
				echo "<th scope='row'><div id='attachment_short_" . $attachment->attachmentID . "'>" . substr($attachmentText, 0,200);

				if (strlen($attachmentText) > 200){
					echo "...&nbsp;&nbsp;<a href='javascript:showFullAttachmentText(\"" . $attachment->attachmentID . "\");'>" . _("more") . "...</a>";
				}

				echo "</div>";
				echo "<div id='attachment_full_" . $attachment->attachmentID . "' style='display:none'>" . $attachmentText;
					echo "&nbsp;&nbsp;<a href='javascript:hideFullAttachmentText(\"" . $attachment->attachmentID . "\");'>" . _("less") . "...</a>";
				echo "</div>";

				echo "</th>";

				$attachmentFileArray=$attachment->getAttachmentFiles();
				$attachmentFile = new AttachmentFile();

				echo "<td>";


				if (count($attachmentFileArray) == 0){
					echo _("(none uploaded)") . "<br />";
				}

				$i=1;
				foreach($attachmentFileArray as $attachmentFile) {
					echo "<a href='attachments/" . $attachmentFile->attachmentURL . "' ". getTarget() .">" . _("view attachment ") . $i . "</a><br />";
					$i++;
				}
				echo "</td>";

				if ($user->canEdit()){
					echo "<td class='actions'><div class='addIconTab'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getAttachmentForm&height=398&width=305&modal=true&licenseID=" . $licenseID . "&attachmentID=" . $attachment->attachmentID . "\",400,310)' class='thickbox' id='editAttachment'><img src='images/edit.gif' title= '"._("Edit")."' /></a>&emsp;<a href='javascript:deleteAttachment(\"". $attachment->attachmentID . "\");'><img  src='images/cross.gif' title= '"._("Remove")."' /></a></div></td>";
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
			echo "<p><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getAttachmentForm&licenseID=" . $licenseID . "&height=380&width=305&modal=true\",400,310)' class='thickbox' id='attachment'>" . _("add attachment") . "</a></p>";
		}

		break;


	//number of attachments, used to display on the tab so user knows whether to look on tab
	case 'getAttachmentsNumber':
		$licenseID = $_GET['licenseID'];
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));

		echo count($license->getAttachments());

		break;

	case 'getNotesNumber':
		$licenseID = $_GET['licenseID'];
		$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));

		echo $license->getNotesCount();

		break;
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
//			if ($consortiumID <> "") $whereAdd[] = " L.consortiumID = '" . $consortiumID . "'";
			if ($consortiumID <> "") {
				$whereAdd[] = " lc.`consortiumID`={$consortiumID}";
			}
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
			echo "<p><i>" . _("Sorry, no documents fit your query") . "</i></p>";
			$i=0;
		}else{
		  //maximum number of pages to display on screen at one time
			$maxDisplay = 25;

			$thisPageNum = count($licenseArray) + $pageStart - 1;
			echo "<h2 class='display-title'>" . sprintf(_("Displaying %1\$d to %2\$d of %3\$d records"), $pageStart, $thisPageNum, $totalRecords) . "</h2>";
			echo "<nav class='pagination' aria-label='"._('Records per page')."'><ul>";

			//print out page selectors
			if ($totalRecords > $numberOfRecords){
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
						$pagination .= "<li><a href='javascript:setPageStart(" . $nextPageStarts  .");' aria-label='" . sprintf(_('Page %d'), $i) . "' class='smallLink'>" . $i . "</a></li>";
					}
				}

				if ($pageStart == $nextPageStarts){
					$pagination .= "<li class='last' aria-hidden='true'><span class='smallText'><i class='fa fa-forward'></i></span></li>";
				}else{
					$pagination .= "<li class='last'><a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink' aria-label='" . sprintf(_('Last page, page %d'), $i - 1) . "'><i class='fa fa-forward'></i></a></li>";
				}
				$pagination .= "</ul></nav>";
				echo $pagination;
			} else 
			if ($consortiumID) {
				echo "<h3><b>" . _("Category") . "</b>: {$licenseObj->getConsortiumName($consortiumID)}</h3>";
			}
			?>
			<table class='dataTable table-border table-striped'>
			<thead>
			<tr>
			<th scope="col"><span class='sortable'><?php echo _("Name");?><span class='arrows'><a href='javascript:setOrder("L.shortName","asc");' aria-label='<?php echo _('Sort by name, ascending'); ?>'><img src='images/arrowup.png'></a><a href='javascript:setOrder("L.shortName","desc");' aria-label='<?php echo _('Sort by name, descending'); ?>'><img src='images/arrowdown.png'></a></span></span></th>
			<th scope="col"><span class='sortable'><?php echo _("Type");?><span class='arrows'><a href='javascript:setOrder("DT.shortName","asc");'aria-label='<?php echo _('Sort by type, ascending'); ?>'><img src='images/arrowup.png'></a><a href='javascript:setOrder("DT.shortName","desc");'aria-label='<?php echo _('Sort by type, descending'); ?>'><img src='images/arrowdown.png'></a></span></span></th>
			<th scope="col"><span class='sortable'><?php echo _("Last Document Revision");?><span class='arrows'><a href='javascript:setOrder("D.revisionDate","asc");' aria-label='<?php echo _('Sort by revision date, ascending'); ?>'><img src='images/arrowup.png'></a><a href='javascript:setOrder("D.revisionDate","desc");' aria-label='<?php echo _('Sort by revision date, descending'); ?>'><img src='images/arrowdown.png'></a></span></span></th>
			</tr>
			</thead>
			<tbody>
			<?php

			foreach ($licenseArray as $license){
				echo "<tr>";
				echo "<th scope='row'><a href='license.php?licenseID=" . $license['licenseID'] . "'>" . $license['licenseName'] . "</a></th>";
				echo "<td>" . $license['Type'] . "</td>";
				echo "<td>" . $license['revisionDate'] . "</td>";
				echo "</tr>";
			}

			?>
			</tbody>
			</table>

			
			<?php
			//print out page selectors
			if ($pagination){
				echo $pagination;
			}
			?>
			
			<select id='numberOfRecords' name='numberOfRecords' onchange='javascript:setNumberOfRecords();' style='width:50px;'>
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
				<tbody>
				<?php

				$i=0;
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
				</tbody>
				</table>

				<?php
			}catch(Exception $e){
				echo "<p class='error'>" . _("There was an error processing this request - please verify configuration.ini is set up for organizations correctly and the database and tables have been created.") . "</p>";
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

				// TODO: remove inline styles below
				foreach($etArray as $expressionTypeArray){

					echo "<div style='margin-top:10px;margin-bottom:20px;padding:5px; border-width:1px;border-color: #e0dfe3;border-style: solid;'>";
					echo "\n<table class='noBorder' style='width:100%;'><tr><td style='text-align:left;width:450px;'><span style='font-weight:bold;font-size:110%;text-align:left;'>" . $expressionTypeArray['document'] . "</span></td>";


					if ($user->canEdit()){
						echo "\n<td style='text-align:right;width:350px;'><a href='license.php?licenseID=" . $expressionTypeArray['licenseID'] . "' ". getTarget() .">" . _("view / edit license") . "</a>&nbsp;&nbsp;<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getExpressionNotesForm&height=330&width=440&modal=true&org=compare&expressionID=" . $expressionTypeArray['expressionID'] . "\",330,440)' class='thickbox' id='ExpressionNotes'>" . _("view / edit ") . strtolower($expressionType->noteType) . _(" notes") . "</a>&nbsp;&nbsp;<a href='documents/" . $expressionTypeArray['documentURL'] . "' ". getTarget() .">" . _("view document") . "</a></td></tr></table>";
					}else{
						echo "\n<td style='text-align:right;'><a href='license.php?licenseID=" . $expressionTypeArray['licenseID'] . "' ". getTarget() .">" . _("view license") . "</a>&nbsp;&nbsp;<a href='documents/" . $expressionTypeArray['documentURL'] . "' ". getTarget() .">" . _("view document") . "</a></td></tr></table>";
					}

					echo "<div style='margin-left:15px; margin-top:3px;'>";

					if ($expressionTypeArray['documentText']){
						echo "<b>" . _("Document Text:") . "</b> <br />" . nl2br($expressionTypeArray['documentText']) . "<br />";
					}


					$expr_notes = "<br /><b>" . ucfirst($expressionTypeArray['noteType']) . _(" Notes:  ") . "</b>";

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
						echo "<br /><b>" . _("Qualifiers:") . "</b><br />  " . $expressionTypeArray['qualifiers'];
					}

					echo "</div>";
					echo "</div>";

				#end expressions loop
				}

			}
			}

		#end expression type loop
		}


		echo "</table>";
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
// TODO: a11y: eliminate tables
			echo "<br /><h3>" . $expressionType->shortName . "</h3>";
			echo "<table class='dataTable table-border table-striped'>";


			if (is_array($etArray) && count($etArray) > 0) {
				?>

				<tr>
				<th style='width:50px;'><?php echo _("License");?></th>
				<th style='width:300px;'><?php echo ucfirst($expressionType->noteType) . _(" Notes");?></th>
				<th style='width:255px;'><?php echo _("Document Text");?></th>
				</tr>

				<?php

				foreach($etArray as $expressionTypeArray){

					echo "\n<tr><td colspan='3'><span style='font-weight:bold'>" . $expressionTypeArray['document'] . "</span>  <a href='license.php?licenseID=" . $expressionTypeArray['licenseID'] . "'>" . _("view license") . "</a></td></tr>";

					if ($expressionTypeArray['documentText']){
						$documentText = $expressionTypeArray['documentText'];
					}else{
						$documentText = _("(document text not entered)");
					}

					echo "\n<tr>";
					echo "<td>&nbsp;</td>";

					echo "<td>";

					$expr_notes = ucfirst($expressionTypeArray['noteType']) . _(" Notes:") . "  <ul class='moved'>";

					$expression = new Expression(new NamedArguments(array('primaryKey' => $expressionTypeArray['expressionID'])));
					$expressionNotes = $expression->getExpressionNotes();

					foreach($expressionNotes as $expressionNote){
						$expr_notes .=  "<li>";
						$expr_notes .= $expressionNote->note;
						$expr_notes .=  "</li>";
					}

					$expr_notes .= "</ul><br />";

					if  (count($expressionNotes) > "0"){
						echo $expr_notes;
					}

					echo "</td>";
					echo "<td>";

					echo "<div id='text_short_" . $expressionTypeArray['expressionID'] . "'>" . substr($documentText, 0,200);

					if (strlen($documentText) > 200){
						echo "...&nbsp;&nbsp;<a href='javascript:showFullDocumentText(\"" . $expressionTypeArray['expressionID'] . "\");'>" . _("more") . "...</a>";
					}

					echo "</div>";
					echo "<div id='text_full_" . $expressionTypeArray['expressionID'] . "' style='display:none'>" . $documentText;
					echo "&nbsp;&nbsp;<a href='javascript:hideFullDocumentText(\"" . $expressionTypeArray['expressionID'] . "\");'>" . _("less") . "...</a>";
					echo "</div>";


					echo "</td>";
					echo "</tr>";

				#end expressions loop
				}

			#end numrows if
			}else{
				echo "<tr><td colspan='3'>(" . _("none for ") . $expressionTypeArray['shortName'] . ")</td></tr>";
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
				echo "<b>" . _("Archived Documents") . "</b>  <i><a href='javascript:updateArchivedDocuments(2)'>" . _("hide archives") . "</a></i>";
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


		$numDocuments = count($documentArray);
		$numRows = count($documentArray);

		if (($numRows > 0) && ($displayArchiveInd != '2')){

		?>
		<table class='verticalFormTable table-border table-striped'>
		<thead>
		<tr>
		<?php if ($isArchive == 'N') { ?>
		<th scope="col"><span class='sortable'><?php echo _("Name");?><span class='arrows'><a href='javascript:setParentOrder("D.shortName","asc");' aria-label='<?php echo _('Sort by name, ascending'); ?>'><img src='images/arrowup<?php if ($parentOrderBy == 'D.shortName asc') echo "_sel"; ?>.gif'></a> <a href='javascript:setParentOrder("D.shortName","desc");' aria-label='<?php echo _('Sort by name, descending'); ?>'><img src='images/arrowdown<?php if ($parentOrderBy == 'D.shortName desc') echo "_sel"; ?>.gif'></a></span></span></th>
		<th scope="col"><span class='sortable'><?php echo _("Type");?><span class='arrows'><a href='javascript:setParentOrder("DT.shortName","asc");' aria-label='<?php echo _('Sort by type, ascending'); ?>'><img src='images/arrowup<?php if ($parentOrderBy == 'DT.shortName asc') echo "_sel"; ?>.gif'></a> <a href='javascript:setParentOrder("DT.shortName","desc");'aria-label='<?php echo _('Sort by type, descending'); ?>'><img src='images/arrowdown<?php if ($parentOrderBy == 'DT.shortName desc') echo "_sel"; ?>.gif'></a></span></span></th>
		<th scope="col"><span class='sortable'><?php echo _("Last Document Revision");?><span class='arrows'><a href='javascript:setParentOrder("D.effectiveDate","asc");' aria-label='<?php echo _('Sort by date, ascending'); ?>'><img src='images/arrowup<?php if ($parentOrderBy == 'D.effectiveDate asc') echo "_sel"; ?>.gif'></a> <a href='javascript:setParentOrder("D.effectiveDate","desc");' aria-label='<?php echo _('Sort by date, descending'); ?>'><img src='images/arrowdown<?php if ($parentOrderBy == 'D.effectiveDate desc') echo "_sel"; ?>.gif'></a></span></span></th>
<!--		<th style='width:180px;'><table class='noBorderTable'><tr><td style='background-color: #e5ebef'>Signatures</td><td class='arrow' style='background-color: #e5ebef'><a href='javascript:setParentOrder("min(signatureDate) asc, min(signerName)","asc");'><img src='images/arrowup<?php if ($parentOrderBy == 'min(signatureDate) asc, min(signerName) asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setParentOrder("max(signatureDate) desc, max(signerName)","desc");'><img src='images/arrowdown<?php if ($parentOrderBy == 'max(signatureDate) desc, max(signerName) desc') echo "_sel"; ?>.gif'></a></td></tr></table></th> -->
		<?php }else{ ?>
		<th scope="col"><span class='sortable'><?php echo _("Name");?><span class='arrows'><a href='javascript:setParentArchivedOrder("D.shortName","asc");' aria-label='<?php echo _('Sort by name, ascending'); ?>'><img src='images/arrowup<?php if ($parentArchivedOrderBy == 'D.shortName asc') echo "_sel"; ?>.gif'></a> <a href='javascript:setParentArchivedOrder("D.shortName","desc");' aria-label='<?php echo _('Sort by name, descending'); ?>'><img src='images/arrowdown<?php if ($parentArchivedOrderBy == 'D.shortName desc') echo "_sel"; ?>.gif'></a></span></span></th>
		<th scope="col"><span class='sortable'><?php echo _("Type");?><span class='arrows'><a href='javascript:setParentArchivedOrder("DT.shortName","asc");' aria-label='<?php echo _('Sort by type, ascending'); ?>'><img src='images/arrowup<?php if ($parentArchivedOrderBy == 'DT.shortName asc') echo "_sel"; ?>.gif'></a> <a href='javascript:setParentArchivedOrder("DT.shortName","desc");' aria-label='<?php echo _('Sort by type, descending'); ?>'><img src='images/arrowdown<?php if ($parentArchivedOrderBy == 'DT.shortName desc') echo "_sel"; ?>.gif'></a></span></span></th>
		<th scope="col"><span class='sortable'><?php echo _("Last Document Revision");?><span class='arrows'><a href='javascript:setParentArchivedOrder("D.effectiveDate","asc");' aria-label='<?php echo _('Sort by date, ascending'); ?>'><img src='images/arrowup<?php if ($parentArchivedOrderBy == 'D.effectiveDate asc') echo "_sel"; ?>.gif'></a> <a href='javascript:setParentArchivedOrder("D.effectiveDate","desc");' aria-label='<?php echo _('Sort by date, descending'); ?>'><img src='images/arrowdown<?php if ($parentArchivedOrderBy == 'D.effectiveDate desc') echo "_sel"; ?>.gif'></a></span></span></th>
<!--		<th style='width:180px;'><table class='noBorderTable'><tr><td style='background-color: #e5ebef'>Signatures</td><td class='arrow' style='background-color: #e5ebef'><a href='javascript:setParentArchivedOrder("min(signatureDate) asc, min(signerName)","asc");'><img src='images/arrowup<?php if ($parentArchivedOrderBy == 'min(signatureDate) asc, min(signerName) asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setParentArchivedOrder("max(signatureDate) desc, max(signerName)","desc");'><img src='images/arrowdown<?php if ($parentArchivedOrderBy == 'max(signatureDate) desc, max(signerName) desc') echo "_sel"; ?>.gif'></a></td></tr></table></th> -->
		<?php } ?>


		<th>&nbsp;</th>
		<?php if ($user->canEdit()){ ?>
		<th scope="col"><?php echo _('Actions'); ?></th>
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
/*
				if (($document->effectiveDate == "0000-00-00") || ($document->effectiveDate == "")){
					$displayEffectiveDate = '';
				}else{
					$displayEffectiveDate = format_date($document->effectiveDate);
				}
*/
				if (($document->revisionDate == "0000-00-00") || ($document->revisionDate == "")){
					$displayRevisionDate = '';
				}else{
					$displayRevisionDate = format_date($document->revisionDate);
				}

				if (($document->expirationDate != "0000-00-00") && ($document->expirationDate != "")){
					$displayExpirationDate = _("archived on: ") . format_date($document->expirationDate);
				}else{
					$displayExpirationDate = '';
				}


				echo "<tr>";
				echo "<td $classAdd>" . $document->shortName . "</td>";
				echo "<td $classAdd>" . $documentType->shortName . "</td>";
				echo "<td $classAdd>" . $displayRevisionDate . "</td>";
//				echo "<td $classAdd>";
//
//				$signature = array();
//				$signatureArray = $document->getSignaturesForDisplay();
//
//				if (is_array($signatureArray) && count($signatureArray) > 0) {
//					echo "<table class='noBorderTable'>";
//
//					foreach($signatureArray as $signature) {
//
//						if (($signature['signatureDate'] != '') && ($signature['signatureDate'] != "0000-00-00")) {
//							$signatureDate = format_date($signature['signatureDate']);
//						}else{
//							$signatureDate='(no date)';
//						}

//						echo "<tr>";
//						echo "<td $classAdd>" . $signature['signerName'] . "</td>";
//						echo "<td $classAdd>" . $signatureDate . "</td>";
//						echo "</tr>";
//
//					}
//					echo "</table>";
//					if ($user->canEdit()){
//						echo "<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getSignatureForm&height=270&width=460&modal=true&documentID=" . $document->documentID . "\",300,460)' class='thickbox' id='signatureForm'>add/view details</a>";
//					}
//

//				}else{
//					echo "(none found)<br />";
//					if ($user->canEdit()){
//						echo "<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getSignatureForm&height=170&width=460&modal=true&documentID=" . $document->documentID . "\",200,460)' class='thickbox' id='signatureForm'>add signatures</a>";
//					}
//				}
//
//				echo "</td>";

				echo "<td $classAdd>";
				if (!$user->isRestricted()) {
					if ($document->documentURL != ""){
						echo "<a href='documents/" . $document->documentURL . "' " . getTarget() . ">" . _("view document") . "</a><br />";
					}else{
						echo _("(none uploaded)") . "<br />";
					}
				}

				if (is_array($document->getExpressions) && count($document->getExpressions) > 0) {
					echo "<a href='javascript:showExpressionForDocument(" . $document->documentID . ");'>" . _("view expressions") . "</a>";
				}

				echo "</td>";

				if ($user->canEdit()){
					echo "<td $classAdd><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getUploadDocument&height=295&width=317&modal=true&licenseID=" . $licenseID . "&documentID=" . $document->documentID . "\",300,320)' class='thickbox' id='editDocument'><div class='addIconTab'><img id='Edit' src='images/edit.gif' title= '"._("Edit")."' /></a> &nbsp <a href='javascript:deleteDocument(\"" . $document->documentID . "\");'><img id='Remove' class='removeIcon' src='images/cross.gif' title= '"._("Remove")."' /></div></a>";
					echo "<br />" . $displayExpirationDate . "</td>";
				}
				echo "</tr>";

				$numberOfChildren = $document->getNumberOfChildren();
				if ($numberOfChildren > 0) {
					//if display for this child is turned off
					if ((($showChildrenDocumentID) && ($showChildrenDocumentID != $document->documentID)) || !($showChildrenDocumentID)) {
						if ($displayArchiveInd == '1') {
							echo "<tr><td colspan='6'><i>" . _("This document has ") . $numberOfChildren . _(" children document(s) not displayed.  ") . "<a href='javascript:updateArchivedDocuments(\"\"," . $document->documentID . ")'>" . _("show all documents for this parent") . "</a></i></td></tr>";
						}else{
							echo "<tr><td colspan='6'><i>" . _("This document has ") . $numberOfChildren . _(" children document(s) not displayed.  ") . "<a href='javascript:updateDocuments(" . $document->documentID . ")'>" . _("show all documents for this parent") . "</a></i></td></tr>";
						}
					}else{
						if ($displayArchiveInd == '1') {
							echo "<tr><td colspan='6'><i>" . _("The following ") . $numberOfChildren . _(" document(s) belong to ") . $document->shortName . ".  <a href='javascript:updateArchivedDocuments(\"\",\"\")'>" . _("hide children documents for this parent") . "</a></i></td></tr>";
						}else{
							echo "<tr><td colspan='6'><i>" . _("The following ") . $numberOfChildren . _(" document(s) belong to ") . $document->shortName . ".  <a href='javascript:updateDocuments(\"\")'>" . _("hide children documents for this parent") . "</a></i></td></tr>";
						}

						?>
						<tr>
						<?php if ($isArchive == 'N') { ?>
						<th><table class='noBorderTable'><tr><td style='background-color: #e5ebef'><?php echo _("Name");?></td><td class='arrow' style='background-color: #e5ebef'><a href='javascript:setChildOrder("D.shortName","asc");'><img src='images/arrowup<?php if ($childOrderBy == 'D.shortName asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildOrder("D.shortName","desc");'><img src='images/arrowdown<?php if ($childOrderBy == 'D.shortName desc') echo "_sel"; ?>.gif'></a></td></tr></table></th>
						<th><table class='noBorderTable'><tr><td style='background-color: #e5ebef'><?php echo _("Type");?></td><td class='arrow' style='background-color: #e5ebef'><a href='javascript:setChildOrder("DT.shortName","asc");'><img src='images/arrowup<?php if ($childOrderBy == 'DT.shortName asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildOrder("DT.shortName","desc");'><img src='images/arrowdown<?php if ($childOrderBy == 'DT.shortName desc') echo "_sel"; ?>.gif'></a></td></tr></table></th>
						<th style='width:120px;'><table class='noBorderTable'><tr><td style='background-color: #e5ebef'><?php echo _("Effective Date");?></td><td class='arrow' style='background-color: #e5ebef'><a href='javascript:setChildOrder("D.effectiveDate","asc");'><img src='images/arrowup<?php if ($childOrderBy == 'D.effectiveDate asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildOrder("D.effectiveDate","desc");'><img src='images/arrowdown<?php if ($childOrderBy == 'D.effectiveDate desc') echo "_sel"; ?>.gif'></a></td></tr></table></th>
						<th style='width:180px;'><table class='noBorderTable'><tr><td style='background-color: #e5ebef'><?php echo _("Signatures");?></td><td class='arrow' style='background-color: #e5ebef'><a href='javascript:setChildOrder("min(signatureDate) asc, min(signerName)","asc");'><img src='images/arrowup<?php if ($childOrderBy == 'min(signatureDate) asc, min(signerName) asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildOrder("max(signatureDate) desc, max(signerName)","desc");'><img src='images/arrowdown<?php if ($childOrderBy == 'max(signatureDate) desc, max(signerName) desc') echo "_sel"; ?>.gif'></a></td></tr></table></th>
						<?php }else{ ?>
						<th><table class='noBorderTable'><tr><td style='background-color: #e5ebef'><?php echo _("Name");?></td><td class='arrow' style='background-color: #e5ebef'><a href='javascript:setChildArchivedOrder("D.shortName","asc");'><img src='images/arrowup<?php if ($childArchivedOrderBy == 'D.shortName asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildArchivedOrder("D.shortName","desc");'><img src='images/arrowdown<?php if ($childArchivedOrderBy == 'D.shortName desc') echo "_sel"; ?>.gif'></a></td></tr></table></th>
						<th><table class='noBorderTable'><tr><td style='background-color: #e5ebef'><?php echo _("Type");?></td><td class='arrow' style='background-color: #e5ebef'><a href='javascript:setChildArchivedOrder("DT.shortName","asc");'><img src='images/arrowup<?php if ($childArchivedOrderBy == 'DT.shortName asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildArchivedOrder("DT.shortName","desc");'><img src='images/arrowdown<?php if ($childArchivedOrderBy == 'DT.shortName desc') echo "_sel"; ?>.gif'></a></td></tr></table></th>
						<th style='width:120px;'><table class='noBorderTable'><tr><td style='background-color: #e5ebef'><?php echo _("Effective Date");?></td><td class='arrow' style='background-color: #e5ebef'><a href='javascript:setChildArchivedOrder("D.effectiveDate","asc");'><img src='images/arrowup<?php if ($childArchivedOrderBy == 'D.effectiveDate asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildArchivedOrder("D.effectiveDate","desc");'><img src='images/arrowdown<?php if ($childArchivedOrderBy == 'D.effectiveDate desc') echo "_sel"; ?>.gif'></a></td></tr></table></th>
						<th style='width:180px;'><table class='noBorderTable'><tr><td style='background-color: #e5ebef'><?php echo _("Signatures");?></td><td class='arrow' style='background-color: #e5ebef'><a href='javascript:setChildArchivedOrder("min(signatureDate) asc, min(signerName)","asc");'><img src='images/arrowup<?php if ($childArchivedOrderBy == 'min(signatureDate) asc, min(signerName) asc') echo "_sel"; ?>.gif'></a>&nbsp;<a href='javascript:setChildArchivedOrder("max(signatureDate) desc, max(signerName)","desc");'><img src='images/arrowdown<?php if ($childArchivedOrderBy == 'max(signatureDate) desc, max(signerName) desc') echo "_sel"; ?>.gif'></a></td></tr></table></th>
						<?php } ?>
						<th>&nbsp;</th>
						<?php if ($user->canEdit()){ ?>
						<th><?php echo _('Actions'); ?></th>
						<?php } ?>
						</tr>

						<?php
						$childrenDocumentArray = $document->getChildrenDocuments($childOrderBy);
						$classAdd='';
						foreach($childrenDocumentArray as $childDocument) {

							$documentType = new DocumentType(new NamedArguments(array('primaryKey' => $childDocument->documentTypeID)));


							if (($childDocument->effectiveDate == "0000-00-00") || ($childDocument->effectiveDate == "")){
								$displayEffectiveDate = '';
							}else{
								$displayEffectiveDate = format_date($childDocument->effectiveDate);
							}

							if ((($childDocument->expirationDate == "0000-00-00") || ($childDocument->expirationDate == "")) && ($user->canEdit())){
								$displayExpirationDate = "<a href='javascript:archiveDocument(" . $childDocument->documentID . ");'>" . _("archive document") . "</a>";
							}else{
								$displayExpirationDate = _("archived on: ") . format_date($childDocument->expirationDate);
							}


							echo "<tr>";
							echo "<td $classAdd>" . $childDocument->shortName . "</td>";
							echo "<td $classAdd>" . $documentType->shortName . "</td>";
							echo "<td $classAdd>" . $displayEffectiveDate . "</td>";
							echo "<td $classAdd>";

							$signature = array();
							$signatureArray = $childDocument->getSignaturesForDisplay();
							// TODO: a11y: eliminate nested tables
							if (is_array($signatureArray) && count($signatureArray) > 0) {
								echo "<table class='noBorderTable'>";


								foreach($signatureArray as $signature) {
									if (($signature['signatureDate'] != '') && ($signature['signatureDate'] != "0000-00-00")) {
										$signatureDate = format_date($signature['signatureDate']);
									}else{
										$signatureDate=_('(no date)');
									}

									echo "<tr>";
									echo "<td $classAdd>" . $signature['signerName'] . "</td>";
									echo "<td $classAdd>" . $signatureDate . "</td>";
									echo "</tr>";

								}
								echo "</table>";
								if ($user->canEdit()){
									echo "<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getSignatureForm&height=270&width=460&modal=true&documentID=" . $childDocument->documentID . "\",300,460)' class='thickbox' id='signatureForm'>" . _("add/view details") . "</a>";
								}


							}else{
								echo _("(none found)") . "<br />";
								if ($user->canEdit()){
									echo "<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getSignatureForm&height=170&width=460&modal=true&documentID=" . $childDocument->documentID . "\",200,460)' class='thickbox' id='signatureForm'>" . _("add signatures") . "</a>";
								}
							}

							echo "</td>";

							echo "<td $classAdd>";
							if (!$user->isRestricted) {
								if ($childDocument->documentURL != ""){
									echo "<a href='documents/" . $childDocument->documentURL . "' " . getTarget() . ">" . _("view document") . "</a><br />";
								}else{
									echo _("(none uploaded)") . "<br />";
								}							}

							if (is_array($childDocument->getExpressions) && count($childDocument->getExpressions) > 0) {
								echo "<a href='javascript:showExpressionForDocument(" . $childDocument->documentID . ");'>" . _("view expressions") . "</a>";
							}

							echo "</td>";

							if ($user->canEdit()){
								echo "<td $classAdd><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getUploadDocument&height=285&width=305&modal=true&licenseID=" . $licenseID . "&documentID=" . $childDocument->documentID . "\",300,310)' class='thickbox' id='editDocument'>" . _("edit document") . "</a><br /><a href='javascript:deleteDocument(\"" . $childDocument->documentID . "\");'>" . _("remove document") . "</a>";
								//echo "<br />" . $displayExpirationDate . "</td>";
							}
							echo "</tr>";

							$numberOfChildren = $childDocument->getNumberOfChildren;

							if ($numberOfChildren > 0){
								if ($displayArchiveInd == '1') {
									echo "<tr><td colspan='6'><i>" . _("The following ") . $numberOfChildren . _(" document(s) belong to ") . $childDocument->shortName . ".</i></td></tr>";
								}else{
									echo "<tr><td colspan='6'><i>" . _("The following ") . $numberOfChildren . _(" document(s) belong to ") . $childDocument->shortName . ".</i></td></tr>";
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
				echo "<i>" . sprintf(_("%d archive(s) available."), $numRows) . "  <a href='javascript:updateArchivedDocuments(1)'>" . _("show archives") . "</a></i><br /><br />";
			}
		}

		if (($user->canEdit()) && ($displayArchiveInd != "")){
			$duglicense = new License(new NamedArguments(array('primaryKey' => $licenseID)));
			$dugArray = $duglicense->getDocuments();
			$numDug = count($dugArray);
			if ( $numDug == 0 ) {
				echo "<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getUploadDocument&licenseID=" . $licenseID . "&height=310&width=310&modal=true\",310,310)' class='thickbox' id='uploadDocument'>" . _("upload new document") . "</a>";
			} else {
				echo _("Only one active document is allowed.") . " <a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getUploadDocument&licenseID=" . $licenseID . "&isArchived=1&height=310&width=310&modal=true\",310,310)' class='thickbox' id='uploadDocument'>" . _("upload archived document") . "</a>";
			}
			echo '<br /><br />';
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

				<table class='verticalFormTable table-border table-striped'>
				<tr>
				<th><?php echo _("Type");?></th>
				<th><?php echo _("Document Text");?></th>
				<?php if ($user->canEdit()){ ?>
					<th><?php echo _("Qualifier");?></th>
					<th><?php echo _("Actions"); ?></th>
				<?php } ?>
				</tr>

				<?php



				$expressionArray = $documentObj->getExpressionsForDisplay();

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
						<!-- TODO: a11y: eliminate nested tables -->
						<table class='noBorderTable'>
						<tr>
						<td class='alt' ><?php echo $expressionIns['expressionTypeName']; ?>
						<?php
						//if not configured to use the terms tool, hide the production use in terms tool checkbox/display
						if ((strtoupper($expressionIns['noteType']) == 'DISPLAY') && ($util->useTermsTool())){
							if ($user->isAdmin()) {
								if ($expressionIns['productionUseInd'] == "1"){
									echo "</td><td class='alt' style='float: right;text-align:right;'><label><input type='checkbox' id='productionUseInd_" . $expressionIns['expressionID'] . "' name='productionUseInd_" . $expressionIns['expressionID'] . "' onclick='javascript:changeProdUse(" . $expressionIns['expressionID'] . ")' checked></label></td>";
								}else{
									echo "</td><td class='alt' style='float: right;text-align:right;'><label><input type='checkbox' id='productionUseInd_" . $expressionIns['expressionID'] . "' name='productionUseInd_" . $expressionIns['expressionID'] . "' onclick='javascript:changeProdUse(" . $expressionIns['expressionID'] . ")'></label></td>";
								}
							}else{
								if ($expressionIns['productionUseInd'] == "1"){
									echo "<br /><br /><i>" . _("used in terms tool") . "</i></td>";
								}
							}

						}
						?>
						</tr>
						</table>
						<span id='span_prod_use_<?php echo $expressionIns['expressionID']; ?>' class='redText'></span>
					</td>

					<?php
					echo "<td class='alt'>" . nl2br($expressionIns['documentText']) . "</td>";

					if ($user->canEdit()){
						echo "<td class='alt'>";

						if (is_array($qualifierArray) && count($qualifierArray) > 0) {
							echo implode("<br />", $qualifierArray);
						}else{
							echo "&nbsp;";
						}


						echo "</td>";
						echo "<td class='actions'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getExpressionForm&licenseID=" . $licenseID . "&expressionID=" . $expressionIns['expressionID'] . "&height=420&width=345&modal=true\",420,350)' class='thickbox'>" . _("edit") . "</a>&nbsp;&nbsp;<a href='javascript:deleteExpression(" . $expressionIns['expressionID'] . ");'>" . _("remove") . "</a></td>";
					}
					echo "</tr>";

					if ($user->canEdit()){
						echo "<tr><td class='alt'>&nbsp;</td><td colspan='4' class='alt'>" . ucfirst($expressionIns['noteType']) . _(" Notes:") . "  <ul class='moved'>";
					}else{
						echo "<tr><td class='alt'>&nbsp;</td><td colspan='2' class='alt'>" . ucfirst($expressionIns['noteType']) . _(" Notes:") . "  <ul class='moved'>";
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
						echo "<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getExpressionNotesForm&height=330&width=440&modal=true&expressionID=" . $expressionIns['expressionID'] . "\",330,440)' class='thickbox' id='ExpressionNotes'>" . _("add/view ") . lcfirst($expressionIns['noteType']) . _(" notes") . "</a>";
					}
					echo "</td>";
					echo "</tr>";
					if ($user->canEdit()){
						echo "<tr><td colspan='4'>&nbsp;</td></tr>";
					}else{
						echo "<tr><td colspan='2'>&nbsp;</td></tr>";
					}


				}
				?>

			</table>

			<?php
			}

		}else{
			echo _("(none found)");
		}

		if ($user->canEdit()){
			echo "<br /><br /><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getExpressionForm&licenseID=" . $licenseID . "&height=420&width=345&modal=true\",420,350)' class='thickbox' id='expression'>" . _("add new expression") . "</a>";
		}
		break;




	//generic admin data (lookup table) display - all tables have ID and shortName so we can simplify retrieving this data
	case 'getAdminList':
		$className = $_GET['tableName'];
		$instance = new $className();
		$resultArray = $instance->allAsArray();
		if (is_array($resultArray) && count($resultArray) > 0) {
			?>
			<table class='dataTable table-striped table-border'>
				<thead>
					<tr>
						<th scope="col"><?php echo _('Name'); ?></th>
						<th scope="col"><?php echo _('Actions'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php

				foreach($resultArray as $result){
					echo "<tr>";
					echo "<td>" . $result['shortName'] . "</td>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getAdminUpdateForm&tableName=" . $className . "&updateID=" . $result[lcfirst($className) . 'ID'] . "&height=130&width=250&modal=true\",350,450)' class='thickbox' id='expression'><img id='Edit' class='editIcon' src='images/edit.gif' title= '"._("Edit")."' /></a>";
					echo "<a href='javascript:deleteData(\"" . $className . "\",\"" . $result[lcfirst($className) . 'ID'] . "\")'><img id='Remove' class='removeIcon' src='images/cross.gif' title= '"._("Remove")."' /></a></td>";
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


	//display user info for admin screen
	case 'getAdminUserList':

		$instanceArray = array();
		$user = new User();
		$tempArray = array();
		$util = new Utility();

		if (is_array($user->allAsArray()) && count($user->allAsArray()) > 0) {

			?>
			<table class='dataTable table-striped table-border'>
				<thead>
				<tr>
				<th><?php echo _("Login ID");?></th>
				<th><?php echo _("First Name");?></th>
				<th><?php echo _("Last Name");?></th>
				<th><?php echo _("Privilege");?></th>
				<?php
				//if not configured to use terms tool, hide the Terms Tool Update Email
				if ($util->useTermsTool()){
					echo "<th>" . _("Terms Tool Update Email") . "</th>";
				}
				?>
				<th class="actions"><?php echo _("Actions");?></th>
				</tr>
				</thead>
				<tbody>
				<?php

				foreach($user->allAsArray() as $instance) {
					$privilege = new Privilege(new NamedArguments(array('primaryKey' => $instance['privilegeID'])));

					echo "<tr>";
					echo "<td>" . $instance['loginID'] . "</td>";
					echo "<td>" . $instance['firstName'] . "</td>";
					echo "<td>" . $instance['lastName'] . "</td>";
					echo "<td>" . $privilege->shortName . "</td>";
					//if not configured to use SFX, hide the Terms Tool Update Email
					if ($util->useTermsTool()){
						echo "<td>" . $instance['emailAddressForTermsTool'] . "</td>";
					}
					echo "<td class='actions'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm&loginID=" . $instance['loginID'] . "&height=210&width=295&modal=true\",350,400)' class='thickbox' id='expression'><img id='Edit' class='EditIcon' src='images/edit.gif' title= '"._("Edit")."' /></a>";
					echo "<a href='javascript:deleteUser(\"" . $instance['loginID'] . "\")'><img id='Remove' class='removeIcon' src='images/cross.gif' title= '"._("Remove")."' /></a></td>";
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
			<table class='dataTable table-striped table-border'>
				<tr>
				<th><?php echo _("Expression Type");?></th>
				<th><?php echo _("Note Type");?></th>
				<th class="actions"><?php echo _("Actions");?></th>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<td>" . $instance['shortName'] . "</td>";
					echo "<td>" . $instance['noteType'] . "</td>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getExpressionTypeForm&expressionTypeID=" . $instance['expressionTypeID'] . "&height=158&width=265&modal=true\",160,270)' class='thickbox'>" . _("update") . "</a>";
					echo "<a href='javascript:deleteExpressionType(\"" . $instance['expressionTypeID'] . "\")'>" . _("remove") . "</a></td>";
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)");
		}

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
			<th scope="col"><?php echo _("Qualifier");?></th>
			</tr>
			</thead>
			<tbody>
			<?php

			foreach($expressionType->all() as $expressionTypeObj) {
				$i = 0; //counter to display expression type first time only
				foreach ($expressionTypeObj->getQualifiers() as $qualifier){
					if ($i == 0) $displayET = $expressionTypeObj->shortName; else $displayET = '&nbsp;';
					echo "<tr>";
					echo "<td>" . $displayET . "</td>";
					echo "<td>" . $qualifier->shortName . "</td>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getQualifierForm&qualifierID=" . $qualifier->qualifierID . "&height=158&width=295&modal=true\",160,300)' class='thickbox'>" . _("update") . "</a>";
					echo "<a href='javascript:deleteQualifier(\"" . $qualifier->qualifierID . "\")'>" . _("remove") . "</a></td>";
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
				if (!isset($_GET['page'])) echo "<label for='qualifierID'>" . _("Limit by Qualifier:") . "</label>";
			?>
				<select name='qualifierID' id='qualifierID' <?php if ((isset($_GET['page']))) echo "style='width:150px'"; ?> onchange='javsacript:updateSearch();'>
				<option value='' <?php if ((!$selectedValue) || ($reset == 'Y')) echo "selected"; ?>></option>
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

			$i=0;
			if (is_array($qualifierArray) && count($qualifierArray) > 0) {
				echo "<table>";
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



	default:
			if (empty($action))
        return;
       printf(_("Action %s not set up!"), $action);
       break;


}



?>
