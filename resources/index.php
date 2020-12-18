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

$currentPage = $_SERVER["SCRIPT_NAME"];
$parts = Explode('/', $currentPage);
$currentPage = $parts[count($parts) - 1];

//used for creating a "sticky form" for back buttons
//except we don't want it to retain if they press the 'index' button
//check what referring script is

if (CoralSession::get('ref_script') != "resource.php"){
	Resource::resetSearch();
}
CoralSession::set('ref_script', $currentPage = '');
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
			echo (new Html())->hidden_search_field_tag($hidden, isset($search[$hidden]) ? $search[$hidden] : '' );
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
	<?php echo (new Html())->text_search_field_tag('name', isset($search['name']) ? $search['name'] : '' ); ?>
	<br />
	<div id='div_searchName' style='<?php if (!isset($search['name'])) echo "display:none;"; ?>margin-left:123px;'><input type='button' name='btn_searchName' value='<?php echo _("go!");?>' class='searchButton' /></div>
	</td>
	</tr>


    <tr>
      <td class='searchRow'>
		    <label for='searchPublisher'><b><?php echo _("Publisher (contains)"); ?></b></label>
        <?php echo (new Html())->text_search_field_tag('publisher', isset($search['publisher']) ? $search['publisher'] : ''); ?>
        <div id='div_searchPublisher' style='<?php echo (empty($search['publisher']) ? "display: none;" : ""); ?>'>
          <input type='button' name='btn_searchPublisher' value='<?php echo _("go!");?>' class='searchButton' />
        </div>
      </td>
    </tr>

    <tr>
      <td class='searchRow'>
        <label for='searchPlatform'><b><?php echo _("Platform (contains)"); ?></b></label>
        <?php echo (new Html())->text_search_field_tag('platform', isset($search['platform']) ? $search['platform'] : ''); ?>
        <div id='div_searchPlatform' style='<?php echo (empty($search['platform']) ? "display: none;" : ""); ?>'>
          <input type='button' name='btn_searchPlatform' value='<?php echo _("go!");?>' class='searchButton' />
        </div>
      </td>
    </tr>

    <tr>
      <td class='searchRow'>
        <label for='searchProvider'><b><?php echo _("Provider (contains)"); ?></b></label>
        <?php echo (new Html())->text_search_field_tag('provider', isset($search['provider']) ? $search['provider'] : ''); ?>
        <div id='div_searchProvider' style='<?php echo (empty($search['provider']) ? "display: none;" : ""); ?>'>
          <input type='button' name='btn_searchProvider' value='<?php echo _("go!");?>' class='searchButton' />
        </div>
      </td>
    </tr>



	<tr>
	<td class='searchRow'><label for='searchResourceISBNOrISSN'><b><?php echo _("ISBN/ISSN");?></b></label>
	<br />
	<?php echo (new Html())->text_search_field_tag('resourceISBNOrISSN', isset($search['resourceISBNOrISSN']) ? $search['resourceISBNOrISSN'] : ''); ?>
	<br />
	<div id='div_searchISBNOrISSN' style='<?php if (!isset($search['resourceISBNOrISSN'])) echo "display:none;"; ?>margin-left:123px;'><input type='button' name='btn_searchResourceISBNOrISSN' value='<?php echo _("go!");?>' class='searchButton' /></div>
	</td>
	</tr>



	<tr>
	<td class='searchRow'><label for='searchFund'><b><?php echo _("Fund");?></b></label>
	<br />
		<select name='search[fund]' id='searchFund' style='width:150px' class ='changeInput'>
			<option value=''><?php echo _("All");?></option>
			<?php
				if (isset($search['fund']) && $search['fund'] == "none"){
					echo "<option value='none' selected>" . _("(none)") . "</option>";
				}else{
					echo "<option value='none'>" . _("(none)") . "</option>";
				}
				$fundType = new Fund();

		foreach($fundType->allAsArray() as $fund) {
				$fundCodeLength = strlen($fund['fundCode']) + 3;
				$combinedLength = strlen($fund['shortName']) + $fundCodeLength;
				$fundName = ($combinedLength <=50) ? $fund['shortName'] : substr($fund['shortName'],0,49-$fundCodeLength) . "&hellip;";
				$fundName .= " [" . $fund['fundCode'] . "]";
                if (isset($search['fund']) && $search['fund'] == $fund['fundID']) {
                    echo "<option value='" . $fund['fundID'] . "' selected='selected'>" . $fundName . "</option>";
                } else {
                    echo "<option value='" . $fund['fundID'] . "'>" . $fundName . "</option>";
                }
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
			if (isset($search['acquisitionTypeID']) && $search['acquisitionTypeID'] == $display['acquisitionTypeID']) {
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
				if (isset($search['statusID']) && $search['statusID'] == $display['statusID']){
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

			if (isset($search['creatorLoginID']) && $search['creatorLoginID'] == $display['loginID']){
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
			if (isset($search['resourceFormatID']) && $search['resourceFormatID'] == $display['resourceFormatID']){
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

		if (isset($search['resourceTypeID']) && $search['resourceTypeID'] == "none"){
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}


		$display = array();
		$resourceType = new ResourceType();

		foreach($resourceType->allAsArray() as $display) {
			if (isset($search['resourceTypeID']) && $search['resourceTypeID'] == $display['resourceTypeID']){
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

		if (isset($search['generalSubjectID']) && $search['generalSubjectID'] == "none"){
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}


		$display = array();
		$generalSubject = new GeneralSubject();

		foreach($generalSubject->allAsArray() as $display) {
			if (isset($search['generalSubjectID']) && $search['generalSubjectID'] == $display['generalSubjectID']){
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

		if (isset($search['detailedSubjectID']) && $search['detailedSubjectID'] == "none"){
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}


		$display = array();
		$detailedSubject = new DetailedSubject();

		foreach($detailedSubject->allAsArray() as $display) {
			if (isset($search['detailedSubjectID']) && $search['detailedSubjectID'] == $display['detailedSubjectID']){
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

		if (isset($search['noteTypeID']) && $search['noteTypeID'] == "none") {
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}

		$display = array();
		$noteType = new NoteType();

		foreach($noteType->allAsArray() as $display) {
			if (isset($search['noteTypeID']) && $search['noteTypeID'] == $display['noteTypeID']) {
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
	<?php echo (new Html())->text_search_field_tag('resourceNote', isset($search['resourceNote']) ? $search['resourceNote'] : ''); ?>
	<br />
	<div id='div_searchResourceNote' style='<?php if (!isset($search['resourceNote'])) echo "display:none;"; ?>margin-left:123px;'><input type='button' name='btn_searchResourceNote' value='<?php echo _("go!");?>' class='searchButton' /></div>
	</td>
	</tr>




	<tr>
	<td class='searchRow'><label for='createDate'><b><?php echo _("Date Created Between");?></b></label><br />
	  <?php echo (new Html())->text_search_field_tag('createDateStart', isset($search['createDateStart']) ? $search['createDateStart'] : '', array('class' => 'date-pick', 'width' => '65px')); ?>
	&nbsp;&nbsp;<b><?php echo _("and");?></b>
	</td>
	</tr>
	<tr>
	<td style="border-top:0px;padding-top:0px;">
	  <?php echo (new Html())->text_search_field_tag('createDateEnd', isset($search['createDateEnd']) ? $search['createDateEnd'] : '', array('class' => 'date-pick', 'width' => '65px')); ?>
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

		if (isset($search['purchaseSiteID']) && $search['purchaseSiteID'] == "none"){
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}

		$display = array();
		$purchaseSite = new PurchaseSite();

		foreach($purchaseSite->allAsArray() as $display) {
			if (isset($search['purchaseSiteID']) && $search['purchaseSiteID'] == $display['purchaseSiteID']){
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

		if (isset($search['authorizedSiteID']) && $search['authorizedSiteID'] == "none") {
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}

		$display = array();
		$authorizedSite = new AuthorizedSite();

		foreach($authorizedSite->allAsArray() as $display) {
			if (isset($search['authorizedSiteID']) && $search['authorizedSiteID'] == $display['authorizedSiteID']){
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

		if (isset($search['administeringSiteID']) && $search['administeringSiteID'] == "none") {
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}

		$display = array();
		$administeringSite = new AdministeringSite();

		foreach($administeringSite->allAsArray() as $display) {
			if (isset($search['administeringSiteID']) && $search['administeringSiteID'] == $display['administeringSiteID']) {
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

		if (isset($search['authenticationTypeID']) && $search['authenticationTypeID'] == "none") {
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}


		$display = array();
		$authenticationType = new AuthenticationType();

		foreach($authenticationType->allAsArray() as $display) {
			if (isset($search['authenticationTypeID']) && $search['authenticationTypeID'] == $display['authenticationTypeID']) {
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
	  if (isset($search['catalogingStatusID']) && $search['catalogingStatusID'] == "none") {
			echo "<option value='none' selected>"._("(none)")."</option>";
		}else{
			echo "<option value='none'>"._("(none)")."</option>";
		}

		$catalogingStatus = new CatalogingStatus();

		foreach($catalogingStatus->allAsArray() as $status) {
			if (isset($search['catalogingStatusID']) && $search['catalogingStatusID'] == $status['catalogingStatusID']) {
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
	<td class='searchRow'><label for='searchStepName'><b><?php echo _("Workflow Step");?></b></label>
	<br />
	<select name='search[stepName]' id='searchStepName' style='width:150px'>
	<option value=''><?php echo _("All");?></option>
	<?php
	  $step = new Step();
		$stepNames = $step->allStepNames();

		foreach($stepNames as $stepName) {
		  if (isset($search['stepName']) && $search['stepName'] == $stepName) {
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
			<option value='RRC'<?php if (isset($search['parent']) && $search['parent'] == 'RRC') { echo " selected='selected'"; }; echo ">" ._("Parent");?></option>
			<option value='RRP'<?php if (isset($search['parent']) && $search['parent'] == 'RRP') { echo " selected='selected'"; }; echo ">" . _("Child");?></option>
			<option value='None'<?php if (isset($search['parent']) && $search['parent'] == 'None') { echo " selected='selected'"; }; echo ">" . _("None");?></option>
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
