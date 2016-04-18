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

//set referring page
$_SESSION['ref_script']=$currentPage;

?>

<table class="headerTable" style="background-image:url('images/header.gif');background-repeat:no-repeat;">
<tr><td>
<span class="headerText"><?php echo _("Licenses In Progress");?>&nbsp;&nbsp;<a href='index.php'><?php echo _("Browse All");?></a></span>
<br />
<br />
<div id='div_licenses'>

<img src = "images/circle.gif"><?php echo _("Loading...");?>

</div>
</td></tr>
</table>
<script type="text/javascript" src="js/in_progress.js"></script>
<?php
include 'templates/footer.php';
?>


