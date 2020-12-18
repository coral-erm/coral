<?php
		$className = $_GET['className'];
		$title = preg_replace("/[A-Z]/", " \\0" , $className);
		//The preg replace above adds a leading space to the string -- this must be removed so gettext can properly translate the string
		$title = trim($title);


		$instanceArray = array();
		$obj = new $className();

		$instanceArray = $obj->allAsArray();
		?>
		<div class= "adminHeader">
			<div ><?php echo "<div class='adminRightHeader'>" . _($title) . "</div>";?></div>
			<div class="addElement"><?php echo "<a href='ajax_forms.php?action=getAdminUpdateForm&className=" . $className . "&updateID=&height=128&width=260&modal=true' class='thickbox'><img id='addType' src='images/plus.gif' title='"._("add")."'/></a>";?></div>
		</div>
		<?php
		if (count($instanceArray) > 0){
			?>
			<table class='linedDataTable'>
				<tr>
				<th style='width:100%;'><?php echo _("Value");?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				</tr>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<td>" . $instance['shortName'] . "</td>";
					echo "<td><a href='ajax_forms.php?action=getAdminUpdateForm&className=" . $className . "&updateID=" . $instance[lcfirst($className) . 'ID'] . "&height=128&width=260&modal=true' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a></td>";
					echo "<td><a href='javascript:void(0);' class='removeData' cn='" . $className . "' id='" . $instance[lcfirst($className) . 'ID'] . "'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)")."<br />";
		}



?>
