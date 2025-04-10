<?php
		$className = $_GET['className'];


		$instanceArray = array();
		$obj = new $className();

		$instanceArray = $obj->allAsArray();

		echo "<h3 class='adminRightHeader'>" . preg_replace("/[A-Z]/", " \\0" , $className) . "</h3>";

		if (is_array($instanceArray) && count($instanceArray) > 0) {
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

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<th scope='row'>" . $instance['shortName'] . "</th>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getGeneralSubjectUpdateForm&className=" . $className . "&updateID=" . $instance[lcfirst($className) . 'ID'] . "&height=128&width=260&modal=true\",150,300)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a>";
					echo "<a href='javascript:void(0);' class='removeData' cn='" . $className . "' id='" . $instance[lcfirst($className) . 'ID'] . "'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
				</tbody>
			</table>
			<?php

		}else{
			echo "<p>". _("(none found)")."</p>";
		}

		echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUpdateForm&className=" . $className . "&updateID=&height=128&width=260&modal=true\",150,300)' class='thickbox'>"._("add new ") . strtolower(preg_replace("/[A-Z]/", " \\0" , lcfirst($className))) . "</a>";

?>

