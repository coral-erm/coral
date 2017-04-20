<?php
/**
 * Module-specific PHP for creating the main menu links in #main-menu-titles
 * Included in ../templates/title.php
 */

?>

<a href='index.php' title="<?php echo _("Home") ?>">
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
</div>
</a>

<?php if ($user->isAdmin() || $user->canEdit()){ ?>
    <a href='ajax_forms.php?action=getNewResourceForm&height=503&width=775&resourceID=&modal=true' class='thickbox' id='newResource' title="<?php echo _("New Resource"); ?>">
        <div class="main-menu-link">
            <img src="images/menu/icon-plus-square.png" />
            <span><?php echo _("New Resource");?></span>
        </div>
    </a>

    <a href='queue.php' title="<?php echo _("My Queue");?>">
        <div class="main-menu-link <?php if ($currentPage == 'queue.php') { echo "active"; } ?>">
            <img src="images/menu/icon-queue.png" />
            <span><?php echo _("My Queue");?></span>
        </div>
    </a>

    <a href='import.php' title="<?php echo _("Import");?>">
        <div class="main-menu-link <?php if ($currentPage == 'import.php') { echo "active"; } ?>">
            <img src="images/menu/icon-import.png" />
            <span><?php echo _("File Import");?></span>
        </div>
    </a>

    <?php if ($user->isAdmin()) { ?>
        <a href='admin.php' title="<?php echo _("Admin");?>">
            <div class="main-menu-link <?php if ($currentPage == 'admin.php') { echo "active"; } ?>">
                <img src="images/menu/icon-admin.png" />
                <span><?php echo _("Admin");?></span>
            </div>
        </a>
    <?php } ?>
<?php } ?>