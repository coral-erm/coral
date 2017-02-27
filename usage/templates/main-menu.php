<?php
/**
 * Module-specific PHP for creating the main menu links in #main-menu-titles
 * Included in ../templates/title.php
 */
?>

<?php if ($user->isAdmin())
{
    ?>
    <a href='index.php'>
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

    <a href='import.php'>
        <div class="main-menu-link <?php if ($currentPage == 'import.php') { echo "active"; } ?>">
            <img src="images/menu/icon-import.png" />
            <span><?php echo _("File Import");?></span>
        </div>
    </a>

    <a href='sushi.php'>
        <div class="main-menu-link <?php if ($currentPage == 'sushi.php') { echo "active"; } ?>">
            <img src="images/menu/icon-sushi.png" />
            <span><?php echo _("SUSHI");?></span>
        </div>
    </a>

    <a href='admin.php'>
        <div class="main-menu-link <?php if ($currentPage == 'admin.php') { echo "active"; } ?>">
            <img src="images/menu/icon-admin.png" />
            <span><?php echo _("Admin");?></span>
        </div>
    </a>

    <a href='reporting.php'>
        <div class="main-menu-link <?php if ($currentPage == 'reporting.php') { echo "active"; } ?>">
            <img src="images/menu/icon-report-options.png" />
            <span><?php echo _("Report Options");?></span>
        </div>
    </a>

    <?php if ($config->settings->reportingModule == "Y") {
    ?>
    <a href='../reports/' target='_blank'>
        <div class="main-menu-link">
            <img src="images/menu/icon-usage.png" />
            <span><?php echo _("Usage Reports");?></span>
        </div>
    </a>
    <?php
}
}
else
{
    ?>
    <a href='index.php'>
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

    <a href='import.php'>
        <div class="main-menu-link <?php if ($currentPage == 'import.php') { echo "active"; } ?>">
            <img src="images/menu/icon-import.png" />
            <span><?php echo _("File Import");?></span>
        </div>
    </a>

    <a href='sushi.php'>
        <div class="main-menu-link <?php if ($currentPage == 'sushi.php') { echo "active"; } ?>">
            <img src="images/menu/icon-sushi.png" />
            <span><?php echo _("SUSHI");?></span>
        </div>
    </a>

    <a href='reporting.php'>
        <div class="main-menu-link <?php if ($currentPage == 'reporting.php') { echo "active"; } ?>">
            <img src="images/menu/icon-report-options.png" />
            <span><?php echo _("Report Options");?></span>
        </div>
    </a>
    <?php
    if ($config->settings->reportingModule == "Y") {
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href='../reports/' target='_blank' id='usage-reports'>
        <div class="main-menu-link">
            <img src="images/menu/icon-usage.png" />
            <span><?php echo _("Usage Reports");?></span>
        </div>
    </a>
    <?php
    }
}