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



$pageTitle=_('Home');
include 'templates/header.php';
$_SESSION['ref_script']=$currentPage;

//below includes search options in left pane only - the results are refreshed through ajax and placed in div searchResults
?>


<main id="main-content">
	<article>
		<div id='searchResults'></div>
	</article>

	<aside id="side" class="block-form" role="search">
		<form onsubmit="updateSearch()">
		<button type="button" class="primary" onclick="updateSearch()"><?php echo _("Search Documents");?></button>
	<!-- <button type="button" class="link" class='newSearch'><?php echo _("Reset search");?></button> -->
	
	<div id='div_feedback'></div>
	
	<p class='searchRow'>
		<label for='searchName'><?php echo _("Name (contains)");?></label>
		<input type='text' name='searchName' id='searchName' value="<?php if (isset($_SESSION['license_shortName']) && ($reset != 'Y')) echo $_SESSION['license_shortName']; ?>" />
	</p>
	
	<input type='hidden' name='organizationID' id='organizationID' value='' />


	<p class='searchRow'>
		<label for='consortiumID'><?php echo _("Category");?></label>
	
		<select name='consortiumID' id='consortiumID' onchange='javsacript:updateSearch();'>
			<option value=''><?php echo _("All");?></option>
			<option value='0'><?php echo _("(none)");?></option>
	<?php
// TODO: broken
/*
		$display = array();

		foreach($license->getConsortiumList() as $display) {
			if ((isset($_SESSION['license_consortiumID'])) && ($_SESSION['license_consortiumID'] == $display['consortiumID']) && ($reset != 'Y')){
				echo "<option value='" . $display['consortiumID'] . "' selected>" . $display['name'] . "</option>";
			}else{
				echo "<option value='" . $display['consortiumID'] . "'>" . $display['name'] . "</option>";
			}
		}
/**/
	?>
	</select>
	</p>
	
	<input type='hidden' name='statusID' id='statusID' value='' />

	
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
	

	<input type='hidden' name='expressionTypeID' id='expressionTypeID' value='' />

	<input type='hidden' id='qualifierID' value='<?php if ((isset($_SESSION['license_qualifierID'])) && ($_SESSION['license_qualifierID']) && ($reset != 'Y')) echo $_SESSION['license_qualifierID']; ?>' />
	
	<p class='searchRow'>
	<?php echo _("Starts with");?>
	</p>
	<ul class="searchAlphabetical">
	<?php
	$license = new License();

	// TODO: i18n alphabets
	$alphArray = range('A','Z');
	$licAlphArray = $license->getAlphabeticalList;

	foreach ($alphArray as $letter){
		echo "<li id='span_letter_" . $letter . "'>";
		if ((isset($licAlphArray[$letter])) && ($licAlphArray[$letter] > 0)){
			echo "<a href='javascript:void(0)' onclick='setStartWith(\"" . $letter . "\")'>" . $letter . "</a>";
		}
		else {
			echo "<span class='searchLetter'>" . $letter . "</span>";
		}
		echo "</li>";
	}


	?>
	</ul>
	
	<!-- <button type="button" class='link newSearch'><?php echo _("Reset search");?></button>
	<input type='hidden' id='reset' value='<?php echo $reset; ?>'/> -->
	<button type="submit" class="primary" onclick="updateSearch()";><?php echo _("Search Documents");?></button>
	</form>
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
