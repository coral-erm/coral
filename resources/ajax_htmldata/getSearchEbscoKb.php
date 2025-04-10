<?php

EbscoKbService::setSearch($_POST['search']);
$params = EbscoKbService::getSearch();

// Don't run a empty title query if no package limit is set
if(empty($params['search']) && $params['type'] == 'titles' && empty($params['packageId'])){
    echo '<p><i>' . _('Please enter a search term.') . '</i></p>';
    exit;
} else {
    $ebscoKb = EbscoKbService::getInstance();
    $ebscoKb->createQuery($params);
    $ebscoKb->execute();
    if(!empty($ebscoKb->error)){
        echo '<p><i>'.$ebscoKb->error.'</i></p>';
        exit;
    }
}

// check for results
$totalRecords = $ebscoKb->numResults();
$items = $ebscoKb->results();
if(empty($totalRecords) || empty($items)){
    echo '<p><i>' . _('No results found.') . '</i></p>';
    exit;
}

// Pagination vars
$page = $ebscoKb->queryParams['offset'];
$recordsPerPage = $ebscoKb->queryParams['count'];
$numPages = ceil($totalRecords / $recordsPerPage);
$maxDisplay = 25;
$pagination = [];
$halfMax = floor($maxDisplay/2);
$i = $page + $halfMax > $numPages ? $page - ($maxDisplay - ($numPages - $page + 1)) : $page - floor($maxDisplay/2);
while(count($pagination) <= $maxDisplay){
    if ($i > $numPages){
        break;
    }
    if($i > 0){
        $pagination[] = $i;
    }
    $i++;
}
$fromCalc = $recordsPerPage * ($page - 1) + 1;
$toCalc = ($fromCalc - 1) + $recordsPerPage;
$toCalc = $toCalc > $totalRecords ? $totalRecords : $toCalc;

// Limited by vendor?
if(!empty($params['vendorId'])){
    $ebscoKb = new EbscoKbService();
    $vendor = $ebscoKb->getVendor($params['vendorId']);

    if(!empty($params['packageId'])){
        $package = $ebscoKb->getPackage($params['vendorId'], $params['packageId']);
    }
}



?>

<?php if(!empty($vendor) && empty($package)): ?>
    <div>
        <!-- TODO: i18n placeholders -->
        <h2>
            <?php echo _('Packages from'); ?> <?php echo $vendor->vendorName; ?>
            <small style="padding-left: 1px">(<?php echo $vendor->packagesSelected . ' ' .  _('of') . ' ' . $vendor->packagesTotal . ' ' . _('selected)'); ?></small>
        </h2>
    </div>
<?php endif; ?>

<?php if(!empty($vendor) && !empty($package)): ?>
    <div>
        <h2>
            <?php printf(_('Title list from %s'), $package->packageName) ?><br />
            <small style="padding-left: 5px;"><?php echo _('Vendor:'); ?> <?php echo $vendor->vendorName; ?></small>
        </h2>
    </div>
<?php endif; ?>

<h2>
    <?php echo sprintf(_("Displaying %1\$d to %2\$d of %3\$d results"), $fromCalc, $toCalc, $totalRecords); ?>	
</h2>

<?php if ($totalRecords > $recordsPerPage): ?>
    <nav class="pagination">
        <ul>
        <?php if($page == 1): ?>
            <li class='first' aria-hidden='true'><span class="smallerText"><i class="fa fa-backward"></i></span></li>
        <?php else: ?>
            <li class='first'><a href="javascript:void(0);" data-page="<?php echo $page - 1; ?>" class="setPage smallLink" aria-label="<?php echo _('Previous page'); ?>"></li>
                <i class='fa fa-backward'></i>
            </a></li>
        <?php endif; ?>


        <?php foreach($pagination as $p): ?>
            <?php if ($p == $page): ?>
                <li aria-current="page"><span class="smallerText"><?php echo $p; ?></span></li>
            <?php else: ?>
                <li><a href='javascript:void(0);' data-page="<?php echo $p; ?>" class="setPage smallLink" aria-label="<?php echo sprintf(_('Page %d'), $p); ?>"><?php echo $p; ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($page + 1 > $numPages): ?>
            <li class="last" aria-hidden='true'><span class="smallerText"><i class="fa fa-forward"></i></span></li>
        <?php else: ?>
            <li class="last"><a href="javascript:void(0);" data-page="<?php echo $page+1; ?>" class="setPage smallLink" aria-label="<?php echo _('Next page'); ?>">
                <i class='fa fa-forward'></i>
            </a></li>
        <?php endif; ?>
    </nav>

<?php endif; ?>

<?php

switch($params['type']){
    case 'titles':
        include_once __DIR__.'/../templates/ebscoKbTitleList.php';
        break;
    case 'vendors':
        include_once __DIR__.'/../templates/ebscoKbVendorList.php';
        break;
    case 'packages':
        include_once __DIR__.'/../templates/ebscoKbPackageList.php';
        break;
    case 'holdings':
        echo '<pre>';
        echo print_r($items);
        echo '</pre>';
        break;
}
