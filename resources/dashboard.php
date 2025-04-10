<?php
	include_once 'directory.php';
	$pageTitle=_('Dashboard');
    $dataTables = true;
    include 'templates/header.php';
    $dashboard = new Dashboard();
?>

<main id="dashboardPage">
    <article>
        <h2><?php echo _("Dashboard: Statistics");?></h2>
        <div id="dashboardTable"></div>

    </article>

    <aside id="side" class="block-form" role="search">

    <form action="dashboard_export.php" method="POST">
        <fieldset>
            <legend><?php echo _("Filter on resources:"); ?></legend>
            <p class='searchRow'>
                <label for="resourceTypeID"><b><?php echo _("Resource type"); ?>:</b></label><br />
                <?php $dashboard->getResourceTypesAsDropdown(); ?><br />
            </p>
            <p class='searchRow'>
                <label for="subjectID"><b><?php echo _("Subject"); ?>:</b></label><br />
                <?php $dashboard->getSubjectsAsDropdown(); ?><br />
            </p>
            <p class='searchRow'>
                <label for="acquisitionTypeID"><b><?php echo _("Acquisition type"); ?>:</b></label><br />
                <?php $dashboard->getAcquisitionTypesAsDropdown(); ?><br />
            </p>
        </fieldset>
        
        <fieldset>
        <legend><?php echo _("Filter on organizations:"); ?></legend>
            <p class="searchRow">
                <label for="organizationID"><b><?php echo _("Organization"); ?>:</b></label><br />
                <?php $dashboard->getOrganizationsAsDropdown(); ?><br />
            </p>
            
            <p class='searchRow'>
                <label for="roleID"><b><?php echo _("Role"); ?>:</b></label><br />
                <?php $dashboard->getOrganizationsRolesAsDropdown(); ?><br />
            </p>
        </fieldset>

        <fieldset>
            <legend><?php echo _("Filter on payments:"); ?></legend>
            <p class='searchRow'>
                <label for="orderTypeID"><b><?php echo _("Order Type"); ?>:</b></label><br />
                <?php $dashboard->getOrderTypesAsDropdown(); ?>
            </p>
            <p class='searchRow'>
                <label for="fundID"><b><?php echo _("Fund"); ?>:</b></label><br />
                <?php $dashboard->getFundsAsDropdown(); ?>
            </p>
            <p class='searchRow'>
                <label for="costDetailsID"><b><?php echo _("Cost Details"); ?>:</b></label><br />
                <?php $dashboard->getCostDetailsAsDropdown(); ?>
            </p>
        </fieldset>

        <p class='searchRow'>
        <label for="year"><b><?php echo _("Year"); ?>:</b></label><br />
        <input type="text" name="year" id="year" size="4" value="<?php echo date('Y');?>" /><br />
        </p>

        <p class='searchRow'>
        <label for="groupBy"><b><?php echo _("Group By"); ?>:</b></label><br />
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

        <p class='searchRow'>
            <input type="button" id="submitDashboard" value="<?php echo _("Display"); ?>" />
            <input type="hidden" name="csv" value="1" />
            <input type="submit" id="getDashboardCSV" value="<?php echo _("Export"); ?>" />
            <input type="reset" value="<?php echo _("Reset"); ?>" />
        </p>
    </form>
</aside>
</main>

<?php
include 'templates/footer.php';
?>
<script src="js/dashboard.js"></script>
</body>
</html>