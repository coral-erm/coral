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

$pageTitle=_('Home');
include 'templates/header.php';

//used for creating a "sticky form" for back buttons
//except we don't want it to retain if they press the 'index' button
//check what referring script is

if (isset($_SESSION['ref_script']) && ($_SESSION['ref_script'] != "license.php")){
	$reset='Y';
}else{
	$reset='N';
}

$_SESSION['ref_script']=$currentPage;


//below includes search options in left pane only - the results are refreshed through ajax and placed in div searchResults
?>


<table class="headerTable" style="background-image:url('images/header.gif');background-repeat:no-repeat;">
<tr style='vertical-align:top;'>
<td style="width:155px;padding-right:10px;">
	<table class='noBorder'>
	<tr><td style="width:75px;">
	<span style='font-size:130%;font-weight:bold;'><?php echo _("Search");?></span><br />
	<a href='javascript:void(0)' class='newSearch'><?php echo _("new search");?></a>
	</td>
	<td><div id='div_feedback'>&nbsp;</div>
	</td></tr>
	</table>

	<table class='borderedFormTable' style="width:150px">

	<tr>
	<td class='searchRow'><label for='searchName'><b><?php echo _("Name (contains)");?></b></label>
	<br />
	<input type='text' name='searchName' id='searchName' style='width:145px' value="<?php if (isset($_SESSION['license_shortName']) && ($reset != 'Y')) echo $_SESSION['license_shortName']; ?>" /><br />
	<div id='div_searchName' style='<?php if ((!isset($_SESSION['license_shortName'])) || ($reset == 'Y')) echo "display:none;"; ?>margin-left:123px;'><input type='button' name='searchName' value='<?php echo _("go!");?>' class='searchButton' /></div>
	</td>
	</tr>


	<tr>
	<td class='searchRow'><label for='organizationID'><b><?php echo _("Publisher/Provider");?></b></label>
	<br />
	<?php
		$license = new License();
		$orgArray = array();

		try {
			$orgArray = $license->getOrganizationList();
			//echo "here is the list from getOrganization: ".$orgArray[['organizationID']."\n";
			?>

			<select name='organizationID' id='organizationID' style='width:150px' onchange='javsacript:updateSearch();'>
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
			echo "<span style='color:red'>"._("There was an error processing this request - please verify configuration.ini is set up for organizations correctly and the database and tables have been created.")."</span>";
		}
	?>

	</td>
	</tr>


	<tr>
	<td class='searchRow'><label for='consortium'><b><?php echo _("Consortium");?></b></label>
	<br />
	<select name='consortiumID' id='consortiumID' style='width:150px' onchange='javsacript:updateSearch();'>
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
	</td>
	</tr>

	<tr>
	<td class='searchRow'><label for='statusID'><b><?php echo _("Status");?></b></label>
	<br />
	<select name='statusID' id='statusID' style='width:150px' onchange='javsacript:updateSearch();'>
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

	</td>
	</tr>


	<tr>
	<td class='searchRow'><label for='documentTypeID'><b><?php echo _("Document Type");?></b></label>
	<br />
	<select name='documentTypeID' id='documentTypeID' style='width:150px' onchange='javsacript:updateSearch();'>
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

	</td>
	</tr>







	<tr>
	<td class='searchRow'><label for='expressionTypeID'><b><?php echo _("Expression Type");?></b></label>
	<br />
	<select name='expressionTypeID' id='expressionTypeID' style='width:150px'>
	<option value='' selected></option>
	<?php

		$display = array();
		$expressionType = new ExpressionType();

		foreach($expressionType->allAsArray() as $display) {
			if ((isset($_SESSION['license_expressionTypeID'])) && ($_SESSION['license_expressionTypeID'] == $display['expressionTypeID']) && ($reset != 'Y')){
				echo "<option value='" . $display['expressionTypeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['expressionTypeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}


	?>
	</select>

	</td>
	</tr>

	<tr id='tr_Qualifiers'>
	<td class='searchRow'><label for='qualifierID'><b><?php echo _("Qualifier");?></b></label>
	<br />
	<div id='div_Qualifiers'>
	<input type='hidden' id='qualifierID' value='<?php if ((isset($_SESSION['license_qualifierID'])) && ($_SESSION['license_qualifierID']) && ($reset != 'Y')) echo $_SESSION['license_qualifierID']; ?>' />
	</div>
	</td>
	</tr>


	<tr>
	<td class='searchRow'><label for='searchFirstLetter'><b><?php echo _("Starts with");?></b></label>
	<br />
	<?php
	$license = new License();

	$alphArray = range('A','Z');
	$licAlphArray = $license->getAlphabeticalList;

	foreach ($alphArray as $letter){
		if ((isset($licAlphArray[$letter])) && ($licAlphArray[$letter] > 0)){
			echo "<span class='searchLetter' id='span_letter_" . $letter . "'><a href='javascript:setStartWith(\"" . $letter . "\")'>" . $letter . "</a></span>";
		}else{
			echo "<span class='searchLetter'>" . $letter . "</span>";
		}
		if ($letter == "N") echo "<br />";
	}


	?>
	<br />
	</td>
	</tr>
	</table>
	&nbsp;<a href='javascript:void(0)' class='newSearch'><?php echo _("new search");?></a>
	<input type='hidden' id='reset' value='<?php echo $reset; ?>'>

</td>
<td>

<div id='searchResults'></div>

</td></tr>
</table>

</td>
</tr>
</table>
<br />
<script type="text/javascript" src="js/index.js"></script>

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


<?php
include 'templates/footer.php';
?>


