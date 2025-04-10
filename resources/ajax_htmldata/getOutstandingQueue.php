<?php


		$resourceArray = array();
		$resourceArray = $user->getOutstandingTasks();

		echo "<h3 class='adminRightHeader'>"._("Outstanding Tasks")."</h3>";



		if (count($resourceArray) == "0"){
			echo "<i>"._("No outstanding requests")."</i>";
		}else{
		?>


			<table class='dataTable table-border table-striped'>
			<thead>
			<tr>
				<th scope="col"><?php echo _("ID");?></th>
				<th scope="col"><?php echo _("Name");?></th>
				<th scope="col"><?php echo _("Order");?></th>
				<th scope="col"><?php echo _("Acquisition Type");?></th>
				<th scope="col"><?php echo _("Workflow Step");?></th>
				<th scope="col"><?php echo _("Start Date");?></th>
			</tr>
		</thead>

		<?php
			foreach ($resourceArray as $resource){
				$taskArray = $user->getOutstandingTasksByResource($resource['resourceID']);
				$countTasks = count($taskArray);

				$acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $resource['acquisitionTypeID'])));
				$status = new Status(new NamedArguments(array('primaryKey' => $resource['statusID'])));

		?>
				<tr id='tr_<?php echo $resource['resourceID']; ?>'>
					<td><a href='resource.php?resourceID=<?php echo $resource['resourceID']; ?>&resourceAcquisitionID=<?php echo $resource['resourceAcquisitionID']?>'><?php echo $resource['resourceID']; ?></a></td>
					<th scope="row"><a href='resource.php?resourceID=<?php echo $resource['resourceID']; ?>&resourceAcquisitionID=<?php echo $resource['resourceAcquisitionID']?>'><?php echo $resource['titleText']; ?></a></th>
					<td><?php echo $resource['subscriptionStartDate']; ?> - <?php echo $resource['subscriptionEndDate']; ?></a></td>
					<td><?php echo $acquisitionType->shortName; ?></td>

					<?php
						if (is_array($taskArray) && count($taskArray) > 0) {
							foreach ($taskArray as $task){
								if ($j > 0){
								?>
								<tr>
								<td style='border-top-style:none;'>&nbsp;</td>
								<td style='border-top-style:none;'>&nbsp;</td>
								<td style='border-top-style:none;'>&nbsp;</td>
								<td style='border-top-style:none;'>&nbsp;</td>

								<?php
									$styleAdd=" style='border-top-style:none;'";
								}else{
									$styleAdd="";
								}


								echo "<td " . $styleAdd . ">" . $task['stepName'] . "</td>";
								echo "<td " . $styleAdd . ">" . format_date($task['startDate']) . "</td>";
								echo "</tr>";

								$j++;
							}

						}else{
							echo "<td>&nbsp;</td><td>&nbsp;</td></tr>";
						}


			}

			echo "</tbody></table>";


		}

?>

