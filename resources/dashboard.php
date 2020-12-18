<?php
	include_once 'directory.php';
	$pageTitle=_('Dashboard');
    $customJSInclude .= '<script type="text/javascript" src="../js/plugins/datatables.min.js"></script>' . "\n";
    $customJSInclude .= '<script type="text/javascript" src="../js/plugins/datatables_defaults.js"></script>' . "\n";
	include 'templates/header.php';
    $dashboard = new Dashboard();

?>
<link rel="stylesheet" type="text/css" href="../css/datatables.min.css"/>
<script type="text/javascript" src="js/dashboard.js"></script>
<div id="dashboardPage"><h1><?php echo _("Dashboard: Statistics");?></h1>
<br />
<div style='text-align:left;'>
<table class="headerTable" style="background-image:url('images/header.gif');background-repeat:no-repeat;">
<tr style='vertical-align:top;'>
<td style="width:155px;padding-right:10px;">
<table class='noBorder' id='title-search'>
	<tr><td style='text-align:left;width:75px;vertical-align:top' align='left'>

	<table class='borderedFormTable' style="width:150px">

	<tr>
	<td class='searchRow'>
    <form action="dashboard_export.php" method="POST">
    <fieldset>
    <legend><?php echo _("Filter on resources:"); ?></legend>
    <label for="resourceTypeID"><b><?php echo _("Resource type"); ?>:</b></label><br />
    <?php $dashboard->getResourceTypesAsDropdown(); ?><br />
    </td>
	</tr>
	<tr>
	<td class='searchRow'>
    <label for="subjectID"><b><?php echo _("Subject"); ?>:</b></label><br />
    <?php $dashboard->getSubjectsAsDropdown(); ?><br />
    </td>
	</tr>
	<tr>
	<td class='searchRow'>
    <label for="acquisitionTypeID"><b><?php echo _("Acquisition type"); ?>:</b></label><br />
    <?php $dashboard->getAcquisitionTypesAsDropdown(); ?><br />
    </td>
	</tr>
    </fieldset>
    <tr>
    <td class="searchRow">
    <fieldset>
    <legend><?php echo _("Filter on organizations:"); ?></legend>
    <label for="organizationID"><b><?php echo _("Organization"); ?>:</b></label><br />
    <?php $dashboard->getOrganizationsAsDropdown(); ?><br />
    </td>
	</tr>
	<tr>
	<td class='searchRow'>
    <label for="roleID"><b><?php echo _("Role"); ?>:</b></label><br />
    <?php $dashboard->getOrganizationsRolesAsDropdown(); ?><br />
    </td>
	</tr>
    </fieldset>
	<tr>
	<td class='searchRow'>
    <fieldset>
    <legend><?php echo _("Filter on payments:"); ?></legend>
    <label for="orderTypeID"><b><?php echo _("Order Type"); ?>:</b></label><br />
    <?php $dashboard->getOrderTypesAsDropdown(); ?>
    </td>
	</tr>
    <tr>
	<td class='searchRow'>
    <label for="fundID"><b><?php echo _("Fund"); ?>:</b></label><br />
    <?php $dashboard->getFundsAsDropdown(); ?>
    </td>
    </tr>
	<tr>
	<td class='searchRow'>
    <label for="costDetailsID"><b><?php echo _("Cost Details"); ?>:</b></label><br />
    <?php $dashboard->getCostDetailsAsDropdown(); ?>
    </fieldset>
    </td>
	</tr>
	<tr>
	<td class='searchRow'>
    <label for="year"><b><?php echo _("Year"); ?>:</b></label><br /><input type="text" name="year" id="year" size="4" value="<?php echo date('Y');?>" /><br />
    </td>
	</tr>
	<tr>
	<td class='searchRow'>
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
    </td>
	</tr>
	<tr>
	<td class='searchRow'>
    <input type="button" id="submitDashboard" value="<?php echo _("Display"); ?>" />
    <input type="hidden" name="csv" value="1" />
    <input type="submit" id="getDashboardCSV" value="<?php echo _("Export"); ?>" />
    <input type="reset" value="<?php echo _("Reset"); ?>" />
    </form>
    </td></tr>
    </table>
    </div>
 </td>
<td style="vertical-align:top">
<div id="dashboardTable" />
</td></tr>
</table>
</div>

