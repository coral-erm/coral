<?php

		$alertEmailAddress = new AlertEmailAddress();
		$alertDaysInAdvance = new AlertDaysInAdvance();


		$emailAddressArray = $alertEmailAddress->allAsArray();
		$daysInAdvanceArray = $alertDaysInAdvance->allAsArray();
		?>
		<div class="adminHeaderAlert header">
			<h3 class='adminRightHeader'><?php echo ("Alert Settings");?></h3>
			<span class="addElement"><?php echo "<div><span class= 'addIconAlert'>"._("Add an email:")."  &nbsp;</span><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminAlertEmailForm&alertEmailAddressID=\",228,360,)' class='thickbox'><img id='addAlertEmail' src='images/plus.gif' title='"._("add Email")."' /></a></div>";?></span></br>
		</div>
		<?php
		if (is_array($emailAddressArray) && count($emailAddressArray) > 0) {
			?>
			<h4><?php echo _("Email Addresses");?></h4>
			<table class='linedDataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("Email Address");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				</tr>
				</thead>
				<tbody>

				<?php

				foreach($emailAddressArray as $emailAddress) {
					echo "<tr>";
					echo "<th scope='row'>" . $emailAddress['emailAddress'] . "</th>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminAlertEmailForm&alertEmailAddressID=" . $emailAddress['alertEmailAddressID'] . "\",228,360)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a>";
					echo "<a href='javascript:deleteAlert(\"AlertEmailAddress\", " . $emailAddress['alertEmailAddressID'] . ");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
			</tbody>
			</table>
			<?php

		}else{
			echo "<p>". _("(none found)")."</p>";
		}



		?>
		<div class="adminHeader">
			<!-- TODO: is this the appropriate heading? -->
			<div class="header">
				<h3><?php echo _("Alert Days");?></h3>
				<div class="addElement"><?php echo "<div><span class= 'addIconAlert'> "._("Add a day:")." &nbsp;</span><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminAlertDaysForm&alertDaysInAdvanceID=\",228,360)' class='thickbox'><img id='addAlertDay' src='images/plus.gif' title='"._("add day")."'/></a></div>";?></div>
			</div>
		</div>
		<?php
		if (is_array($daysInAdvanceArray) && count($daysInAdvanceArray) > 0) {
			?>

			<table class='linedDataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("Days in advance of expiration");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				</tr>
				</thead>
				<tbody>
				<?php

				foreach($daysInAdvanceArray as $daysInAdvance) {
					echo "<tr>";
					echo "<th scope='row'>" . $daysInAdvance['daysInAdvanceNumber'] . "</th>";
					echo "<td class='actions'><a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminAlertDaysForm&alertDaysInAdvanceID=" . $daysInAdvance['alertDaysInAdvanceID'] . "\",228,360)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a>";
					echo "<a href='javascript:deleteAlert(\"AlertDaysInAdvance\", " . $daysInAdvance['alertDaysInAdvanceID'] . ");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
			</tbody>	
			</table>
			<?php

		}else{
			echo "<p>". _("(none found)")."</p>";
		}






?>
