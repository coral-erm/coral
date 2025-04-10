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
include_once 'directory.php';

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

<main id="main-content">
	<nav id="side" aria-label="<?php echo _("Publisher / Platform Tools"); ?>" class="sidemenu">
		<ul class="nav side">
			<?php echo usage_sidemenu($links, watchString($_GET['showTab'])); ?>
		</ul>
	</nav>
	<article>
		<div class="header flex">
			<h2><?php echo $displayName; ?></h2>
			<?php if ($platformID): ?>
				<span class="editElement">
					<button type="button" aria-label="<?php printf(_('Edit %s'), $displayName); ?>" onclick='myDialog("ajax_forms.php?action=getUpdatePlatformForm&platformID=<?php echo $platformID; ?>&height=530&width=518&modal=true",530,520)' class='thickbox link'>
						<i class="fa fa-pencil" aria-hidden="true"></i>
					</button>
				</span>
				<span class="deleteElement">
					<a class="destroy" aria-label="<?php echo $deleteText; ?>" href="deletePublisherPlatformConfirmation.php?<?php echo $deleteParam; ?>">
						<i class="fa fa-trash" aria-hidden="true"></i>
					</a>
				</span>
			<?php endif; ?>
		</div>
    
		<input type='hidden' name='platformID' id='platformID' value='<?php echo $platformID; ?>'>
		<input type='hidden' name='publisherPlatformID' id='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>

		<?php foreach ($links as $key => $value) { 
				if ($_GET['showTab'] == $key) { ?>
				<div id ='div_<?php echo $key ?>' class="usage_tab_content">
					<div class='mainContent'>
						<div class='div_mainContent'>
						</div>
					</div>
				</div>

		<?php } 
		} ?>
</article>
</main>

<?php
include 'templates/footer.php';
?>
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

</body>
</html>