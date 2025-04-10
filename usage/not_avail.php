<?php
/*
**************************************************************************************************************************
** CORAL Usage Statistics Module
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

$pageTitle=_('Not Available');
$moduleTitle = _('Usage Statistics');
include_once '../templates/header.php';
?>

<main id="main-content">
	<article>
		<?php
		if ($_GET['errorMessage']){
			echo "<h3 class='error'>" . $_GET['errorMessage'] . "</h3>";
		}else{
			echo "<p class='error'>" . _("Please contact your Administrator for access to the Usage Statistics Module.") . "</p>";
		}
		?>
	</article>
</main>
<?php
//print footer
include 'templates/footer.php';
?>
</body>
</html>
