<?php
	$resourceID = $_GET['resourceID'];
    $resourceAcquisitionID = isset($_GET['resourceAcquisitionID']) ? $_GET['resourceAcquisitionID'] : null;
	if (isset($_GET['archiveInd'])) $archiveInd = $_GET['archiveInd']; else $archiveInd='';
	if (isset($_GET['showArchivesInd'])) $showArchivesInd = $_GET['showArchivesInd']; else $showArchivesInd='';

	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
    $resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));


		//get contacts
		$resContactArray = array();
		$orgContactArray = array();

		if ((isset($archiveInd)) && ($archiveInd == "1")){
			//if we want archives to be displayed
			if ($showArchivesInd == "1"){
				$resContactArray = $resourceAcquisition->getArchivedContacts('resources');
				$orgContactArray = $resourceAcquisition->getArchivedContacts('organizations');
				if (count($resContactArray) > 0 || count($orgContactArray) > 0){
					echo "<i><b>"._("The following are archived contacts:")."</b></i>";
				}
			}
		}else{
			$resContactArray = $resourceAcquisition->getUnarchivedContacts('resources');
			$orgContactArray = $resourceAcquisition->getUnarchivedContacts('organizations');
		}


		if (count($resContactArray) > 0 || count($orgContactArray) > 0){
        ?>
        <script>
        $('#div_fullRightPanel').hide();
        </script>
        <?php
            if (count($resContactArray) > 0) {
					echo "<div class='formTitle' style='padding:4px; font-weight:bold; margin-bottom:8px;'>"._("Order Specific:")."</div>";
                    displayContactArray($resContactArray, $user, $resourceAcquisitionID, $showArchivesInd, 'resources');
            } else {
                    echo "<i>"._("No Order Specific Contacts")."</i><br /><br />";
            }

					if ($user->canEdit() && ($archiveInd != 1) && ($showArchivesInd != 1)){ ?>
						<a href='ajax_forms.php?action=getContactForm&height=389&width=620&modal=true&type=named&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>' class='thickbox' id='newNamedContact'><?php echo _("add contact");?></a><br /><br /><br />
					<?php
					}

            if (count($orgContactArray) > 0) {
					echo "<div class='formTitle' style='padding:4px; font-weight:bold; margin-bottom:8px;'>"._("Inherited:")."</div>";
                    displayContactArray($orgContactArray, $user, $resourceAcquisitionID, $showArchivesInd, 'organizations');
            }

		} else {
			if (($archiveInd != 1) && ($showArchivesInd != 1)){
				echo "<i>"._("No contacts available")."</i><br /><br />";
				if (($user->canEdit())){ ?>
					<a href='ajax_forms.php?action=getContactForm&height=389&width=620&modal=true&type=named&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>' class='thickbox' id='newNamedContact'><?php echo _("add contact");?></a>
				<?php
				}
			}
		}

		if (($showArchivesInd == "0") && ($archiveInd == "1") && (count($resourceAcquisition->getArchivedContacts()) > 0)){
			echo "<i>" . count($resourceAcquisition->getArchivedContacts()) . _(" archived contact(s) available.")."  <a href='javascript:updateArchivedContacts(1);'>"._("show archived contacts")."</a></i><br />";
		}

		if (($showArchivesInd == "1") && ($archiveInd == "1") && (count($resourceAcquisition->getArchivedContacts()) > 0)){
			echo "<i><a href='javascript:updateArchivedContacts(0);'>"._("hide archived contacts")."</a></i><br />";
		}

		echo "<br /><br />";

function displayContactArray($contactArray, $user, $resourceAcquisitionID, $showArchivesInd, $type) {
    $util = new Utility();
    echo '<table class="dataTable"><thead><tr>';
    if ($showArchivesInd == "1") { echo ("<th>" . _("No longer valid since") . "</th>"); }
    echo '<th> ' .  _("Name") . '</th>';
    echo '<th> ' .  _("Title") . '</th>';
    echo '<th> ' .  _("Role(s)") . '</th>';
    echo '<th> ' .  _("Address") . '</th>';
    echo '<th> ' .  _("Phone") . '</th>';
    echo '<th> ' .  _("Alt phone") . '</th>';
    echo '<th> ' .  _("Fax") . '</th>';
    echo '<th> ' .  _("Email") . '</th>';
    echo '<th> ' .  _("Notes") . '</th>';
    if ($type == 'organizations') {
        echo '<th> ' .  _("Organizations") . '</th>';
    }
    echo '<th> ' .  _("Last update") . '</th>';
    if ($type == 'resources') {
        echo '<th> ' .  _("Actions") . '</th>';
    }
    echo '</tr></thead><tbody>';
    foreach ($contactArray as $contact) {
				echo '<tr>';
				if (($contact['archiveDate'] != '0000-00-00') && ($contact['archiveDate'])) {
				    echo "<td>" . format_date($contact['archiveDate']) . "</td>";
				}
				echo '<td>' . $contact['name'] . '</td>';
				echo '<td>' . $contact['title'] . '</td>';
				echo '<td>' . $contact['contactRoles'] . '</td>';
				echo '<td>' . nl2br($contact['addressText']) . '</td>';
				echo '<td>' . $contact['phoneNumber'] . '</td>';
				echo '<td>' . $contact['altPhoneNumber'] . '</td>';
				echo '<td>' . $contact['faxNumber'] . '</td>';
				echo '<td>';
                if ($contact['emailAddress']) { echo "<a href='mailto:" . $contact['emailAddress'] . "'>" . $contact['emailAddress'] . "</a>"; }
                echo '</td>';
				echo '<td>' . nl2br($contact['noteText']) . '</td>';
                if ($type == 'organizations') {
                    echo '<td>';
                    if (isset($contact['organizationName'])){
                        echo $contact['organizationName'] . "&nbsp;&nbsp;<a href='" . $util->getCORALURL() . "organizations/orgDetail.php?showTab=contacts&organizationID=" . $contact['organizationID'] . "' target='_blank'><img src='images/arrow-up-right.gif' alt='"._("Visit Contact in Organizations Module")."' title='"._("Visit Contact in Organizations Module")."' style='vertical-align:top;'></a>"; 
                    }
                    echo '</td>';
                }
 
				echo '<td><i>' . format_date($contact['lastUpdateDate']) . '</i></td>';
                if (($user->canEdit()) && ($type == 'resources')){
                    echo '<td>';
						echo "<a href='ajax_forms.php?action=getContactForm&height=389&width=620&modal=true&type=named&resourceAcquisitionID=" . $resourceAcquisitionID . "&contactID=" . $contact['contactID'] . "' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit contact")."'></a>";
						echo "&nbsp;&nbsp;<a href='javascript:void(0)' class='removeContact' id='" . $contact['contactID'] . "'><img src='images/cross.gif' alt='"._("remove note")."' title='"._("remove contact")."'></a>";
                    echo '</td>';
					}
                echo '</tr>';
    }
    echo '</table>';
}

?>

