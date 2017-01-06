<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.2
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


//used for creating a "sticky form" for back buttons
//except we don't want it to retain if they press the 'index' button
//check what referring script is

if (CoralSession::get('ref_script') != "resource.php"){
	Resource::resetSearch();
}
CoralSession::set('ref_script', $currentPage);
$search = Resource::getSearch();

//print header
$pageTitle=_('Home');
include 'templates/header.php';


?>

<div style='text-align:left;'>
<table class="headerTable" style="background-image:url('images/header.gif');background-repeat:no-repeat;">
<tr style='vertical-align:top;'>
<td style="width:155px;padding-right:10px;">
	<form method="get" action="ajax_htmldata.php?action=getSearchResources" id="resourceSearchForm">
		<?php
		foreach(array('orderBy','page','recordsPerPage','startWith') as $hidden) {
			echo Html::hidden_search_field_tag($hidden, $search[$hidden]);
		}
		?>
		
	<table class='noBorder' id='title-search'>
	<tr><td style='text-align:left;width:75px;' align='left'>
	<span style='font-size:130%;font-weight:bold;'><?php echo _("Search");?></span><br />
	<a href='javascript:void(0)' class='newSearch' title="<?php echo _("new search");?>"><?php echo _("new search");?></a>
	</td>
	<td><div id='div_feedback'>&nbsp;</div>
	</td></tr>
	</table>

	<table class='borderedFormTable' style="width:150px">

	<tr>
	<td class='searchRow'><label for='searchName'><b><?php echo _("Name (contains)");?></b></label>
	<br />
	<?php echo Html::text_search_field_tag('name', $search['name']); ?>
	<br />
	<div id='div_searchName' style='<?php if (!$search['name']) echo "display:none;"; ?>margin-left:123px;'><input type='button' name='btn_searchName' value='<?php echo _("go!");?>' class='searchButton' /></div>
	</td>
	</tr>



	<tr>
	<td class='searchRow'><label for='searchResourceISBNOrISSN'><b><?php echo _("ISBN/ISSN");?></b></label>
	<br />
	<?php echo Html::text_search_field_tag('resourceISBNOrISSN', $search['resourceISBNOrISSN']); ?>
	<br />
	<div id='div_searchISBNOrISSN' style='<?php if (!$search['resourceISBNOrISSN']) echo "display:none;"; ?>margin-left:123px;'><input type='button' name='btn_searchResourceISBNOrISSN' value='<?php echo _("go!");?>' class='searchButton' /></div>
	</td>
	</tr>



	<tr>
	<td class='searchRow'><label for='searchFund'><b><?php echo _("Fund");?></b></label>
	<br />
		<select name='search[fund]' id='searchFund' style='width:150px' class ='changeInput'>
			<option value=''><?php echo _("All");?></option>
			<?php
				if ($search['fund'] == "none"){
					echo "<option value='none' selected>" . _("(none)") . "</option>";
				}else{
					echo "<option value='none'>" . _("(none)") . "</option>";
				}
				$fundType = new Fund();
			
		foreach($fundType->allAsArray() as $fund) {
				$fundCodeLength = strlen($fund['fundCode']) + 3;
				$combinedLength = strlen($fund['shortName']) + $fundCodeLength;
				$fundName = ($combinedLength <=50) ? $fund['shortName'] : substr($fund['shortName'],0,49-$fundCodeLength) . "&hellip;";
				$fundName .= " [" . $fund['fundCode'] . "]</option>";
				echo "<option value='" . $fund['fundID'] . "'>" . $fundName . "</option>";
		}

			?>
		</select>
	</td>
	</tr>



	<tr>
	<td class='searchRow'><label for='searchAcquisitionTypeID'><b><?php echo _("Acquisition Type");?></b></label>
	<br />
	<select name='search[acquisitionTypeID]' id='searchAcquisitionTypeID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>
	<?php

	  $display = array();
	  $acquisitionType = new AcquisitionType();

		foreach($acquisitionType->allAsArray() as $display) {
			if ($search['acquisitionTypeID'] == $display['acquisitionTypeID']) {
				echo "<option value='" . $display['acquisitionTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['acquisitionTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>

	<tr>
	<td class='searchRow'><label for='searchStatusID'><b><?php echo _("Status");?></b></label>
	<br />
	<select name='search[statusID]' id='searchStatusID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>
	<?php

		$display = array();
		$status = new Status();

		foreach($status->allAsArray() as $display) {
			//exclude saved status
			if (strtoupper($display['shortName']) != 'SAVED'){
				if ($search['statusID'] == $display['statusID']){
					echo "<option value='" . $display['statusID'] . "' selected>" . $display['shortName'] . "</option>";
				}else{
					echo "<option value='" . $display['statusID'] . "'>" . $display['shortName'] . "</option>";
				}
			}
		}

	?>
	</select>
	</td>
	</tr>






	<tr>
	<td class='searchRow'><label for='searchCreatorLoginID'><b><?php echo _("Creator");?></b></label>
	<br />
	<select name='search[creatorLoginID]' id='searchCreatorLoginID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>

	<?php

		$display = array();
		$resource = new Resource();

		foreach($resource->getCreatorsArray() as $display) {
			if ($display['firstName']){
				$name = $display['lastName'] . ", " . $display['firstName'];
			}else{
				$name = $display['loginID'];
			}

			if ($search['creatorLoginID'] == $display['loginID']){
				echo "<option value='" . $display['loginID'] . "' selected>" . $name . "</option>";
			}else{
				echo "<option value='" . $display['loginID'] . "'>" . $name . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>



	<tr>
	<td class='searchRow'><label for='searchResourceFormatID'><b><?php echo _("Resource Format");?></b></label>
	<br />
	<select name='search[resourceFormatID]' id='searchResourceFormatID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>
	<?php

		$display = array();
		$resourceFormat = new ResourceFormat();

		foreach($resourceFormat->allAsArray() as $display) {
			if ($search['resourceFormatID'] == $display['resourceFormatID']){
				echo "<option value='" . $display['resourceFormatID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['resourceFormatID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>


	<tr>
	<td class='searchRow'><label for='searchResourceTypeID'><b><?php echo _("Resource Type");?></b></label>
	<br />
	<select name='search[resourceTypeID]' id='searchResourceTypeID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>

	<?php

		if ($search['resourceTypeID'] == "none"){
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}


		$display = array();
		$resourceType = new ResourceType();

		foreach($resourceType->allAsArray() as $display) {
			if ($search['resourceTypeID'] == $display['resourceTypeID']){
				echo "<option value='" . $display['resourceTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['resourceTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>


	<tr>
	<td class='searchRow'><label for='searchGeneralSubjectID'><b><?php echo _("General Subject");?></b></label>
	<br />
	<select name='search[generalSubjectID]' id='searchGeneralSubjectID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>

	<?php

		if ($search['generalSubjectID'] == "none"){
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}


		$display = array();
		$generalSubject = new GeneralSubject();

		foreach($generalSubject->allAsArray() as $display) {
			if ($search['generalSubjectID'] == $display['generalSubjectID']){
				echo "<option value='" . $display['generalSubjectID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['generalSubjectID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>

	<tr>
	<td class='searchRow'><label for='searchDetailedSubjectID'><b><?php echo _("Detailed Subject");?></b></label>
	<br />
	<select name='search[detailedSubjectID]' id='searchDetailedSubjectID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>

	<?php

		if ($search['detailedSubjectID'] == "none"){
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}


		$display = array();
		$detailedSubject = new DetailedSubject();

		foreach($detailedSubject->allAsArray() as $display) {
			if ($search['detailedSubjectID'] == $display['detailedSubjectID']){
				echo "<option value='" . $display['detailedSubjectID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['detailedSubjectID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>
	
	<tr>
	<td class='searchRow'><label for='searchFirstLetter'><b><?php echo _("Starts with");?></b></label>
	<br />
	<?php
	$resource = new Resource();

	$alphArray = range('A','Z');
	$resAlphArray = $resource->getAlphabeticalList;

	foreach ($alphArray as $letter){
		if ((isset($resAlphArray[$letter])) && ($resAlphArray[$letter] > 0)){
			echo "<span class='searchLetter' id='span_letter_" . $letter . "'><a href='javascript:setStartWith(\"" . $letter . "\")' title=\"Starts with $letter\">" . $letter . "</a></span>";
			if ($letter == "N") echo "<br />";
		}else{
			echo "<span class='searchLetter'>" . $letter . "</span>";
			if ($letter == "N") echo "<br />";
		}
	}


	?>
	<br />
	</td>
	</tr>

	</table>

	<div id='hideShowOptions'><a href='javascript:void(0);' name='showMoreOptions' id='showMoreOptions' title="<?php echo _("more options...");?>"><?php echo _("more options...");?></a></div>
	<div id='div_additionalSearch' style='display:none;'>
	<table class='borderedFormTable' style="width:150px">

	<tr>
	<td class='searchRow'><label for='searchNoteTypeID'><b><?php echo _("Note Type");?></b></label>
	<br />
	<select name='search[noteTypeID]' id='searchNoteTypeID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>
	<?php

		if ($search['noteTypeID'] == "none") {
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}

		$display = array();
		$noteType = new NoteType();

		foreach($noteType->allAsArray() as $display) {
			if ($search['noteTypeID'] == $display['noteTypeID']) {
				echo "<option value='" . $display['noteTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['noteTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>


	<tr>
	<td class='searchRow'><label for='searchResourceNote'><b><?php echo _("Note (contains)");?></b></label>
	<br />
	<?php echo Html::text_search_field_tag('resourceNote', $search['resourceNote']); ?>
	<br />
	<div id='div_searchResourceNote' style='<?php if (!$search['resourceNote']) echo "display:none;"; ?>margin-left:123px;'><input type='button' name='btn_searchResourceNote' value='<?php echo _("go!");?>' class='searchButton' /></div>
	</td>
	</tr>




	<tr>
	<td class='searchRow'><label for='createDate'><b><?php echo _("Date Created Between");?></b></label><br />
	  <?php echo Html::text_search_field_tag('createDateStart', $search['createDateStart'], array('class' => 'date-pick', 'width' => '65px')); ?>
	&nbsp;&nbsp;<b><?php echo _("and");?></b>
	</td>
	</tr>
	<tr>
	<td style="border-top:0px;padding-top:0px;">
	  <?php echo Html::text_search_field_tag('createDateEnd', $search['createDateEnd'], array('class' => 'date-pick', 'width' => '65px')); ?>
	<br />
	<div id='div_searchCreateDate' style='display:none;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' class='searchButton' value='<?php echo _("go!");?>' /></div>
	</td>
	</tr>



	<tr>
	<td class='searchRow'><label for='searchPurchaseSiteID'><b><?php echo _("Purchase Site");?></b></label>
	<br />
	<select name='search[purchaseSiteID]' id='searchPurchaseSiteID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>
	<?php

		if ($search['purchaseSiteID'] == "none"){
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}

		$display = array();
		$purchaseSite = new PurchaseSite();

		foreach($purchaseSite->allAsArray() as $display) {
			if ($search['purchaseSiteID'] == $display['purchaseSiteID']){
				echo "<option value='" . $display['purchaseSiteID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['purchaseSiteID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>




	<tr>
	<td class='searchRow'><label for='searchAuthorizedSiteID'><b><?php echo _("Authorized Site");?></b></label>
	<br />
	<select name='search[authorizedSiteID]' id='searchAuthorizedSiteID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>
	<?php

		if ($search['authorizedSiteID'] == "none") {
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}

		$display = array();
		$authorizedSite = new AuthorizedSite();

		foreach($authorizedSite->allAsArray() as $display) {
			if ($search['authorizedSiteID'] == $display['authorizedSiteID']){
				echo "<option value='" . $display['authorizedSiteID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['authorizedSiteID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>




	<tr>
	<td class='searchRow'><label for='searchAdministeringSiteID'><b><?php echo _("Administering Site");?></b></label>
	<br />
	<select name='search[administeringSiteID]' id='searchAdministeringSiteID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>
	<?php

		if ($search['administeringSiteID'] == "none") {
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}

		$display = array();
		$administeringSite = new AdministeringSite();

		foreach($administeringSite->allAsArray() as $display) {
			if ($search['administeringSiteID'] == $display['administeringSiteID']) {
				echo "<option value='" . $display['administeringSiteID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['administeringSiteID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>


	<tr>
	<td class='searchRow'><label for='searchAuthenticationTypeID'><b><?php echo _("Authentication Type");?></b></label>
	<br />
	<select name='search[authenticationTypeID]' id='searchAuthenticationTypeID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>
	<?php

		if ($search['authenticationTypeID'] == "none") {
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}


		$display = array();
		$authenticationType = new AuthenticationType();

		foreach($authenticationType->allAsArray() as $display) {
			if ($search['authenticationTypeID'] == $display['authenticationTypeID']) {
				echo "<option value='" . $display['authenticationTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['authenticationTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>
	
	<tr>
	<td class='searchRow'><label for='searchCatalogingStatusID'><b><?php echo _("Cataloging Status");?></b></label>
	<br />
	<select name='search[catalogingStatusID]' id='searchCatalogingStatusID' style='width:150px'>
	<option value=''><?php echo _("All");?></option>
	<?php
	  if ($search['catalogingStatusID'] == "none") {
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}

		$catalogingStatus = new CatalogingStatus();
		
		foreach($catalogingStatus->allAsArray() as $status) {
			if ($search['catalogingStatusID'] == $status['catalogingStatusID']) {
				echo "<option value='" . $status['catalogingStatusID'] . "' selected>" . $status['shortName'] . "</option>";
			}else{
				echo "<option value='" . $status['catalogingStatusID'] . "'>" . $status['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</td>
	</tr>
  
  <tr>
	<td class='searchRow'><label for='searchStepName'><b><?php echo _("Routing Step");?></b></label>
	<br />
	<select name='search[stepName]' id='searchStepName' style='width:150px'>
	<option value=''><?php echo _("All");?></option>
	<?php
	  $step = new Step();
		$stepNames = $step->allStepNames();
		
		foreach($stepNames as $stepName) {
		  if ($search['stepName'] == $stepName) {
		    $stepSelected = " selected";
		  } else {
		    $stepSelected = false;
		  }
		  echo "<option value=\"" . htmlspecialchars($stepName) . "\" $stepSelected>" . htmlspecialchars($stepName) . "</option>";
		}

	?>
	</select>
	</td>
	</tr>
	<tr>
		<td class='searchRow'><label for='searchParents'><b>Relationship</b></label>
		<select name='search[parent]' id='searchParents' style='width:150px'>
			<option value=''><?php echo _("All");?></option>
			<option value='RRC'><?php echo _("Parent");?></option>
			<option value='RRP'><?php echo _("Child");?></option>
		</select>
	</td>
	</tr>


	</table>
	</div>

	</form>
</td>
<td>
<div id='div_searchResults'></div>
</td></tr>
</table>
</div>
<br />
<script type="text/javascript" src="js/index.js"></script>
<script type='text/javascript'>
<?php
  //used to default to previously selected values when back button is pressed
  //if the startWith is defined set it so that it will default to the first letter picked
  if ((CoralSession::get('res_startWith')) && ($reset != 'Y')){
	  echo "startWith = '" . CoralSession::get('res_startWith') . "';";
	  echo "$(\"#span_letter_" . CoralSession::get('res_startWith') . "\").removeClass('searchLetter').addClass('searchLetterSelected');";
  }

  if ((CoralSession::get('res_pageStart')) && ($reset != 'Y')){
	  echo "pageStart = '" . CoralSession::get('res_pageStart') . "';";
  }

  if ((CoralSession::get('res_recordsPerPage')) && ($reset != 'Y')){
	  echo "recordsPerPage = '" . CoralSession::get('res_recordsPerPage') . "';";
  }

  if ((CoralSession::get('res_orderBy')) && ($reset != 'Y')){
	  echo "orderBy = \"" . CoralSession::get('res_orderBy') . "\";";
  }

	echo "</script>";

	//print footer
	include 'templates/footer.php';
?>
