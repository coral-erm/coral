<?php
	$resourceID = $_GET['resourceID'];
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

		$externalLoginArray = $resource->getExternalLoginArray();

		$resELFlag = 0;
		$orgELFlag = 0;

		if (is_array($externalLoginArray) && count($externalLoginArray) > 0) {
			foreach ($externalLoginArray as $externalLogin){

				if ($resELFlag == 0 && array_key_exists('organizationName', $externalLogin) && $externalLogin['organizationName'] == ''){
					echo "<div class='formTitle' style='padding:4px; font-weight:bold; margin-bottom:8px;'>"._("Resource Specific:")."</div>";
					$resELFlag = 1;
				}else if ($orgELFlag == 0 && array_key_exists('organizationName', $externalLogin) && $externalLogin['organizationName'] != ''){
					if ($resELFlag == 0){
						echo "<i>"._("No Resource Specific Accounts")."</i><br /><br />";
					}

					if ($user->canEdit()){ ?>
						<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getAccountForm&height=314&width=403&modal=true&resourceID=<?php echo $resourceID; ?>",350,450)' class='thickbox' id='newAccount'><?php echo _("add new account");?></a>
						<br /><br /><br />
					<?php
					}

					echo "<div class='formTitle' style='padding:4px; font-weight:bold; margin-bottom:8px;'>"._("Inherited:")."</div>";
					$orgELFlag = 1;
				}else{
					echo "<br />";
				}

			?>
				
		<div class="header">
			<h3><?php echo $externalLogin['externalLoginType']; ?></h3>
			<span class="addElement">
				<?php
					if ($user->canEdit() &&
											(!array_key_exists('organizationName', $externalLogin) || (array_key_exists('organizationName', $externalLogin) && $externalLogin['organizationName'] == ''))) { ?>

						<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getAccountForm&height=314&width=403&modal=true&resourceID=<?php echo $resourceID; ?>&externalLoginID=<?php echo $externalLogin['externalLoginID']; ?>",350,450)' class='thickbox addElement'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit account");?>'></a>  <a href='javascript:void(0);' class='removeAccount' id='<?php echo $externalLogin['externalLoginID']; ?>'><img src='images/cross.gif' alt='<?php echo _("remove account");?>' title='<?php echo _("remove account");?>'></a>
						<?php
					}else{
						echo "&nbsp;";
					}
				?>
			</span>
		</div>

		<div class='grid-form'>
				
				<dl class="dl-grid">
				<?php if (isset($externalLogin['organizationName'])) { ?>
				<dt><?php echo('Organization:'); ?></dt>
				<dd><?php echo $externalLogin['organizationName'] . "&nbsp;&nbsp;<a href='" . $util->getCORALURL() . "organizations/orgDetail.php?showTab=accounts&organizationID=" . $externalLogin['organizationID'] . "' " . getTarget() . "><img src='images/arrow-up-right.gif' alt='"._("Visit Account in Organizations Module")."' title='"._("Visit Account in Organizations Module")."'></a>"; ?></dd>
				<?php
				}

				if ($externalLogin['loginURL']) { ?>
				
				<dt><?php echo('Login URL:'); ?></dt>
				<dd><?php echo $externalLogin['loginURL']; ?>&nbsp;&nbsp;<a href='<?php echo $externalLogin['loginURL']; ?>' <?php echo getTarget(); ?>><img src='images/arrow-up-right.gif' alt='<?php echo _("Visit Login URL");?>' title='<?php echo _("Visit Login URL");?>' style='vertical-align:top;'></a></dd>
				
				<?php
				}

				if ($externalLogin['username']) { ?>
				<dt><?php echo _("User Name:");?></dt>
				<dd><?php echo $externalLogin['username']; ?></dd>
				<?php
				}

				if ($externalLogin['password']) { ?>
				<dt><?php echo _("Password:");?></dt>
				<dd><?php echo $externalLogin['password']; ?></dd>
				<?php
				}

				if ($externalLogin['updateDate']) { ?>
				<dt><?php echo _("Last Updated:");?></dt>
				<dd><i><?php echo format_date($externalLogin['updateDate']); ?></i></dd>
				<?php
				}

				if ($externalLogin['emailAddress']) { ?>
				<dt><?php echo _("Registered Email:");?></dt>
				<dd><?php echo $externalLogin['emailAddress']; ?></dd>
				<?php
				}

				if ($externalLogin['noteText']) { ?>
				<dt><?php echo _("Notes:");?></dt>
				<dd><?php echo nl2br($externalLogin['noteText']); ?></dd>
				<?php
				}
				?>
				</div>
			<?php
			}
		} else {
			echo "<p><i>"._("No accounts available")."</i></p>";

		}

		if ($user->canEdit() && ($orgELFlag == 0)){ ?>
			<p><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getAccountForm&height=314&width=403&modal=true&resourceID=<?php echo $resourceID; ?>",350,450)' class='thickbox' id='newAccount'><?php echo _("add new account");?></a></p>
		<?php
		}

?>

