<?php
		$resourceID = $_GET['resourceID'];
		$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));


    try {
      $resource->removeResourceAndChildren();
      echo "<span class='success'>";
      echo _("Resource successfully deleted.");
      echo "</span>";
    } catch (Exception $e) {
      echo "<span class='error'>";
      echo $e->getMessage();
      echo "</span>";
    }

?>
