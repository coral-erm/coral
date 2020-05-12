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
if($download) {
  $excelfile = str_replace (' ','_',$pageTitle) . '.xls';
  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=" . $excelfile);
}


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

?>

<?php if($download): ?>
<html>
<head>
</head>
<body>
<?php else: ?>
<?php include 'templates/header.php'; ?>
<?php endif; ?>


<h2><?php echo $pageTitle;?></h2>
<?php if(empty($download)): ?>
<a href="<?php echo $_SERVER['REQUEST_URI']. '&download=true'; ?>">Download</a>
<?php endif; ?>


<table border='1'>
<tr>
  <?php foreach ($columns as $column): ?>
    <th><?php echo _($column['display']); ?></th>
  <?php endforeach; ?>
  <?php if($resourceType == 'Item'): ?>
    <?php foreach ($parentTypes as $parentType): ?>
      <?php foreach ($parentColumns as $column): ?>
        <th><?php echo _($parentType . ' ' . $column['display']) ?></th>
      <?php endforeach; ?>
    <?php endforeach; ?>
  <?php endif; ?>
</tr>
<?php foreach($titleArray as $title): ?>
  <tr>
    <?php foreach ($columns as $column): ?>
      <td><?php echo $title[$column['key']]; ?></td>
    <?php endforeach; ?>
    <?php
      if($resourceType == 'Item') {
        foreach($parentTypes as $parentType) {
          $idKey = strtolower($parentType) . 'ID';
          if(!empty($title[$idKey])) {
            $parentTitle = new Title(new NamedArguments(array('primaryKey' => $title[$idKey])));
          } else {
            $parentTitle = new Title();
          }
          foreach ($parentColumns as $column) {
            echo "\n<td>" . $parentTitle->$column['key'] .'</td>';
          }
        }
      }
    ?>
  </tr>
<?php endforeach; ?>

<?php if($download): ?>
</table>
</body>
</html>
<?php else: ?>
  <?php include 'templates/footer.php'; ?>
<?php endif; ?>

