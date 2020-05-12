<?php

include_once 'directory.php';
include "common.php";

$titleArray = array();
$year = $_GET['year'];
$layoutID = $_GET['layoutID'];
$publisherPlatformID = $_GET['publisherPlatformID'];
$platformID = $_GET['platformID'];
$archiveInd = $_GET['archiveInd'];
$download = $_GET['download'];

if ($archiveInd == '1') {
  $archive= ' ' . _('Archive');
}else{
  $archive='';
}

//determine config settings for outlier usage
$config = new Configuration();
$outlier = array();
$outlier[0]['color']='';

if ($config->settings->useOutliers == "Y"){
  $outliers = new Outlier();
  $outlierArray = array();

  foreach($outliers->allAsArray as $outlierArray) {
    $outlier[$outlierArray['outlierID']]['color'] = $outlierArray['color'];
  }
}

if (isset($_GET['publisherPlatformID']) && ($_GET['publisherPlatformID'] != '')){
  $publisherPlatformID = $_GET['publisherPlatformID'];
  $platformID = '';
  $obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));
}else{
  $platformID = $_GET['platformID'];
  $publisherPlatformID = '';
  $obj = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));
}

$display_name = $obj->reportDisplayName;

/*
 * Setup Layout
 */
$layoutID = $_GET['layoutID'];
$layout = new Layout(new NamedArguments(array('primaryKey' => $layoutID)));

//read layouts ini file to get the layouts to map to columns in the database
$layoutsArray = parse_ini_file("layouts.ini", true);
$layoutKey = $layoutsArray['ReportTypes'][$layout->layoutCode];
$reportTypeDisplay = $layout->name;
$resourceType = $layout->resourceType;
$layoutCode = $layout->layoutCode;
$layoutID = $layout->primaryKey;
$layoutColumns = $layoutsArray[$layoutKey]['columns'];
$columnsToCheck = $layoutsArray[$layoutKey]['columnToCheck'];

$pageTitle = $display_name . " " . $reportTypeDisplay . " " .$year;
if($download) {
  $excelfile = str_replace (' ','_',$pageTitle) . '.xls';
  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=" . $excelfile);
}

$itemReport = in_array($layoutCode, array('IR_R5','IR_A1_R5','IR_M1_R5'));
$monthlyStats = $obj->getMonthlyStatsByLayout($layoutID, $year);

function getTitleIdentifiers($titleID) {
  $lookupIdentifiers = new Title(new NamedArguments(array('primaryKey' => $titleID)));
  $titleIdentifiers = $lookupIdentifiers->getIdentifiers();
  $identifierArray = array();
  foreach($titleIdentifiers as $ident) {
    $identifierArray[strtolower($ident->identifierType)] = $ident->identifier;
  }
  return $identifierArray;
}

// cache for identifiers to reduce DB calls
$identifierCache = array();

$rows = array();
foreach($monthlyStats as $stat) {
  // the rowKey is a combination of all the different counter 'types', this allows us to present separate rows of the same
  // title, but with different access, section, and activity types
  $rowKey = $stat['sectionType'].$stat['accessMethod'].$stat['accessType'].$stat['yop'];
  // R4 JR reports are not separated by activity type
  if (in_array($layoutCode, array('JR1_R4', 'JR1a_R4'))) {
    // and we only want the ft_total
    if (in_array($stat['activityType'],array('ft_html','ft_pdf'))) {
      continue;
    }
  } else {
    $rowKey .= $stat['activityType'];
  }

  // setup Identifiers
  if (!array_key_exists($stat['titleID'], $identifierCache)) {
    $identifierCache[$stat['titleID']] = getTitleIdentifiers($stat['titleID']);
  }
  $stat = array_merge($stat, $identifierCache[$stat['titleID']]);

  // These reports require looking up a potential parent or component title
  if (in_array($layoutCode, array('IR_R5','IR_A1_R5'))) {

    foreach(array('parent','component') as $parent) {
      $parentID = $stat[$parent.'ID'];
      if(!empty($parentID)) {
        $parentObject = new Title(new NamedArguments(array('primaryKey' => $parentID)));
        $parentAttrArray = array(
          $parent.'Title' => $parentObject->title,
          $parent.'DataType' => $parentObject->resourceType
        );
        if (!array_key_exists($parentID, $identifierCache)) {
          $identifierCache[$parentID] = getTitleIdentifiers($parentID);
        }
        foreach($identifierCache[$parentID] as $key => $value) {
          $parentAttrArray[$parent.ucfirst($key)] = $value;
        }
        $stat = array_merge($stat, $parentAttrArray);
      }
    }
  }

  if(array_key_exists($stat['titleID'], $rows)) {
    if(array_key_exists($rowKey, $rows[$stat['titleID']])) {
      $rows[$stat['titleID']][$rowKey]['months'][$stat['month']] = $stat['usageCount'];
    } else {
      $rows[$stat['titleID']][$rowKey] = array(
        'titleInfo' => $stat,
        'months' => array(
          $stat['month'] => $stat['usageCount']
        )
      );
    }
  } else {
    $rows[$stat['titleID']] = array(
      $rowKey => array (
        'titleInfo' => $stat,
        'months' => array(
          $stat['month'] => $stat['usageCount']
        )
      )
    );
  }
}

