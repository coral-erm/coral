<?php

include_once 'directory.php';
include "common.php";

if (isset($_GET['publisherPlatformID'])){
  $publisherPlatformID = $_GET['publisherPlatformID'];
  $platformID = '';
}

if (isset($_GET['platformID'])){
  $platformID = $_GET['platformID'];
  $publisherPlatformID = '';
}


if (empty($platformID) && empty($publisherPlatformID)){
  header( 'Location: publisherPlatformList.php?error=1' );
  exit;
}

$config = new Configuration();

if ($publisherPlatformID) {
  $obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $publisherPlatformID)));
  $pub = new Publisher(new NamedArguments(array('primaryKey' => $obj->publisherID)));
  $deleteParam = "publisherPlatformID=$publisherPlatformID";
  $type = 'publisherPlatform';
  $displayName = $pub->name;
}else if ($platformID){
  $obj = new Platform(new NamedArguments(array('primaryKey' => $platformID)));
  $deleteParam = "platformID=$platformID";
  $type = 'platform';
  $displayName = $obj->name;
}

if (isset($_GET['statsOnly'])) {
  $statsOnly = $_GET['statsOnly'];
} else {
  $statsOnly = false;
}

if($statsOnly) {
  $actionName = _('Delete Stats Confirmation');
  $deleteParam .= '&statsOnly=true';
} else {
  $actionName = _('Delete Confirmation');
}

$pageTitle = $displayName . ': ' . $actionName;


include 'templates/header.php';

?>


<table class="headerTable" style="background-image:url('images/header.gif');background-repeat:no-repeat;">
  <tr><td>
      <table style='width:897px;'>
        <tr style='vertical-align:top'>
          <td><h1><?php echo $pageTitle; ?></h1></td>
        </tr>
        <tr>
          <td></td>
        </tr>
      </table>
      <div style='width:900px;'>
        <h2 style="margin: 20px 0;"><?php echo _('Confirm the following deletions:'); ?></h2>

        <?php if(!$statsOnly): ?>

        <!-- Publisher or Publisher Platform -->
        <?php
        if ($type == 'platform') {
          echo '<h3>'. _('Platform') . '</h3>';
        } else {
          echo '<h3>'. _('Publisher') . '</h3>';
        }
          echo "<ul><li>$displayName</li></ul>";
        ?>

        <!-- Associated Platform Publishers -->
        <?php
        if ($type == 'platform') {
          $publisherPlatformArray = $obj->getPublisherPlatforms();
          if (count($publisherPlatformArray) > 0 ) {
            echo '<h3>'. _('Publishers associated with this Platform') . '</h3>';
            echo '<p style="margin-bottom: 10px;"><small><em>' . _('If the publisher is associated with another platform, only the statistics gathered from this platform will be deleted') . '</em></small></p>';

            echo '<ul>';
            foreach($publisherPlatformArray as $publisherPlatform) {
              $publisher = new Publisher(new NamedArguments(array('primaryKey' => $publisherPlatform->publisherID)));
              echo "<li>$publisher->name</li>";
            }
            echo '</ul>';
          }
        }
        ?>

        <!-- Imports -->
        <?php
        if ($type == 'platform') {
          $importLogArray = $obj->getImportLogs();
          $displayImportLogItems = array();
          if (count($importLogArray) > 0 ) {
            foreach($importLogArray as $importLog) {
              $importLogPlatforms = $importLog->getPlatforms();
              if (count($importLogPlatforms) == 1 ) {
                $displayImportLogItems[] = '<li>' . format_date($importLog->importDateTime) . ' -- Files: ' . $importLog->logFileURL . ', ' . $importLog->archiveFileURL . '</li>';
              }
            }
          }
          if(count($displayImportLogItems) > 0) {
            echo '<h3>'. _('Import Logs') . '</h3>';
            echo '<ul>' . implode('',$displayImportLogItems) . '</ul>';
          }
        }
        ?>

        <!-- SUSHI Configs -->
        <?php
        $sushiService = new SushiService();
        if ($type == 'platform'){
          $sushiService->getByPlatformID($obj->platformID);
        } else {
          $sushiService->getByPublisherPlatformID($obj->publisherPlatformID);
        }

        if (($sushiService->platformID != '') || ($sushiService->publisherPlatformID != '')){
          echo '<h3>'. _('SUSHI Service') . '</h3>';
          echo "<ul><li>R$sushiService->releaseNumber ($sushiService->reportLayouts)</li></ul>";
        }

        $globname = implode('_', explode(' ', $displayName));
        $files = array();
        foreach (glob("counterstore/*$globname*.xml") as $filename) {
          $files[] = '<li>' . str_replace('counterstore/', '', $filename) . '</li>';
        }
        if (count($files) > 0) {
          echo '<h3>'. _('SUSHI XML Files') . '</h3>';
          echo '<ul>' . implode($files) . '</ul>';
        }
        ?>

        <?php endif; ?>

        <!-- Stats -->
        <?php
        $statsArray = $obj->getFullStatsDetails();
        if (count($statsArray) > 0){
          echo '<h3>' . _('Statistics') . ' <small style="font-size: .8rem"><em>* - ' . _('has outliers') . '</em></small></h3>';
          $holdYear = "";
          foreach($statsArray as $statArray){
            $year = $statArray['year'];
            if ($year != $holdYear){
              $endPreviousUl = $holdYear == '' ? '' : '</ul>';
              echo "$endPreviousUl<ul><li>$year<ul>";
              $holdYear = $year;
            }

            $archive = $statArray['archiveInd'] == '1' ? "&nbsp;" . _('(archive)') : '';
            $outlierText = $statArray['outlierID'] > 0 ? '*' : '';
            echo '<li>'.$statArray['resourceType'] . 's' . $outlierText . $archive . ': ';

            //loop through each month
            $monthArray = array();
            $queryMonthArray = array();
            $queryMonthArray = explode(",",$statArray['months']);

            //we need to eliminate duplicates - mysql doesnt allow group inside group_concats
            foreach ($queryMonthArray as $resultMonth){
              $infoArray=array();
              $infoArray=explode("|",$resultMonth);
              $outlier = $infoArray[1] > 0 ? '*' : '';
              $monthArray[] = numberToMonth($infoArray[0]).$outlier;
            }

            echo implode(', ', $monthArray);
            echo '</li>';
          }
        }
        ?>
      </div>

      <div style="text-align: center">
        <a href="deletePublisherPlatform.php?<?php echo $deleteParam; ?>" class="save-button">Confirm</a>
      </div>
    </td></tr>
</table>


<?php

include 'templates/footer.php';

?>
