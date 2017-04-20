
<?php
/**
 * Module-specific PHP for creating the main menu links in #main-menu-titles
 * Included in ../templates/title.php
 */


if ($user->isAdmin()) { ?>

    <a href='index.php'>
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

    <a href='ajax_forms.php?action=getLicenseForm&height=265&width=260&modal=true&newLicenseID=' class='thickbox' id='newLicense'>
        <div class="main-menu-link">
            <img src="images/menu/icon-plus-square.png" />
            <span><?php echo _("New License");?></span>
        </div>
    </a>

    <a href='in_progress.php'>
        <div class="main-menu-link <?php if ($currentPage == 'in_progress.php') { echo "active"; } ?>">
            <img src="images/menu/icon-license-progress.png" />
            <span><?php echo _("License In Progress");?></span>
        </div>
    </a>

    <a href='compare.php'>
        <div class="main-menu-link <?php if ($currentPage == 'compare.php') { echo "active"; } ?>">
            <img src="images/menu/icon-expression.png" />
            <span><?php echo _("Expression Comparison");?></span>
        </div>
    </a>

    <?php if (($config->settings->resourcesModule == 'Y') && (strlen($config->settings->resourcesDatabaseName) > 0)) { ?>
        <a href='calendar.php'>
            <div class="main-menu-link <?php if ($currentPage == 'calendar.php') { echo "active"; } ?>">
                <img src="images/menu/icon-calendar.png" />
                <span><?php echo _("Calendar");?></span>
            </div>
        </a>
    <?php } ?>

    <a href='onix_import.php'>
        <div class="main-menu-link <?php if ($currentPage == 'onix_import.php') { echo "active"; } ?>">
            <img src="images/menu/icon-import.png" />
            <span><?php echo _("ONIX-PL File Import");?></span>
        </div>
    </a>

    <a href='admin.php'>
        <div class="main-menu-link <?php if ($currentPage == 'admin.php') { echo "active"; } ?>">
            <img src="images/menu/icon-admin.png" />
            <span><?php echo _("Admin");?></span>
        </div>
    </a>

    <?php
}
else if ($user->canEdit()) { ?>

    <a href='index.php'>
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

    <a href='ajax_forms.php?action=getLicenseForm&height=265&width=260&modal=true&newLicenseID=' class='thickbox' id='newLicense'>
        <div class="main-menu-link">
            <img src="images/menu/icon-plus-square.png" />
            <span><?php echo _("New License");?></span>
        </div>
    </a>

    <a href='in_progress.php'>
        <div class="main-menu-link <?php if ($currentPage == 'in_progress.php') { echo "active"; } ?>">
            <img src="images/menu/icon-license-progress.png" />
            <span><?php echo _("License In Progress");?></span>
        </div>
    </a>

    <a href='compare.php'>
        <div class="main-menu-link <?php if ($currentPage == 'compare.php') { echo "active"; } ?>">
            <img src="images/menu/icon-expression.png" />
            <span><?php echo _("Expression Comparison");?></span>
        </div>
    </a>

    <?php if (($config->settings->resourcesModule == 'Y') && (strlen($config->settings->resourcesDatabaseName) > 0)) { ?>
        <a href='calendar.php'>
            <div class="main-menu-link <?php if ($currentPage == 'calendar.php') { echo "active"; } ?>">
                <img src="images/menu/icon-calendar.png" />
                <span><?php echo _("Calendar");?></span>
            </div>
        </a>
    <?php } ?>

    <a href='onix_import.php'>
        <div class="main-menu-link <?php if ($currentPage == 'onix_import.php') { echo "active"; } ?>">
            <img src="images/menu/icon-import.png" />
            <span><?php echo _("ONIX-PL File Import");?></span>
        </div>
    </a>

    <?php
}
else { ?>

    <a href='index.php'>
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

    <a href='ajax_forms.php?action=getLicenseForm&height=265&width=260&modal=true&newLicenseID=' class='thickbox' id='newLicense'>
        <div class="main-menu-link">
            <img src="images/menu/icon-plus-square.png" />
            <span><?php echo _("New License");?></span>
        </div>
    </a>

    <a href='in_progress.php'>
        <div class="main-menu-link <?php if ($currentPage == 'in_progress.php') { echo "active"; } ?>">
            <img src="images/menu/icon-license-progress.png" />
            <span><?php echo _("License In Progress");?></span>
        </div>
    </a>

    <a href='compare.php'>
        <div class="main-menu-link <?php if ($currentPage == 'compare.php') { echo "active"; } ?>">
            <img src="images/menu/icon-expression.png" />
            <span><?php echo _("Expression Comparison");?></span>
        </div>
    </a>

    <?php if (($config->settings->resourcesModule == 'Y') && (strlen($config->settings->resourcesDatabaseName) > 0)) { ?>
        <a href='calendar.php'>
            <div class="main-menu-link <?php if ($currentPage == 'calendar.php') { echo "active"; } ?>">
                <img src="images/menu/icon-calendar.png" />
                <span><?php echo _("Calendar");?></span>
            </div>
        </a>
    <?php } ?>

    <a href='onix_import.php'>
        <div class="main-menu-link <?php if ($currentPage == 'onix_import.php') { echo "active"; } ?>">
            <img src="images/menu/icon-import.png" />
            <span><?php echo _("ONIX-PL File Import");?></span>
        </div>
    </a>

    <?php
} ?>
