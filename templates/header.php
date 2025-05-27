<?php

/*
**************************************************************************************************************************
** CORAL header template
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

/**
 * Common header elements for each module.
 *
 * Gets included in module-specific header.php templates. Before including this template, the following variables should
 * be set:
 * $pageTitle = the name of the current page (done before including module-specific header on each page)
 * $moduleTitle = the name of the current module (done in the module-specific header before including common header)
 *
 * Module-specific header contents should be set after including this template.
 *
 * Usage example (in resources/templates/header.php):
 * $moduleTitle = _('Resources');
 * include_once '../templates/header.php';
 * 
 * Note that coral/index.php does not use this file
 */

$util = new Utility();
$config = new Configuration();

//get CORAL URL for 'Change Module' and logout link.
$coralURL = $util->getCORALURL();
//get CORAL home directory path for global script and stylesheet links
$coralPath = parse_url($coralURL, PHP_URL_PATH);
//get the current module path for including module-specific scripts
$modulePath = str_replace($coralPath, '', pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME));
//get the current page to determine which menu button should be depressed
$currentPage = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_BASENAME);
// Used to determine which module to exclude from the change module list and to determine name of title icon image file
$currentModule = basename(dirname($_SERVER['SCRIPT_FILENAME']));

global $http_lang;
?>

<!DOCTYPE html>
<html lang="<?php echo str_replace('_', '-', $http_lang); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="CACHE-CONTROL" CONTENT="public">

    <title>
        <?php 
          // <title> should go most specific >>> least specific
          // are we looking at an item within a page?
          if (isset($itemTitle) && !empty($itemTitle))
            echo $itemTitle . ' - ';
          
            // are we looking at a page other than the module's index page?
          if ($pageTitle !== $moduleMenu['index']['text'])
            echo $pageTitle . ' - ';
          
          echo $moduleTitle . ' - ' . _("CORAL eRM");
        ?>
    </title>
    <link rel="icon" href="<?php echo $coralPath; ?>images/favicon.ico">
    <link rel="icon" href="<?php echo $coralPath; ?>images/favicon.svg" type="image/svg+xml">
    <!-- Common stylesheets -->
    <link rel="stylesheet" href="<?php echo $coralPath; ?>css/thickbox.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo $coralPath; ?>css/datePicker.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo $coralPath; ?>css/jquery.autocomplete.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo $coralPath; ?>css/jquery.tooltip.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
    <script src="<?php echo $coralPath; ?>js/plugins/Gettext.js"></script>
    <script src="<?php echo $coralPath; ?>js/plugins/translate.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script defer src="<?php echo $coralPath; ?>js/plugins/jquery.autocomplete.js"></script>
    <script defer src="<?php echo $coralPath; ?>js/plugins/datejs-patched-for-i18n.js"></script>
    <script defer src="<?php echo $coralPath; ?>js/plugins/jquery.datePicker-patched-for-i18n.js"></script>
    <script src="<?php echo $coralPath; ?>js/common.js"></script>
    <?php //Add dataTables if they're set. Only in resources/Dasboard.php
    $dataTablesSet = ($dataTables) ?? FALSE;
    if ($dataTablesSet) { ?>
      <script defer src="<?php echo $coralPath; ?>js/plugins/datatables.min.js"></script>
      <script defer src="<?php echo $coralPath; ?>js/plugins/datatables_defaults.js"></script>
      <link rel="stylesheet" type="text/css" href="<?php echo $coralPath; ?>css/datatables.min.css" />
    <?php } ?>
    <!-- main stylesheet -->
    <link rel="preload" href="<?php echo $coralPath; ?>css/style.css" as="style" />
    <link rel="stylesheet" href="<?php echo $coralPath; ?>css/style.css" type="text/css" media="all" />
    <!-- module-specific files -->
    <?php 
    if (!empty($stylesheets) && is_array($stylesheets)) {
      foreach ($stylesheets as $style) { ?>
        <link rel="preload" href="<?php echo $style; ?>" as="style" />
        <link rel="stylesheet" type="text/css" href="<?php echo $style; ?>" media="screen" />
    <?php }
    }
    if (!empty($modulePath)) { ?>
      <!-- TODO: consolidate modules' common.js files into /js/common.js -->
      <script src="js/common.js"></script>
    <?php 
    }
    
    // Add translation for the JavaScript files
    $str = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
    $default_l = $lang_name->getLanguage($str);
    if($default_l==null || empty($default_l)){$default_l=$str;}
    if(isset($_COOKIE["lang"])){
        if($_COOKIE["lang"]==$http_lang && $_COOKIE["lang"] != "en_US"){
            echo "<link rel='gettext' type='application/x-po' href='".$coralURL."locale/".$http_lang."/LC_MESSAGES/messages.po' />";
        }
    }else if($default_l==$http_lang && $default_l != "en_US"){
        echo "<link rel='gettext' type='application/x-po' href='".$coralURL."locale/".$http_lang."/LC_MESSAGES/messages.po' />";
    }
    ?>
