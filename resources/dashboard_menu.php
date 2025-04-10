<?php
	include_once 'directory.php';
	$pageTitle=_('Dashboards');
	include 'templates/header.php';

?>
<h1><?php echo _("Dashboards"); ?></h1>
<ul class="nav">
	<li><a href="dashboard.php"><img src="../images/icon-usage.png" /><?php echo _("Statistics"); ?></a></li>
	<li><a href="dashboard_yearly_costs.php"><img src="../images/icon-usage.png" /><?php echo _("Yearly costs"); ?></a></li>
</ul>
