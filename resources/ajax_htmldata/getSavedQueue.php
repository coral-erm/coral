<?php

		$resourceArray = array();
		$resourceArray = $user->getResourcesInQueue('saved');

		echo "<div class='adminRightHeader'>"._("Saved Requests")."</div>";



		if (count($resourceArray) == "0"){
			echo "<i>"._("No saved requests")."</i>";
		}else{
		?>

			<table class='dataTable' style='width:570px;margin-bottom:5px;'>
			<tr>
				<th><?php echo _("ID");?></th>
				<th><?php echo _("Name");?></th>
				<th><?php echo _("Date Created");?></th>
				<th><?php echo _("Status");?></th>
				<th>&nbsp;</th>
			</tr>

		<?php
			$i=0;
			foreach ($resourceArray as $resource){

				$i++;
				if ($i % 2 == 0){
					$classAdd="";
				}else{
					$classAdd="class='alt'";
				}

				$status = new Status(new NamedArguments(array('primaryKey' => $resource['statusID'])));



		?>
				<tr id='tr_<?php echo $resource['resourceID']; ?>'>
					<!-- <td <?php echo $classAdd; ?>><a href='ajax_forms.php?action=getNewResourceForm&height=483&width=775&resourceID=<?php echo $resource['resourceID']; ?>&modal=true' class='thickbox'><?php echo $resource['resourceID']; ?></a></td> -->
				 	<td <?php echo $classAdd; ?><a href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getNewResourceForm&resourceID=<?php echo $resource['resourceID']; ?>", 1000,1000)' class='thickbox'><?php echo $resource['resourceID']; ?></a></td>	

<!--					<td <?php echo $classAdd; ?>><a href='ajax_forms.php?action=getNewResourceForm&height=483&width=775&resourceID=<?php echo $resource['resourceID']; ?>&modal=true' class='thickbox'><?php echo $resource['titleText']; ?></a></td> -->


  <td <?php echo $classAdd; ?>><a href='javascript:void(0);'  onclick='javascript:myDialog("ajax_forms.php?action=getNewResourceForm&resourceID=<?php echo $resource['resourceID']; ?>", 1000,1000)' class='thickbox'><?php echo $resource['titleText']; ?></a></td>




					<td <?php echo $classAdd; ?>><?php echo format_date($resource['createDate']); ?></td>
					<td <?php echo $classAdd; ?>><?php echo $status->shortName; ?></td>
					<td <?php echo $classAdd; ?> style='text-align:right; width:40px;'>
					<a  href='javascript:void(0);' onclick='javascript:myDialog("ajax_forms.php?action=getNewResourceForm&resourceID=<?php echo $resource['resourceID']; ?>", 1000,1000)' class='thickbox'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit request");?>'></a>&nbsp;
					<a href='javascript:void(0);' class='deleteRequest' id='<?php echo $resource['resourceID']; ?>'><img src='images/cross.gif' alt='<?php echo _("remove request");?>' title='<?php echo _("remove request");?>'></a>
					</td>
				</tr>



			<?php
			}

			echo "</table>";

		}

?>

