<?php

		$alertEmailAddress = new AlertEmailAddress();
		$alertDaysInAdvance = new AlertDaysInAdvance();


		$emailAddressArray = $alertEmailAddress->allAsArray();
		$daysInAdvanceArray = $alertDaysInAdvance->allAsArray();
		?>
		<div class="adminHeaderAlert">
		<?php echo "<div class='adminRightHeader'>"._("Alert Settings")."</div><br>";?>
		<div class="addElement"><?php echo "<div><span class= 'addIconAlert'>"._("Add an email:")."  &nbsp;</span><a href='ajax_forms.php?action=getAdminAlertEmailForm&alertEmailAddressID=&height=128&width=260&modal=true' class='thickbox'><img id='addAlertEmail' src='images/plus.gif' title='"._("add Email")."' /></a></div>";?></div></br>
		</div>
		<?php
		if (count($emailAddressArray) > 0){
			?>
			<table class='linedDataTable'>
				<tr>
				<th style='width:100%;'><?php echo _("Email Address");?></th>
				<th style='width:20px;'>&nbsp;</th>
				<th style='width:20px;'>&nbsp;</th>
				</tr>
				<?php

				foreach($emailAddressArray as $emailAddress) {
					echo "<tr>";
					echo "<td>" . $emailAddress['emailAddress'] . "</td>";
					echo "<td><a href='ajax_forms.php?action=getAdminAlertEmailForm&alertEmailAddressID=" . $emailAddress['alertEmailAddressID'] . "&height=128&width=260&modal=true' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a></td>";
					echo "<td><a href='javascript:deleteAlert(\"AlertEmailAddress\", " . $emailAddress['alertEmailAddressID'] . ");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)")."<br />";
		}



		?>
		<div class="adminHeader">
			</div>
			<div class="addElement" style= "padding-bottom: 8px;"><?php echo "<div><span class= 'addIconAlert'> "._("Add a day:")." &nbsp;</span><a href='ajax_forms.php?action=getAdminAlertDaysForm&alertDaysInAdvanceID=&height=128&width=260&modal=true' class='thickbox'><img id='addAlertDay' src='images/plus.gif' title='"._("add day")."'/></a></div>";?></div>

		</div>
		<?php
		if (count($daysInAdvanceArray) > 0){
			?>
			<table class='linedDataTable'>
				<tr>
				<th style='width:100%;'><?php echo _("Days in advance of expiration");?></th>
				<th style='width:20px;'&nbsp;</th>
				<th style='width:20px;'>&nbsp;</th>
				</tr>
				<?php

				foreach($daysInAdvanceArray as $daysInAdvance) {
					echo "<tr>";
					echo "<td>" . $daysInAdvance['daysInAdvanceNumber'] . "</td>";
					echo "<td><a href='ajax_forms.php?action=getAdminAlertDaysForm&alertDaysInAdvanceID=" . $daysInAdvance['alertDaysInAdvanceID'] . "&height=128&width=260&modal=true' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit")."'></a></td>";
					echo "<td><a href='javascript:deleteAlert(\"AlertDaysInAdvance\", " . $daysInAdvance['alertDaysInAdvanceID'] . ");'><img src='images/cross.gif' alt='"._("remove")."' title='"._("remove")."'></a></td>";
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)")."<br />";
		}






?>
