<?php

		$generalSubject = new GeneralSubject();
		$generalSubjectArray = $generalSubject->allAsArray();

		$detailedSubject = new DetailedSubject();
		$detailedSubjectArray = $detailedSubject->allAsArray();
		?>
		<div class='adminHeader header'>
			<h3 class='adminRightHeader'><?php echo _("General Subject");?></h3>
			<div class='addElement'><?php echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getGeneralSubjectUpdateForm&className=" . "GeneralSubject" . "&updateID=\",245,360)' class='thickbox'><img id='addNewGeneralSubject' src='images/plus.gif' title='"._("add new general subject")."'/></a>";?></div>
		</div>
		<?php
		if (is_array($generalSubjectArray) && count($generalSubjectArray) > 0) {
			?>
			<table class='linedDataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("Value");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				</tr>
				</thead>
				<tbody>
				<?php

				foreach($generalSubjectArray as $instance) {
					echo "<tr>";
					echo "<th scope='row'>" . $instance['shortName'] . "</th>";
					
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getGeneralSubjectUpdateForm&className=" . "GeneralSubject" . "&updateID=" . $instance[lcfirst("GeneralSubject") . 'ID'] . "\",228,360)' class='thickbox'><img src='images/edit.gif' alt='edit' title='edit'></a>";

						$generalSubject = new GeneralSubject();
						if ($generalSubject->inUse($instance[lcfirst("GeneralSubject") . 'ID']) == 0) {
							echo "<td><a href='javascript:deleteGeneralSubject(\"GeneralSubject\", " . $instance[lcfirst("GeneralSubject") . 'ID'] . ");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a>";
						} else {
							echo "<td><img src='images/do_not_enter.png' alt='"._("subject in use")."' title='"._("subject in use")."' /></td>";
						}

					echo "</tr>";
				}

				?>
				</tbody>
			</table>
			<?php

		}else{
			echo "<p>". _("(none found)")."</p>";
		}


		?>

		<br /><br />
		<div class='adminHeader header'>
			<h3 class='adminRightHeader'><?php echo _("Detailed Subject");?></h3>
			<div class='addElement'><?php echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getDetailSubjectUpdateForm&className=" . "DetailedSubject" . "&updateID=\",245,360)' class='thickbox'><img id='addNewDetailedSubject' src='images/plus.gif' title='"._("add new detailed subject")."'/></a>";?>
		</div>
		</div>

		<?php
		if (is_array($detailedSubjectArray) && count($detailedSubjectArray) > 0) {
			?>
			<table class='linedDataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("Value");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				</tr>
				<?php

				foreach($detailedSubjectArray as $instance) {
					echo "<tr>";
					echo "<th scope='row'>" . $instance['shortName'] . "</th>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getDetailSubjectUpdateForm&className=" . "DetailedSubject" . "&updateID=" . $instance[lcfirst("DetailedSubject") . 'ID'] . "\",228,360)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a>";
						$detailedSubject = new DetailedSubject();
						if ($detailedSubject->inUse($instance[lcfirst("DetailedSubject") . 'ID'], -1) == 0) {
									echo "<a href='javascript:deleteDetailedSubject(\"DetailedSubject\", " . $instance[lcfirst("DetailedSubject") . 'ID'] . ");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
						} else {
							echo "<img src='images/do_not_enter.png' alt='"._("subject in use")."' title='"._("subject in use")."' /></td>";
						}
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo "<p>". _("(none found)")."</p>";
		}


		?>

		<?php

		echo "<h3 class='adminRightHeader'>" . _("Subject Relationships") . "</h3>";

		if (is_array($generalSubjectArray) && count($generalSubjectArray) > 0) {
			?>
			<table class='linedDataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("General Subject");?></th>
				<th scope="col"><?php echo _("Detailed Subject");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				</tr>
				</thead>
				<tbody>
				
				<?php

				foreach($generalSubjectArray as $ug) {
					$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $ug['generalSubjectID'])));

					echo "<tr>";
					echo "<th scope='row'>" . $generalSubject->shortName . "</th>";
					echo "<td>";
					foreach ($generalSubject->getDetailedSubjects() as $detailedSubjects){
						echo $detailedSubjects->shortName . "<br />";
					}
					echo "</td>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getGeneralDetailSubjectForm&generalSubjectID=" . $generalSubject->generalSubjectID . "\",500,405)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a></td>";
					echo "</tr>";
				}

				?>
				</tbody>
			</table>
			<?php

		}else{
			echo "<p>". _("(none found)")."</p>";
		}

?>
