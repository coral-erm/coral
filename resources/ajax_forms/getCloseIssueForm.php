<?php
	$issueID = $_GET['issueID'];

?>
<div id="closeIssue">
	<form>
		<input type="hidden" id="issueID" name="issueID" value="<?php echo $issueID; ?>">
		<table class="thickboxTable" style="width:98%;background-image:url('images/title.gif');background-repeat:no-repeat;">
			<tr>
				<td colspan='2'>
					<span id='headerText' class='headerText'>Issue Resolution</span><br />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<label for="resolutionText">Resolution:</label>
					<textarea id="resolutionText" name="resolutionText"></textarea>
				</td>
			</tr>
		</table>
		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td class="text-left"><input type="button" value="submit" name="submitCloseIssue" id="submitCloseIssue"></td>
				<td class='text-right'><input type='button' value='cancel' onclick="myCloseDialog()"></td>
			</tr>
		</table>

	</form>
</div>

