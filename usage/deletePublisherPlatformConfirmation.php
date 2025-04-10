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



<main id="main-content">
  <article>
    <h2><?php echo $pageTitle; ?></h2>
    <h3><?php echo _('Confirm the following deletions:'); ?></h3>

        <?php if(!$statsOnly): ?>

        <!-- Publisher or Publisher Platform -->
        <?php
        if ($type == 'platform') {
          echo '<h4>'. _('Platform') . '</h4>';
        } else {
          echo '<h4>'. _('Publisher') . '</h4>';
        }
          echo "<ul><li>$displayName</li></ul>";
        ?>

        <!-- Associated Platform Publishers -->
        <?php
        if ($type == 'platform') {
          $publisherPlatformArray = $obj->getPublisherPlatforms();
          if (count($publisherPlatformArray) > 0 ) {
            echo '<h4>'. _('Publishers associated with this Platform') . '</h4>';
            echo '<p><small><em>' . _('If the publisher is associated with another platform, only the statistics gathered from this platform will be deleted') . '</em></small></p>';

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
            echo '<h4>'. _('Import Logs') . '</h4>';
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
          echo '<h4>'. _('SUSHI Service') . '</h4>';
          echo "<ul><li>R$sushiService->releaseNumber ($sushiService->reportLayouts)</li></ul>";
        }

        $globname = implode('_', explode(' ', $displayName));
        $files = array();
        foreach (glob("counterstore/*$globname*.xml") as $filename) {
          $files[] = '<li>' . str_replace('counterstore/', '', $filename) . '</li>';
        }
        if (is_array($files) && count($files) > 0) {
          echo '<h4>'. _('SUSHI XML Files') . '</h4>';
          echo '<ul>' . implode($files) . '</ul>';
        }
        ?>

        <?php endif; ?>

        <!-- Stats -->
        <?php
        $statsArray = $obj->getFullStatsDetails();
        if (is_array($statsArray) && count($statsArray) > 0) {
          // TODO: i18n placeholders
          echo '<h4>' . _('Statistics') . ' <small><em>* - ' . _('has outliers') . '</em></small></h4>';
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

      <p class="center">
        <a href="deletePublisherPlatform.php?<?php echo $deleteParam; ?>"><?php echo _('Confirm') ?></a>
      </p>
  </article>
</main>
<?php

include 'templates/footer.php';

?>
</body>
</html>