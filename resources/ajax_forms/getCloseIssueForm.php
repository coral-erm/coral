<?php
	$issueID = $_GET['issueID'];

?>
<div id="closeIssue">
	<form class="form-grid">
		<input type="hidden" id="issueID" name="issueID" value="<?php echo $issueID; ?>">
		
		<h2 id='headerText' class='headerText'><?php echo _('Issue Resolution');?></h2>
		
		<label for="resolutionText"><?php echo _('Resolution:'); ?></label>
		<textarea id="resolutionText" name="resolutionText"></textarea>

		<p class='actions'>
				<input type="submit" value="submit" name="submitCloseIssue" id="submitCloseIssue" class="primary">
				<input type='button' value='cancel' onclick="myCloseDialog()" class="secondary">
		</p>

	</form>
</div>

