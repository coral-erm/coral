<?php
if (is_numeric($_POST['downtimeID'])) {
	$downtime = new Downtime(new NamedArguments(array('primaryKey' => $_POST['downtimeID'])));

	$downtime->endDate = ($_POST['endDate']) ?  date('Y-m-d H:i:s', strtotime(create_date_from_js_format($_POST['endDate'])->format('Y-m-d')." ".$_POST['endTime']['hour'].":".$_POST['endTime']['minute'].$_POST['endTime']['meridian'])):null;

	$downtime->note = ($_POST['note']) ? $_POST['note']:null;

	$downtime->save();
}
?>
