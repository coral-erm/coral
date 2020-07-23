<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
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

$itemReport = in_array($layoutCode, array('IR_R5','IR_A1_R5','IR_M1_R5'));

//$monthlyStats = $obj->getMonthlyStatsByLayout($layoutID, $year);

function getTitleIdentifiers($titleID) {
  global $platformID;
  global $publisherPlatformID;
  $lookupIdentifiers = new Title(new NamedArguments(array('primaryKey' => $titleID)));
  $titleIdentifiers = $lookupIdentifiers->getIdentifiers();

  // First, group the identifiers so we can extract the correct association
  $groupedIdentifiers = array();
  foreach($titleIdentifiers as $ident) {
    $type = strtolower($ident->identifierType);
    $key = $type == 'proprietary identifier' ? 'pi' : $type;
    if (!isset($groupedIdentifiers[$key])) {
      $groupedIdentifiers[$key] = array();
    }
    $groupedIdentifiers[$key][] = array(
      'identifier' => $ident->identifier,
      'publisherPlatformID' => $ident->publisherPlatformID,
      'platformID' => $ident->platformID
    );
  }

  $identifierArray = array();
  foreach ($groupedIdentifiers as $key => $values) {
    if (count($values) == 1) {
      // if the number of identifiers for this type is 1, use that
      $identifierArray[$key] = $values[0]['identifier'];
    } else {
      // else need to find the one corresponding to the publisherPlatform
      if (!empty($publisherPlatformID)) {
        foreach ($values as $v) {
          if ($v['publisherPlatformID'] == $publisherPlatformID) {
            $value = $v['identifier'];
          }
        }
      } elseif (!empty($platformID)) {
        foreach ($values as $v) {
          if ($v['platformID'] == $platformID) {
            $value = $v['identifier'];
          }
        }
      } else {
        $value = $values[0]['identifier'];
      }
      $identifierArray[$key] = $value;
    }
  }
  return $identifierArray;
}

// cache for identifiers to reduce DB calls
$identifierCache = array();


function compileStats($monthlyStats) {
  global $identifierCache;
  global $layoutCode;
  global $layoutColumns;
  global $year;
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

  foreach($rows as $titleID => $subRow) {
    foreach($subRow as $rowKey => $data) {
      $report[$titleID.$rowKey] = array();
      foreach($layoutColumns as $columnKey) {
        if (in_array($layoutCode, array('JR1_R4', 'JR1a_R4')) && $columnKey == 'activityType') {
          continue;
        }
        if ($columnKey == 'dataType') {
          $report[$titleID.$rowKey][] = $data['titleInfo']['resourceType'];
        }
        if($columnKey == 'ytd') {
          $total = 0;
          foreach($data['months'] as $month => $count) {
            $total += $count;
          }
          $report[$titleID.$rowKey][] = $total;
        } else {
          $columnKey = $columnKey == 'publisherID' ? 'counterPublisherID' : $columnKey;
          $report[$titleID.$rowKey][] = $data['titleInfo'][$columnKey];
        }
      }
      // JR reports need the ytdPDF and ytdHTML from yearlyUsageSummary
      if (in_array($layoutCode, array('JR1_R4', 'JR1a_R4'))) {
        $pdfHtmlCounts = new Title(new NamedArguments(array('primaryKey' => $titleID)));
        $ytdCounts = $pdfHtmlCounts->getYearlyStats(null, $year, $data['titleInfo']['publisherPlatformID'], null);
        $ytdCounts = $ytdCounts[0];
        $report[$titleID.$rowKey][] = $ytdCounts['ytdHTMLCount'];
        $report[$titleID.$rowKey][] = $ytdCounts['ytdPDFCount'];
      }
      foreach(range(1,12) as $monthInt) {
        $report[$titleID.$rowKey][] = empty($data['months'][$monthInt]) ? '' : $data['months'][$monthInt];
      }
    }
  }
  return $report;
}


$headers = array();

foreach ($columnsToCheck as $column) {
  $headers[] = _($column);
}
foreach(range(1,12) as $monthInt) {
  $headers[] = numberToMonth($monthInt) . '-' . $year;
}

if ($download) {
  if ($download === 'csv') {
    // CSV for download
    $filename = str_replace(' ', '_', $pageTitle) . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=$filename");
    $output = fopen('php://output', 'w');
    fputs($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, $headers);
  }
  if ($download === 'tsv') {
    $filename = str_replace (' ','_',$pageTitle) . '.tsv';
    header('Content-type: text/tab-separated-values; charset=utf-8');
    header("Content-Disposition: attachment;filename=$filename");
    echo chr(255) . chr(254);
    echo mb_convert_encoding(implode("\t", $headers), 'UTF-16LE', 'UTF-8');
    $tsv_data .= "\n";
  }
} else  {
  include 'templates/header.php';
  echo "<style>";
  echo "table { position: relative; }";
  echo "table.dataTable th { position: sticky; top: 0; background: rgba(200, 200, 200, 1) !important;}";
  echo "table.dataTable th:first-child { min-width: 300px; }";
  echo "</style>";
  echo "<h2>$pageTitle</h2>";
  echo '<a href="' . $_SERVER['REQUEST_URI'] . '&download=tsv">Download TSV</a>';
  echo '<a href="' . $_SERVER['REQUEST_URI'] . '&download=csv" style="margin-left: 10px;">Download CSV</a>';
  echo '<table class="dataTable fixed_headers"><tr><th>';
  echo implode("</th><th>", $headers);
  echo '</th></tr>';
}

$offset = 0;
$limit = 5000;
$monthlyCount = 1;
$monthlyStats = array('test');
do {
  $monthlyStats = $obj->getMonthlyStatsByLayout($layoutID, $year, $limit, $offset);
  if (count($monthlyStats) > 0) {
    $report = compileStats($monthlyStats);
    foreach ($report as $row) {
      if ($download && $download == 'csv') {
        fputcsv($output, $row);
      } elseif ($download && $download == 'tsv') {
        echo mb_convert_encoding(implode("\t", $row) . "\n", 'UTF-16LE', 'UTF-8');
      } else {
        echo '<tr><td>' . implode('</td><td>', $row) . "</td></tr>";
      }
    }
  }
  $offset += $limit;
} while (count($monthlyStats) > 0);

if (empty($download)) {
  echo '</table>';
  include 'templates/footer.php';
}


