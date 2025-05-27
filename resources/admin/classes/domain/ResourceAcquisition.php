<?php
class ResourceAcquisition extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}

	//returns array of notes objects
	public function getNotes($tabName = NULL) {

		if ($tabName) {
			$query = "SELECT * FROM ResourceNote RN
						WHERE entityID = '" . $this->resourceAcquisitionID . "'
						AND UPPER(tabName) = UPPER('" . $tabName . "')
						ORDER BY updateDate desc";
		}else{
			$query = "SELECT RN.*
						FROM ResourceNote RN
						LEFT JOIN NoteType NT ON NT.noteTypeID = RN.noteTypeID
						WHERE entityID = '" . $this->resourceAcquisitionID . "'
						ORDER BY updateDate desc, NT.shortName";
		}

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceNoteID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new ResourceNote(new NamedArguments(array('primaryKey' => $row['resourceNoteID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

	//returns array of the initial note object
	public function getInitialNote() {
		$noteType = new NoteType();

		$query = "SELECT * FROM ResourceNote RN
					WHERE entityID = '" . $this->resourceAcquisitionID . "'
					AND noteTypeID = " . $noteType->getInitialNoteTypeID() . "
					ORDER BY noteTypeID desc LIMIT 0,1";


		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceNoteID'])) {
			return new ResourceNote(new NamedArguments(array('primaryKey' => $result['resourceNoteID'])));
		} else{
			return new ResourceNote();
		}
	}


	//returns array of ResourceStep objects for this Resource
	public function getResourceSteps() {


		$query = "SELECT * FROM ResourceStep
					WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'
					ORDER BY (archivingDate IS NOT NULL), archivingDate DESC, displayOrderSequence, stepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new ResourceStep(new NamedArguments(array('primaryKey' => $row['resourceStepID'])));
			array_push($objects, $object);
		}

		return $objects;

	}

    public function getCurrentWorkflowID() {
        $query = "SELECT Step.workflowID FROM Step, ResourceStep
                    WHERE ResourceStep.resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'
                    AND ResourceStep.archivingDate IS NULL
                    AND ResourceStep.stepID = Step.stepID LIMIT 1";

		$result = $this->db->processQuery($query, 'assoc');
        return isset($result['workflowID']) ? $result['workflowID'] : NULL;
    }

    public function getCurrentWorkflowResourceSteps(){
		$query = "SELECT * FROM ResourceStep
					WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'
                    AND archivingDate IS NULL ORDER BY displayOrderSequence, stepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])){
			$object = new ResourceStep(new NamedArguments(array('primaryKey' => $result['resourceStepID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceStep(new NamedArguments(array('primaryKey' => $row['resourceStepID'])));
				array_push($objects, $object);
			}
		}

		return $objects;

	}

  public function isCurrentWorkflowComplete() {
    $status = new Status();
    $statusID = $status->getIDFromName('complete');
    $resource = new Resource(new NamedArguments(array('primaryKey' => $this->resourceID)));
    if ($resource->statusID == $statusID) {
      return true;
    }
    $steps = $this->getCurrentWorkflowResourceSteps();
    foreach ($steps as $step) {
      if (!$step->isComplete()) return false;
    }
    return true;
  }


    public function getDistinctWorkflows() {
        $query = "SELECT DISTINCT archivingDate FROM ResourceStep
					WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'
                    AND archivingDate IS NOT NULL
					ORDER BY archivingDate ASC";

		$result = $this->db->processQuery($query, 'assoc');

		return $result;

    }


    public function getArchivedResourceSteps() {
        return $this->getResourceSteps(true);
    }


	//returns first steps (object) in the workflow for this resource
	public function getFirstSteps() {

		$query = "SELECT * FROM ResourceStep
					WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'
					AND (priorStepID is null OR priorStepID = '0')
                    AND archivingDate IS NULL
					ORDER BY stepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new ResourceStep(new NamedArguments(array('primaryKey' => $row['resourceStepID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

    public function archiveWorkflow() {
        // And archive the workflow
        $query = "UPDATE ResourceStep SET archivingDate=NOW() WHERE archivingDate IS NULL AND resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";
		$result = $this->db->processQuery($query);
    }

    public function deleteWorkflow() {
        $query = "DELETE FROM ResourceStep WHERE archivingDate IS NULL AND resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";
		$result = $this->db->processQuery($query);
    }

	//enters resource into new workflow
	public function enterNewWorkflow($workflowID = null, $sendEmail = true){
		$config = new Configuration();

        $resource = new Resource(new NamedArguments(array('primaryKey' => $this->resourceID)));

		//make sure this resource is marked in progress in case it was archived
		$status = new Status();
		$resource->statusID = $status->getIDFromName('progress');
		$resource->save();


		//Determine the workflow this resource belongs to
		$workflowObj = new Workflow();

        if ($workflowID == null) {
            $workflowID = $workflowObj->getWorkflowID($resource->resourceTypeID, $resource->resourceFormatID, $this->acquisitionTypeID);
        }
		if ($workflowID){

			$workflow = new Workflow(new NamedArguments(array('primaryKey' => $workflowID)));
			$resourceTypeObj = new ResourceType();
            $resourceFormatObj = new ResourceFormat();
            $acquisitionTypeObj = new AcquisitionType();

            //set new resourceType, resourceFormat and acquisitionType for the resource, according to the selected workflow
            $resource->resourceTypeID = ($workflow->resourceTypeIDValue != null) ? $workflow->resourceTypeIDValue : $resourceTypeObj->getResourceTypeIDByName('any');
            $resource->resourceFormatID =  ($workflow->resourceFormatIDValue != null) ? $workflow->resourceFormatIDValue : $resourceFormatObj->getResourceFormatIDByName('any');
            $this->acquisitionTypeID = ($workflow->acquisitionTypeIDValue != null) ? $workflow->acquisitionTypeIDValue : $acquisitionTypeObj->getAcquisitionTypeIDByName('any');

            $resource->save();
            $this->save();

			//Copy all of the step attributes for this workflow to a new resource step
			foreach ($workflow->getSteps() as $step) {
				$resourceStep = new ResourceStep();

				$resourceStep->resourceStepID 		= '';
				$resourceStep->resourceAcquisitionID = $this->resourceAcquisitionID;
				$resourceStep->stepID 				= $step->stepID;
				$resourceStep->priorStepID			= $step->priorStepID;
				$resourceStep->stepName				= $step->stepName;
                $resourceStep->stepStartDate        = '';
                $resourceStep->stepEndDate          = '';
                $resourceStep->archivingDate        = '';
                $resourceStep->endLoginID           = '';
				$resourceStep->userGroupID			= $step->userGroupID;
				$resourceStep->displayOrderSequence	= $step->displayOrderSequence;

				$resourceStep->save();

			}

			//Start the first step
			//this handles updating the db and sending notifications for approval groups
			foreach ($this->getFirstSteps() as $resourceStep) {
				$resourceStep->startStep($sendEmail);

			}
		}


		//send an email notification to the feedback email address and the creator
		$cUser = new User(new NamedArguments(array('primaryKey' => $this->createLoginID)));
		$acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $this->acquisitionTypeID)));

		$firstName = ($cUser->firstName) ?? FALSE;
		$lastName = ($cUser->lastName) ?? FALSE;
		$loginID = ($this->createLoginID) ?? FALSE;
		$creator = 	($firstName && $lastName) ? "{$firstName} {$lastName}" : //Use first and last name if they exist.
					(($loginID) ? $loginID : //Otherwise use the create login id.
					"(unknown user)"); //Otherwise use unknown user.

		if (($config->settings->feedbackEmailAddress) || ($cUser->emailAddress)) {
			$email = new Email();
			$util = new Utility();

			$email->message = $util->createMessageFromTemplate('NewResourceMain', $this->resourceID, $resource->titleText, '', '', $creator);

			if ($cUser->emailAddress) {
				$emailTo[] 			= $cUser->emailAddress;
			}

			if ($config->settings->feedbackEmailAddress != '') {
				$emailTo[] 			=  $config->settings->feedbackEmailAddress;
			}

			$email->to = implode(",", $emailTo);

			if ($acquisitionType->shortName) {
				$email->subject		= "CORAL Alert: New " . $acquisitionType->shortName . " Resource Added: " . $resource->titleText;
			}else{
				$email->subject		= "CORAL Alert: New Resource Added: " . $resource->titleText;
			}

			$email->send();

		}

	}

	//completes a workflow (changes status to complete and sends notifications to creator and "master email")
	public function completeWorkflow() {
		$config = new Configuration();
		$util = new Utility();
		$status = new Status();
		$statusID = $status->getIDFromName('complete');
        $resource = new Resource(new NamedArguments(array('primaryKey' => $this->resourceID)));

		if ($statusID) {
			$resource->statusID = $statusID;
			$resource->save();
		}

		//send notification to creator and master email address
		$cUser = new User(new NamedArguments(array('primaryKey' => $resource->createLoginID)));

		//formulate emil to be sent
		$email = new Email();
		$email->message = $util->createMessageFromTemplate('CompleteResource', $resource->resourceID, $resource->titleText, '', $resource->systemNumber, '');

		$creatorUserEmail = ($cUser->emailAddress) ?? FALSE;
		$configAddress = ($config->settings->feedbackEmailAddress) ?? '';
		$emailTo = [];
		if($creatorUserEmail){
			$emailTo[] = $creatorUserEmail;
		}
		if($configAddress !== ''){
			$emailTo[] = $configAddress;
		}
		$emailsExist = (count($emailTo) > 0);
		if($emailsExist){
			$email->to = implode(",", $emailTo);

			$email->subject		= "CORAL Alert: Workflow completion for " . $resource->titleText;

			$email->send();
		}
	}




	private function getDownTimeResults($archivedOnly=false) {
		$query = "SELECT d.*
					FROM Downtime d
					WHERE d.resourceAcquisitionID='{$this->resourceAcquisitionID}' AND d.entityTypeID=2";
		if ($archivedOnly) {
			$query .= " AND d.endDate < CURDATE()";
		} else {
			$query .= " AND (d.endDate >= CURDATE() OR d.endDate IS NULL)";
		}
		$query .= "	ORDER BY d.dateCreated DESC";

		return $this->db->processQuery($query, 'assoc');
	}



	public function getDowntime($archivedOnly=false) {
		$result = $this->getDownTimeResults($archivedOnly);

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['downtimeID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new Downtime(new NamedArguments(array('primaryKey' => $row['downtimeID'])));
			array_push($objects, $object);
		}
		return $objects;
	}


	public function getIssues($archivedOnly=false) {
		$query = "SELECT i.*
					FROM Issue i
					LEFT JOIN IssueRelationship ir ON ir.issueID=i.issueID
					WHERE ir.resourceAcquisitionID='$this->resourceAcquisitionID' AND ir.entityTypeID=2";
		if ($archivedOnly) {
			$query .= " AND i.dateClosed IS NOT NULL";
		} else {
			$query .= " AND i.dateClosed IS NULL";
		}
		$query .= "	ORDER BY i.dateCreated DESC";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['issueID'])){ $result = [$result]; }
		foreach ($result as $row) {
			$object = new Issue(new NamedArguments(array('primaryKey' => $row['issueID'])));
			array_push($objects, $object);
		}
		return $objects;
	}

	//returns array of associated licenses
	public function getLicenseArray() {
		$config = new Configuration;

		//if the lic module is installed get the lic name from lic database
		if ($config->settings->licensingModule == 'Y') {
			$dbName = $config->settings->licensingDatabaseName;

			$resourceLicenseArray = array();

			$query = "SELECT * FROM ResourceLicenseLink WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['licenseID'])) {
				$licArray = array();

				//first, get the license name
				$query = "SELECT shortName FROM " . $dbName . ".License WHERE licenseID = " . $result['licenseID'];

				if ($licResult = $this->db->query($query)) {
					while ($licRow = $licResult->fetch_assoc()) {
						$licArray['license'] = $licRow['shortName'];
						$licArray['licenseID'] = $result['licenseID'];
						$licArray['resourceLicenseLinkID'] = $result['resourceLicenseLinkID'];
					}
				}

				array_push($resourceLicenseArray, $licArray);
			}else{
				foreach ($result as $row) {
					$licArray = array();

					//first, get the license name
					$query = "SELECT shortName FROM " . $dbName . ".License WHERE licenseID = " . $row['licenseID'];

					if ($licResult = $this->db->query($query)) {
						while ($licRow = $licResult->fetch_assoc()) {
							$licArray['license'] = $licRow['shortName'];
							$licArray['licenseID'] = $row['licenseID'];
                            $licArray['resourceLicenseLinkID'] = $row['resourceLicenseLinkID'];
						}
					}

					array_push($resourceLicenseArray, $licArray);

				}

			}

			return $resourceLicenseArray;
		}
	}

	//returns array of resource license status objects
	public function getResourceLicenseStatuses() {

		$query = "SELECT * FROM ResourceLicenseStatus WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "' ORDER BY licenseStatusChangeDate desc;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceLicenseStatusID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new ResourceLicenseStatus(new NamedArguments(array('primaryKey' => $row['resourceLicenseStatusID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

	//returns LicenseStatusID of the most recent resource license status
	public function getCurrentResourceLicenseStatus() {

		$query = "SELECT licenseStatusID FROM ResourceLicenseStatus RLS WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "' AND licenseStatusChangeDate = (SELECT MAX(licenseStatusChangeDate) FROM ResourceLicenseStatus WHERE ResourceLicenseStatus.resourceAcquisitionID = '" . $this->resourceAcquisitionID . "') LIMIT 0,1;";

		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['licenseStatusID'])) {
			return $result['licenseStatusID'];
		}
	}

    //removes this order
    public function removeResourceAcquisition() {
        //delete data from child linked tables
        $this->deleteResourcePayments();
        $this->deleteLicenses();
        $this->deleteAccess();
        $this->deleteContacts();
        $this->deleteAttachments();
        $this->deleteNotes();
        $this->delete();
    }


	//removes resource licenses
	public function removeResourceLicenses() {

		$query = "DELETE
			FROM ResourceLicenseLink
			WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";

		$result = $this->db->processQuery($query);
	}



	//removes resource license statuses
	public function removeResourceLicenseStatuses() {

		$query = "DELETE
			FROM ResourceLicenseStatus
			WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";

		$result = $this->db->processQuery($query);
	}



    // Copy what was linked to the resourceAcquisition given in parameter to this one
    public function cloneFrom($sourceID) {
        $source = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $sourceID)));

        // Copy cost history
        $this->cloneResourcePayments($source);

        // Copy licenses
        $this->cloneLicenses($source);

        // Copy access
        $this->cloneAccess($source);

        // Copy cataloging
        // Nothing to do, already cloned in ResourceAcquisition

        // Copy contacts
        $this->cloneContacts($source);

        // Copy attachments
        $this->cloneAttachments($source);
    }

    public function cloneAttachments($source) {
        foreach ($source->getAttachments() as $s) {

            $s->attachmentID = null;
            $newID = $s->saveAsNew();
            $query = "UPDATE Attachment SET ResourceAcquisitionID=" . $this->resourceAcquisitionID . " WHERE attachmentID=" . $newID;
            $result = $this->db->processQuery($query);
        }
    }

    public function cloneContacts($source) {
        foreach ($source->getUnarchivedContacts() as $s) {
            if ($s['contactID'] != null && $s['contactID'] != '') {
                $c = new Contact(new NamedArguments(array('primaryKey' => $s['contactID'])));
                $contactRoles = $c->getContactRoles();
                $c->contactID = null;
                $newID = $c->saveAsNew();
                if ($newID) {
                    $query = "UPDATE Contact SET ResourceAcquisitionID=" . $this->resourceAcquisitionID . " WHERE contactID=" . $newID;
                    $result = $this->db->processQuery($query);
                    foreach ($contactRoles as $contactRole) {
                        $query = "INSERT INTO ContactRoleProfile(contactID, contactRoleID) VALUES ($newID, " . $contactRole->contactRoleID . ")";
                        $result = $this->db->processQuery($query);
                    }
                }
            }
        }
    }

    public function cloneResourcePayments($source) {
        foreach ($source->getResourcePayments() as $srp) {
           $srp->resourcePaymentID = null;
           $newRPID = $srp->saveAsNew();
           $query = "UPDATE ResourcePayment SET resourceAcquisitionID=" . $this->resourceAcquisitionID . " WHERE resourcePaymentID=" . $newRPID;
           $result = $this->db->processQuery($query);
        }
    }

    public function cloneAccess($source) {
        foreach ($source->getAdministeringSitesLinks() as $s) {
            $s->resourceAdministeringSiteLinkID = null;
            $newID = $s->saveAsNew();
            $query = "UPDATE ResourceAdministeringSiteLink SET resourceAcquisitionID=" . $this->resourceAcquisitionID . " WHERE resourceAdministeringSiteLinkID=" . $newID;
           $result = $this->db->processQuery($query);
        }
        foreach ($source->getAuthorizedSitesLinks() as $s) {
            $s->resourceAuthorizedSiteLinkID = null;
            $newID = $s->saveAsNew();
            $query = "UPDATE ResourceAuthorizedSiteLink SET resourceAcquisitionID=" . $this->resourceAcquisitionID . " WHERE resourceAuthorizedSiteLinkID=" . $newID;
           $result = $this->db->processQuery($query);
        }
    }

    public function cloneLicenses($source) {
        foreach ($source->getLicenseArray() as $s) {
            $rll = new ResourceLicenseLink();
            $rll->resourceAcquisitionID = $this->resourceAcquisitionID;;
            $rll->licenseID = $s['licenseID'];
            $rll->save;
        }

        foreach ($source->getResourceLicenseStatuses() as $s) {
            $s->resourceLicenseStatusID = null;
            $newID = $s->saveAsNew();
            $query = "UPDATE ResourceLicenseStatus SET resourceAcquisitionID=" . $this->resourceAcquisitionID . " WHERE resourceLicenseStatusID=" . $newID;
            $result = $this->db->processQuery($query);
        }
    }

    public function deleteResourcePayments() {
        foreach ($this->getResourcePayments() as $srp) {
            $srp->delete();
        }
    }

    public function deleteAccess() {
        foreach ($this->getAdministeringSitesLinks() as $s) {
            $s->delete();
        }
        foreach ($this->getAuthorizedSitesLinks() as $s) {
            $s->delete();
        }
    }

    public function deleteLicenses() {
        foreach ($this->getLicenseArray() as $s) {
            $rll = new ResourceLicenseLink(new NamedArguments(array('primaryKey' => $s['resourceLicenseLinkID'])));
            $rll->delete();
        }
        foreach ($this->getResourceLicenseStatuses() as $s) {
            $s->delete();
        }
    }

    public function deleteContacts() {
        foreach ($this->getContacts() as $c) {
            $c->removeContactRoles();
            $c->delete();
        }
    }

    public function deleteAttachments() {
        foreach ($this->getAttachments() as $s) {
            $s->delete();
        }
    }

    public function deleteNotes() {
        foreach (array("Acquisitions", "Access", "Cataloging") as $tabName) {
            foreach ($this->getNotes($tabName) as $s) {
                $s->delete();
            }
        }
    }

	//returns array of contact objects
	public function getUnarchivedContacts($moduleFilter=false) {
		$config = new Configuration;
		$contactsArray = array();

		if (!$moduleFilter || $moduleFilter == 'resources') {
			//get resource specific contacts first
			$query = "SELECT C.*, GROUP_CONCAT(CR.shortName SEPARATOR '<br /> ') contactRoles
				FROM Contact C, ContactRole CR, ContactRoleProfile CRP
				WHERE (archiveDate = '0000-00-00' OR archiveDate is null)
				AND C.contactID = CRP.contactID
				AND CRP.contactRoleID = CR.contactRoleID
				AND resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'
				GROUP BY C.contactID
				ORDER BY C.name";

			$result = $this->db->processQuery($query, 'assoc');

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['contactID'])) { $result = [$result]; }
			foreach ($result as $row) {
				array_push($contactsArray, $row);
			}
		}


		//if the org module is installed also get the org contacts from org database
		if ($config->settings->organizationsModule == 'Y' && (!$moduleFilter || $moduleFilter == 'organizations')) {
			$dbName = $config->settings->organizationsDatabaseName;

			$query = "SELECT distinct OC.*, O.name organizationName, GROUP_CONCAT(DISTINCT CR.shortName SEPARATOR '<br /> ') contactRoles
					FROM " . $dbName . ".Contact OC, " . $dbName . ".ContactRole CR, " . $dbName . ".ContactRoleProfile CRP, " . $dbName . ".Organization O, Resource R, ResourceAcquisition RA, ResourceOrganizationLink ROL
					WHERE (OC.archiveDate = '0000-00-00' OR OC.archiveDate is null)
					AND R.resourceID = ROL.resourceID
					AND ROL.organizationID = OC.organizationID
					AND CRP.contactID = OC.contactID
					AND CRP.contactRoleID = CR.contactRoleID
					AND O.organizationID = OC.organizationID
                    AND R.resourceID = RA.resourceID
					AND RA.resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'
					GROUP BY OC.contactID, O.name
					ORDER BY OC.name";

			$result = $this->db->processQuery($query, 'assoc');

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['contactID'])) { $result = [$result]; }
			foreach ($result as $row) {
				array_push($contactsArray, $row);
			}

		}
		return $contactsArray;
	}


	//returns array of contact objects
	public function getArchivedContacts() {

		$config = new Configuration;
		$contactsArray = array();

		//get resource specific contacts
		$query = "SELECT C.*, GROUP_CONCAT(CR.shortName SEPARATOR '<br /> ') contactRoles
			FROM Contact C, ContactRole CR, ContactRoleProfile CRP
			WHERE (archiveDate != '0000-00-00' && archiveDate != '')
			AND C.contactID = CRP.contactID
			AND CRP.contactRoleID = CR.contactRoleID
			AND resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'
			GROUP BY C.contactID
			ORDER BY C.name";

		$result = $this->db->processQuery($query, 'assoc');


		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['contactID'])) { $result = [$result]; }
		foreach ($result as $row) {
			array_push($contactsArray, $row);
		}

		//if the org module is installed also get the org contacts from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;

			$query = "SELECT DISTINCT OC.*, O.name organizationName, GROUP_CONCAT(DISTINCT CR.shortName SEPARATOR '<br /> ') contactRoles
					FROM " . $dbName . ".Contact OC, " . $dbName . ".ContactRole CR, " . $dbName . ".ContactRoleProfile CRP, " . $dbName . ".Organization O, Resource R, ResourceAcquisition RA, ResourceOrganizationLink ROL
					WHERE (OC.archiveDate != '0000-00-00' && OC.archiveDate is not null)
					AND R.resourceID = ROL.resourceID
					AND ROL.organizationID = OC.organizationID
					AND CRP.contactID = OC.contactID
					AND CRP.contactRoleID = CR.contactRoleID
					AND O.organizationID = OC.organizationID
                    AND R.resourceID = RA.resourceID
					AND RA.resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'
					GROUP BY OC.contactID, O.name
					ORDER BY OC.name";


			$result = $this->db->processQuery($query, 'assoc');


			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['contactID'])) { $result = [$result]; }
			foreach ($result as $row) {
				array_push($contactsArray, $row);
			}

		}
		return $contactsArray;
	}

	//returns array of contact objects
	public function getContacts() {

		$query = "SELECT * FROM Contact
					WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['contactID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new Contact(new NamedArguments(array('primaryKey' => $row['contactID'])));
			array_push($objects, $object);
		}

		return $objects;
	}


	//removes payment records
	public function removeResourcePayments() {

		$query = "DELETE
			FROM ResourcePayment
			WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";

		$result = $this->db->processQuery($query);
	}

	//returns array of ResourcePayment objects
	public function getResourcePayments() {

		$query = "SELECT * FROM ResourcePayment WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "' ORDER BY year DESC, subscriptionStartDate DESC";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourcePaymentID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new ResourcePayment(new NamedArguments(array('primaryKey' => $row['resourcePaymentID'])));
			array_push($objects, $object);
		}

		return $objects;
	}


	//returns array of attachments objects
	public function getAttachments() {

		$query = "SELECT * FROM Attachment A, AttachmentType AT
					WHERE AT.attachmentTypeID = A.attachmentTypeID
					AND resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'
					ORDER BY AT.shortName";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['attachmentID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new Attachment(new NamedArguments(array('primaryKey' => $row['attachmentID'])));
			array_push($objects, $object);
		}

		return $objects;
	}


	//removes resourceAcquisition authorized sites
	public function removeAuthorizedSites() {

		$query = "DELETE
			FROM ResourceAuthorizedSiteLink
			WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";

		$result = $this->db->processQuery($query);
	}

    public function getOrganization() {

        if (!$this->organizationID) return null;

		$config = new Configuration;
        $dbName = $config->settings->organizationsDatabaseName;

		$query = ($config->settings->organizationsModule == 'Y') ?
            "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $this->organizationID :
            "SELECT shortName AS name FROM Organization WHERE organizationID = " . $this->organizationID;

        if ($orgResult = $this->db->query($query)) {
            while ($orgRow = $orgResult->fetch_assoc()) {
                $orgArray['organization'] = $orgRow['name'];
                $orgArray['organizationID'] = $this->organizationID;;
            }
        }

        return $orgArray;
    }

	public function hasCatalogingInformation() {
		return ($this->recordSetIdentifier || $this->recordSetIdentifier || $this->bibSourceURL || $this->catalogingTypeID || $this->catalogingStatusID || $this->numberRecordsAvailable || $this->numberRecordsLoaded || $this->hasOclcHoldings);
	}

	//removes resourceAcquisition administering sites
	public function removeAdministeringSites() {

		$query = "DELETE
			FROM ResourceAdministeringSiteLink
			WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";

		$result = $this->db->processQuery($query);
	}


	//removes resource purchase sites
	public function removePurchaseSites() {

		$query = "DELETE
			FROM ResourcePurchaseSiteLink
			WHERE resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";

		$result = $this->db->processQuery($query);
	}

	//returns array of purchase site objects
	public function getPurchaseSites() {

		$query = "SELECT PurchaseSite.* FROM PurchaseSite, ResourcePurchaseSiteLink RPSL where RPSL.purchaseSiteID = PurchaseSite.purchaseSiteID AND resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['purchaseSiteID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new PurchaseSite(new NamedArguments(array('primaryKey' => $row['purchaseSiteID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

	//returns array of authorized site objects
	public function getAuthorizedSites() {

		$query = "SELECT AuthorizedSite.* FROM AuthorizedSite, ResourceAuthorizedSiteLink RPSL where RPSL.authorizedSiteID = AuthorizedSite.authorizedSiteID AND resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['authorizedSiteID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new AuthorizedSite(new NamedArguments(array('primaryKey' => $row['authorizedSiteID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

    public function getAuthorizedSitesLinks() {
        $query = "SELECT * FROM ResourceAuthorizedSiteLink where resourceAcquisitionID=" . $this->resourceAcquisitionID;

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceAuthorizedSiteLinkID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new ResourceAuthorizedSiteLink(new NamedArguments(array('primaryKey' => $row['resourceAuthorizedSiteLinkID'])));
			array_push($objects, $object);
		}

		return $objects;
    }

    public function getPurchaseSitesLinks() {
        $query = "SELECT * FROM ResourcePurchaseSiteLink where resourceAcquisitionID=" . $this->resourceAcquisitionID;

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourcePurchaseSiteLinkID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new ResourcePurchaseSiteLink(new NamedArguments(array('primaryKey' => $row['resourcePurchaseSiteLinkID'])));
			array_push($objects, $object);
		}

		return $objects;
    }

    public function getAdministeringSitesLinks() {
        $query = "SELECT * FROM ResourceAdministeringSiteLink where resourceAcquisitionID=" . $this->resourceAcquisitionID;

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceAdministeringSiteLinkID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new ResourceAdministeringSiteLink(new NamedArguments(array('primaryKey' => $row['resourceAdministeringSiteLinkID'])));
			array_push($objects, $object);
		}

		return $objects;
    }

	//returns array of administering site objects
	public function getAdministeringSites() {

		$query = "SELECT AdministeringSite.* FROM AdministeringSite, ResourceAdministeringSiteLink RPSL where RPSL.administeringSiteID = AdministeringSite.administeringSiteID AND resourceAcquisitionID = '" . $this->resourceAcquisitionID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['administeringSiteID'])) { $result = [$result]; }
		foreach ($result as $row) {
			$object = new AdministeringSite(new NamedArguments(array('primaryKey' => $row['administeringSiteID'])));
			array_push($objects, $object);
		}

		return $objects;
	}

    // Returns true if today is between the order's subscription start date and end date
    // Returns false otherwise
    public function isActiveToday() {
        $start = new DateTime($this->subscriptionStartDate);
        $end = new DateTime($this->subscriptionEndDate);
        $now = new DateTime(date("Y-m-d"));
        if ($this->subscriptionStartDate && $this->subscriptionEndDate) {
            return ($start <= $now && $end >= $now) ? true : false;
        }
        if ($this->subscriptionStartDate && !$this->subscriptionEndDate) {
            return ($start <= $now) ? true : false;
        }
        if (!$this->subscriptionStartDate && $this->subscriptionEndDate) {
            return ($end >= $now) ? true : false;
        }
        return false;
    }

}

?>
