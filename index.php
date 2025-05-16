<?php
	session_start();
	// "install/index.php" will check if CORAL is installed and version is current
	require_once("install/index.php");

	// Include file of language codes
	include_once 'LangCodes.php';
	$lang_name = new LangCodes();

	// Verify the language of the browser
	global $http_lang;
	if(isset($_COOKIE["lang"])){
		$http_lang = $_COOKIE["lang"];
	}else{
		$codeL = $lang_name->getBrowserLanguage();
		$http_lang = $lang_name->getLanguage($codeL);
		if($http_lang == "")
		  $http_lang = "en_US";
	}
	putenv("LC_ALL=$http_lang");
	setlocale(LC_ALL, $http_lang.".utf8");
	bindtextdomain("messages", dirname(__FILE__) . "/locale");
	textdomain("messages");
?>

<!DOCTYPE html>
<html lang="<?php echo str_replace('_', '-', $http_lang); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="CACHE-CONTROL" CONTENT="public">

	<title><?php echo _("eRM - eResource Management"); ?></title>
	
	<link rel="icon" href="images/favicon.ico">
  <link rel="icon" href="images/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="css/indexstyle.css" type="text/css" media="screen" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="js/plugins/Gettext.js"></script>
	<script src="js/common.js"></script>

	<?php
		// Add translation for the JavaScript files
		global $http_lang;
		$str = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
		$default_l = $lang_name->getLanguage($str);
		if($default_l==null || empty($default_l)){$default_l=$str;}
		if(isset($_COOKIE["lang"])){
			if($_COOKIE["lang"]==$http_lang && $_COOKIE["lang"] != "en_US"){
				echo "<link rel='gettext' type='application/x-po' href='./locale/".$http_lang."/LC_MESSAGES/messages.po' />";
			}
		}else if($default_l==$http_lang && $default_l != "en_US"){
				echo "<link rel='gettext' type='application/x-po' href='./locale/".$http_lang."/LC_MESSAGES/messages.po' />";
		}
	?>
</head>
<body class="home">

	<header>
		<a href="#main-content" class="skip"><?php echo _('Skip to main content'); ?></a>
    	<h1 class="title-main fw-normal"><?php echo _("<b>eRM</b> &bullet; eResource Management");?></h1>
		<p>
			<label for="lang" class="language-select"><?php echo _("Change language:");?></label>
      		<?php $lang_name->getLanguageSelector(); ?>
		</p>
		<p>
			<label for="color-mode-toggle"><?php echo _("Change color mode:"); ?></label>
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
		</p>
	</header>
<main id="main-content">
	<article>
	<ul class="icons">
		<?php
		$mainPageIcon = "";
		$modules = [ "resources" => _("Resources"), "licensing" => _("Licensing"), "organizations" => _("Organizations"), "usage" => _("Usage Statistics"), "management" => _("Management") ];

		foreach ($modules as $key => $value)
		{
			$module = "";
			try
			{
				$mod_conf = Config::getSettingsFor($key);
				if (isset($mod_conf["enabled"]) && $mod_conf["enabled"] == "Y")
				{
					$module = "<a href='{$key}/'><img src='images/icon-{$key}.png' alt='' /><span>{$value}</span></a>";
				}
			}
			catch (Exception $e)
			{
				if ($e->getCode() != Config::ERR_VARIABLES_MISSING)
				{
					throw $e;
				}
			}

			if (empty($module))
			{
				$module = "<img src='images/icon-{$key}-off.png'><span>{$value}</span>";
			}
			$mainPageIcon .= "<li id='{$key}' class='main-page-icons'>$module</li>";
		}
		echo $mainPageIcon;
		?>
	</ul>

	</article>
	</main>

	<footer class="footer">
		<p><?php echo _("Copyright");?> &copy; <?php echo date('Y'); ?>. <?php echo _("CORAL version");?> 2025.04.01</p>
		<p>
			<a href="https://coral-erm.org/" class="site-title-link logo"><?php echo _('CORAL eRM project website'); ?></a>
			<a href="https://github.com/coral-erm/coral/issues" id="report-issue"><?php echo _("Report an Issue");?></a>
		</p>	
	</footer>

	<script>
		const CORAL_ILS_LINK=<?php echo $config->ils->ilsConnector ? 1 : 0; ?>;
		Date.format = '<?php if (function_exists('return_datepicker_date_format')) echo return_datepicker_date_format(); else echo "mm/dd/yyyy"; ?>';
	</script>
</body>
</html>
