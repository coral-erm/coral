<?php

include_once 'directory.php';

$titleArray = array();
$resourceType = $_GET['resourceType'];
$download = $_GET['download'];

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

$pageTitle = $display_name . " " . $resourceType . " " ._("Titles");

$titleArray = $obj->getTitles($resourceType);

switch ($resourceType) {
  case 'Journal':
    $columns = array(
      array('key' => 'title', 'display' => 'Title'),
      array('key' => 'doi', 'display' => 'DOI'),
      array('key' => 'issn', 'display' => 'Print ISSN'),
      array('key' => 'eissn', 'display' => 'Online ISSN'),
      array('key' => 'uri', 'display' => 'URI')
    );
    break;
  case 'Book':
    $columns = array(
      array('key' => 'title', 'display' => 'Title'),
      array('key' => 'doi', 'display' => 'DOI'),
      array('key' => 'isbn', 'display' => 'ISBN'),
      array('key' => 'issn', 'display' => 'Print ISSN'),
      array('key' => 'eissn', 'display' => 'Online ISSN'),
      array('key' => 'uri', 'display' => 'URI')
    );
    break;
  case 'Item':
    $columns = array(
      array('key' => 'title', 'display' => 'Title'),
      array('key' => 'authors', 'display' => 'Authors'),
      array('key' => 'publicationDate', 'display' => 'Publication Date'),
      array('key' => 'articleVersion', 'display' => 'Article Version'),
      array('key' => 'doi', 'display' => 'DOI'),
      array('key' => 'isbn', 'display' => 'ISBN'),
      array('key' => 'issn', 'display' => 'Print ISSN'),
      array('key' => 'eissn', 'display' => 'Online ISSN'),
      array('key' => 'uri', 'display' => 'URI')
    );
    $parentTypes = array('Parent', 'Component');
    $parentColumns = array(
      array('key' => 'title', 'display' => 'Title'),
      array('key' => 'doi', 'display' => 'DOI'),
      array('key' => 'isbn', 'display' => 'ISBN'),
      array('key' => 'issn', 'display' => 'Print ISSN'),
      array('key' => 'eissn', 'display' => 'Online ISSN'),
      array('key' => 'uri', 'display' => 'URI')
    );
    break;
  default:
    $columns = array(
      array('key' => 'title', 'display' => 'Title'),
    );
    break;
}

$report = array (
  'headers' => array(),
  'data' => array()
);

foreach ($columns as $column) {
  $report['headers'][] = _($column['display']);
}
if($resourceType == 'Item'){
  foreach ($parentTypes as $parentType) {
    foreach ($parentColumns as $column) {
      $report['headers'][] = _($parentType . ' ' . $column['display']);
    }
  }
}
foreach($titleArray as $index => $title) {
  $report['data'][$index] = array();
  foreach ($columns as $column) {
    $report['data'][$index][] = $title[$column['key']];
  }
  if($resourceType == 'Item') {
    foreach($parentTypes as $parentType) {
      $idKey = strtolower($parentType) . 'ID';
      if(!empty($title[$idKey])) {
        $parentTitle = new Title(new NamedArguments(array('primaryKey' => $title[$idKey])));
      } else {
        $parentTitle = new Title();
      }
      foreach ($parentColumns as $column) {
        $report['data'][$index][] = $parentTitle->$column['key'];
      }
    }
  }
}



if ($download) {
  if ($download === 'csv') {
    // CSV for download
    $filename = str_replace(' ', '_', $pageTitle) . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=$filename");
    $output = fopen('php://output', 'w');
    fputs($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, $report['headers']);
    foreach ($report['data'] as $row) {
      fputcsv($output, $row);
    }
  }
  if ($download === 'tsv') {
    $filename = str_replace (' ','_',$pageTitle) . '.tsv';
    header('Content-type: text/tab-separated-values; charset=utf-8');
    header("Content-Disposition: attachment;filename=$filename");
    $tsv_data = implode("\t", $report['headers']);
    $tsv_data .= "\n";
    foreach($report['data'] as $row) {
      $tsv_data .= implode("\t", $row) . "\n";
    }
    echo chr(255) . chr(254) . mb_convert_encoding($tsv_data, 'UTF-16LE', 'UTF-8');
  }
} else  {
  include 'templates/header.php';
  echo "<h2>$pageTitle</h2>";
  echo '<a href="' . $_SERVER['REQUEST_URI'] . '&download=tsv">Download TSV</a>';
  echo '<a href="' . $_SERVER['REQUEST_URI'] . '&download=csv" style="margin-left: 10px;">Download CSV</a>';
  echo '<table border="1"><tr><th>';
  echo implode("</th><th>", $report['headers']);
  echo '</th></tr>';
  foreach($report['data'] as $row) {
    echo '<tr><td>' . implode('</td><td>', $row) . "</td></tr>";
  }
  echo '</table>';
  include 'templates/footer.php';
}
