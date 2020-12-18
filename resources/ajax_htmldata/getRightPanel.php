<?php
	$resourceID = $_GET['resourceID'];
	$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
	$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

		$config=new Configuration();

    //get parents resources
    $sanitizedInstance = array();
    $instance = new Resource();
    $parentResourceArray = array();
    foreach ($resource->getParentResources() as $instance) {
      foreach (array_keys($instance->attributeNames) as $attributeName) {
        $sanitizedInstance[$attributeName] = $instance->$attributeName;
      }
      $sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;
      array_push($parentResourceArray, $sanitizedInstance);
    }


		//get children resources
		$childResourceArray = array();
		foreach ($resource->getChildResources() as $instance) {
			foreach (array_keys($instance->attributeNames) as $attributeName) {
				$sanitizedInstance[$attributeName] = $instance->$attributeName;
			}

			$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

			array_push($childResourceArray, $sanitizedInstance);
		}


		//get organizations (already returned in an array)
		$orgArray = $resource->getDistinctOrganizationArray();

		//get licenses (already returned in array)
		$licenseArray = $resourceAcquisition->getLicenseArray();

		echo "<div style='background-color:white; width:219px; padding:7px;'>";
		echo "<div class='rightPanelLink'><a href='summary.php?resourceID=" . $resource->resourceID . "&resourceAcquisitionID=" . $resourceAcquisitionID . "' target='_blank' class='helpfulLink'>"._("Print View")."</a></div>";
		if (($resource->systemNumber) && ($config->settings->catalogURL != '')) {
			echo "<div class='rightPanelLink'><a href='" . $config->settings->catalogURL . $resource->systemNumber . "' target='_blank'>"._("Catalog View")."</a></div>";
		}
		echo "</div>";

    if ((count($parentResourceArray) > 0) || (count($childResourceArray) > 0)){ ?>
			<div style='background-color:white; width:219px; padding:7px;'>
				<?php
        if ((count($parentResourceArray) > 0)){
          echo "<div class='rightPanelHeader'>"._("Parent Record(s)")."</div>";
          foreach ($parentResourceArray as $parentResource){
            $parentResourceObj = new Resource(new NamedArguments(array('primaryKey' => $parentResource['relatedResourceID'])));
              echo "<div class='rightPanelLink'><a href='resource.php?resourceID=" . $parentResourceObj->resourceID . "' target='_BLANK' class='helpfulLink'>" . $parentResourceObj->titleText . "</a></div>";
          }
        }

				if ((count($childResourceArray) > 0)){
					echo "<div class='rightPanelHeader'>"._("Child Record(s)")."</div>";

					$i = 0;
					foreach ($childResourceArray as $childResource){
						$i++;
						$childResourceObj = new Resource(new NamedArguments(array('primaryKey' => $childResource['resourceID'])));
						$initiallyHidden = $i > 20 ? 'helpfulLink__hidden' : '';
						echo "<div class='rightPanelLink'><a href='resource.php?resourceID=" . $childResourceObj->resourceID . "' target='_BLANK' class='helpfulLink ".$initiallyHidden."'>" . $childResourceObj->titleText . "</a></div>";
					    if($i === 20) {
                            echo "<div class='rightPanelLink'><a href='#' class='helpfulLink' id='showAllChildResources' style='padding-left: 10px;'>+ show all resources (" .(count($childResourceArray) - 20)." more)</a></div>";
                        }
					}
				}

				?>
			</div>

		<?php
		}

		if ((count($orgArray) > 0) && ($config->settings->organizationsModule == 'Y')){

		?>

			<div style='background-color:white; width:219px; padding:7px;'>
				<div class='rightPanelHeader'><?php echo _("Organizations Module");?></div>

				<?php
				foreach ($orgArray as $organization){
					echo "<div class='rightPanelLink'><a href='" . $util->getOrganizationURL() . $organization['organizationID'] . "' target='_blank' class='helpfulLink'>" . $organization['organization'] . "</a></div>";
				}

				?>
			</div>
		<?php
		}

		if ((count($licenseArray) > 0) && ($config->settings->licensingModule == 'Y')){

		?>
			<div style='background-color:white; width:219px; padding:7px;'>
				<div class='rightPanelHeader'><?php echo _("Licensing Module");?></div>

				<?php
				foreach ($licenseArray as $license){
					echo "<div class='rightPanelLink'><a href='" . $util->getLicensingURL() . $license['licenseID'] . "' target='_blank' class='helpfulLink'>" . $license['license'] . "</a></div>";
				}

				?>

			</div>

		<?php
		}
		$resourceType = new ResourceType(new NamedArguments(array('primaryKey' => $resource->resourceTypeID)));
		//echo $resourceType->shortName . " " . $resource->resourceTypeID;
		if (($resourceType->includeStats ==  1) && ($config->settings->usageModule == 'Y')){
		?>
			<div style='background-color:white; width:219px; padding:7px;'>
				<div class='rightPanelHeader'><?php echo _("Usage Statistics Module");?></div>

				<?php
			echo "<form method='post' action='../reports/report.php' target='_blank'>";
			echo "<input type='hidden' name='reportID' value='1'>";
			echo "<input type='hidden' name='prm_3' value='".$resource->titleText."'>";
			echo "<input type='submit' value='"._("Get Statistics")."'>";
			echo "</form>";
							?>

			</div>

		<?php
		}

?>

