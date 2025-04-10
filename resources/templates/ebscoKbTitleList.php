<?php
if(empty($page)) {
    $page = 1;
}
?>

<table id='resource_table' class='dataTable table-striped' style='width:840px'>
    <thead>
        <tr>
            <th><?php echo _("Title"); ?></th>
            <th><?php echo _("Resource Type"); ?></th>
            <th><?php echo _("ISXNs"); ?></th>
            <th><?php echo _("Current Status"); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($items as $item): ?>
        <?php $item->loadResource(); ?>
        <tr>
            <td>
                <button type="button" class="btn" onclick='myDialog("ajax_htmldata.php?action=getEbscoKbTitleDetails&height=700&width=730&modal=true&titleId=<?php echo $item->titleId; ?>",740,780)'
                    class="thickbox">
                    <?php echo $item->titleName; ?>
                </button>
            </td>
            <td>
                <?php echo $item->pubType; ?>
            </td>
            <td>
                <ul class="unstyled small">
                    <?php
                    foreach($item->isxnList as $identifier){
                        if(in_array($identifier['type'], [0,1])) {
                            switch($identifier['subtype']){
                                case 1:
                                    $subtype = ' (Print)';
                                    break;
                                case 2:
                                    $subtype = ' (Electronic)';
                                    break;
                                default:
                                    $subtype = '';
                            }
                            echo sprintf('<li class="nowrap">%s%s</li>', $identifier['id'], $subtype);
                        }
                    }
                    ?>
                </ul>
            </td>
            <td class="actions">
                <div class="title-status" data-title-id="<?php echo $item->titleId; ?>"><?php echo _('Processing'); ?>...</div>
            </td>
            <td class="actions">
                <button type="button" class="btn" onclick='myDialog("ajax_htmldata.php?action=getEbscoKbTitleDetails&height=700&width=730&modal=true&titleId=<?php echo $item->titleId; ?>&page=<?php echo $page; ?>",740,780)'
                    class="thickbox btn btn-primary">
                    <?php echo _('manage'); ?>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
