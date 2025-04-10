<table id='resource_table' class='dataTable table-striped'>
    <thead>
        <tr>
            <th><?php echo _("Name"); ?></th>
            <th><?php echo _("Packages"); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($items as $item): ?>
        <tr>
            <td>
                <?php echo $item->vendorName; ?>
            </td>
            <td>
                <?php echo $item->packagesTotal; ?> (<?php echo $item->packagesSelected; ?> selected)
            </td>
            <td class="actions">
                <button
                    class="setVendor add-button"
                    data-vendor-id="<?php echo $item->vendorId; ?>"
                    data-vendor-name="<?php echo $item->vendorName; ?>"
                    >
                        <?php echo _("View Packages"); ?>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>