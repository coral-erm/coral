<?php

/*
**************************************************************************************************************************
** CORAL Organizations Module
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

if (isset($_SESSION['ref_script']) && $_SESSION['ref_script'] != "orgDetail.php") {
	$reset='Y';
}
else {
  $reset = 'N';
}



//print header
$pageTitle=_('Home');
include 'templates/header.php';
$_SESSION['ref_script']=$currentPage;
?>
<main id="main-content">
	<article>
	<div id='div_searchResults'></div>
	</article>

<aside id="side" class="block-form" role="search">
	<div id='title-search'>
		<button type="button" class='primary' onclick="updateSearch();"><?php echo _("Search Organizations");?></button>
		
		<div id='div_feedback' role='status'></div>
		
	</div>

	
	<p class='searchRow'>
		<label for='searchOrganizationName'><?php echo _("Name (contains)");?></label>
		<input type='text' name='searchOrganizationName' id='searchOrganizationName' value="<?php if ($reset != 'Y' && isset($_SESSION['org_organizationName'])) echo $_SESSION['org_organizationName']; ?>" />
	</p>
	
	<p class='searchRow'>
		<label for='searchOrganizationRoleID'><?php echo _("Role");?></label>
			<select name='searchOrganizationRoleID' id='searchOrganizationRoleID' onchange='javsacript:updateSearch();'>
	<option value=''><?php echo _("All");?></option>
	<?php

		$display = array();
		$organizationRole = new OrganizationRole();

		foreach($organizationRole->allAsArray() as $display) {
			if ((isset($_SESSION['org_organizationRoleID'])) && ($_SESSION['org_organizationRoleID'] == $display['organizationRoleID']) && ($reset != 'Y')) {
				echo "<option value='" . $display['organizationRoleID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['organizationRoleID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

	?>
	</select>
	</p>

	<p class='searchRow'>
		<label for='searchContactName'><?php echo _("Contact Name (contains)");?></label>

	<input type='text' name='searchContactName' id='searchContactName' value="<?php if ($reset != 'Y' && isset($_SESSION['org_contactName'])) echo $_SESSION['org_contactName']; ?>" />
	</p>
	
	<p class='searchRow'>
		<?php echo _("Starts with");?>
	</p>
	<ul class="searchAlphabetical">
		<?php
		$organization = new Organization();

		// TODO: i18n alphabets
		$alphArray = range('A','Z');
		$orgAlphArray = $organization->getAlphabeticalList;

		foreach ($alphArray as $letter){
			echo "<li id='span_letter_" . $letter . "'>";
			if ((isset($orgAlphArray[$letter])) && ($orgAlphArray[$letter] > 0)){
				echo "<a href='javascript:setStartWith(\"" . $letter . "\")'>" . $letter . "</a>";
			}
			else {
				echo "<span class='searchLetter'>" . $letter . "</span>";
			}
			echo "</li>";
		}
		?>
	</ul>
	<p class="searchRow actions">
		<button type="submit" class='primary' onclick="updateSearch();"><?php echo _("Search Organizations");?></button>
	</p>
</aside>
</main>

<script type='text/javascript'>
<?php
  //used to default to previously selected values when back button is pressed
  //if the startWith is defined set it so that it will default to the first letter picked
  if ((isset($_SESSION['org_startWith'])) && ($reset != 'Y')){
	  echo "startWith = '" . $_SESSION['org_startWith'] . "';";
	  echo "$(\"#span_letter_" . $_SESSION['org_startWith'] . "\").removeClass('searchLetter').addClass('searchLetterSelected');";
  }

  if ((isset($_SESSION['org_pageStart'])) && ($reset != 'Y')){
	  echo "pageStart = '" . $_SESSION['org_pageStart'] . "';";
  }

  if ((isset($_SESSION['org_numberOfRecords'])) && ($reset != 'Y')){
	  echo "numberOfRecords = '" . $_SESSION['org_numberOfRecords'] . "';";
  }

  if ((isset($_SESSION['org_orderBy'])) && ($reset != 'Y')){
	  echo "orderBy = \"" . $_SESSION['org_orderBy'] . "\";";
  }
	
?>
</script>
<script src="js/index.js"></script>
<?php
include 'templates/footer.php';
?>
</body>
</html>