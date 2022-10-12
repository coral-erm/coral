<?php

		$instanceArray = array();
		$obj = new Fund();

		$instanceArray = $obj->allAsArray();
		?>
		<div class='adminHeader'>
			<div><?php echo "<div class='adminRightHeader'>" . _("Fund") . "</div>"; ?></div>
			<div class='addElement'><?php echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminFundUpdateForm&updateID=\",278,360)' class='thickbox'><img id='addFund' src='images/plus.gif' title='"._("add fund")."'/></a><br/>";?></div>
			<div class='ImportElement'><?php echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"importFunds.php?action=getAdminFundUpdateForm&updateID=\",275,400)' class='thickbox'><img id='ImportFund'src='images/fund-import-blueGrey_12px.png' title='"._("import fund")."'/></a>"; ?></div>

		</div>


		<?php
		if (count($instanceArray) > 0){
			?>
			<table  class='linedDataTable' >
				<tr>
				<th><?php echo _("Code");?></th>
				<th><?php echo _("Name");?></th>
				<th style='width:20px;'>&nbsp;</th>
				<th style='width:20px;'>&nbsp;</th>
				<th style='width:20px;'>&nbsp;</th>
				</tr>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<td>" . $instance['fundCode'] . "</td>";
					echo "<td id='fund-short-name'>" . $instance['shortName'] . "</td>";
					echo "<td><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminFundUpdateForm&updateID=" . $instance['fundID'] . "\",278,360)' class='thickbox'><img src='images/edit.gif' alt='edit' title='edit'></a></td>";
					echo "<td><a href='javascript:deleteFund(\"Fund\", \"" . $instance['fundID'] . "\");'><img src='images/cross.gif' alt='remove' title='remove'></a></td>";
					if ($instance['archived'] == 1)
					{
						echo "<td><input type='checkbox' title='Archive' id='archived' checked value=" . $instance['archived'] . "  onclick='javascript:archiveFund(this.checked, \"" . $instance['fundID'] . "\", \"" . $instance['fundCode'] . "\", \"" . $instance['shortName'] . "\");' > </input></td>";
					}
					else
					{
						echo "<td><input type='checkbox' title='Archive' id='archived' onclick='javascript:archiveFund( this.checked, \"" . $instance['fundID'] . "\", \"" . $instance['fundCode'] . "\", \"" . $instance['shortName'] . "\");' > </input></td>";
					}
					echo "</tr>";
				}
				?>
				<br />
			</table>
			<?php

		}else{
			echo _("(none found)") . "<br />";
		}




?>
