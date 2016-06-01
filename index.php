<?php
//determine CORAL main path so we can check each module below to know which to display
$pagePath = $_SERVER["DOCUMENT_ROOT"];

$currentFile = $_SERVER["SCRIPT_NAME"];
$parts = Explode('/', $currentFile);
for($i=0; $i<count($parts) - 1; $i++){
	$pagePath .= $parts[$i] . '/';
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CORAL - Centralized Online Resources Acquisitions and Licensing</title>
<link rel="stylesheet" href="indexstyle.css" type="text/css" media="screen" />
<link rel="SHORTCUT ICON" href="images/favicon.ico" />
<script type="text/javascript" src="js/plugins/jquery.js"></script>
<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
<h1 style="display:none;">Coral - Centralized Online Resources Acquisitions and Licensing</h1>
<center>
	<table class="background">
	<tr>
	<td>
		<center>
		<table style='margin-top:123px;width:560px;height:420px;'>
		<tr valign='top' style='height:356px;'>
			<td>
				<center>
				<div style='margin-top:12px;'>
				<table class='logos'>

				<tr>
				<?php if (file_exists($pagePath . "resources/index.php")) {?>
				<td style='width:52px;'><img src='images/butterflyfishlogo.gif' hover="images/butterflyfishlogo_over.gif" id="logo_butterflyfish" alt="" /></td><td style='width:194px;'><a href='resources/'><img src='images/resources.gif' alt="Resources module" hover="images/resources_over.gif" class="rollover" id="butterflyfish" /></a></td>
				<?php } else { ?>
				<td style='width:52px;'><img src='images/butterflyfishlogo.gif' alt="" /></td><td style='width:194px;'><img src='images/resources_off.gif' alt="Resources module" id="butterflyfish" /></td>
				<?php } ?>
				</tr>

				<tr>
				<?php if (file_exists($pagePath . "licensing/index.php")) {?>
				<td style='width:52px;'><img src='images/angelfishlogo.gif' hover="images/angelfishlogo_over.gif" id="logo_angelfish" alt="" /></td><td style='width:194px;'><a href='licensing/'><img src='images/licensing.gif' alt="Licensing module" hover="images/licensing_over.gif" class="rollover" id="angelfish" /></a></td>
				<?php } else { ?>
				<td style='width:52px;'><img src='images/angelfishlogo.gif' alt="" /></td><td style='width:194px;'><img src='images/licensing_off.gif' alt="Licensing Module" id="angelfish" /></td>
				<?php } ?>
				</tr>

				<tr>
				<?php if (file_exists($pagePath . "organizations/index.php")) {?>
				<td style='width:52px;'><img src='images/turtlelogo.gif' hover="images/turtlelogo_over.gif" id="logo_turtle" alt="" /></td><td style='width:194px;'><a href='organizations/'><img src='images/organizations.gif' alt="Organizations module" hover="images/organizations_over.gif" class="rollover" id="turtle" /></a></td>
				<?php } else { ?>
				<td style='width:52px;'><img src='images/turtlelogo.gif' alt="" /></td><td style='width:194px;'><img src='images/organizations_off.gif' alt="Organizations module" id="angelfish" /></td>
				<?php } ?>
				</tr>

				<tr>
				<?php if (file_exists($pagePath . "usage/index.php")) {?>
				<td style='width:52px;'><img src='images/seahorselogo.gif' hover="images/seahorselogo_over.gif" id="logo_seahorse" alt="" /></td><td style='width:194px;'><a href='usage/'><img src='images/usagestatistics.gif' alt="Usage Statistics module" hover="images/usagestatistics_over.gif" class="rollover" id="seahorse" /></a></td>
				<?php } else { ?>
				<td style='width:52px;'><img src='images/seahorselogo.gif' alt="" /></td><td style='width:194px;'><img src='images/usagestatistics_off.gif' alt="Usage Statistics module" id="angelfish" /></td>
				<?php } ?>
				</tr>
				</table>

				</div>
				</center>
			</td>
		</tr>
		</table>
		</center>
	</td>
	</tr>
	</table>

</center>
</body>
</html>
