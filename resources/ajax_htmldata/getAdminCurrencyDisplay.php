<?php

		$instanceArray = array();
		$obj = new Currency();

		$instanceArray = $obj->allAsArray();
		?>
		<div class='adminHeader header'>
			<h3 class='adminRightHeader'><?php echo _("Currency");?></h3>
			<span class='addElement'><?php echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminCurrencyUpdateForm&updateID=\",278,360)' class='thickbox'><img id='addCurrency' src='images/plus.gif' title='"._("add Currency")."' /></a>";?></span>
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
				</tr>
				</thead>
				<tbody>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<th scope='row'>" . $instance['currencyCode'] . "</th>";
					echo "<td>" . $instance['shortName'] . "</td>";
					//echo "<td><a href='ajax_forms.php?action=getAdminCurrencyUpdateForm&updateID=" . $instance['currencyCode'] . "&height=178&width=260&modal=true' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a></td>";
				 	echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminCurrencyUpdateForm&updateID=" . $instance['currencyCode'] . "\",278,360)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a>";
					echo "<a href='javascript:deleteCurrency(\"Currency\", \"" . $instance['currencyCode'] . "\");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
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
