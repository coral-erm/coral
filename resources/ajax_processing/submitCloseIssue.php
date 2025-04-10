<?php
if (!empty($_POST['issueID'])){
	$issueID = $_POST['issueID'];
	$issue = new Issue(new NamedArguments(array('primaryKey' => $issueID)));
	$issue->resolutionText = $_POST['resolutionText'];
	$issue->dateClosed = date("Y-m-d H:i:s");
	try {
		$issue->save();
	} catch (Exception $e) {
		echo "<span class='error'>";
		echo $e->getMessage();
		echo "</span>";
	}
}
