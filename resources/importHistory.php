<?php

	include_once 'directory.php';

if (isset($_GET['detail'])) {

    $importHistoryID = $_GET['detail'];

	$pageTitle=_('Import detail');
	include 'templates/header.php';

    $import = new ImportHistory(new NamedArguments(array('primaryKey' => $importHistoryID)));
    $importedResources = json_decode($import->importedResources);
    ?>
    <h2><?php echo _('Import detail'); ?></h2>
	<br />
	<h3><?php echo _('Summary'); ?></h3>
    <!-- TODO: i18n placeholders -->
	<ul>
	<li><?php echo _('Import date: '); ?><?php echo $import->importDate; ?></li>
	<li><?php echo _('Filename: '); ?><a href="attachments/<?php echo $import->filename; ?>"><?php echo $import->filename; ?></a></li>
	<li><?php echo _('Number of imported resources: '); ?><?php echo $import->resourcesCount; ?></li>
	</ul>
	<br />
	<h3><?php echo _('Imported resources'); ?></h3>
    <table class="dataTable">
    <thead>
    <tr>
	<th><?php echo _('Title'); ?></th>
    <th><?php echo _('ISSN'); ?></th>
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
	<a href="importHistory.php"><?php echo _('Back to import history'); ?></a>
<?php


} else {

	$pageTitle=_('Import History');
	include 'templates/header.php';

    $imports = new ImportHistory();
    ?>
    <main id="main-content">
    <article>
    <h2><?php echo _('Import History'); ?></h2>
    <?php if (is_array($imports->allAsArray()) && count($imports->allAsArray()) > 0) { ?>
    <table class="dataTable">
    <thead>
    <tr>
    <th><?php echo _('Date'); ?></th>
    <th><?php echo _('Filename'); ?></th>
    <th><?php echo _('Resources count'); ?></th>
    <th><?php echo _('Details'); ?></th>
    </thead>
    </tr>
    <tbody>
    <?php
    foreach($imports->allAsArray() as $import) {
        print "<tr>";
        print "<td>" . $import['importDate'] . "</td>";
        print "<td><a href=\"attachments/" . $import['filename']  . "\">" . $import['filename'] . "</a></td>";
        print "<td>" . $import['resourcesCount'] . "</td>";
        print "<td><a href=\"importHistory.php?detail=" . $import['importHistoryID'] . "\">Details</a>";
        print "</tr>";
    }
    ?>
    </tbody>
    </table>
    <?php
    }
    else { 
        echo '<p>' . _('No imports found.') . '</p>';
    }
    ?>
	<p><a href="import.php"><?php echo _('Back to import'); ?></a></p>
    </article>
    </main>
<?php
}
include 'templates/footer.php';
?>
</body>
</html>