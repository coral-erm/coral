<?php

		$resourceArray = array();
		$resourceArray = $user->getResourcesInQueue('saved');

		echo "<h3 class='adminRightHeader'>"._("Saved Requests")."</h3>";



		if (count($resourceArray) == "0"){
			echo "<i>"._("No saved requests")."</i>";
		}else{
		?>

			<table class='dataTable table-border table-striped'>
			<thead>
			<tr>
				<th scope="col" class="numeric"><?php echo _("ID");?></th>
				<th scope="col"><?php echo _("Name");?></th>
				<th scope="col" class="date"><?php echo _("Date Created");?></th>
				<th scope="col"><?php echo _("Status");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
			</tr>
		</thead>
		<tbody>

		<?php
			foreach ($resourceArray as $resource){
				$status = new Status(new NamedArguments(array('primaryKey' => $resource['statusID'])));
		?>
				<tr id='tr_<?php echo $resource['resourceID']; ?>'>
					<!-- <td><a href='ajax_forms.php?action=getNewResourceForm&height=483&width=775&resourceID=<?php echo $resource['resourceID']; ?>&modal=true' class='thickbox'><?php echo $resource['resourceID']; ?></a></td> -->
				 	<td class="numeric"><a href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getNewResourceForm&resourceID=<?php echo $resource['resourceID']; ?>", 1000,1000)' class='thickbox'><?php echo $resource['resourceID']; ?></a></td>	

<!--					<td><a href='ajax_forms.php?action=getNewResourceForm&height=483&width=775&resourceID=<?php echo $resource['resourceID']; ?>&modal=true' class='thickbox'><?php echo $resource['titleText']; ?></a></td> -->


  				<th scope="row"><a href='javascript:void(0);'  onclick='javascript:myDialog("ajax_forms.php?action=getNewResourceForm&resourceID=<?php echo $resource['resourceID']; ?>", 1000,1000)' class='thickbox'><?php echo $resource['titleText']; ?></a></th>
					<td class="date"><?php echo format_date($resource['createDate']); ?></td>
					<td><?php echo $status->shortName; ?></td>
					<td class="actions">
					<a  href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getNewResourceForm&resourceID=<?php echo $resource['resourceID']; ?>", 1000,1000)' class='thickbox'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit request");?>'></a>&nbsp;
					<a href='javascript:void(0);' class='deleteRequest' id='<?php echo $resource['resourceID']; ?>'><img src='images/cross.gif' alt='<?php echo _("remove request");?>' title='<?php echo _("remove request");?>'></a>
					</td>
				</tr>



			<?php
			}

			echo "</tbody></table>";

		}

?>

