<?php
function draw_install_page_template()
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>CORAL - Centralized Online Resources Acquisitions and Licensing</title>
	<link rel="SHORTCUT ICON" href="images/favicon.ico" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/skeleton/2.0.4/skeleton.min.css">
	<link rel="stylesheet" href="css/install.css">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link  rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/install.js"></script>
</head>

<body class="container">

	<div class="row main">
		<div class="two columns">&nbsp;</div>
		<div class="eight columns">
			<h1 class="content-head is-center heading">
				CORAL Installer
			</h1>
			<div class="installation_stuff">
				<div class="section-title"></div>
				<div class="messages">
					<div class="message">
						Welcome to the CORAL Installer.
					</div>
				</div>
				<div class="mainbody">
				</div>
			</div>
			<div class="redirection">
				<div class="row">
					<div class="three columns">
						<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>
					</div>
					<div class="nine columns">
						<div class="completion_title"></div>
						<p class="completion_message"></p>
						<p>
							<span class="redirection_message"></span><span class="countdown"></span>
						</p>
						<ul class="completed_test_holder">
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="two columns">&nbsp;</div>
	</div>
	<div class="percentageComplete"></div>
</body>
</html>
<?php
}
