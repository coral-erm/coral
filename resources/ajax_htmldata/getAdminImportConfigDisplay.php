<?php
		$instanceArray = array();
		$obj = new ImportConfig();

		$instanceArray = $obj->allAsArray();
		?>
		<div class='adminHeader'>
		<div><?php echo "<div class='adminRightHeader'>" . _("Import Configuration") . "</div>";?></div>
		<div class='addElement'><?php 	echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminImportConfigUpdateForm&updateID=\",860,1024)' class='thickbox'><img id='addImportConfig' src='images/plus.gif' title='"._("add import configuration")."' /></a><br/>";?></div>
		</div>
		<?php
		if (count($instanceArray) > 0){
			?>
			<table  class='linedDataTable' >
				<tr>
				<th><?php echo _("Name");?></th>
				<th style='width:20px;'>&nbsp;</th>
				<th style='width:20px;'>&nbsp;</th>
				</tr>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<td>" . $instance['shortName'] . "</td>";
					echo "<td><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminImportConfigUpdateForm&updateID=" . $instance['importConfigID'] . "\",800,1024)' class='thickbox'><img src='images/edit.gif' alt='edit' title='edit'></a></td>";
					echo "<td><a href='javascript:deleteImportConfig(\"ImportConfig\", \"" . $instance['importConfigID'] . "\");'><img src='images/cross.gif' alt='remove' title='remove'></a></td>";
					echo "</tr>";
				}
				?>
				<br />
			</table>
			<?php

		}else{
			echo "(none found)<br />";
		}

?>
