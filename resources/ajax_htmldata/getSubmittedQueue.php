<?php
		$resourceArray = array();
		$resourceArray = $user->getResourcesInQueue('progress');

		echo "<h3 class='adminRightHeader'>"._("Submitted Requests")."</h3>";

		if (count($resourceArray) == "0"){
			echo "<p><i>"._("No submitted requests")."</i></p>";
		}else{
		?>

			<table class='dataTable table-border table-striped'>
			<thead>
			<tr>
				<th scope="col" class="numeric"><?php echo _("ID");?></th>
				<th scope="col"><?php echo _("Name");?></th>
				<th scope="col" class="date"><?php echo _("Date Created");?></th>
				<th scope="col"><?php echo _("Status");?></th>
			</tr>
			</thead>
			<tbody>
		<?php
			foreach ($resourceArray as $resource){
				$status = new Status(new NamedArguments(array('primaryKey' => $resource['statusID'])));

		?>
				<tr id='tr_<?php echo $resource['resourceID']; ?>'>
					<td class="numeric"><a href='resource.php?resourceID=<?php echo $resource['resourceID']; ?>'><?php echo $resource['resourceID']; ?></a></td>
					<th scope="row"><a href='resource.php?resourceID=<?php echo $resource['resourceID']; ?>'><?php echo $resource['titleText']; ?></a></th>
					<td class="date"><?php echo format_date($resource['createDate']); ?></td>
					<td><?php echo $status->shortName; ?></td>
					</td>
				</tr>



			<?php
			}

			echo "</tbody></table>";

		}

?>

