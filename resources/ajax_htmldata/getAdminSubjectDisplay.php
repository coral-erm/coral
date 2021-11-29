<?php

		$generalSubject = new GeneralSubject();
		$generalSubjectArray = $generalSubject->allAsArray();

		$detailedSubject = new DetailedSubject();
		$detailedSubjectArray = $detailedSubject->allAsArray();
		?>
		<div class='adminHeader'>
			<div><?php echo "<div class='adminRightHeader'>" . _("General Subject") . "</div>";?></div>
			<div class='addElement' style="margin-right: 4px"><?php echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getGeneralSubjectUpdateForm&className=" . "GeneralSubject" . "&updateID=\",245,360)' class='thickbox'><img id='addNewGeneralSubject' src='images/plus.gif' title='"._("add new general subject")."'/></a>";?></div>
		</div>
		<?php
		if (count($generalSubjectArray) > 0){
			?>
			<table class='linedDataTable'>
				<tr>
				<th style='width:100%;'><?php echo _("Value");?></th>
				<th style='width:20px;'>&nbsp;</th>
				<th style='width:20px;'>&nbsp;</th>
				</tr>
				<?php

				foreach($generalSubjectArray as $instance) {
					echo "<tr>";
					echo "<td>" . $instance['shortName'] . "</td>";
					
					echo "<td><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getGeneralSubjectUpdateForm&className=" . "GeneralSubject" . "&updateID=" . $instance[lcfirst("GeneralSubject") . 'ID'] . "\",228,360)' class='thickbox'><img src='images/edit.gif' alt='edit' title='edit'></a></td>";

						$generalSubject = new GeneralSubject();
						if ($generalSubject->inUse($instance[lcfirst("GeneralSubject") . 'ID']) == 0) {
							echo "<td><a href='javascript:deleteGeneralSubject(\"GeneralSubject\", " . $instance[lcfirst("GeneralSubject") . 'ID'] . ");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
						} else {
							echo "<td><img src='images/do_not_enter.png' alt='"._("subject in use")."' title='"._("subject in use")."' /></td>";
						}

					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)")."<br />";
		}


		?>

		<br /><br />
		<div class='adminHeader'>
			<div><?php echo "<div class='adminRightHeader'>" . _("Detailed Subject") . "</div>";?></div>
			<div class='addElement' style="margin-right: 4px"><?php echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getDetailSubjectUpdateForm&className=" . "DetailedSubject" . "&updateID=\",245,360)' class='thickbox'><img id='addNewDetailedSubject' src='images/plus.gif' title='"._("add new detailed subject")."'/></a>";?>
				</div>
		</div>

		<?php
		if (count($detailedSubjectArray) > 0){
			?>
			<table class='linedDataTable'>
				<tr>
				<th style='width:100%;'><?php echo _("Value");?></th>
				<th style='width:20px;'>&nbsp;</th>
				<th style='width:20px;'>&nbsp;</th>
				</tr>
				<?php

				foreach($detailedSubjectArray as $instance) {
					echo "<tr>";
					echo "<td>" . $instance['shortName'] . "</td>";
					echo "<td><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getDetailSubjectUpdateForm&className=" . "DetailedSubject" . "&updateID=" . $instance[lcfirst("DetailedSubject") . 'ID'] . "\",228,360)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a></td>";
						$detailedSubject = new DetailedSubject();
						if ($detailedSubject->inUse($instance[lcfirst("DetailedSubject") . 'ID'], -1) == 0) {
									echo "<td><a href='javascript:deleteDetailedSubject(\"DetailedSubject\", " . $instance[lcfirst("DetailedSubject") . 'ID'] . ");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
						} else {
							echo "<td><img src='images/do_not_enter.png' alt='"._("subject in use")."' title='"._("subject in use")."' /></td>";
						}
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)")."<br />";
		}


		?>

		<br /><br />

		<?php

		echo "<div class='adminRightHeader'>" . _("Subject Relationships") . "</div>";

		if (count($generalSubjectArray) > 0){
			?>
			<table class='linedDataTable' style='width:100%'>
				<tr>
				<th><?php echo _("General Subject");?></th>
				<th><?php echo _("Detailed Subject");?></th>
				<th style='width:20px;'>&nbsp;</th>
				</tr>
				<?php

				foreach($generalSubjectArray as $ug) {
					$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $ug['generalSubjectID'])));

					echo "<tr>";
					echo "<td>" . $generalSubject->shortName . "</td>";
					echo "<td>";
					foreach ($generalSubject->getDetailedSubjects() as $detailedSubjects){
						echo $detailedSubjects->shortName . "<br />";
					}
					echo "</td>";
					echo "<td><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getGeneralDetailSubjectForm&generalSubjectID=" . $generalSubject->generalSubjectID . "\",500,405)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a></td>";
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)")."<br />";
		}

?>
