<?php
		//if this is an existing contact
		if (isset($_POST['contactID'])) $contactID=$_POST['contactID']; else $contactID='';

		if ($contactID){
			$contact = new Contact(new NamedArguments(array('primaryKey' => $contactID)));
		}else{
			//set up new contact
			$contact = new Contact();
			$contact->contactID	= '';
		}

		$contact->lastUpdateDate = date( 'Y-m-d H:i:s' );
		$contact->resourceAcquisitionID = $_POST['resourceAcquisitionID'] ? $_POST['resourceAcquisitionID'] : $contact->resourceAcquisitionID;
		$contact->name           = $_POST['name'];
		$contact->title          = $_POST['title'];
		$contact->addressText    = $_POST['addressText'];
		$contact->phoneNumber    = $_POST['phoneNumber'];
		$contact->altPhoneNumber = $_POST['altPhoneNumber'];
		$contact->faxNumber      = $_POST['faxNumber'];
		$contact->emailAddress   = $_POST['emailAddress'];
		$contact->noteText       = $_POST['noteText'];

		if (((!$contact->archiveDate) || ($contact->archiveDate == '0000-00-00')) && ($_POST['archiveInd'] == "1")){
			$contact->archiveDate = date( 'Y-m-d H:i:s' );
		}else if ($_POST['archiveInd'] == "0"){
			$contact->archiveDate = '';
		}

		try {
			$contact->save();

			if (!$contactID){
				$contactID=$contact->primaryKey;
			}

			//first remove all orgs, then we'll add them back
			$contact->removeContactRoles();

			foreach (explode(',', $_POST['contactRoles']) as $id){
				if ($id){
					$contactRoleProfile = new ContactRoleProfile();
					$contactRoleProfile->contactID = $contactID;
					$contactRoleProfile->contactRoleID = $id;
					$contactRoleProfile->save();
				}
			}


		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
