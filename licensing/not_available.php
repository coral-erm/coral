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
$pageTitle=_('Not Available');

include 'templates/header.php';
?>

<main id="main-content">
	<article>
		<p class='error'>
			<?php
			if ($_GET['errorMessage']){
				echo $_GET['errorMessage'];
			}else{
				echo _("Please contact your Administrator for access to the Licensing Module.");
			}
			?>
		</p>
	</article>
</main>
<?php
include 'templates/footer.php';
?>