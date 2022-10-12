<?php
	$resourceID = $_GET['resourceID'];
	$generalSubject = new GeneralSubject();
	$generalSubjectArray = $generalSubject->allAsArray();
?>
		<div id='div_updateForm'>
		<div class='formTitle' style='width:403px;'><span class='headerText' style='margin-left:7px;'></span><?php echo _("Add General / Detail Subject Link");?></div>

	<?php
		if (count($generalSubjectArray) > 0){
			?>
			<table class='linedDataTable' style='width:100%'>
				<tr>
				<th><?php echo _("General Subject Name");?></th>
				<th><?php echo _("Detail Subject Name");?></th>
				<th>&nbsp;</th>
				</tr>
				<?php

				foreach($generalSubjectArray as $ug) {
					$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $ug['generalSubjectID'])));

					echo "<tr>";
					echo "<td>" . $generalSubject->shortName . "</td>";
					echo "<td></td>";
					echo "<td><a href='javascript:void(0);' class='resourcesSubjectLink' resourceID='" . $resourceID . " 'generalSubjectID='" . $ug['generalSubjectID'] . " 'detailSubjectID='" . -1 . "'><input class='add-button' type='button' title='"._("add")."' value='"._("Add")."'/></a></td>";

					foreach ($generalSubject->getDetailedSubjects() as $detailedSubjects){
						echo "<tr>";
						echo "<td></td>";
						echo "<td>";
						echo $detailedSubjects->shortName . "</td>";
						echo "<td><a href='javascript:void(0);' class='resourcesSubjectLink' resourceID='" . $resourceID . " 'generalSubjectID='" . $ug['generalSubjectID'] . " 'detailSubjectID='" . $detailedSubjects->detailedSubjectID . "'><input class='add-button' type='button' title='"._("add")."' value='"._("Add")."'/></a></td>";
						echo "</tr>";
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

		<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button'></td>
		</div>

		<script type="text/javascript" src="js/forms/resourceSubject.js?random=<?php echo rand(); ?>"></script>
		<!-- <script type="text/javascript">document.getElementById("div_updateForm").className="modalScroll";</script> -->
