<?php
$downtimeID = is_numeric($_GET['downtimeID']) ? $_GET['downtimeID']:null;

if ($downtimeID) {
	$downtime = new Downtime(new NamedArguments(array('primaryKey' => $downtimeID)));

?>
<form id="resolveDowntimeForm" class="form-grid">
	<input name="downtimeID" type="hidden" value="<?php echo $downtime->downtimeID;?>" />

	<h2 class="headerText"><?php echo _('Resolve Downtime'); ?></h2>	
	<h3><?php echo _('Downtime Resolution:'); ?></h3>
	
	<label for="endDate"><?php echo _('Date'); ?></label>
	<input class="date-pick" type="text" name="endDate" id="endDate" aria-describedby="span_error_endDate" placeholder='mm/dd/yyyy' />
	<span id='span_error_endDate' class='error updateDowntimeError'></span>
	
	<fieldset class="subgrid">
		<legend><?php echo _('Time'); ?></legend>
		<div class="form-group">
			<?php
			echo buildTimeForm("endTime");
			?>
			<span id='span_error_endDate' class='error updateDowntimeError'></span>
		</div>
	</fieldset>
	
	<label for="note"><?php echo _('Note:'); ?></label>
	<textarea id="note" name="note"><?php echo $downtime->note;?></textarea>
	
	<p class='actions'>
		<input type='button' value='submit' name='submitUpdatedDowntime' id='submitUpdatedDowntime' class="primary">
		<input type='button' value='cancel' onclick="myCloseDialog()" class="secondary">
	</p>
</form>
<?php
} else {
?>
		<p class="error">
			<?php echo _('Unable to retrieve Downtime.'); ?>
		</p>
		<p class='actions'>
			<input type='button' value='cancel' onclick="myCloseDialog()" class="secondary">
		
		</p>
<?php
}
