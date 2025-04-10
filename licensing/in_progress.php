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

$pageTitle=_('Licenses In Progress');
include 'templates/header.php';
?>
<main id="main-content">
  <article>
    <h2><?php echo _("Licenses In Progress");?></h2>
    <p><a href='index.php'><?php echo _("Browse All");?></a></p>

    <div id='div_licenses'>
      <img src = "images/circle.gif"><?php echo _("Loading...");?>
    </div>
  </article>
</main>

<?php
include 'templates/footer.php';
?>
<script src="js/in_progress.js"></script>
</body>
</html>