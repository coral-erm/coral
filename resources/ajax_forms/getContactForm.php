<?php
	$resourceID = $_GET['resourceID'];
    $resourceAcquisitionID = isset($_GET['resourceAcquisitionID']) ? $_GET['resourceAcquisitionID'] : null;
	if (isset($_GET['contactID'])) $contactID = $_GET['contactID']; else $contactID = '';
	$contact = new Contact(new NamedArguments(array('primaryKey' => $contactID)));

		if (($contact->archiveDate) && ($contact->archiveDate != '0000-00-00')){
			$invalidChecked = 'checked';
		}else{
			$invalidChecked = '';
		}

		//get contact roles
		$sanitizedInstance = array();
		$instance = new ContactRole();
		$contactRoleProfileArray = array();
		foreach ($contact->getContactRoles() as $instance) {
			$contactRoleProfileArray[] = $instance->contactRoleID;
		}

		//get all contact roles for output in drop down
		$contactRoleArray = array();
		$contactRoleObj = new ContactRole();
		$contactRoleArray = $contactRoleObj->allAsArray();
?>
		<div id='div_contactForm'>
		<form id='contactForm'>
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>
		<input type='hidden' name='editResourceAcquisitionID' id='editResourceAcquisitionID' value='<?php echo $resourceAcquisitionID; ?>'>
		<input type='hidden' name='editContactID' id='editContactID' value='<?php echo $contactID; ?>'>

		<div class='formTitle' style='width:603px;'><span class='headerText' style='margin-left:7px;'><?php if ($contactID){ echo _("Edit Contact"); } else { echo _("Add Contact"); } ?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class="surroundBox" style="width:600;">
		<tr>
		<td>
			<table class='noBorder' style='width:560px; margin:15px 20px 10px 20px;'>
			<tr>
			<td>

				<table class='noBorder'>
					<tr>
					<td style='text-align:left'><label for='contactName'><b><?php echo _("Name:");?></b></label></td>
					<td>
					<input type='text' id='contactName' name='contactName' value = '<?php echo $contact->name; ?>' style='width:150px' class='changeInput' /><span id='span_error_contactName' class='smallDarkRedText'>
					</td>
					</tr>

					<tr>
					<td style='text-align:left'><label for='contactTitle'><b><?php echo _("Title:");?></b></label></td>
					<td>
					<input type='text' id='contactTitle' name='contactTitle' value = '<?php echo $contact->title; ?>' style='width:150px' class='changeInput' />
					</td>
					</tr>

					<tr>
					<td style='text-align:left'><label for='phoneNumber'><b><?php echo _("Phone:");?></b></label></td>
					<td>
					<input type='text' id='phoneNumber' name='phoneNumber' value = '<?php echo $contact->phoneNumber; ?>' style='width:150px' class='changeInput' />
					</td>
					</tr>

					<tr>
					<td style='text-align:left'><label for='altPhoneNumber'><b><?php echo _("Alt Phone:");?></b></label></td>
					<td>
					<input type='text' id='altPhoneNumber' name='altPhoneNumber' value = '<?php echo $contact->altPhoneNumber; ?>' style='width:150px' class='changeInput' />
					</td>
					</tr>

					<tr>
					<td style='text-align:left'><label for='faxNumber'><b><?php echo _("Fax:");?></b></label></td>
					<td>
					<input type='text' id='faxNumber' name='faxNumber' value = '<?php echo $contact->faxNumber; ?>' style='width:150px' class='changeInput' />
					</td>
					</tr>

					<tr>
					<td style='text-align:left'><label for='emailAddress'><b><?php echo _("Email:");?></b></label></td>
					<td>
					<input type='text' id='emailAddress' name='emailAddress' value = '<?php echo $contact->emailAddress; ?>' style='width:150px' class='changeInput' />
					</td>
					</tr>

					<tr>
					<td style='text-align:left'><label for='addressText'><b><?php echo _("Address:");?></b></label></td>
					<td>
					<textarea rows='3' id='addressText' style='width:150px'><?php echo $contact->addressText; ?></textarea>
					</td>
					</tr>

					<tr>
					<td style='text-align:left'><label for='invalidInd'><b><?php echo _("Archived:");?></b></label></td>
					<td>
					<input type='checkbox' id='invalidInd' name='invalidInd' <?php echo $invalidChecked; ?> />
					</td>
					</tr>
				</table>
			</td>
			<td>
				<table class='noBorder'>
				<tr>
				<td style='vertical-align:top;text-align:left;'><label for='orgRoles'><b><?php echo _("Role(s):");?></b></label></td>
				<td>

					<table>
					<?php
					$i=0;
					if (count($contactRoleArray) > 0){
						foreach ($contactRoleArray as $contactRoleIns){
							$i++;
							if(($i % 3)==1){
								echo "<tr>\n";
							}
							if (in_array($contactRoleIns['contactRoleID'],$contactRoleProfileArray)){
								echo "<td style='vertical-align:top;text-align:left;'><input class='check_roles' type='checkbox' name='" . $contactRoleIns['contactRoleID'] . "' id='" . $contactRoleIns['contactRoleID'] . "' value='" . $contactRoleIns['contactRoleID'] . "' checked />   <span class='smallText'>" . $contactRoleIns['shortName'] . "</span></td>\n";
							}else{
								echo "<td style='vertical-align:top;text-align:left;'><input class='check_roles' type='checkbox' name='" . $contactRoleIns['contactRoleID'] . "' id='" . $contactRoleIns['contactRoleID'] . "' value='" . $contactRoleIns['contactRoleID'] . "' />   <span class='smallText'>" . $contactRoleIns['shortName'] . "</span></td>\n";
							}
							if(($i % 3)==0){
								echo "</tr>\n";
							}
						}

						if(($i % 3)==1){
							echo "<td>&nbsp;</td><td>&nbsp;</td></tr>\n";
						}else if(($i % 3)==2){
							echo "<td>&nbsp;</td></tr>\n";
						}
					}
					?>
					</table>
					<span id='span_error_contactRole' class='smallDarkRedText'></span>
				</td>
				</tr>


				<tr>
				<td style='text-align:left'><label for='addressText'><b><?php echo _("Notes:");?></b></label></td>
				<td>
				<textarea rows='3' id='noteText' style='width:220px'><?php echo $contact->noteText; ?></textarea>
				</td>
				</tr>

				</table>


			</td>
			</tr>
			</table>

		</td>
		</tr>
		</table>



		<hr style='width:610px;margin:15px 0px 10px 0px;' />

		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitContactForm' id ='submitContactForm' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button'></td>
			</tr>
		</table>

		</form>
		</div>


		<script type="text/javascript" src="js/forms/contactForm.js?random=<?php echo rand(); ?>"></script>

