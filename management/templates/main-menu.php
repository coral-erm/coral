<?php
/**
 * Module-specific PHP for creating the main menu links in #main-menu-titles
 * Included in ../templates/title.php
 */
?>
<?php if ($user->isAdmin()){ ?>

    <a href='index.php'>
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

    <a href='ajax_forms.php?action=getLicenseForm&height=350&width=300&modal=true&newLicenseID=' class='thickbox' id='newLicense'>
        <div class="main-menu-link">
            <img src="images/menu/icon-new-doc.png" />
            <span><?php echo _("New Document");?></span>
        </div>
    </a>

    <a href='admin.php'>
        <div class="main-menu-link <?php if ($currentPage == 'admin.php') { echo "active"; } ?>">
            <img src="images/menu/icon-admin.png" />
            <span><?php echo _("Admin");?></span>
        </div>
    </a>

<?php }else if ($user->canEdit()){ ?>

    <a href='index.php'>
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

    <a href='ajax_forms.php?action=getLicenseForm&height=350&width=300&modal=true&newLicenseID=' class='thickbox' id='newLicense'>
        <div class="main-menu-link">
            <img src="images/menu/icon-new-doc.png" />
            <span><?php echo _("New Document");?></span>
        </div>
    </a>

<?php }else{ ?>

    <a href='index.php'>
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

    <a href='ajax_forms.php?action=getLicenseForm&height=350&width=300&modal=true&newLicenseID=' class='thickbox' id='newLicense'>
        <div class="main-menu-link">
            <img src="images/menu/icon-new-doc.png" />
            <span><?php echo _("New Document");?></span>
        </div>
    </a>

<?php }