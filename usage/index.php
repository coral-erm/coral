<?php

/*
**************************************************************************************************************************
** CORAL Usage Statistics v. 1.1
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


session_start();

include_once 'directory.php';

//print header
$pageTitle = _('Usage Statistics');
include 'templates/header.php';

//used for creating a "sticky form" for back buttons
//except we don't want it to retain if they press the 'index' button
//check what referring script is

if ((isset($_SESSION['ref_script'])) and ($_SESSION['ref_script'] != "publisherPlatform.php")){
	$reset = "Y";
}

$_SESSION['ref_script']=$currentPage;


?>
<main id="main-content">
	<article>
		<div id='div_searchResults'></div>
	</article>
		<aside id="side" class="block-form" role="search">
			<!-- TODO: primary button -->
			<button type="button" class='newSearch primary'><?php echo _("Search Statistics");?></button>
			<div id='div_feedback'></div>

	<p class='searchRow'>
		<label for='searchName'><?php echo _("Name (contains)");?></label>
	
		<input type='text' name='searchName' id='searchName' value="<?php if ($reset != 'Y') echo $_SESSION['plat_searchName']; ?>" />
		<div id='div_searchName' style='<?php if ((!$_SESSION['plat_searchName']) || ($reset == 'Y')) echo "display:none;"; ?>'>
			<input type='button' name='btn_searchName' value='<?php echo _("go!");?>' class='searchButton' />
		</div>
	
	</p>
	
	<p class='searchRow'>
		<?php echo _("Starts with");?>
	</p>
	<ul class="searchAlphabetical">
		<?php
		$platform = new Platform();

		// TODO: i18n alphabets
		$alphArray = range('A','Z');
		$pAlphArray = $platform->getAlphabeticalList;

		foreach ($alphArray as $letter){
			echo "<li id='span_letter_" . $letter . "'>";
			if ((isset($pAlphArray[$letter])) && ($pAlphArray[$letter] > 0)){
				echo "<a href='javascript:void(0)' onclick='setStartWith(\"" . $letter . "\")'>" . $letter . "</a>";
			}
			else {
				echo "<span class='searchLetter'>" . $letter . "</span>";
			}
			echo "</li>";
		}
		?>
	</ul>
	
	</aside>
</main>
<?php
//print footer
include 'templates/footer.php';
?>
<script type="text/javascript" src="js/index.js"></script>
<script type='text/javascript'>
<?php
  //used to default to previously selected values when back button is pressed
  //if the startWith is defined set it so that it will default to the first letter picked
  if ((isset($_SESSION['plat_startWith'])) && ($reset != 'Y')){
	  echo "startWith = '" . $_SESSION['plat_startWith'] . "';";
	  echo "$(\"#span_letter_" . $_SESSION['plat_startWith'] . "\").removeClass('searchLetter').addClass('searchLetterSelected');";
  }

  if ((isset($_SESSION['plat_pageStart'])) && ($reset != 'Y')){
	  echo "pageStart = '" . $_SESSION['plat_pageStart'] . "';";
  }

  if ((isset($_SESSION['plat_recordsPerPage'])) && ($reset != 'Y')){
	  echo "recordsPerPage = '" . $_SESSION['plat_recordsPerPage'] . "';";
  }

  if ((isset($_SESSION['plat_orderBy'])) && ($reset != 'Y')){
	  echo "orderBy = \"" . $_SESSION['plat_orderBy'] . "\";";
  }

?>
</script>
</body>
</html>