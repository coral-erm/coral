<?php
	$resourceID = $_GET['resourceID'];
    $resourceAcquisitionID = isset($_GET['resourceAcquisitionID']) ? $_GET['resourceAcquisitionID'] : null;
	if (isset($_GET['archiveInd'])) $archiveInd = $_GET['archiveInd']; else $archiveInd='';
	if (isset($_GET['showArchivesInd'])) $showArchivesInd = $_GET['showArchivesInd']; else $showArchivesInd='';

	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
    $resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

		$util = new Utility();

		//these are used to display the header since the arrays have resource and organization level contacts combined
		$resContactFlag = 0;
		$orgContactFlag = 0;

		//get contacts
		$sanitizedInstance = array();
		$contactArray = array();
		$contactObjArray = array();

		if ((isset($archiveInd)) && ($archiveInd == "1")){
			//if we want archives to be displayed
			if ($showArchivesInd == "1"){
				if (is_array($resourceAcquisition->getArchivedContacts()) && count($resourceAcquisition->getArchivedContacts()) > 0) {
					echo "<i><b>"._("The following are archived contacts:")."</b></i>";
				}
				$contactArray = $resourceAcquisition->getArchivedContacts();
			}
		}else{
			$contactArray = $resourceAcquisition->getUnarchivedContacts();
		}


		if (is_array($contactArray) && count($contactArray) > 0) {
			foreach ($contactArray as $contact){
				if (($resContactFlag == 0) && (!isset($contact['organizationName']))){
					echo "<h3>"._("Order Specific:")."</h3>";
					$resContactFlag = 1;
				}else if (($orgContactFlag == 0) && (isset($contact['organizationName']))){
					if ($resContactFlag == 0){
						echo "<p><i>"._("No Order Specific Contacts")."</i></p>";
					}

					if ($user->canEdit() && ($archiveInd != 1) && ($showArchivesInd != 1)){ ?>
						<p><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getContactForm&type=named&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",400,800)' class='thickbox' id='newNamedContact'><?php echo _("add contact");?></a></p>
					<?php
					}

					echo "<h3>"._("Inherited:")."</h3>";
					$orgContactFlag = 1;
				}else

				?>

				<dl class='dl-grid contact-list'>
					<div class="header">
						<dt>
							<?php echo _("Name: ") ?>
						</dt>
						<dd>
				
						<?php if ($contact['name']) { echo $contact['name']; } ?>

							<span class="actions">
							<?php
								if (($user->canEdit()) && (!isset($contact['organizationName']))){
									echo "<a href='javascript:void(0);' onclick='javascript:myDialog(\"ajax_forms.php?action=getContactForm&type=named&resourceID=" . $resourceID . "&contactID=" . $contact['contactID'] . "\",400,800)'  class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit contact")."'></a>";
									echo "&nbsp;&nbsp;<a href='javascript:void(0)' class='removeContact' id='" . $contact['contactID'] . "'><img src='images/cross.gif' alt='"._("remove note")."' title='"._("remove contact")."'></a>";
								}else{
									echo "&nbsp;";
								}

							?>
							</span>
						</dd>
					</div>
				<?php
				if (isset($contact['organizationName'])){ ?>

					<dt><?php echo _("Organization:");?></dt>
					<dd><?php echo $contact['organizationName'] . "&nbsp;&nbsp;<a href='" . $util->getCORALURL() . "organizations/orgDetail.php?showTab=contacts&organizationID=" . $contact['organizationID'] . "' " . getTarget() . "><img src='images/arrow-up-right.gif' alt='"._("Visit Contact in Organizations Module")."' title='"._("Visit Contact in Organizations Module")."' style='vertical-align:top;'></a>"; ?></dd>
				
				<?php
				}

				if (($contact['archiveDate'] != '0000-00-00') && ($contact['archiveDate'])) { ?>
					<dt class="archived"><?php echo _("No longer valid:");?></dt>
					<dd class="archived"><i><?php echo format_date($contact['archiveDate']); ?></i></dd>
				<?php
				}

				if ($contact['title']) { ?>
					<dt><?php echo _("Title:");?></dt>
					<dd><?php echo $contact['title']; ?></dd>
				<?php
				}

				if ((isset($contact['addressText'])) && ($contact['addressText'] != '')){ ?>
					<dt><?php echo _("Address:");?></dt>
					<dd><?php echo nl2br($contact['addressText']); ?></dd>
				<?php
				}

				if ((isset($contact['state']) || (isset($contact['country']))) && (($contact['state'] != '') || ($contact['country'] != ''))){ ?>
					<dt><?php echo _("Location:");?></dt>
					<dd><?
						// TODO: i18n addresses
						if (!($contact['state'])) {
							echo $contact['country'];
						}else if (!($contact['country'])) {
							echo $contact['state'];
						}else{
							echo $contact['state'] . ", " . $contact['country'];
						}
						?>
					</dd>
				<?php
				}

				if ($contact['phoneNumber']) { ?>
					<dt><?php echo _("Phone:");?></dt>
					<dd><?php echo $contact['phoneNumber']; ?></dd>
				<?php
				}

				if ($contact['altPhoneNumber']) { ?>
					<dt><?php echo _("Alt Phone:");?></dt>
					<dd><?php echo $contact['altPhoneNumber']; ?></dd>
				<?php
				}

				if ($contact['faxNumber']) { ?>
					<dt><?php echo _("Fax:");?></dt>
					<dd><?php echo $contact['faxNumber']; ?></dd>
				<?php
				}

				if ($contact['emailAddress']) { ?>
					<dt><?php echo _("Email:");?></dt>
					<dd><a href='mailto:<?php echo $contact['emailAddress']; ?>'><?php echo $contact['emailAddress']; ?></a></dd>
				<?php
				}

				if ($contact['noteText']) { ?>
					<dt><?php echo _("Notes:");?></dt>
					<dd><?php echo nl2br($contact['noteText']); ?></dd>
				<?php
				}
				?>

					<dt><?php echo _('Roles: ') ?></dt>
					<dd><?php echo $contact['contactRoles']; ?></dd>

				<?php
				if ($contact['lastUpdateDate']) { ?>
					<dt><?php echo _("Last Updated:");?></dt>
					<dd><i><?php echo format_date($contact['lastUpdateDate']); ?></i></dd>
				<?php
				}
				?>

				</dl>
			<?php
			}


			if ($user->canEdit() && ($orgContactFlag == 0) && ($showArchivesInd != 1)){ ?>
				<p><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getContactForm&type=named&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>", 400, 800)' class='thickbox' id='newNamedContact'><?php echo _("add contact");?></a></p>
			<?php
			}


		} else {
			if (($archiveInd != 1) && ($showArchivesInd != 1)){
				echo "<p><i>"._("No contacts available")."</i></p>";
				if (($user->canEdit())){ ?>
					<p><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getContactForm&type=named&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",400,800)' class='thickbox' id='newNamedContact'><?php echo _("add contact");?></a></p>
				<?php
				}
			}
		}

		if (($showArchivesInd == "0") && ($archiveInd == "1") && (count($resourceAcquisition->getArchivedContacts()) > 0)){
			// TODO: i18n placeholders
			echo "<p><i>" . count($resourceAcquisition->getArchivedContacts()) . _(" archived contact(s) available.")."  <a href='javascript:updateArchivedContacts(1);'>"._("show archived contacts")."</a></i></p>";
		}

		if (($showArchivesInd == "1") && ($archiveInd == "1") && (count($resourceAcquisition->getArchivedContacts()) > 0)){
			echo "<p><i><a href='javascript:updateArchivedContacts(0);'>"._("hide archived contacts")."</a></i></p>";
		}

?>

