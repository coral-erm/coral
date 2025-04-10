<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
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
CoralSession::set('ref_script', $currentPage = '');

$pageTitle=_('My Queue');
$tabs = array(
	"OutstandingTasks" => _("Outstanding Tasks"),
	"SavedRequests" => _("Saved Requests"),
	"SubmittedRequests" => _("Submitted Requests")
);
$itemTitle = $tabs[$_GET['showTab']];
include 'templates/header.php';
?>


<main id="main-content">
	<article>
		<h2><?php echo _("My Queue");?></h2>
		
		<div id='div_QueueContent'>
			<img src = "images/circle.gif" /><?php echo _("Loading...");?>
		</div>
		<p class='error' id='div_error'></p>
	</article>
		<nav id="side" aria-label="<?php echo _('Task and Request Queues'); ?>">
			<ul class='queueMenuTable nav side'>
				<?php
				foreach ($tabs as $key => $value) {
					$ariaCurrent = '';
					if ($_GET['showTab'] == $key) {
						$ariaCurrent = ' aria-current="page" ';
					}
					
					echo "<li class='queueMenuLink'>
									<a href='?showTab=$key' id='{$key}' $ariaCurrent>"._($value)."
										<span class='count span_".$key."Number'></span>
									</a>
								</li>";
				}
				?>
			</ul>
		</nav>
</main>

	<script type="text/javascript" src="js/queue.js"></script>

<?php
include 'templates/footer.php';
?>
</body>
</html>

