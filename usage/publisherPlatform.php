<?php

if (isset($_GET['publisherPlatformID'])){
	$publisherPlatformID = $_GET['publisherPlatformID'];
	$platformID = '';
}

if (isset($_GET['platformID'])){
	$platformID = $_GET['platformID'];
	$publisherPlatformID = '';
}



if (($publisherPlatformID == '') && ($platformID == '')){
	header( 'Location: publisherPlatformList.php?error=1' ) ;
}


$pageTitle = _('View or Edit Publisher / Platform');

include 'templates/header.php';


if ($publisherPlatformID) {
	$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $publisherPlatformID)));
	$pub = new Publisher(new NamedArguments(array('primaryKey' => $obj->publisherID)));
	$deleteParam = "publisherPlatformID=$publisherPlatformID";
	$deleteText = _('Delete Publisher');
	$displayName = $pub->name;
}else if ($platformID){
	$obj = new Platform(new NamedArguments(array('primaryKey' => $platformID)));
  $deleteParam = "platformID=$platformID";
  $deleteText = _('Delete Platform');
	$displayName = $obj->name;
}
?>


<table class="headerTable" style="background-image:url('images/header.gif');background-repeat:no-repeat;">
<tr><td>
	<table style='width:897px;'>
	<tr style='vertical-align:top'>
	<td><span class="headerText"><?php echo $displayName; ?></span><br /><br /></td>
	<td style='text-align:right;'>
    <a href="deletePublisherPlatformConfirmation.php?<?php echo $deleteParam; ?>" class="save-button" style="background-color: #7a0026;"><?php echo $deleteText; ?></a>
  </td>
	</tr>
	</table>


	<input type='hidden' name='platformID' id='platformID' value='<?php echo $platformID; ?>'>
	<input type='hidden' name='publisherPlatformID' id='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>


	<div style='width:900px;'>

		<div style='float:left; width:797px;vertical-align:top;margin:0; padding:0;'>
		<div style="width: 797px;" id ='div_imports' class="usage_tab_content">
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: initial;">
				<tr>
					<td class="sidemenu">
						<?php echo usage_sidemenu(watchString('imports')); ?>
					</td>
					<td class='mainContent'>
						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>
		</div>

		<div style="display:none;width: 797px;" id ='div_titles' class="usage_tab_content">
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: initial;">
				<tr>
					<td class="sidemenu">
						<?php echo usage_sidemenu(watchString('titles')); ?>
					</td>
					<td class='mainContent'>
						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div style="display:none;width: 797px;" id ='div_statistics' class="usage_tab_content">
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: initial;">
				<tr>
					<td class="sidemenu">
						<?php echo usage_sidemenu(watchString('statistics')); ?>
					</td>
					<td class='mainContent'>
						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div style="display:none;width: 797px;" id ='div_logins' class="usage_tab_content">
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: initial;">
				<tr>
					<td class="sidemenu">
						<?php echo usage_sidemenu(watchString('logins')); ?>
					</td>
					<td class='mainContent'>
						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>
		</div>
			<div style="display:none;width: 797px;" id ='div_sushi' class="usage_tab_content">

			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: initial;">
				<tr>
					<td class="sidemenu">
						<?php echo usage_sidemenu(watchString('sushi')); ?>
					</td>
					<td class='mainContent'>
						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	</div>
</td></tr>
</table>



<script type="text/javascript" src="js/publisherPlatform.js"></script>
    <script>
      $(document).ready(function() {
		<?php if ((isset($_GET['showTab'])) && ($_GET['showTab'] == 'sushi')){ ?>
        	$('a.showSushi').click();
        <?php }else{ ?>
        	$('a.showImports').click().css("font-weight", "bold");
        <?php } ?>
      });
    </script>

	<?php

include 'templates/footer.php';

?>
