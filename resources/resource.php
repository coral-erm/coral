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

$resourceID = $_GET['resourceID'];
$resourceAcquisitionID = isset($_GET['resourceAcquisitionID']) ? $_GET['resourceAcquisitionID'] : null;
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$status = new Status(new NamedArguments(array('primaryKey' => $resource->statusID)));
$resourceAcquisitions = $resource->getResourceAcquisitions();

//set referring page
if ((isset($_GET['ref'])) && ($_GET['ref'] == 'new')){
  CoralSession::set('ref_script', 'new');
}else{
  CoralSession::set('ref_script', $currentPage);
}

$links = array(
	'product' => _("Product"),
	'orders' => _("Orders"),
	'acquisitions' => _("Acquisitions"),
	'access' => _("Access"),
	'cataloging' => _("Cataloging"),
	'contacts' => _("Contacts"),
	'accounts' => _("Accounts"),
	'issues' => _("Issues"),
	'attachments' => _("Attachments"),
	'workflow' => _("Workflow"),
);

$pageTitle = $resource->titleText;
if (isset($_GET['showTab'])) {
	$itemTitle = $links[$_GET['showTab']];
}

include 'templates/header.php';

if ($user->accountTabIndicator !== 1) {
	unset($links['accounts']);
}

if ($resource->titleText){
	?>
	<main id="main-content">
	<nav id="side" class="sidemenu" aria-label="<?php echo _('Resource Data'); ?>">
		<ul class="nav side">
			<?php echo resource_sidemenu($links, watchString($_GET['showTab'])); ?>
		</ul>
	</nav>

		<article>
			<h2 id='span_resourceName'><?php echo $resource->titleText; ?></h2>

			<input type='hidden' name='resourceID' id='resourceID' value='<?php echo $resourceID; ?>'>
      <?php
                if ($resource->countResourceAcquisitions() > 1) {
            ?>
            <div id="resourceAcquisitionSelectDiv">
            <label for="resourceAcquisitionSelect"><?php echo _('Order:'); ?></label>
            <select id="resourceAcquisitionSelect">
            <?php
                    $selected = false;
                    foreach ($resourceAcquisitions as $resourceAcquisition) {
                        echo "<option value=\"$resourceAcquisition->resourceAcquisitionID\"";
                        if (!$selected) {
                            if ($resourceAcquisitionID == $resourceAcquisition->resourceAcquisitionID ||
                                (!$resourceAcquisitionID && $resourceAcquisition->isActiveToday())) {
                                    $selected = true;
                                    echo " selected=\"selected\"";
                            }
                        }
                        echo ">";
                        if ($resourceAcquisition->subscriptionStartDate && $resourceAcquisition->subscriptionEndDate) {
                            echo "$resourceAcquisition->subscriptionStartDate - $resourceAcquisition->subscriptionEndDate";
                        } elseif ($resourceAcquisition->subscriptionStartDate) {
                            printf(_("Start date: %s"), $resourceAcquisition->subscriptionStartDate);
                        } elseif ($resourceAcquisition->subscriptionEndDate) {
                            printf(_("End date: %s"), $resourceAcquisition->subscriptionEndDate);
                        } else {
                            printf(_("Order %s"), $resourceAcquisition->resourceAcquisitionID);
                        }
                        $organization = $resourceAcquisition->getOrganization();
                        if ($organization) {
                            echo " - " . $organization['organization'];
                        }
                        echo "</option>";
                    }
                    echo "</select>";
                    echo ("</div>");
                } else {
                    echo '<input type="hidden" id="resourceAcquisitionSelect" value="'.$resourceAcquisitions[0]->resourceAcquisitionID .'" />';
                }
            ?>
			<div id='div_new'>
                <?php if (isset($_GET['ref']) && $_GET['ref'] == 'new'): ?>
									<p class="success">
										<i class="fa fa-check fa-2x"></i>
										<b><?php echo _("Success!");?></b>
										<?php echo _("New resource added"); ?>
									</p>
                <?php endif; ?>
			</div>
		</div>

		<?php 
		foreach ($links as $resource_tab => $tab_title) { ?>
			<div id='div_<?php echo $resource_tab; ?>' class="tabpanel resource_tab_content">
								<div class='div_mainContent'></div>
								<?php if ($resource_tab == 'contacts') { ?>
									<div id='div_archivedContactDetails'></div>
								<?php
								}
								?>
				</div>
		<?php 
		}
		?>
	</article>
	<aside id="links">
		<div id='div_fullRightPanel' class='rightPanel'>
			<h3 id="side-menu-title"><?php echo _("Helpful Links"); ?></h3>
			<div id='div_rightPanel'></div>
		</div>

		<p>
		<?php if ($config->settings->feedbackEmailAddress != '') {?>
						<a href="mailto: <?php echo $config->settings->feedbackEmailAddress; ?>?subject=<?php echo $resource->titleText . ' (Resource ID: ' . $resource->resourceID . ')'; ?>" class='btn secondary'><?php echo _("Send feedback on this resource");?></a>
		<?php } ?>
		</p>

	</aside>
</main>
<?php

}

//print footer
include 'templates/footer.php';
?>
<script type="text/javascript" src="js/resource.js"></script>
</body>
</html>