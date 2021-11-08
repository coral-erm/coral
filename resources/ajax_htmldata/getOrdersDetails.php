<?php
$resourceID = $_GET['resourceID'];
$resourceAcquisitionID = $_GET['resourceAcquisitionID'];

if ($resourceAcquisitionID) {
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
	$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

	$orderType = new OrderType(new NamedArguments(array('primaryKey' => $resourceAcquisition->orderTypeID)));
		$acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $resourceAcquisition->acquisitionTypeID)));

		//get purchase sites
		$sanitizedInstance = array();
		$instance = new PurchaseSite();
		$purchaseSiteArray = array();
		foreach ($resourceAcquisition->getPurchaseSites() as $instance) {
			$purchaseSiteArray[]=$instance->shortName;
		}

        $organization = $resourceAcquisition->getOrganization();
        
		if(isset($organization['organization'])){
			$organizationName = $organization['organization'];
		}else{
			$organizationName = "";
		}
        //$organization = new Organization(new NamedArguments(array('primaryKey' => $resourceAcquisition->organizationID)));
        //$organizationName = $organization->shortName;

?>
		<table class='linedFormTable' style='padding:0x;margin:0px;height:100%;'>
			<tr>
			<th colspan='2' style='vertical-align:bottom;'>
			<span style='float:left;vertical-align:bottom;'><?php echo _("Order");?></span>
            <span style='float:right;vertical-align:bottom;'>
			<?php if ($user->canEdit()){ ?>
				<a href='ajax_forms.php?action=getOrderForm&height=400&width=440&modal=true&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>' class='thickbox' id='editOrder'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit order information");?>'></a>
			<?php } ?>
            <?php if ($user->isAdmin && $resource->countResourceAcquisitions() > 1) { ?>
                <a href='javascript:void(0);'
                    class='removeOrder'
                    id='<?php echo $resourceAcquisitionID; ?>'
                    >

                    <img src='images/cross.gif'
                        alt='<?php echo _("remove order");?>'
                        title='<?php echo _("remove order");?>' /></a>
            <?php } ?>
            </span>
			</th>
			</tr>

            <?php if ($resourceAcquisition->organizationID) { ?>
				<tr>
				<td style='vertical-align:top;width:110px;'><?php echo _("Organization:");?></td>
				<td style='width:350px;'><?php echo $organizationName; ?></td>
				</tr>
			<?php } ?>

			<?php if ($resourceAcquisition->acquisitionTypeID) { ?>
				<tr>
				<td style='vertical-align:top;width:110px;'><?php echo _("Acquisition Type:");?></td>
				<td style='width:350px;'><?php echo $acquisitionType->shortName; ?></td>
				</tr>
			<?php } ?>

			<?php if ($resourceAcquisition->orderNumber) { ?>
				<tr>
				<td style='vertical-align:top;width:110px;'><?php echo _("Order Number:");?></td>
				<td style='width:350px;'><?php echo $resourceAcquisition->orderNumber; ?></td>
				</tr>
			<?php } ?>

			<?php if ($resourceAcquisition->systemNumber) { ?>
				<tr>
				<td style='vertical-align:top;width:110px;'><?php echo _("System Number:");?></td>
				<td style='width:350px;'>
				<?php
					echo $resourceAcquisition->systemNumber;
					if ($config->settings->catalogURL != ''){
						echo "&nbsp;&nbsp;<a href='" . $config->settings->catalogURL . $resourceAcquisition->systemNumber . "' target='_blank'>"._("catalog view")."</a>";
					}
				?>
				</td>
				</tr>
			<?php } ?>

            <?php if ($resourceAcquisition->libraryNumber) { ?>
				<tr>
				<td style='vertical-align:top;width:110px;'><?php echo _("Library Number:");?></td>
				<td style='width:350px;'><?php echo $resourceAcquisition->libraryNumber; ?></td>
				</tr>
			<?php } ?>

			<?php if (count($purchaseSiteArray) > 0) { ?>
				<tr>
				<td style='vertical-align:top;width:110px;'><?php echo _("Purchasing Sites:");?></td>
				<td style='width:350px;'><?php echo implode(", ", $purchaseSiteArray); ?></td>
				</tr>
			<?php } ?>

			<?php if (($resourceAcquisition->subscriptionStartDate) && ($resourceAcquisition->subscriptionStartDate != '0000-00-00')) { ?>
			<tr>
			<td style='vertical-align:top;width:110px;'><?php echo _("Sub Start:");?></td>
			<td style='width:350px;'><?php echo format_date($resourceAcquisition->subscriptionStartDate); ?></td>
			</tr>
			<?php } ?>

			<?php if (($resourceAcquisition->subscriptionEndDate) && ($resourceAcquisition->subscriptionEndDate != '0000-00-00')) { ?>
			<tr>
			<td style='vertical-align:top;width:110px;'>Current Sub End:</td>
			<td style='width:350px;'><?php echo format_date($resourceAcquisition->subscriptionEndDate); ?>&nbsp;&nbsp;
			<?php if ($resourceAcquisition->subscriptionAlertEnabledInd == "1") { echo "<i>"._("Expiration Alert Enabled")."</i>"; } ?>
			</td>
			</tr>
			<?php } ?>

			</table>
			<?php if ($user->canEdit()){ ?>
				<a href='ajax_forms.php?action=getOrderForm&height=400&width=440&modal=true&resourceID=<?php echo $resourceAcquisition->resourceID; ?>' class='thickbox'><?php echo _("create new order");?></a> - 
				<a href='ajax_forms.php?action=getOrderForm&height=400&width=440&modal=true&resourceAcquisitionID=<?php echo $resourceAcquisition->resourceAcquisitionID; ?>&resourceID=<?php echo $resourceAcquisition->resourceID; ?>&op=clone' class='thickbox'><?php echo _("clone order");?></a> - 
				<a href='ajax_forms.php?action=getOrderForm&height=400&width=440&modal=true&resourceAcquisitionID=<?php echo $resourceAcquisition->resourceAcquisitionID; ?>&resourceID=<?php echo $resourceAcquisition->resourceID; ?>' class='thickbox'><?php echo _("edit order information");?></a>
			<?php } ?>
<?php } else {
echo _("This resource does not seem to have an order. It should have one. Please "); ?><a href='ajax_forms.php?action=getOrderForm&height=400&width=440&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox'><?php echo _("create an order");?></a>
<?php
}
?>
