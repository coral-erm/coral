<?php

		$instanceArray = array();
		$obj = new Fund();

		$instanceArray = $obj->allAsArray();
		?>
		<div class='adminHeader header'>
			<h3 class='adminRightHeader'><?php echo  _("Fund"); ?></h3>
				<span class='addElement'><?php echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminFundUpdateForm&updateID=\",278,360)' class='thickbox'><img id='addFund' src='images/plus.gif' title='"._("add fund")."'/></a><br/>";?></span>
				<span class='ImportElement'><?php echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"importFunds.php?action=getAdminFundUpdateForm&updateID=\",275,400)' class='thickbox'><img id='ImportFund'src='images/fund-import-blueGrey_12px.png' title='"._("import fund")."'/></a>"; ?></span>
		</div>


		<?php
		if (is_array($instanceArray) && count($instanceArray) > 0) {
			?>
			<table class='linedDataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("Code");?></th>
				<th scope="col"><?php echo _("Name");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				<th scope="col"><?php echo _("Archived");?></th>
				</tr>
				</thead>
				<tbody>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<th scope='row'>" . $instance['fundCode'] . "</th>";
					echo "<td id='fund-short-name'>" . $instance['shortName'] . "</td>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminFundUpdateForm&updateID=" . $instance['fundID'] . "\",278,360)' class='thickbox'><img src='images/edit.gif' alt='edit' title='edit'></a>";
					echo "<a href='javascript:deleteFund(\"Fund\", \"" . $instance['fundID'] . "\");'><img src='images/cross.gif' alt='remove' title='remove'></a></td>";
					if ($instance['archived'] == 1)
					{
						echo "<td><label><input type='checkbox' title='Archive' id='archived' checked value=" . $instance['archived'] . "  onclick='javascript:archiveFund(this.checked, \"" . $instance['fundID'] . "\", \"" . $instance['fundCode'] . "\", \"" . $instance['shortName'] . "\");' > </input></label></td>";
					}
					else
					{
						echo "<td><label><input type='checkbox' title='Archive' id='archived' onclick='javascript:archiveFund( this.checked, \"" . $instance['fundID'] . "\", \"" . $instance['fundCode'] . "\", \"" . $instance['shortName'] . "\");' > </input></label></td>";
					}
					echo "</tr>";
				}
				?>
				</tbody>
			</table>
			<?php

		}else{
			echo _("(none found)") . "<br />";
		}




?>
