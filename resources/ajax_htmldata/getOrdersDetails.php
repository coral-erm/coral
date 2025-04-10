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
<div class="header">
	<h3><?php echo _("Order");?></h3>
	<span class="addElement">
			<?php if ($user->canEdit()){ ?>
				<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getOrderForm&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",500,500)' class='thickbox' id='editOrder'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit order information");?>'></a>
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
</div>
	
	<dl class="dl-grid dl-compact">
	    <?php if ($resourceAcquisition->organizationID) { ?>
				<dt><?php echo _("Organization:");?></dt>
				<dd><?php echo $organizationName; ?></dd>
			<?php } ?>

			<?php if ($resourceAcquisition->acquisitionTypeID) { ?>
				<dt><?php echo _("Acquisition Type:");?></dt>
				<dd><?php echo $acquisitionType->shortName; ?></dd>
			<?php } ?>

			<?php if ($resourceAcquisition->orderNumber) { ?>
				<dt><?php echo _("Order Number:");?></dt>
				<dd><?php echo $resourceAcquisition->orderNumber; ?></dd>
			<?php } ?>

			<?php if ($resourceAcquisition->systemNumber) { ?>
				<dt><?php echo _("System Number:");?></dt>
				<dd>
				<?php
					echo $resourceAcquisition->systemNumber;
					if ($config->settings->catalogURL != ''){
						echo "&nbsp;&nbsp;<a href='" . $config->settings->catalogURL . $resourceAcquisition->systemNumber . "' " . getTarget() . ">"._("catalog view")."</a>";
					}
				?>
				</dd>
			<?php } ?>

            <?php if ($resourceAcquisition->libraryNumber) { ?>
				<dt><?php echo _("Library Number:");?></dt>
				<dd><?php echo $resourceAcquisition->libraryNumber; ?></dd>
			<?php } ?>

			<?php if (is_array($purchaseSiteArray) && count($purchaseSiteArray) > 0) { ?>
				<dt><?php echo _("Purchasing Sites:");?></dt>
				<dd><?php echo implode(", ", $purchaseSiteArray); ?></dd>
			<?php } ?>

			<?php if (($resourceAcquisition->subscriptionStartDate) && ($resourceAcquisition->subscriptionStartDate != '0000-00-00')) { ?>
			<dt><?php echo _("Sub Start:");?></dt>
			<dd><?php echo format_date($resourceAcquisition->subscriptionStartDate); ?></dd>
			<?php } ?>

			<?php if (($resourceAcquisition->subscriptionEndDate) && ($resourceAcquisition->subscriptionEndDate != '0000-00-00')) { ?>
			<dt><?php echo _("Current Sub End:");?></dt>
			<dd><?php echo format_date($resourceAcquisition->subscriptionEndDate); ?>&nbsp;&nbsp;
			<?php if ($resourceAcquisition->subscriptionAlertEnabledInd == "1") { echo "<i>"._("Expiration Alert Enabled")."</i>"; } ?>
			</dd>
			<?php } ?>

			</dl>
			<?php if ($user->canEdit()){ ?>
				<p>
				<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getOrderForm&resourceID=<?php echo $resourceAcquisition->resourceID; ?>",500,500)' class='thickbox'><?php echo _("create new order");?></a> - 
				<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getOrderForm&resourceAcquisitionID=<?php echo $resourceAcquisition->resourceAcquisitionID; ?>&resourceID=<?php echo $resourceAcquisition->resourceID; ?>&op=clone", 500,500)' class='thickbox'><?php echo _("clone order");?></a> - 
				<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getOrderForm&resourceAcquisitionID=<?php echo $resourceAcquisition->resourceAcquisitionID; ?>&resourceID=<?php echo $resourceAcquisition->resourceID; ?>",500,500)' class='thickbox'><?php echo _("edit order information");?></a>
			</p>
			<?php } ?>
<?php } else {
	// TODO: i18n placeholders
echo _("This resource does not seem to have an order. It should have one. Please "); ?><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getOrderForm&height=400&width=440&modal=true&resourceID=<?php echo $resourceID; ?>",430,480)' class='thickbox'><?php echo _("create an order");?></a>
<?php
}
?>
