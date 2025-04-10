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


//set referring page
$_SESSION['ref_script']=$currentPage;

$pageTitle=_('Terms Report');
include 'templates/header.php';
?>

<main id="main-content">
	<article>
		
<label for="expressionTypeID"><?php echo _('Limit by Expression Type:'); ?></label>

<select name='expressionTypeID' id='expressionTypeID' onchange='updateTermsReport();'>

<?php

	$display = array();
	$expressionType = new ExpressionType();

	foreach($expressionType->allAsArray() as $display) {
		// TODO: i18n these strings? 
		if (($display['noteType'] == 'Display') && ($display['shortName'] != "Interlibrary Loan (additional notes)")){
			$selected = '';
			if ($display['shortName'] == "Interlibrary Loan"){
				$selected = ' selected ';
			}
			echo "<option value='" . $display['expressionTypeID'] . "' ".$selected.">" . $display['shortName'] . "</option>";
		}
	}

?>

</select>


<div id='div_report'>

</div>

</article>
</main>
<?php
include 'templates/footer.php';
?>
<script src="js/terms_report.js"></script>
</body>
</html>
