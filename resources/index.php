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
CoralSession::set('ref_script', $currentPage = '');
$search = Resource::getSearch();

//print header
$pageTitle=_('Home');
include 'templates/header.php';
?>

<main id="main-content">
<article>
	<div id='div_searchResults'></div>
</article>

<aside id="side" class="block-form" role="search">
	<form method="get" action="ajax_htmldata.php?action=getSearchResources" id="resourceSearchForm">
		<?php
		foreach(array('orderBy','page','recordsPerPage','startWith') as $hidden) {
			echo (new Html())->hidden_search_field_tag($hidden, isset($search[$hidden]) ? $search[$hidden] : '' );
		}
		?>

	<div id='div_feedback' role='status'></div>

	<button type='button' class='submit-button primary' onclick="updateSearch();"><?php echo _("Search Resources");?></button>

	<p class='searchRow'>
		<label for='searchName'><?php echo _("Name (contains)");?></label>
		<?php echo (new Html())->text_search_field_tag('name', isset($search['name']) ? $search['name'] : '' ); ?>
	</p>
	
	<p class='searchRow'>
		<label for='searchPublisher'><?php echo _("Publisher (contains)"); ?></label>
		<?php echo (new Html())->text_search_field_tag('publisher', isset($search['publisher']) ? $search['publisher'] : ''); ?>
	</p>
	
      <p class='searchRow'>
        <label for='searchPlatform'><?php echo _("Platform (contains)"); ?></label>
        <?php echo (new Html())->text_search_field_tag('platform', isset($search['platform']) ? $search['platform'] : ''); ?>
      </p>

      <p class='searchRow'>
        <label for='searchProvider'><?php echo _("Provider (contains)"); ?></label>
        <?php echo (new Html())->text_search_field_tag('provider', isset($search['provider']) ? $search['provider'] : ''); ?>
      </p>

	<p class='searchRow'>
		<label for='searchOrderNumber'><?php echo _("Order Number"); ?></label>
		<?php 
			$defaultOrder = ($search['orderNumber']) ?? '';
			echo (new Html())->text_search_field_tag('orderNumber', $defaultOrder); 
		?>
	</p>
			
	<p class='searchRow'><label for='searchResourceISBNOrISSN'><?php echo _("ISBN/ISSN");?></label>
	<?php echo (new Html())->text_search_field_tag('resourceISBNOrISSN', isset($search['resourceISBNOrISSN']) ? $search['resourceISBNOrISSN'] : ''); ?>
</p>

	<p class='searchRow'>
		<label for='searchFund'><?php echo _("Fund");?></label>
		<select name='search[fund]' id='searchFund' class ='changeInput'>
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
				// TODO: i18n
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
</p>

	<p class='searchRow'>
		<label for='searchAcquisitionTypeID'><?php echo _("Acquisition Type");?></label>
	
		<select name='search[acquisitionTypeID]' id='searchAcquisitionTypeID'>
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
</p>

	<p class='searchRow'>
		<label for='searchStatusID'><?php echo _("Status");?></label>
	
		<select name='search[statusID]' id='searchStatusID'>
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
</p>

	<p class='searchRow'>
		<label for='searchCreatorLoginID'><?php echo _("Creator");?></label>

		<select name='search[creatorLoginID]' id='searchCreatorLoginID'>
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
</p>

	<p class='searchRow'>
		<label for='searchResourceFormatID'><?php echo _("Resource Format");?></label>
		<select name='search[resourceFormatID]' id='searchResourceFormatID'>
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
</p>


	
	<p class='searchRow'>
		<label for='searchResourceTypeID'><?php echo _("Resource Type");?></label>
	<select name='search[resourceTypeID]' id='searchResourceTypeID'>
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
</p>


	<p class='searchRow'>
		<label for='searchGeneralSubjectID'><?php echo _("General Subject");?></label>

		<select name='search[generalSubjectID]' id='searchGeneralSubjectID'>
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
</p>

	
	<p class='searchRow'>
		<label for='searchDetailedSubjectID'><?php echo _("Detailed Subject");?></label>

	<select name='search[detailedSubjectID]' id='searchDetailedSubjectID'>
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
</p>

