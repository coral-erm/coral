<?php

		$resourceID = ($_GET['resourceID']) ?? FALSE;
		if ($resourceID){
		$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
		}else{
			$resource = new Resource();
		}

        // get resource acquisition for this resource 
        // at this point, there are none (resource not saved yet)
        // or only one (resource saved as draft)
        if ($resource->resourceID) {
            $resourceAcquisitions = $resource->getResourceAcquisitions();
            $resourceAcquisition = $resourceAcquisitions[0];
        }

		//used for default currency
		$config = new Configuration();

		//get all acquisition types for output in drop down
		$acquisitionTypeArray = array();
		$acquisitionTypeObj = new AcquisitionType();
		$acquisitionTypeArray = $acquisitionTypeObj->sortedArray();

		//get all resource formats for output in drop down
		$resourceFormatArray = array();
		$resourceFormatObj = new ResourceFormat();
		$resourceFormatArray = $resourceFormatObj->sortedArray();

		//get all resource types for output in drop down
		$resourceTypeArray = array();
		$resourceTypeObj = new ResourceType();
		$resourceTypeArray = $resourceTypeObj->allAsArray();


		//get all currency for output in drop down
		$currencyArray = array();
		$currencyObj = new Currency();
		$currencyArray = $currencyObj->allAsArray();

		//get all Order Types for output in drop down
		$orderTypeArray = array();
		$orderTypeObj = new OrderType();
		$orderTypeArray = $orderTypeObj->allAsArray();

		//get all Cost Details for output in drop down
		$costDetailsArray = array();
		$costDetailsObj = new CostDetails();
		$costDetailsArray = $costDetailsObj->allAsArray();

		//get notes
		if ($resourceID){
			$resourceNote = $resource->getInitialNote;
		}else{
			$resourceNote = new ResourceNote();
		}

		$orgArray = $resource->getOrganizationArray();
		if (count($orgArray)>0){
			foreach ($orgArray as $org){
				$providerText = $org['organization'];
				$orgID = $org['organizationID'];
			}
		}else{
			$providerText = $resource->providerText;
			$orgID = '';
		}
