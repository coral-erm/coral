<?php

		$generalSubject = new GeneralSubject();
		$generalSubjectArray = $generalSubject->allAsArray();

		$detailedSubject = new DetailedSubject();
		$detailedSubjectArray = $detailedSubject->allAsArray();

		echo "<div class='adminRightHeader'>" . _("General Subject") . "</div>";

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
					echo "<td><a href='ajax_forms.php?action=getGeneralSubjectUpdateForm&className=" . "GeneralSubject" . "&updateID=" . $instance[lcfirst("GeneralSubject") . 'ID'] . "&height=128&width=260&modal=true' class='thickbox'><img src='images/edit.gif' alt='edit' title='edit'></a></td>";

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

		echo "<a href='ajax_forms.php?action=getGeneralSubjectUpdateForm&className=" . "GeneralSubject" . "&updateID=&height=145&width=260&modal=true' class='thickbox'>"._("add new ") . _(trim(strtolower(preg_replace("/[A-Z]/", " \\0" , lcfirst("GeneralSubject"))))) . "</a>";

		?>

		<br /><br />

		<?php
		echo "<div class='adminRightHeader'>" . _("Detailed Subject") . "</div>";

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
					echo "<td><a href='ajax_forms.php?action=getDetailSubjectUpdateForm&className=" . "DetailedSubject" . "&updateID=" . $instance[lcfirst("DetailedSubject") . 'ID'] . "&height=128&width=260&modal=true' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a></td>";
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

		echo "<a href='ajax_forms.php?action=getDetailSubjectUpdateForm&className=" . "DetailedSubject" . "&updateID=&height=145&width=260&modal=true' class='thickbox'>"._("add new ") . _(trim(strtolower(preg_replace("/[A-Z]/", " \\0" , lcfirst("DetailedSubject"))))) . "</a>";

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
					echo "<td><a href='ajax_forms.php?action=getGeneralDetailSubjectForm&generalSubjectID=" . $generalSubject->generalSubjectID . "&height=400&width=305&modal=true' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a></td>";
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)")."<br />";
		}

?>
