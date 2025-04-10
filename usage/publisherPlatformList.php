<?php


$pageTitle = _("Edit Publishers / Platforms");


include 'templates/header.php';


?>

<script type="text/javascript" src="js/publisherPlatformList.js"></script>

<h2 class="headerText"><?php echo _("Publisher / Platform Update");?></h2>

  <?php

	$platforms = new Platform();
	$platform = array();
	$platformArray = $platforms->getPlatformArray();

	if (is_array($platformArray) && count($platformArray) > 0) {
		foreach($platformArray as $platform) {
			echo "<div class='header'><h3 class='PlatformText'>" . $platform['name'] . "</h3> <a href='publisherPlatform.php?platformID=" . $platform['platformID'] . "' class='addElement'>" . _("view / edit") . "</a></div>";
			echo "<details>";
			echo "<summary>" . _("show publisher list") . "</summary>";

			echo "<ul id='div_" . $platform['platformID'] . "'>";

			$platformObj = new Platform(new NamedArguments(array('primaryKey' => $platform['platformID'])));

			//loop through each publisher under this platform
			$publisherPlatform = new PublisherPlatform();
			foreach($platformObj->getPublisherPlatforms() as $publisherPlatform) {
				$publisher = new Publisher(new NamedArguments(array('primaryKey' => $publisherPlatform->publisherID)));
				echo "<li>" . $publisher->name . " <a href='publisherPlatform.php?publisherPlatformID=" . $publisherPlatform->publisherPlatformID . "' class='addElement'>" . _("view / edit") . "</a></li>";
			}

			echo "</ul>";
			echo "</details>";
		}
	}else{
		echo "<p><i>" . _("No publishers / platforms found.") . "</i></p>";
	}


  ?>


<?php include 'templates/footer.php'; ?>