// sorting
uasort($rows, function($a, $b) {
  $aCompare = reset($a)['titleInfo']['title'];
  $bCompare = reset($b)['titleInfo']['title'];
  if ($aCompare == $bCompare) {
    return 0;
  }
  return $aCompare > $bCompare ? 1 : -1;
});
?>

<?php if($download): ?>
<html>
<head>
</head>
<body>
<?php else: ?>
<?php include 'templates/header.php'; ?>
<style>
table {
  position: relative;
}
table.dataTable th {
  position: sticky;
  top: 0;
  background: rgba(200, 200, 200, 1) !important;
}
table.dataTable th:first-child {
  min-width: 300px;
}
</style>
<?php endif; ?>


<h2><?php echo $pageTitle;?></h2>
<?php if(empty($download)): ?>
  <a href="<?php echo $_SERVER['REQUEST_URI']. '&download=true'; ?>">Download</a>
<?php endif; ?>

<?php if($download): ?>
<table border='1'>
<?php else: ?>
<table class="dataTable fixed_headers">
<?php endif; ?>
  <tr>
    <?php foreach ($columnsToCheck as $column): ?>
      <th><?php echo _($column); ?></th>
    <?php endforeach; ?>
    <?php foreach(range(1,12) as $monthInt): ?>
      <th><?php echo numberToMonth($monthInt) . '-' . $year; ?></th>
    <?php endforeach; ?>
  </tr>
  <?php foreach($rows as $titleID => $subRow): ?>
    <?php foreach($subRow as $rowKey => $data): ?>
      <tr>
        <?php foreach($layoutColumns as $columnKey): ?>
          <?php
            if (in_array($layoutCode, array('JR1_R4', 'JR1a_R4')) && $columnKey == 'activityType') {
              continue;
            }
          ?>
          <?php if($columnKey == 'ytd'): ?>
            <?php
              $total = 0;
              foreach($data['months'] as $month => $count) {
                $total += $count;
              }
              echo '<td>'.$total.'</td>';
            ?>
          <?php else: ?>
            <td><?php echo $data['titleInfo'][$columnKey]; ?></td>
          <?php endif; ?>
        <?php endforeach; ?>
        <?php
          // JR reports need the ytdPDF and ytdHTML from yearlyUsageSummary
          if (in_array($layoutCode, array('JR1_R4', 'JR1a_R4'))) {
            $pdfHtmlCounts = new Title(new NamedArguments(array('primaryKey' => $titleID)));
            $ytdCounts = $pdfHtmlCounts->getYearlyStats(null, $year, $data['titleInfo']['publisherPlatformID'], null);
            $ytdCounts = $ytdCounts[0];
            echo '<td>'.$ytdCounts['ytdHTMLCount'].'</td>';
            echo '<td>'.$ytdCounts['ytdPDFCount'].'</td>';
          }
        ?>
        <?php foreach(range(1,12) as $monthInt): ?>
          <td><?php echo empty($data['months'][$monthInt]) ? '' : $data['months'][$monthInt] ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  <?php endforeach; ?>
</table>
<?php if($download): ?>
</body>
</html>
<?php else: ?>
  <?php include 'templates/footer.php'; ?>
<?php endif; ?>
