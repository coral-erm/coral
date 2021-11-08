<?php
include_once '../directory.php';
include_once '../user.php';
//include_once '../user.php';

class Html {

  public function nameToID($str) {
    $str = preg_replace('/[^a-zA-Z0-9]/', ' ', $str);
    $str = explode(' ', $str);
    $str = array_map('ucfirst', $str);
    $str = lcfirst(implode('', $str));
    return $str;
  }

  public function humanize($str) {
    $str = trim($str);
    $str = preg_replace('/ID$/i', '', $str);
    $str = preg_replace('/[A-Z]+/', " $0", $str);
    $str = preg_replace('/[^a-z0-9\s+]/i', '', $str);
    $str = preg_replace('/\s+/', ' ', $str);
    $str = explode(' ', $str);
    $str = array_map('ucwords', $str);
    return implode(' ', $str);
  }

  public function label_tag($for, $name = null, $required = false) {
    if ($name === null) {
      $name = (new Html())->humanize($for);
    }

    if ($required) {
      $required_text = '&nbsp;&nbsp;<span class="bigDarkRedText">*</span>';
    } else {
      $required_text = '';
    }

    return '<label for="'. htmlspecialchars($for).'">'.htmlspecialchars($name).':'.$required_text.'</label>';
  }

  public function hidden_field_tag($name, $value, $options = array()) {
    $default_id = (new Html())->nameToID($name);
    $default_options = array('id' => $default_id);
    $options = array_merge($default_options, $options);

    return '<input type="hidden" id="'.htmlspecialchars($options['id']).'" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value). '" />';
  }

  public function hidden_search_field_tag($name, $value, $options = array()) {
    return (new Html())->hidden_field_tag("search[".$name."]", $value, $options);
  }

  public function text_field_tag($name, $value, $options = array()) {
    $default_id = (new Html())->nameToID($name);
    $default_options = array('width' => '180px', 'id' => $default_id, 'class' => 'changeInput');
    $options = array_merge($default_options, $options);

    return '<input type="text" id="'.htmlspecialchars($options['id']).'" name="'.htmlspecialchars($name).'" style="width:'.$options['width'].'" class="'.htmlspecialchars($options['class']).'" value="'.htmlspecialchars($value). '" /><span id="span_error_'.htmlspecialchars($options['id']).'" class="smallDarkRedText"></span>';
  }

  public function text_field($field, $object, $options = array()) {
    return (new Html())->text_field_tag($field, $object->$field, $options);
  }

  public function text_search_field_tag($name, $value, $options = array()) {
    $default_options = array('width' => '145px', 'class' => '');
    $options = array_merge($default_options, $options);
    return (new Html())->text_field_tag("search[".$name."]", $value, $options);
  }



  public function select_field($field, $object, $collection, $options = array()) {
    $default_options = array('width' => '180px');
    $options = array_merge($default_options, $options);

    $str = '<select id="'.$field.'" name="'.$field.'" style="width:'.$options['width'].'"><option></option>';
    foreach ($collection as $item) {
      if (is_subclass_of($item, 'DatabaseObject')) {
        $key = $item->getPrimaryKeyName();
        $value = $item->$key;
        $name = $item->shortName;
      } else {
        $value = $item;
        $name = $item;
      }
      if ($value == $object->$field) {
        $str .= '<option value="'.htmlspecialchars($value).'" selected="selected">'.htmlspecialchars($name).'</option>';
      } else {
        $str .= '<option value="'.htmlspecialchars($value).'">'.htmlspecialchars($name).'</option>';
      }
    }
    $str .= '</select><span id="span_error_'.$field.'" class="smallDarkRedText"></span>';
    return $str;
  }
}

if(isset($_GET['resourceID'])){
	$resourceID = $_GET['resourceID'];
}else{
	$resourceID = '';
}

if(isset($_GET['resourceAcquisitionID'])){
	$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
}else{
	$resourceAcquisitionID = '';
}

$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

$catalogingStatus = new CatalogingStatus();
$catalogingType = new CatalogingType();
?>
<div id='div_catalogingForm'>
<form id='catalogingForm' method="post" action="resources/cataloging_update.php">
<input type='hidden' name='resourceID' id='resourceID' value='<?php echo $resourceID; ?>'>
<input type='hidden' name='resourceAcquisitionID' id='resourceAcquisitionID' value='<?php echo $resourceAcquisitionID; ?>'>

<div class='formTitle' style='width:715px; margin-bottom:5px;'><span class='headerText'><?php echo _("Edit Cataloging");?></span></div>

<span class='smallDarkRedText' id='span_errors'></span>

<table class='noBorder' style='width:100%;'>
<tr style='vertical-align:top;'>
<td style='vertical-align:top;' colspan='2'>


<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='accessHead'><b><?php echo _("Record Set");?></b></label>&nbsp;&nbsp;</span>

<table class='surroundBox' style='width:710px;'>
<tr>
<td>
  <?php //debug($resource); 
  
  $htm = new Html();
 
  ?>

	<table class='noBorder' style='width:670px; margin:15px 20px 10px 20px;'>
	<tr>
	<td style="width:400px;">
		<table>
		<tr>
		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo $htm->label_tag('recordSetIdentifier', _('Identifier')); ?></td>
		<td><?php echo $htm->text_field('recordSetIdentifier', $resourceAcquisition, array('width' => '240px')) ?>
		</td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo $htm->label_tag('bibSourceURL', _('Source URL')); ?></td>
		<td><?php echo $htm->text_field('bibSourceURL', $resourceAcquisition, array('width' => '240px')) ?>
		</td>
		</tr>
		
		<tr>
		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo $htm->label_tag('catalogingTypeID', _('Cataloging Type')); ?></td>
		<td>
		  <?php echo $htm->select_field('catalogingTypeID', $resourceAcquisition, $catalogingType->all(), array('width' => '150px')); ?>
		</td>
		</tr>
		
		<tr>
		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo $htm->label_tag('catalogingStatusID', _('Cataloging Status')); ?></td>
		<td>
		  <?php echo $htm->select_field('catalogingStatusID', $resourceAcquisition, $catalogingStatus->all(), array('width' => '150px')); ?>
		</td>
		</tr>
		
		</table>

	</td>
	<td>
		<table>

      <tr>
  		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo $htm->label_tag('numberRecordsAvailable', _('# Records Available')); ?></td>
  		<td>
  		  <?php echo $htm->text_field('numberRecordsAvailable', $resourceAcquisition, array('width' => '60px')) ?>
  		</td>
  		</tr>

  		<tr>
  		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo $htm->label_tag('numberRecordsLoaded', _('# Records Loaded')); ?></td>
  		<td>
  		  <?php echo $htm->text_field('numberRecordsLoaded', $resourceAcquisition, array('width' => '60px')) ?>
  		</td>
  		</tr>
		
		<tr>
		<td style='vertical-align:top;text-align:left;font-weight:bold;'><?php echo $htm->label_tag('hasOclcHoldings', _('OCLC Holdings')); ?></td>
		<td><input type='checkbox' value="1" id='hasOclcHoldings' name='hasOclcHoldings' <?php if ($resourceAcquisition->hasOclcHoldings) { echo 'checked'; } ?> /></td>
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