<?php
		$className = $_GET['className'];
		$title = preg_replace("/[A-Z]/", " \\0" , $className);
		//The preg replace above adds a leading space to the string -- this must be removed so gettext can properly translate the string
		$title = trim($title);


		$instanceArray = array();
		$obj = new $className();

		$instanceArray = $obj->allAsArray();
		?>
		<div class= "adminHeader header">
			<h3 class='adminRightHeader'><?php echo _($title);?></h3>
			<span class="addElement"><?php echo "<a href='javascript:void(0);' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUpdateForm&className=" . $className . "\", 200,300)' class='thickbox'><img id='addType' src='images/plus.gif' title='"._("add")."'/></a>";?></span>
		</div>
		<?php
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
					echo "<td class='actions'><a href='javascript:void(0);' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUpdateForm&className=" . $className . "&updateID=" . $instance[lcfirst($className) . 'ID'] . "\", 200,300)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a>";
					echo "<a href='javascript:void(0);' class='removeData' cn='" . $className . "' id='" . $instance[lcfirst($className) . 'ID'] . "'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
				</tbody>
			</table>

            <script>$('.removeData').on('click', function () { deleteData($(this).attr("cn"), $(this).attr("id")); });</script>

			<?php

		}else{
			echo "<p>". _("(none found)")."</p>";
		}



?>