?>
		<div id='div_resourceSubmitForm'>
		<form id='resourcePromptForm' class="large">


		<input type='hidden' id='organizationID' value='<?php echo $orgID; ?>' />
		<input type='hidden' id='editResourceID' value='<?php echo $resourceID; ?>' />
		<h2><?php if ($resourceID) { echo _("Edit Saved Resource"); }else{ echo _("Add New Resource"); } ?></h2>
		<p class='required'><?php echo _("* required fields");?></p>
		
		<h3><?php echo _("Product");?></h3>
		<div class="flex flex-auto">
		<div class="form-grid">
		<label for='titleText'><?php echo _("Name:");?> 
			<span class='required'>*</span>
		</label>
		<input required type='text' id='titleText' class='changeInput' value="<?php echo $resource->titleText; ?>" aria-describedby="span_error_titleText" />
		<span id='span_error_titleText' class='error'></span>
	
		<label for='descriptionText'><?php echo _("Description:");?></label>
		<textarea rows='3' id='descriptionText'><?php echo $resource->descriptionText; ?></textarea>
	
		<label for='providerText'><?php echo _("Provider:");?></label>
		<input type='text' id='providerText' class='changeInput' value='<?php echo $providerText; ?>' aria-describedby="span_error_providerText" />
		<span id='span_error_providerText' class='error'></span>
		
		<label for='resourceURL'><?php echo _("URL:");?></label>
		<input type='url' id='resourceURL' class='changeInput' value='<?php echo $resource->resourceURL; ?>' aria-describedby="span_error_resourceURL" />
		<span id='span_error_resourceURL' class='error'></span>
		
		<label for='resourceAltURL'><?php echo _("Alt URL:");?></label>
		<input type='url' id='resourceAltURL' class='changeInput' value='<?php echo $resource->resourceAltURL; ?>' aria-describedby="span_error_resourceAltURL" />
		<span id='span_error_resourceAltURL' class='error'></span>
		
		<fieldset class="subgrid">	
			<legend class="fw-normal"><?php echo _("Format");?> <span class='required'>*</span></legend>
			<div class="form-group">
				<p id='span_error_resourceFormatID' class='error'></p>
			
				<ul class="unstyled">
				<?php
					foreach ($resourceFormatArray as $resourceFormat){
						$checked = '';
						//determine default
						if ($resourceID){
							if ($resourceFormat['resourceFormatID'] == $resource->resourceFormatID) $checked = 'checked';
						//otherwise default to electronic
						}else{
							if (strtoupper($resourceFormat['shortName']) == 'ELECTRONIC') $checked = 'checked';
						}

						echo "<li class='checkbox'><input required type='radio' name='resourceFormatID' id='resourceFormatID-" . $resourceFormat['resourceFormatID'] . "' value='" . $resourceFormat['resourceFormatID'] . "' " . $checked . " aria-describedby='span_error_resourceFormatID' />";
						echo "<label for='resourceFormatID-" . $resourceFormat['resourceFormatID'] . "'>" . $resourceFormat['shortName'] . "</label></li>";
					}

					?>
					</ul>
				</div>
		</fieldset>
		<fieldset class="subgrid">
			<legend class="fw-normal"><?php echo _("Acquisition Type");?> <span class='required'>*</span></legend>
			<div class="form-group">
				<ul class="unstyled">
					<?php
					foreach ($acquisitionTypeArray as $acquisitionType){
						$checked = '';
						//set default
						if ($resourceID){
							if ($acquisitionType['acquisitionTypeID'] == $resourceAcquisition->acquisitionTypeID) $checked = 'checked';
						}else{
							if (strtoupper($acquisitionType['shortName']) == 'PAID') $checked = 'checked';
						}

						echo "<li class='checkbox'><input required type='radio' name='acquisitionTypeID' id='acquisitionTypeID-" . $acquisitionType['acquisitionTypeID'] . "' value='" . $acquisitionType['acquisitionTypeID'] . "' " . $checked . " />";
						echo "<label for='acquisitionTypeID-" . $acquisitionType['acquisitionTypeID'] . "'>" . $acquisitionType['shortName'] . "</label></li>\n";
					}

					?>
				</ul>
			</div>
		</fieldset>
	</div>
	<div class="form-grid">
		<fieldset class="subgrid">
			<legend class="fw-normal"><?php echo _("Resource Type");?> <span class='required'>*</span></legend>
			<div class="form-group">
				<p id='span_error_resourceTypeID' class='error'></p>
				<ul class="unstyled">
					<?php
					
					foreach ($resourceTypeArray as $resourceType){

						$checked='';
						//determine default checked
						if ($resourceID){
							if (strtoupper($resourceType['resourceTypeID']) == $resource->resourceTypeID) $checked = 'checked';
						}

						echo "<li class='checkbox'><input required type='radio' name='resourceTypeID' id='resourceTypeID-" . $resourceType['resourceTypeID'] . "' value='" . $resourceType['resourceTypeID'] . "' " . $checked . " aria-describedby='span_error_resourceTypeID' />";
						echo "<label for='resourceTypeID-" . $resourceType['resourceTypeID'] . "'>" . $resourceType['shortName'] . "</label></li>\n";

					}

					?>
				</ul>
			</div>
		</fieldset>
		

		<label for="noteText"><?php echo _("Notes");?></label>
		<textarea rows='5' id='noteText' name='noteText'><?php echo $resourceNote->noteText; ?></textarea>
		</div>
		</div>
		<p class="actions">
			<input type='submit' value='<?php echo _("Submit to Workflow");?>' id='progress' class='submitResource submit-button primary'>
			<input type='button' value='<?php echo _("Save to Queue");?>' id='save' class='submitResource secondary'>
			<input type='button' value='<?php echo _("Cancel");?>' onclick="myCloseDialog('#NewResourceForm')"  class='secondary'>
		</p>
		</form>
		</div>
		<script type="text/javascript" src="js/forms/resourceNewForm.js?random=<?php echo rand(); ?>"></script>