<p class='searchRow'>
		<?php echo _("Starts with");?>
	</p>
	<ul class="searchAlphabetical">
		<?php
		$resource = new Resource();

		// TODO: i18n alphabets
		$alphArray = range('A','Z');
		$resAlphArray = $resource->getAlphabeticalList;

		foreach ($alphArray as $letter){
			echo "<li id='span_letter_" . $letter . "'>";
			if ((isset($resAlphArray[$letter])) && ($resAlphArray[$letter] > 0)){
				echo "<a href='javascript:setStartWith(\"" . $letter . "\")'>" . $letter . "</a>";
			}
			else {
				echo "<span class='searchLetter'>" . $letter . "</span>";
			}
			echo "</li>";
		}
		?>
	</ul>
	
	<!-- TODO: modern toggle here -->
	<details>
		<summary class="btn"><?php echo _("more options...");?></summary>
		
		<div id='div_additionalSearch'>

		<p class='searchRow'>
			<label for='searchNoteTypeID'><?php echo _("Note Type");?></label>
			<select name='search[noteTypeID]' id='searchNoteTypeID'>
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
		</p>
		<p class='searchRow'>
			<label for='searchResourceNote'><?php echo _("Note (contains)");?></label>
			<?php echo (new Html())->text_search_field_tag('resourceNote', isset($search['resourceNote']) ? $search['resourceNote'] : ''); ?>
		</p>

		<fieldset class='searchRow'>
			<legend class="label date-input-label"><?php echo _("Date Created Between");?></legend>
			<div class="flex">
				<div class="date-input-range">
					<?php echo (new Html())->text_search_field_tag('createDateStart', isset($search['createDateStart']) ? $search['createDateStart'] : '', array('class' => 'date-pick')); ?>
					<label for="searchCreateDateStart"><?php echo _('Start'); ?></label>
				</div>
				<div class="date-input-range">
					<?php echo (new Html())->text_search_field_tag('createDateEnd', isset($search['createDateEnd']) ? $search['createDateEnd'] : '', array('class' => 'date-pick')); ?>
					<label for="searchCreateDateEnd"><?php echo _('End'); ?></label>
				</div>
			</div>
	</fieldset>
		
		<p class='searchRow'>
			<label for='searchPurchaseSiteID'><?php echo _("Purchase Site");?></label>
			<select name='search[purchaseSiteID]' id='searchPurchaseSiteID'>
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
		</p>

		<p class='searchRow'>
			<label for='searchAuthorizedSiteID'><?php echo _("Authorized Site");?></label>
			<select name='search[authorizedSiteID]' id='searchAuthorizedSiteID'>
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
		</p>

		<p class='searchRow'>
			<label for='searchAdministeringSiteID'><?php echo _("Administering Site");?></label>
			<select name='search[administeringSiteID]' id='searchAdministeringSiteID'>
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
		</p>
		
		<p class='searchRow'>
			<label for='searchAuthenticationTypeID'><?php echo _("Authentication Type");?></label>
			<select name='search[authenticationTypeID]' id='searchAuthenticationTypeID'>
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
		</p>

		<p class='searchRow'>
			<label for='searchCatalogingStatusID'><?php echo _("Cataloging Status");?></label>
			<select name='search[catalogingStatusID]' id='searchCatalogingStatusID'>
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
		</p>
		
		<p class='searchRow'>
			<label for='searchStepName'><?php echo _("Workflow Step");?></label>
		
			<select name='search[stepName]' id='searchStepName'>
				<option value=''><?php echo _("All");?></option>
				<?php
					$step = new Step();
					$stepNames = $step->allStepNames();

					foreach($stepNames as $stepName) {
						if (isset($search['stepName']) && $search['stepName'] == $stepName) {
							$stepSelected = " selected";
						} else {
							$stepSelected = '';
						}
						echo "<option value=\"" . htmlspecialchars($stepName) . "\" $stepSelected>" . htmlspecialchars($stepName) . "</option>";
					}

				?>
			</select>
		</p>
		
			<p class='searchRow'>
				<label for='searchParents'><?php echo _('Relationship'); ?></label>
				<select name='search[parent]' id='searchParents'>
					<option value=''><?php echo _("All");?></option>
					<option value='RRC'<?php if (isset($search['parent']) && $search['parent'] == 'RRC') { echo " selected='selected'"; }?>><?php echo _("Parent");?></option>
					<option value='RRP'<?php if (isset($search['parent']) && $search['parent'] == 'RRP') { echo " selected='selected'"; }?>><?php echo _("Child");?></option>
					<option value='None'<?php if (isset($search['parent']) && $search['parent'] == 'None') { echo " selected='selected'";}?>><?php echo _("None");?></option>
				</select>
			</p>
		</div>
		</details>
		<button type="submit" class="primary"><?php echo _('Search Resources'); ?></button>
	</form>
</aside>
</main>

<?php
	include 'templates/footer.php';
?>
<script src="js/index.js"></script>
</body>
</html>