</head>
<body class="<?php echo $currentModule; ?>">

<header>
  <div id="brand">
    <a href="#main-content" class="skip"><?php echo _('Skip to main content'); ?></a>
    <h1>
      <a href="<?php echo $coralPath; ?>" class="site-title-link">
        <?php echo _('CORAL eRM'); ?>
      </a>
      <a href="<?php echo $coralPath . $modulePath; ?>/" class="module-title-link">
        <?php echo $moduleTitle; ?>
      </a>
    </h1>
  </div>
  <div id="utilities">
    <nav id="account" aria-label="<?php echo _('Account and Help'); ?>">
      <p class="hello">
        <?php
        // TODO: shatter programmers' beliefs about names
        // see https://developer.apple.com/documentation/foundation/personnamecomponentsformatter for examples
        
        //user may not have their first name / last name set up
        if ( $user->lastName ) {
          $displayName = $user->firstName . " " . $user->lastName;
        } else {
          $displayName = $user->loginID;
        }
        echo sprintf(_("Hello, %s"), $displayName);
        ?>
      </p>
      <ul>
        <?php if ( $config->settings->authModule == 'Y' ) { ?>
          <li>
            <a href="<?php echo $coralURL; ?>auth/?logout" id="logout" title="<?php echo _("Log out"); ?>"><?php echo _("Log out"); ?></a>
          </li>
        <?php } ?>
        <li><a href='http://docs.coral-erm.org/' id="docs" <?php echo getTarget(); ?>><?php echo _("Help"); ?></a>

        <li>
          <button type="button" id="color-mode-toggle" class="btn-secondary">
            <svg role="img" id="color-mode-light" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
              <title><?php echo _('Color mode: light'); ?></title>
              <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"></path>
            </svg>
            <svg role="img" id="color-mode-dark" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
              <title><?php echo _('Color mode: dark'); ?></title>
              <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"></path>
              <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"></path>
            </svg>
            <span class="visually-hidden"><?php echo _("Toggle color mode"); ?></span>
          </button>
        </li>

        <li id="setLanguage">
            <?php $lang_name->getLanguageSelector(); ?>
        </li>
      </ul>
    </nav>
  </div> 
</header>

