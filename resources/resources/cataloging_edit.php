<?php
include_once '../directory.php';
include_once '../user.php';

$resourceID = $_GET['resourceID'];
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

$catalogingStatus = new CatalogingStatus();
$catalogingType = new CatalogingType();
?>
<div id='div_catalogingForm'>
<form id='catalogingForm' method="post" action="resources/cataloging_update.php">
<input type='hidden' name='resourceID' id='resourceID' value='<?php echo $resourceID; ?>'>

<div class='formTitle' style='width:715px; margin-bottom:5px;'><span class='headerText'><?php echo _("Edit Cataloging");?></span></div>

<span class='smallDarkRedText' id='span_errors'></span>

<table class='noBorder' style='width:100%;'>
<tr style='vertical-align:top;'>
<td style='vertical-align:top;' colspan='2'>


<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='accessHead'><b><?php echo _("Record Set");?></b></label>&nbsp;&nbsp;</span>

<table class='surroundBox' style='width:710px;'>
<tr>
<td>
  <?php //debug($resource); ?>
  
	<table class='noBorder' style='width:670px; margin:15px 20px 10px 20px;'>
	<tr>
	<td style="width:400px;">
		<table>
		<tr>
		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo Html::label_tag('recordSetIdentifier', _('Identifier')); ?></td>
		<td><?php echo Html::text_field('recordSetIdentifier', $resource, array('width' => '240px')) ?>
		</td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo Html::label_tag('bibSourceURL', _('Source URL')); ?></td>
		<td><?php echo Html::text_field('bibSourceURL', $resource, array('width' => '240px')) ?>
		</td>
		</tr>
		
		<tr>
		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo Html::label_tag('catalogingTypeID', _('Cataloging Type')); ?></td>
		<td>
		  <?php echo Html::select_field('catalogingTypeID', $resource, $catalogingType->all(), array('width' => '150px')); ?>
		</td>
		</tr>
		
		<tr>
		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo Html::label_tag('catalogingStatusID', _('Cataloging Status')); ?></td>
		<td>
		  <?php echo Html::select_field('catalogingStatusID', $resource, $catalogingStatus->all(), array('width' => '150px')); ?>
		</td>
		</tr>
		
		</table>

	</td>
	<td>
		<table>
    
      <tr>
  		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo Html::label_tag('numberRecordsAvailable', _('# Records Available')); ?></td>
  		<td>
  		  <?php echo Html::text_field('numberRecordsAvailable', $resource, array('width' => '60px')) ?>
  		</td>
  		</tr>

  		<tr>
  		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo Html::label_tag('numberRecordsLoaded', _('# Records Loaded')); ?></td>
  		<td>
  		  <?php echo Html::text_field('numberRecordsLoaded', $resource, array('width' => '60px')) ?>
  		</td>
  		</tr>
		
		<tr>
		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo Html::label_tag('hasOclcHoldings', _('OCLC Holdings')); ?></td>
		<td><input type='checkbox' value="1" id='hasOclcHoldings' name='hasOclcHoldings' <?php if ($resource->hasOclcHoldings) { echo 'checked'; } ?> /></td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>

</td>
</tr>
</table>


<hr style='width:710px;margin:15px 0px 10px 0px;' />

<table class='noBorderTable' style='width:125px;'>
<tr>
	<td style='text-align:left'><input type='submit' value='<?php echo _("submit");?>' name='submitCatalogingChanges' id ='submitCatalogingChanges' class='submit-button'></td>
	<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="kill(); tb_remove();" class='cancel-button'></td>
</tr>
</table>

</form>
</div>
<script type="text/javascript" src="js/forms/catalogingForm.js?random=<?php echo rand(); ?>"></script>