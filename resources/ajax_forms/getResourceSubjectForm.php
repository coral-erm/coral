<?php
	$resourceID = $_GET['resourceID'];
	$generalSubject = new GeneralSubject();
	$generalSubjectArray = $generalSubject->allAsArray();
?>
		<div id='div_updateForm'>
		<h2 class='headerText'><?php echo _("Add General / Detail Subject Link");?></h2>

	<?php
		if (is_array($generalSubjectArray) && count($generalSubjectArray) > 0) {
			?>
			<table class='linedDataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("General Subject Name");?></th>
				<th scope="col"><?php echo _("Detail Subject Name");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				</tr>
				</thead>
				<tbody>
				<?php

				foreach($generalSubjectArray as $ug) {
					$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $ug['generalSubjectID'])));

					echo "<tr>";
					echo "<th scope='row' id='subject-title-". $ug['generalSubjectID'] ."'>" . $generalSubject->shortName . "</th>";
					echo "<td></td>";
					echo "<td class='actions'><a href='javascript:void(0);' class='resourcesSubjectLink' resourceID='" . $resourceID . " 'generalSubjectID='" . $ug['generalSubjectID'] . " 'detailSubjectID='" . -1 . "'><input class='add-button secondary' type='button' title='".sprintf(_("add %s"), $generalSubject->shortName)."' value='"._("Add")."'/></a></td>";
					echo "</tr>";
					foreach ($generalSubject->getDetailedSubjects() as $detailedSubjects){
						echo "<tr>";
						echo "<td></td>";
						echo "<td id='subject-title-". $ug['generalSubjectID'] ."'>";
						echo $detailedSubjects->shortName . "</td>";
						echo "<td class='actions'><a href='javascript:void(0);' class='resourcesSubjectLink' resourceID='" . $resourceID . " 'generalSubjectID='" . $ug['generalSubjectID'] . " 'detailSubjectID='" . $detailedSubjects->detailedSubjectID . "'><input class='add-button secondary' type='button' title='".sprintf(_("add %s"), $detailedSubjects->shortName)."' value='"._("Add")."'/></a></td>";
						echo "</tr>";
					}
				}

				?>
			</tbody>	
			</table>
			<?php

		}else{
			echo "<p>". _("(none found)")."</p>";
		}
		?>

		<p class="actions">
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>
		</div>

		<script type="text/javascript" src="js/forms/resourceSubject.js?random=<?php echo rand(); ?>"></script>
		<!-- <script type="text/javascript">document.getElementById("div_updateForm").className="modalScroll";</script> -->
