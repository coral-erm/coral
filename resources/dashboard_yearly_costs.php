<?php
    include_once 'directory.php';
    $pageTitle=_('Dashboard');
    $customJSInclude .= '<script type="text/javascript" src="../js/plugins/datatables.min.js"></script>' . "\n";
    $customJSInclude .= '<script type="text/javascript" src="../js/plugins/datatables_defaults.js"></script>' . "\n";
    include 'templates/header.php';
    $dashboard = new Dashboard();

?>
<main id="dashboardPage">
    <article>
    <h2><?php echo _("Dashboard: yearly costs");?></h2>
    <div id="dashboardTable"></div>
</article>
<aside id="side" class="block-form" role="search">
    <form action="dashboard_yearly_costs_exports.php" method="POST">
    <fieldset>
        <legend><?php echo _("Filter on resources:"); ?></legend>
        <p class='searchRow'>
            <label for="resourceTypeID"><?php echo _("Resource type"); ?>:</label>
            <?php $dashboard->getResourceTypesAsDropdown(); ?>
        </p>
        <p class='searchRow'>
            <label for="subjectID"><?php echo _("Subject"); ?>:</label>
            <?php $dashboard->getSubjectsAsDropdown(); ?>
        </p>
        <p class='searchRow'>
            <label for="acquisitionTypeID"><?php echo _("Acquisition type"); ?>:</label>
            <?php $dashboard->getAcquisitionTypesAsDropdown(); ?>
        </p>
    </fieldset>
    
    <fieldset>
        <p class="searchRow">
            <legend><?php echo _("Filter on organizations:"); ?></legend>
            <label for="organizationID"><?php echo _("Organization"); ?>:</label>
            <?php $dashboard->getOrganizationsAsDropdown(); ?>
        </p>
        <p class='searchRow'>
            <label for="roleID"><?php echo _("Role"); ?>:</label>
            <?php $dashboard->getOrganizationsRolesAsDropdown(); ?>
        </p>
    </fieldset>

    <fieldset>
        <legend><?php echo _("Filter on payments:"); ?></legend>
        <p class='searchRow'>
            <label for="orderTypeID"><?php echo _("Order Type"); ?>:</label>
            <?php $dashboard->getOrderTypesAsDropdown(); ?>
        </p>

        <p class='searchRow'>
            <label for="fundID"><?php echo _("Fund"); ?>:</label>
            <?php $dashboard->getFundsAsDropdown(); ?>
        </p>

        <p class='searchRow'>
            <label for="costDetailsID"><?php echo _("Cost Details"); ?>:</label>
            <?php $dashboard->getCostDetailsAsDropdown(); ?>
        </p>
    </fieldset>

    <p class='searchRow'>
        <label for="startYear"><?php echo _("Year (start)"); ?>:</label>
        <input type="text" name="startYear" id="startYear" size="4" value="<?php echo (date('Y') - 1); ?>" />
        <label for="endYear"><?php echo _("Year (end)"); ?>:</label>
        <input type="text" name="endYear" id="endYear" size="4" value="<?php echo date('Y');?>" />
    </p>

	<p class='searchRow'>
        <label for="groupBy"><?php echo _("Group By"); ?>:</label>
        <select name="groupBy" id="groupBy">
            <option value=""><?php echo _("Resource"); ?></option>
            <option value="resourceType"><?php echo _("Resource Type"); ?></option>
            <option value="GS.shortName"><?php echo _("Subject"); ?></option>
            <option value="acquisitionType"><?php echo _("Acquisition Type"); ?></option>
            <option value="fundName"><?php echo _("Fund"); ?></option>
            <option value="libraryNumber"><?php echo _("Library Number"); ?></option>
            <option value="organizationName"><?php echo _("Organization"); ?></option>
        </select>
    </p>

    <p class='searchRow actions'>
        <input type="button" id="submitDashboardYearlyCosts" value="<?php echo _("Display"); ?>" />
        <input type="hidden" name="csv" value="1" />
        <input type="submit" id="getDashboardCSV" value="<?php echo _("Export"); ?>" />
        <input type="reset" value="<?php echo _("Reset"); ?>" />
    </p>
    </form>
</aside>
</main>

<link rel="stylesheet" type="text/css" href="../css/datatables.min.css"/>
<script type="text/javascript" src="js/dashboard.js"></script>
