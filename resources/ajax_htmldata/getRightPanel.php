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

		echo "<ul class='unstyled'>";
		echo "<li><a href='summary.php?resourceID=" . $resource->resourceID . "&resourceAcquisitionID=" . $resourceAcquisitionID . "' " . getTarget() . " class='helpfulLink'>"._("Print View")."</a></li>";
		if (($resource->systemNumber) && ($config->settings->catalogURL != '')) {
			echo "<li><a href='" . $config->settings->catalogURL . $resource->systemNumber . "' " . getTarget() . ">"._("Catalog View")."</a></li>";
		}
		echo "</ul>";

    if ((count($parentResourceArray) > 0) || (count($childResourceArray) > 0)){ ?>
			<div>
				<?php
        if ((count($parentResourceArray) > 0)){
          echo "<h4>"._("Parent Record(s)")."</h4>";
					echo "<ul class='unstyled'>";
          foreach ($parentResourceArray as $parentResource){
            $parentResourceObj = new Resource(new NamedArguments(array('primaryKey' => $parentResource['relatedResourceID'])));
              echo "<li><a href='resource.php?resourceID=" . $parentResourceObj->resourceID . "' " . getTarget() . " class='helpfulLink'>" . $parentResourceObj->titleText . "</a></li>";
          }
					echo "</ul>";
        }

				if ((count($childResourceArray) > 0)){
					echo "<h4>"._("Child Record(s)")."</h4>";
					echo "<ul class='unstyled'>";
					$i = 0;
					foreach ($childResourceArray as $childResource){
						$i++;
						$childResourceObj = new Resource(new NamedArguments(array('primaryKey' => $childResource['resourceID'])));
						$initiallyHidden = $i > 20 ? 'helpfulLink__hidden' : '';
						echo "<li><a href='resource.php?resourceID=" . $childResourceObj->resourceID . "' " . getTarget() . " class='helpfulLink ".$initiallyHidden."'>" . $childResourceObj->titleText . "</a></li>";
					    if ($i === 20) {
                echo "<li><a href='#' class='helpfulLink' id='showAllChildResources'>" . sprintf(_('+ show all resources (%d more)'), count($childResourceArray) - 20) . "</a></li>";
              }
					}
					echo "</ul>";
				}

				?>
			</div>

		<?php
		}

		if ((count($orgArray) > 0) && ($config->settings->organizationsModule == 'Y')){

		?>

			
				<h4><?php echo _("Organizations Module");?></h4>
				<ul class="unstyled">
				<?php
				foreach ($orgArray as $organization){
					echo "<li><a href='" . $util->getOrganizationURL() . $organization['organizationID'] . "' " . getTarget()  . " class='helpfulLink'>" . $organization['organization'] . "</a></li>";
				}

				?>
				</ul>
		<?php
		}

		if ((count($licenseArray) > 0) && ($config->settings->licensingModule == 'Y')){

		?>
			
				<h4><?php echo _("Licensing Module");?></h4>	
				<ul class="unstyled">
				<?php
				foreach ($licenseArray as $license){
					echo "<li><a href='" . $util->getLicensingURL() . $license['licenseID']  . "' " . getTarget() . " class='helpfulLink'>" . $license['license'] . "</a></li>";
				}

				?>
			</ul>

		<?php
		}
		$resourceType = new ResourceType(new NamedArguments(array('primaryKey' => $resource->resourceTypeID)));
		//echo $resourceType->shortName . " " . $resource->resourceTypeID;
		if (($resourceType->includeStats ==  1) && ($config->settings->usageModule == 'Y')){
		?>
			
				<h4><?php echo _("Usage Statistics Module");?></h4>

				<?php
				echo "<form method='post' action='../reports/report.php' " . getTarget() . ">";
				echo "<input type='hidden' name='reportID' value='1'>";
				echo "<input type='hidden' name='prm_3' value='".$resource->titleText."'>";
				echo "<input type='submit' value='"._("Get Statistics")."'>";
				echo "</form>";
				?>

		<?php
		}

?>

