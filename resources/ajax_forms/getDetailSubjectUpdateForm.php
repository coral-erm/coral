<?php
		$className = $_GET['className'];
		$updateID = $_GET['updateID'];

		if ($updateID){
			$instance = new $className(new NamedArguments(array('primaryKey' => $updateID)));
		}else{
			$instance = new $className();
		}
?>
		<div id='div_updateForm'>

		<input type='hidden' id='editClassName' value='<?php echo $className; ?>'>
		<input type='hidden' id='editUpdateID' value='<?php echo $updateID; ?>'>

		<div class='formTitle' style='width:245px;'><span class='headerText' style='margin-left:7px;'><?php if ($updateID){ echo _("Edit ") . preg_replace("/[A-Z]/", " \\0" , $className); } else { echo _("Add ") . preg_replace("/[A-Z]/", " \\0" , $className); } ?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class="surroundBox" style="width:250px;">
		<tr>
		<td>

			<table class='noBorder' style='width:200px; margin:10px;'>
			<tr>
			<td><input type="text" id="updateVal" value="<?php echo $instance->shortName; ?>" style="width:190px;"/></td>
			</tr>
			</table>

		</td>
		</tr>
		</table>

		<br />
		<table class='noBorderTable' style='width:125px;'>
			<tr>

				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitDetailedSubjectForm' id ='submitDetailedSubjectForm' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button'></td>
			</tr>
		</table>


		</form>
		</div>

		<script type="text/javascript" src="js/forms/detailedSubjectForm.js?random=<?php echo rand(); ?>"></script>
