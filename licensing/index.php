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
*/


include_once 'directory.php';

//used for creating a "sticky form" for back buttons
//except we don't want it to retain if they press the 'index' button
//check what referring script is

if (isset($_SESSION['ref_script']) && ($_SESSION['ref_script'] != "license.php")){
	$reset='Y';
}else{
	$reset='N';
}

$_SESSION['ref_script']=$currentPage;

$pageTitle=_('Home');

include_once 'templates/header.php';
//below includes search options in left pane only - the results are refreshed through ajax and placed in div searchResults
?>
<main id="main-content">
	<article>
		<div id='searchResults'></div>
	</article>

	<aside id="side" class="block-form" role="search">
	<button type="button" class="primary" onclick="updateSearch();"><?php echo _('Search Licenses'); ?></button>
	<div id='div_feedback' role='status'></div>
	
	
	<p class='searchRow'>
		<label for='searchName'><?php echo _("Name (contains)");?></label>
		<input type='text' name='searchName' id='searchName' value="<?php if (isset($_SESSION['license_shortName']) && ($reset != 'Y')) echo $_SESSION['license_shortName']; ?>" />
	</p>
	<p class='searchRow'>
		<label for='organizationID'><?php echo _("Publisher/Provider");?></label>
	<?php
		$license = new License();
		$orgArray = array();

		try {
			$orgArray = $license->getOrganizationList();
			?>

			<select name='organizationID' id='organizationID' onchange='javsacript:updateSearch();'>
			<option value=''><?php echo _("All");?></option>

			<?php
			foreach($license->getOrganizationList() as $display) {
				if ((isset($_SESSION['license_organizationID'])) && ($_SESSION['license_organizationID'] == $display['organizationID']) && ($reset != 'Y')){
					echo "<option value='" . $display['organizationID'] . "' selected>" . $display['name'] . "</option>";
				}else{
					echo "<option value='" . $display['organizationID'] . "'>" . $display['name'] . "</option>";
				}
			}
			?>
			</select>
			<?php
		}catch (Exception $e){
			echo "<span class='error'>"._("There was an error processing this request - please verify configuration.ini is set up for organizations correctly and the database and tables have been created.")."</span>";
		}
	?>

	</p>

	<p class='searchRow'>
		<label for='consortiumID'><?php echo _("Consortium");?></label>
		<select name='consortiumID' id='consortiumID' onchange='javsacript:updateSearch();'>
	<option value=''><?php echo _("All");?></option>
	<option value='0'><?php echo _("(none)");?></option>
	<?php

		$display = array();

		foreach($license->getConsortiumList() as $display) {
			if ((isset($_SESSION['license_consortiumID'])) && ($_SESSION['license_consortiumID'] == $display['consortiumID']) && ($reset != 'Y')){
				echo "<option value='" . $display['consortiumID'] . "' selected>" . $display['name'] . "</option>";
			}else{
				echo "<option value='" . $display['consortiumID'] . "'>" . $display['name'] . "</option>";
			}
		}

	?>
	</select>
	</p>

	<p class='searchRow'>
		<label for='statusID'><?php echo _("Status");?></label>
		<select name='statusID' id='statusID' onchange='javsacript:updateSearch();'>
	<option value='' selected></option>
	<?php

		$display = array();
		$status = new Status();

		foreach($status->allAsArray() as $display) {
			if ((isset($_SESSION['license_statusID'])) && ($_SESSION['license_statusID'] == $display['statusID']) && ($reset != 'Y')){
				echo "<option value='" . $display['statusID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['statusID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>

	</p>
	
	<p class='searchRow'>
		<label for='documentTypeID'><?php echo _("Document Type");?></label>
	<select name='documentTypeID' id='documentTypeID' onchange='javsacript:updateSearch();'>
	<option value='' selected></option>
	<?php

		$display = array();
		$documentType = new DocumentType();

		foreach($documentType->allAsArray() as $display) {
			if ((isset($_SESSION['license_documentTypeID'])) && ($_SESSION['license_documentTypeID'] == $display['documentTypeID']) && ($reset != 'Y')){
				echo "<option value='" . $display['documentTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['documentTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}


	?>
	</select>

	</p>
	
	<p class='searchRow'>
		<label for='expressionTypeID'><?php echo _("Expression Type");?></label>
	
	<select name='expressionTypeID' id='expressionTypeID'>
	<option value='' selected></option>
	<?php

		$display = array();
		$expressionType = new ExpressionType();

		foreach($expressionType->allAsArray() as $display) {
			$selected = '';
			if ((isset($_SESSION['license_expressionTypeID'])) && ($_SESSION['license_expressionTypeID'] == $display['expressionTypeID']) && ($reset != 'Y')){
				$selected = ' selected ';
			}
			echo "<option value='" . $display['expressionTypeID'] . "' ".$selected.">" . $display['shortName'] . "</option>";
		}


	?>
	</select>

	</p>
	
	<p class='searchRow' id="tr_Qualifiers">
		<b><?php echo _("Qualifier");?></b>
		<div id='div_Qualifiers'>
		<input type='hidden' id='qualifierID' value='<?php if ((isset($_SESSION['license_qualifierID'])) && ($_SESSION['license_qualifierID']) && ($reset != 'Y')) echo $_SESSION['license_qualifierID']; ?>' />
		</div>
	</p>

	<p class='searchRow'>
		<?php echo _("Starts with");?>
	</p>
	<ul class="searchAlphabetical">
		<?php
		// TODO: i18n alphabets
		$alphArray = range('A','Z');
		$licAlphArray = $license->getAlphabeticalList;

		foreach ($alphArray as $letter){
			echo "<li id='span_letter_" . $letter . "'>";
			if ((isset($licAlphArray[$letter])) && ($licAlphArray[$letter] > 0)){
				echo "<a href='javascript:setStartWith(\"" . $letter . "\")'>" . $letter . "</a>";
			}
			else {
				echo "<span class='searchLetter'>" . $letter . "</span>";
			}
			echo "</li>";
		}
		?>
	</ul>
	
	<p>
	<input type='hidden' id='reset' value='<?php echo $reset; ?>'>
	<!-- <button type="button" class='link newSearch'><?php echo _("Reset search");?></button> -->
	<button type="submit" class="primary"><?php echo _("Search Licenses");?></button>
	</p>
	</aside>
	

</main>
<?php
include 'templates/footer.php';
?>
<script type='text/javascript'>
<?php
  //used to default to previously selected values when back button is pressed
  //if the startWith is defined set it so that it will default to the first letter picked
  if (($_SESSION['license_startWith']) && ($reset != 'Y')){
	  echo "startWith = '" . $_SESSION['license_startWith'] . "';";
	  echo "$(\"#span_letter_" . $_SESSION['license_startWith'] . "\").removeClass('searchLetter').addClass('searchLetterSelected');";
  }

  if (($_SESSION['license_pageStart']) && ($reset != 'Y')){
	  echo "pageStart = '" . $_SESSION['license_pageStart'] . "';";
  }

  if (($_SESSION['license_numberOfRecords']) && ($reset != 'Y')){
	  echo "numberOfRecords = '" . $_SESSION['license_numberOfRecords'] . "';";
  }

  if (($_SESSION['license_orderBy']) && ($reset != 'Y')){
	  echo "orderBy = \"" . $_SESSION['license_orderBy'] . "\";";
  }
?>

</script>
<script src="js/index.js"></script>
</body>
</html>

