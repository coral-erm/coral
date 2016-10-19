<?php

	include_once 'directory.php';

if ($_GET['detail']) {

    $importHistoryID = $_GET['detail'];

	$pageTitle=_('Import detail');
	include 'templates/header.php';

    $import = new ImportHistory(new NamedArguments(array('primaryKey' => $importHistoryID)));
    $importedResources = json_decode($import->importedResources);
    ?>
    <h2>Import detail</h2>
	<br />
	<h3>Summary</h3>
	<ul>
	<li>Import date: <?php echo $import->importDate; ?></li>
	<li>Filename: <?php echo $import->filename; ?></li>
	<li>Number of imported resources: <?php echo $import->resourcesCount; ?></li>
	</ul>
	<br />
	<h3>Imported resources</h3>
    <table class="linedDataTable">
    <thead>
    <tr>
	<th>Title</th>
    <th>ISSN</th>
    </thead>
    </tr>
    <tbody>
    <?php
    foreach ($importedResources as $importedResource) {
        $resource = new Resource(new NamedArguments(array('primaryKey' => $importedResource)));
		$isbnOrIssn = $resource->getIsbnOrIssn();
        print "<tr>";
        print "<td><a href=\"" . $util->getResourceRecordURL() . $resource->resourceID . "\">$resource->titleText</a></td>";
		print "<td>"  . join(' ', 
							array_map(
									function($object) { return $object->isbnOrIssn; }, 
									$isbnOrIssn
									 )
							) . 
			  "</td>";
        print "</tr>";
    }
	?>
    </tbody>
    </table>
	<a href="importHistory.php">Back to import history</a>
<?php


} else {

	$pageTitle=_('Import history');
	include 'templates/header.php';

    $imports = new ImportHistory();
    ?>
    <h2>Import history</h2>
    <table class="linedDataTable">
    <thead>
    <tr>
    <th>Date</th>
    <th>Filename</th>
    <th>Resources count</th>
    <th>Details</th>
    </thead>
    </tr>
    <tbody>
    <?php
    foreach($imports->allAsArray() as $import) {
        print "<tr>";
        print "<td>" . $import['importDate'] . "</td>";
        print "<td>" . $import['filename'] . "</td>";
        print "<td>" . $import['resourcesCount'] . "</td>";
        print "<td><a href=\"importHistory.php?detail=" . $import['importHistoryID'] . "\">Details</a>";
        print "</tr>";
    }
    ?>
    </tbody>
    </table>
	<a href="import.php">Back to import</a>

<?php
}
?>
