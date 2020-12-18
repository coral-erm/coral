<?php

$titleId = filter_input(INPUT_GET, 'titleId', FILTER_SANITIZE_STRING);

if(empty($titleId)){
    echo '<p>No title ID provided</p>';
    exit;
}

$ebscoKb = EbscoKbService::getInstance();
$title = $ebscoKb->getTitle($titleId);
$title->loadResource();

?>

<?php if($title->selected): ?>
    <span class="text-success"><i class="fa fa-check"></i>
        <?php
            if($title->resource){
                echo _('Selected & Imported');
            } else {
                echo _('Selected');
            }
        ?>
    </span>
<?php else: ?>
    <?php if($title->resource): ?>
        <span class="text-warning"><i class="fa fa-warning"></i>
            <?php echo _('Imported but Not Selected'); ?>
        </span>
    <?php endif; ?>
<?php endif; ?>

