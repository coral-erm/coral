<?php
	$resourceID = $_GET['resourceID'];
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

		$externalLoginArray = $resource->getExternalLoginArray();

		$resELFlag = 0;
		$orgELFlag = 0;

		if (count($externalLoginArray) > 0){
			foreach ($externalLoginArray as $externalLogin){

				if ($resELFlag == 0 && array_key_exists('organizationName', $externalLogin) && $externalLogin['organizationName'] == ''){
					echo "<div class='formTitle' style='padding:4px; font-weight:bold; margin-bottom:8px;'>"._("Resource Specific:")."</div>";
					$resELFlag = 1;
				}else if ($orgELFlag == 0 && array_key_exists('organizationName', $externalLogin) && $externalLogin['organizationName'] != ''){
					if ($resELFlag == 0){
						echo "<i>"._("No Resource Specific Accounts")."</i><br /><br />";
					}

					if ($user->canEdit()){ ?>
						<a href='ajax_forms.php?action=getAccountForm&height=314&width=403&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox' id='newAccount'><?php echo _("add new account");?></a>
						<br /><br /><br />
					<?php
					}

					echo "<div class='formTitle' style='padding:4px; font-weight:bold; margin-bottom:8px;'>"._("Inherited:")."</div>";
					$orgELFlag = 1;
				}else{
					echo "<br />";
				}

			?>
				<table class='linedFormTable'>
				<tr>
				<th colspan='2' style='background:#f5f8fa;'>
				<span style='float:left; vertical-align:bottom;'>
					<?php echo $externalLogin['externalLoginType']; ?>
				</span>

				<span style='float:right;'>
				<?php
					if ($user->canEdit() &&
                       (!array_key_exists('organizationName', $externalLogin) || (array_key_exists('organizationName', $externalLogin) && $externalLogin['organizationName'] == ''))) { ?>

						<a href='ajax_forms.php?action=getAccountForm&height=314&width=403&modal=true&resourceID=<?php echo $resourceID; ?>&externalLoginID=<?php echo $externalLogin['externalLoginID']; ?>' class='thickbox'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit account");?>'></a>  <a href='javascript:void(0);' class='removeAccount' id='<?php echo $externalLogin['externalLoginID']; ?>'><img src='images/cross.gif' alt='<?php echo _("remove account");?>' title='<?php echo _("remove account");?>'></a>
						<?php
					}else{
						echo "&nbsp;";
					}
				?>
				</span>
				</th>
				</tr>

				<?php if (isset($externalLogin['organizationName'])) { ?>
				<tr>
				<td style='vertical-align:top; width:130px;'>Organization:</td>
				<td><?php echo $externalLogin['organizationName'] . "&nbsp;&nbsp;<a href='" . $util->getCORALURL() . "organizations/orgDetail.php?showTab=accounts&organizationID=" . $externalLogin['organizationID'] . "' target='_blank'><img src='images/arrow-up-right.gif' alt='"._("Visit Account in Organizations Module")."' title='"._("Visit Account in Organizations Module")."' style='vertical-align:top;'></a>"; ?></td>
				</tr>
				<?php
				}

				if ($externalLogin['loginURL']) { ?>
				<tr>
				<td style='vertical-align:top; width:130px;'>Login URL:</td>
				<td><?php echo $externalLogin['loginURL']; ?>&nbsp;&nbsp;<a href='<?php echo $externalLogin['loginURL']; ?>' target='_blank'><img src='images/arrow-up-right.gif' alt='<?php echo _("Visit Login URL");?>' title='<?php echo _("Visit Login URL");?>' style='vertical-align:top;'></a></td>
				</tr>
				<?php
				}

				if ($externalLogin['username']) { ?>
				<tr>
				<td style='vertical-align:top; width:130px;'><?php echo _("User Name:");?></td>
				<td><?php echo $externalLogin['username']; ?></td>
				</tr>
				<?php
				}

				if ($externalLogin['password']) { ?>
				<tr>
				<td style='vertical-align:top; width:130px;'><?php echo _("Password:");?></td>
				<td><?php echo $externalLogin['password']; ?></td>
				</tr>
				<?php
				}

				if ($externalLogin['updateDate']) { ?>
				<tr>
				<td style='vertical-align:top; width:130px;'><?php echo _("Last Updated:");?></td>
				<td><i><?php echo format_date($externalLogin['updateDate']); ?></i></td>
				</tr>
				<?php
				}

				if ($externalLogin['emailAddress']) { ?>
				<tr>
				<td style='vertical-align:top; width:130px;'><?php echo _("Registered Email:");?></td>
				<td><?php echo $externalLogin['emailAddress']; ?></td>
				</tr>
				<?php
				}

				if ($externalLogin['noteText']) { ?>
				<tr>
				<td style='vertical-align:top; width:130px;'><?php echo _("Notes:");?></td>
				<td><?php echo nl2br($externalLogin['noteText']); ?></td>
				</tr>
				<?php
				}
				?>
				</table>
			<?php
			}
		} else {
			echo "<i>"._("No accounts available")."</i><br /><br />";

		}

		if ($user->canEdit() && ($orgELFlag == 0)){ ?>
			<a href='ajax_forms.php?action=getAccountForm&height=314&width=403&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox' id='newAccount'><?php echo _("add new account");?></a>
			<br /><br /><br />
		<?php
		}

?>

