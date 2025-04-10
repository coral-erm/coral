<?php
		$instanceArray = array();
		$obj = new ImportConfig();

		$instanceArray = $obj->allAsArray();
		?>
		<div class='adminHeader header'>
			<h3 class='adminRightHeader'><?php echo _("Import Configuration");?></h3>	
			<span class='addElement'><?php 	echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminImportConfigUpdateForm&updateID=\",860,1024)' class='thickbox'><img id='addImportConfig' src='images/plus.gif' title='"._("add import configuration")."' /></a><br/>";?></span>
	</div>
		<?php
		if (is_array($instanceArray) && count($instanceArray) > 0) {
			?>
			<table class='linedDataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("Name");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				</tr>
				</thead>
				<tbody>
				</tr>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<th scope='row'>" . $instance['shortName'] . "</th>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminImportConfigUpdateForm&updateID=" . $instance['importConfigID'] . "\",800,1024)' class='thickbox'><img src='images/edit.gif' alt='edit' title='edit'></a></td>";
					echo "<a href='javascript:deleteImportConfig(\"ImportConfig\", \"" . $instance['importConfigID'] . "\");'><img src='images/cross.gif' alt='remove' title='remove'></a></td>";
					echo "</tr>";
				}
				?>
				</tbody>
			</table>
			<?php

		}else{
			echo "(none found)<br />";
		}

?>
