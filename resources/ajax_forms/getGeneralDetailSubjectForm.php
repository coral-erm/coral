<?php
	if (isset($_GET['generalSubjectID'])) $generalSubjectID = $_GET['generalSubjectID']; else $generalSubjectID = '';
	$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $generalSubjectID)));


		//get all users for output in drop down
		$allDetailedSubjectArray = array();
		$detailedSubjectObj = new DetailedSubject();
		$allDetailedSubjectArray = $detailedSubjectObj->allAsArray();

		//get Detail Subjects already set up for this General subject in case it's an edit
		$dsSubjectArray = $generalSubject->getDetailedSubjects();
?>
		<div id='div_detailedSubjectForm'>
		<form id='detailedSubjectForm'>
		<input type='hidden' name='editgeneralSubjectID' id='editgeneralSubjectID' value='<?php echo $generalSubjectID; ?>'>

		<div class='formTitle' style='width:280px; margin-bottom:5px;position:relative;'><span class='headerText'><?php echo _("Add / Edit Subject Relationships"); ?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class='noBorder' style='width:100%;'>
		<tr style='vertical-align:top;'>
		<td style='vertical-align:top;position:relative;'>


			<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='rule'><b><?php echo _("General Subject");?></b></label>&nbsp;&nbsp;</span>

			<table class='surroundBox' style='width:275px;'>
			<tr>
			<td>

				<table class='noBorder' style='width:235px; margin:15px 20px 10px 20px;'>
				<tr>
				<td>&nbsp;</td>
				<td>
				<input type='text' id='shortName' name='shortName' value = '<?php echo $generalSubject->shortName; ?>' style='width:110px' class='changeInput' /><span id='span_error_groupName' class='smallDarkRedText'></span>
				</td>
				</tr>

				</table>
			</td>
			</tr>
			</table>

			<div style='height:10px;'>&nbsp;</div>

			</td>
			</tr>
			<tr style='vertical-align:top;'>
			<td>

			<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='detailedSubjectID'><b><?php echo _("Detailed Subjects");?></b></label>&nbsp;&nbsp;</span>

			<table class='surroundBox' style='width:275px;'>
			<tr>
			<td>

				<table class='noBorder smallPadding newdetailedSubjectTable' style='width:205px; margin:15px 35px 0px 35px;'>

					<tr class='newdetailedSubjectTR'>
						<td>
							<select class='changeSelect detailedSubjectID' style='width:145px;'>
								<option value=''></option>
								<?php

								foreach ($allDetailedSubjectArray as $dSubject){
									echo "<option value='" . $dSubject['detailedSubjectID'] . "'>" . $dSubject['shortName'] . "</option>\n";
								}
								?>
							</select>
						</td>

				<td style='vertical-align:top;text-align:left;width:40px;'>
				<a href='javascript:void();'>
					<input class='adddetailedSubject add-button' title='<?php echo _("add detail subject");?>' type='button' value='<?php echo _("Add");?>'/>
				</a>
				</td>
				</tr>
				</table>
				<div class='smallDarkRedText' id='div_errordetailedSubject' style='margin:0px 35px 7px 35px;'></div>

				<table class='noBorder smallPadding detailedSubjectTable' style='width:205px; margin:0px 35px 0px 35px;'>
				<tr>
				<td colspan='2'>
					<hr style='width:200px;' />
				</td>
				</tr>

				<?php

				if (count($dsSubjectArray) > 0){
					foreach ($dsSubjectArray as $dsSubject){
					?>
						<tr class='newdetailedSubject'>
						<td>
						<select class='changeSelect detailedSubjectID' style='width:145px;'>
						<option value='<?php echo $dsSubject->detailedSubjectID ?>'><?php echo $dsSubject->shortName ?></option>
						</select>
						</td>

						<td style='vertical-align:top;text-align:left;width:40px;'>
						<?php
							// Check to see if detail subject is in use.  If not allow removal.
							$subjectObj = new DetailedSubject();
							if ($subjectObj->inUse($dsSubject->detailedSubjectID, $generalSubject->generalSubjectID) == 0)  { ?>
								<a href='javascript:void();'><img src='images/cross.gif' alt="<?php echo _("remove detailed subject");?>" title="<?php echo _("remove detailed subject");?>" class='remove' /></a>
						<?php } else { ?>
								<img src='images/do_not_enter.png' alt="<?php echo _("subject in use");?>" title="<?php echo _("subject in use");?>" />
						<?php }  ?>
						</td>
						</tr>
					<?php
					}
				}
				?>
				</table>
			</td>
			</tr>
			</table>
		</td>
		</tr>
		</table>


		<hr style='width:283px;margin-top:15px; margin-bottom:10px;' />

		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitDetailSubjectForm' id ='submitDetailSubjectForm' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button'></td>
			</tr>
		</table>

		</form>
		</div>

		<script type="text/javascript" src="js/forms/generalDetailSubjectForm.js?random=<?php echo rand(); ?>"></script>

