<?php

		$instanceArray = array();
		$obj = new PricingFormula();

		$instanceArray = $obj->allAsArray();

		echo "<div class='adminRightHeader'>"._("Pricing formulas")."</div>";

		if (count($instanceArray) > 0){
			?>
			<table class='linedDataTable'>
				<tr>
				<th style='width:100%;'><?php echo _("Name");?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				</tr>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<td>" . $instance['shortName'] . "</td>";
					echo "<td><a href='ajax_forms.php?action=getAdminPricingFormulaUpdateForm&pricingFormulaID=" . $instance['pricingFormulaID'] . "&height=530&width=385&modal=true' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a></td>";
					echo "<td><a href='javascript:deletePricingFormula(" . $instance['pricingFormulaID'] . ");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)");
		}

		echo "<a href='ajax_forms.php?action=getAdminPricingFormulaUpdateForm&updateID=&height=530&width=385&modal=true' class='thickbox'>"._("add new formula")."</a>";

?>