<nav id="main" aria-label="<?php echo _("Modules"); ?>">
  <button class="btn menu-toggle" id="modules-toggle" type="button" aria-expanded="false" aria-controls="modules"><?php echo _("Modules"); ?></button>
  <ul class="nav" id="modules">
    <?php if (file_exists($util->getCORALPath() . "/index.php")) { 
      if ($currentModule == $coralPath) {
        $ariaCurrentModule = 'aria-current="page"';
      }
      else {
        $ariaCurrentModule = '';
      }
      ?>
      <li><a href="<?php echo $coralURL; ?>" <?php echo $ariaCurrentModule; ?>><?php echo _("Main Menu");?></a></li>
    <?php }
    if ($config->settings->resourcesModule == 'Y') { 
      if ($currentModule == 'resources') {
        $ariaCurrentModule = 'aria-current="page"';
      }
      else {
        $ariaCurrentModule = '';
      }
      ?>
      <li><a href="<?php echo $coralURL . 'resources/"'; ?>" <?php echo $ariaCurrentModule; ?>><?php echo _("Resources"); ?></a></li>
    <?php }
    if ($config->settings->organizationsModule == 'Y') { 
      if ($currentModule == 'organizations') {
        $ariaCurrentModule = 'aria-current="page"';
      }
      else {
        $ariaCurrentModule = '';
      }
      ?>
      <li><a href="<?php echo $coralURL . 'organizations/"'; ?>" <?php echo $ariaCurrentModule; ?>><?php echo _("Organizations");?></a></li>
    <?php }
    if ($config->settings->licensingModule == 'Y') { 
      if ($currentModule == 'licensing') {
        $ariaCurrentModule = 'aria-current="page"';
      }
      else {
        $ariaCurrentModule = '';
      }
      ?>
      <li><a href="<?php echo $coralURL . 'licensing/"'; ?>" <?php echo $ariaCurrentModule; ?>><?php echo _("Licensing");?></a></li>
    <?php }
    if ($config->settings->usageModule == 'Y') { 
      if ($currentModule == 'usage') {
        $ariaCurrentModule = 'aria-current="page"';
      }
      else {
        $ariaCurrentModule = '';
      }
      ?>
      <li><a href="<?php echo $coralURL . 'usage/"'; ?>" <?php echo $ariaCurrentModule; ?>><?php echo _("Usage Statistics");?></a></li>
    <?php }
    if ($config->settings->managementModule == 'Y') { 
      if ($currentModule == 'management') {
        $ariaCurrentModule = 'aria-current="page"';
      }
      else {
        $ariaCurrentModule = '';
      }
      ?>
      <li><a href="<?php echo $coralURL . 'management/"'; ?>" <?php echo $ariaCurrentModule; ?>><?php echo _("Management");?></a></li>
    <?php } ?>
  </ul>


  <button class="btn menu-toggle" id="tools-toggle" type="button" aria-expanded="false" aria-controls="tools"><?php echo _("Tools"); ?></button>
  <ul class="nav" id="tools">
  <?php
  // menu links are defined in modules' templates/header.php file
  
  foreach ($moduleMenu as $item) {
    $ariaCurrent = '';
    if ( isset($item['url']) && $item['url'] == $currentPage ) {
      $ariaCurrent = ' aria-current="page" ';
    }
    // New tabs only if configuration allows
    $target = '';
    if ( isset($item['target']) && $item['target'] == '_blank' ) {
      $target = getTarget();
    }
    ?>
    <li>
      <?php if ( isset($item['action']) ) { ?>
        <a href="javascript:void(0)" id="<?php echo $item['id']; ?>" class="<?php echo $item['classes']; ?>" onclick="<?php echo $item['action']; ?>">
          <?php echo $item['text']; ?>
        </a>
      <?php 
      } 
      else { 
        $url = ($item['url']) ?? "";
        $classes = ($item['classes']) ?? "";
        $id = ($item['id']) ?? "";
        $text = ($item['text']) ?? "";
        echo "<a href='{$url}' id='{$id}' class='{$classes}' {$ariaCurrent}{$target}>{$text}</a>";
      } 
      ?>
    </li>
<?php 
  } 
  ?>
</ul>
</nav>

<p id="span_message" role="status">
  <?php
    $messages = [];
    $messages[] = ($_POST['message']) ?? "";
    $messages[] = ($errorMessage) ?? "";
    $messages[] = ($err) ?? "";
    echo implode("", $messages);
  ?>
</p>