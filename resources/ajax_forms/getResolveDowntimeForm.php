<?php
$downtimeID = is_numeric($_GET['downtimeID']) ? $_GET['downtimeID']:null;

if ($downtimeID) {
	$downtime = new Downtime(new NamedArguments(array('primaryKey' => $downtimeID)));

?>
<form id="resolveDowntimeForm">
	<input name="downtimeID" type="hidden" value="<?php echo $downtime->downtimeID;?>" />
	<table class="thickboxTable" style="width:98%;background-image:url('images/title.gif');background-repeat:no-repeat;">
		<tr>
			<td colspan="2">
				<h1>Resolve Downtime</h1>
			</td>
		</tr>
		<tr>
			<td>
				<label>Downtime Resolution:</label>
			</td>
			<td>
				<div>
					<div><i>Date</i></div>
					<input class="date-pick" type="text" name="endDate" id="endDate" />
					<span id='span_error_endDate' class='smallDarkRedText updateDowntimeError'></span>
				</div>
				<div style="clear:both;">
					<div><i>Time</i></div>
<?php
echo buildTimeForm("endTime");
?>
					<span id='span_error_endDate' class='smallDarkRedText updateDowntimeError'></span>
				</div>
			</td>
		</tr>
		<tr>
			<td><label>Note:</label></td>
			<td>
				<textarea name="note"><?php echo $downtime->note;?></textarea>
			</td>
		</tr>
	</table>
	<table class='noBorderTable' style='width:125px;'>
		<tr>
			<td style='text-align:left'><input type='button' value='submit' name='submitUpdatedDowntime' id='submitUpdatedDowntime'></td>
			<td style='text-align:right'><input type='button' value='cancel' onclick="myCloseDialog()"></td>
		</tr>
	</table>
</form>
<?php
} else {
?>
		<div>
			Unable to retrieve Downtime.
		</div>
		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:right'><input type='button' value='cancel' onclick="myCloseDialog()"></td>
			</tr>
		</table>
<?php
}
