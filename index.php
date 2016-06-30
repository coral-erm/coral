<?php
	//determine CORAL main path so we can check each module below to know which to display
	$pagePath = $_SERVER["DOCUMENT_ROOT"];

	$currentFile = $_SERVER["SCRIPT_NAME"];
	$parts = Explode('/', $currentFile);
	for($i=0; $i<count($parts) - 1; $i++){
		$pagePath .= $parts[$i] . '/';
	}

	session_start();

	// Include file of language codes
	include_once 'LangCodes.php';
	$lang_name = new LangCodes();

	// Verify the language of the browser
	global $http_lang;
	if(isset($_COOKIE["lang"])){
	    $http_lang = $_COOKIE["lang"];
	}else{
	    $codeL = str_replace("-","_",substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5));
	    $http_lang = $lang_name->getLanguage($codeL);
	    if($http_lang == "")
	      $http_lang = "en_US";
	}
	putenv("LC_ALL=$http_lang");
	setlocale(LC_ALL, $http_lang.".utf8");
	bindtextdomain("messages", dirname(__FILE__) . "/locale");
	textdomain("messages");
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>eRM - eResource Management</title>
<link rel="stylesheet" href="indexstyle.css" type="text/css" media="screen" />
<link rel="SHORTCUT ICON" href="images/favicon.ico" />
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/plugins/jquery.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/plugins/Gettext.js"></script>

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
<body>

<div id="wrapper-main">

	<div id="title-main">
		<div id="main-page-title"><strong><?php echo _("eRM");?></strong> &bullet; <?php echo _("eResource Management");?></div>
		<div class='boxRight'>
			<p class="fontText"><?= _("Change language:");?></p>
			<select name="lang" id="lang" class="dropDownLang">
				<?php
	            // Get all translations on the 'locale' folder
	            $route='locale';
	            $lang[]="en_US"; // add default language
	            if (is_dir($route)) {
	                if ($dh = opendir($route)) {
	                    while (($file = readdir($dh)) !== false) {
	                        if (is_dir("$route/$file") && $file!="." && $file!=".."){
	                            $lang[]=$file;
	                        }
	                    }
	                    closedir($dh);
	                }
	            }else {
	                echo "<br>"._("Invalid translation route!");
	            }
	            // Get language of navigator
	            $defLang = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);

	            // Show an ordered list
	            sort($lang);
	            for($i=0; $i<count($lang); $i++){
	                if(isset($_COOKIE["lang"])){
	                    if($_COOKIE["lang"]==$lang[$i]){
	                        echo "<option value='".$lang[$i]."' selected='selected'>".$lang_name->getNameLang($lang[$i])."</option>";
	                    }else{
	                        echo "<option value='".$lang[$i]."'>".$lang_name->getNameLang($lang[$i])."</option>";
	                    }
	                }else{
	                    if($defLang==substr($lang[$i],0,5)){
	                        echo "<option value='".$lang[$i]."' selected='selected'>".$lang_name->getNameLang($lang[$i])."</option>";
	                    }else{
	                        echo "<option value='".$lang[$i]."'>".$lang_name->getNameLang($lang[$i])."</option>";
	                    }
	                }
	            }
				?>
			</select>
		</div>
	</div>
</div>

		<div class="icons">

			<div class='main-page-icons'>
				<?php if (file_exists($pagePath . "resources/index.php")) {?>
					<a href='resources/'>
						<img src='images/icon-resources.png' class="rollover" />
						<span><?php echo _("Resources");?></span>
					</a>
				<?php } else { ?>
					<div class='main-page-icons-off'>
						<img src='images/icon-resources-off.png'>
						<span><?php echo _("Resources");?></span>
					</div>
				<?php } ?>
			</div>


			<div class='main-page-icons'>
				<?php if (file_exists($pagePath . "licensing/index.php")) {?>
					<a href='licensing/'>
						<img src='images/icon-licensing.png' class="rollover" />
						<span><?php echo _("Licensing");?></span>
					</a>
				<?php } else { ?>
					<div class='main-page-icons-off'>
						<img src='images/icon-licensing-off.png' />
						<span><?php echo _("Licensing");?></span>
					</div>
				<?php } ?>
			</div>

			<div class='main-page-icons'>
				<?php if (file_exists($pagePath . "organizations/index.php")) {?>
					<a href='organizations/'>
						<img src='images/icon-organizations.png' class="rollover" />
						<span><?php echo _("Organizations");?></span>
					</a>
				<?php } else { ?>
					<div class='main-page-icons-off'>
						<img src='images/icon-organizations-off.png' />
						<span><?php echo _("Organizations");?></span>
					</div>
				<?php } ?>
			</div>

			<div class='main-page-icons'>
				<?php if (file_exists($pagePath . "usage/index.php")) {?>
					<a href='usage/'>
						<img src='images/icon-usage.png' class="rollover" />
						<span><?php echo _("Usage Statistics");?></span>
					</a>
				<?php } else { ?>
					<div class='main-page-icons-off'>
						<img src='images/icon-usage-off.png' />
						<span><?php echo _("Usage Statistics");?></span>
					</div>
				<?php } ?>
			</div>

			<div class='main-page-icons'>
				<?php if (file_exists($pagePath . "management/index.php")) {?>
					<a href='management/'>
						<img src='images/icon-management.png' class="rollover" />
						<span><?php echo _("Management");?></span>
					</a>
				<?php } else { ?>
					<div class='main-page-icons-off'>
						<img src='images/icon-management-off.png' />
						<span><?php echo _("Management");?></span>
					</div>
				<?php } ?>
			</div>

		</div>

		<div id="powered-by-text"><?php echo _("Powered by");?><img src="images/logo-coral.jpg" /></div>


<script>
    /*
     * Functions to change the language with the dropdown
     */
    $("#lang").change(function() {
        setLanguage($("#lang").val());
        location.reload();
    });
    // Create a cookie with the code of language
    function setLanguage(lang) {
		var wl = window.location, now = new Date(), time = now.getTime();
        var cookievalid=2592000000; // 30 days (1000*60*60*24*30)
        time += cookievalid;
		now.setTime(time);
		document.cookie ='lang='+lang+';path=/'+';domain='+wl.host+';expires='+now;
    }
</script>
</body>
</html>
