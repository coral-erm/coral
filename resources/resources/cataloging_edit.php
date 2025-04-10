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
      $required_text = '<span class="required">*</span>';
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
    $default_options = array('id' => $default_id, 'class' => 'changeInput');
    $options = array_merge($default_options, $options);

    return '<input type="text" id="'.htmlspecialchars($options['id']).'" name="'.htmlspecialchars($name).'" class="'.htmlspecialchars($options['class']).'" value="'.htmlspecialchars($value). '" aria-describedby="span_error_'.htmlspecialchars($options['id']).'" /><span id="span_error_'.htmlspecialchars($options['id']).'" class="error"></span>';
  }

  public function text_field($field, $object, $options = array()) {
    return (new Html())->text_field_tag($field, $object->$field, $options);
  }

  public function text_search_field_tag($name, $value, $options = array()) {
    $default_options = array('class' => '');
    $options = array_merge($default_options, $options);
    return (new Html())->text_field_tag("search[".$name."]", $value, $options);
  }



  public function select_field($field, $object, $collection, $options = array()) {
    $default_options = array();
    $options = array_merge($default_options, $options);

    $str = '<select id="'.$field.'" name="'.$field.'" aria-describedby="span_error_'.$field.'"><option></option>';
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
    $str .= '</select><p id="span_error_'.$field.'" class="error"></p>';
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
<form id='catalogingForm' method="post" action="resources/cataloging_update.php" class="large">
<input type='hidden' name='resourceID' id='resourceID' value='<?php echo $resourceID; ?>'>
<input type='hidden' name='resourceAcquisitionID' id='resourceAcquisitionID' value='<?php echo $resourceAcquisitionID; ?>'>

<h2><?php echo _("Edit Cataloging");?></h2>
<p class='error' id='span_errors'></p>
<h3><?php echo _("Record Set");?></h3>

  <?php //debug($resource); 
  
  $htm = new Html();
 
  ?>
  <div class="form-grid grid-columns">

    <label for='recordSetIdentifier'><?php echo _('Identifier'); ?></label>
    <?php echo $htm->text_field('recordSetIdentifier', $resourceAcquisition, array()) ?>
  
    <label for='numberRecordsAvailable'><?php echo _('# Records Available'); ?></label>
    <?php echo $htm->text_field('numberRecordsAvailable', $resourceAcquisition, array()) ?>
    
    <label for='bibSourceURL'><?php echo _('Source URL'); ?></label>
    <?php echo $htm->text_field('bibSourceURL', $resourceAcquisition, array()) ?>
  
    <label for='numberRecordsLoaded'><?php echo _('# Records Loaded'); ?></label>
    <?php echo $htm->text_field('numberRecordsLoaded', $resourceAcquisition, array()) ?>
    
    <label for='catalogingTypeID'><?php echo _('Cataloging Type'); ?></label>
    <?php echo $htm->select_field('catalogingTypeID', $resourceAcquisition, $catalogingType->all(), array()); ?>
  
    <label for='catalogingStatusID'><?php echo _('Cataloging Status'); ?></label>
    <?php echo $htm->select_field('catalogingStatusID', $resourceAcquisition, $catalogingStatus->all(), array()); ?>
  
    <label for='hasOclcHoldings'><?php echo _('OCLC Holdings'); ?></label>
    <input type='checkbox' value="1" id='hasOclcHoldings' name='hasOclcHoldings' <?php if ($resourceAcquisition->hasOclcHoldings) { echo 'checked'; } ?> />
    
  </div>

<p class="actions">
  <input type='submit' value='<?php echo _("submit");?>' name='submitCatalogingChanges' id ='submitCatalogingChanges' class='submit-button primary'>
  <input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog();" class='cancel-button secondary'>
</p>

</form>
</div>
<script type="text/javascript" src="js/forms/catalogingForm.js?random=<?php echo rand(); ?>"></script>