<?php
		$resourceID = $_POST['resourceID'];

		//get this resource
		$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

		$resource->updateLoginID 		= $loginID;
		$resource->updateDate			= date( 'Y-m-d H:i:s' );

		$resource->titleText 			= $_POST['titleText'];
		$resource->descriptionText 		= $_POST['descriptionText'];
		$resource->resourceFormatID 	= $_POST['resourceFormatID'];
		$resource->resourceTypeID 		= $_POST['resourceTypeID'];
		$resource->resourceURL 			= $_POST['resourceURL'];
		$resource->resourceAltURL 		= $_POST['resourceAltURL'];

    $isbnarray = json_decode($_POST['isbnOrISSN']);
    $resource->setIsbnOrIssn($isbnarray);

		//to determine status id
		$status = new Status();

		if (((!$resource->archiveDate) || ($resource->archiveDate == '0000-00-00')) && ($_POST['archiveInd'] == "1")){
			$resource->archiveDate = date( 'Y-m-d' );
			$resource->archiveLoginID = $loginID;
			$resource->statusID = $status->getIDFromName('archive');
		}else if ($_POST['archiveInd'] == "0"){
			//if archive date is currently set and being removed, mark status as complete
			if (($resource->archiveDate != '') && ($resource->archiveDate != '0000-00-00')){
				$resource->statusID = $status->getIDFromName('complete');
			}
			$resource->archiveDate = '';
			$resource->archiveLoginID = '';
		}



		try {
			$resource->save();

		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}


		//update resource relationship (currently code only allows parent)
		//first remove the existing relationship then add it back
		$resource->removeParentResources();

    if (($_POST['parentResourcesID'])){
      $parentResourcesArray = json_decode($_POST['parentResourcesID']);
      foreach($parentResourcesArray as $parentResource) {
        $resourceRelationship = new ResourceRelationship();
        $resourceRelationship->resourceID = $resourceID;
        $resourceRelationship->relatedResourceID = $parentResource;
        $resourceRelationship->relationshipTypeID = '1';  //hardcoded because we're only allowing parent relationships
        try {
          $resourceRelationship->save();
        } catch (Exception $e) {
					echo "<span class='error'>";
          echo $e->getMessage();
					echo "</span>";
        }
      }
    }

		//next, delete and then re-insert the aliases
		$alias = new Alias();
		foreach ($resource->getAliases() as $alias) {
			$alias->delete();
		}

		$aliasTypeArray = array();
		$aliasTypeArray = explode(':::', $_POST['aliasTypes']);
		$aliasNameArray = array();
		$aliasNameArray = explode(':::', $_POST['aliasNames']);


		foreach ($aliasTypeArray as $key => $value){
			if (($value) && ($aliasNameArray[$key])){
				$alias = new Alias();
				$alias->resourceID = $resourceID;
				$alias->aliasTypeID = $value;
				$alias->shortName = $aliasNameArray[$key];

				$alias->save();


			}
		}



		//now delete and then re-insert the organizations
		$resource->removeResourceOrganizations();

		$organizationRoleArray = array();
		$organizationRoleArray = explode(':::', $_POST['organizationRoles']);
		$organizationArray = array();
		$organizationArray = explode(':::', $_POST['organizations']);


		foreach ($organizationRoleArray as $key => $value){
			if (($value) && ($organizationArray[$key])){
				$resourceOrganizationLink = new ResourceOrganizationLink();
				$resourceOrganizationLink->resourceID = $resourceID;
				$resourceOrganizationLink->organizationRoleID = $value;
				$resourceOrganizationLink->organizationID = $organizationArray[$key];

				$resourceOrganizationLink->save();
			}
		}

?>
