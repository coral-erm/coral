<?php

$pageTitle=_('Home');

include 'templates/header.php';

?>

<table class="headerTable">

<tr style='vertical-align:top;'>
<td style="padding-right:10px;" id="import-file">

<div class="headerText" style='margin:5px 5px 9px 3px;'><?php echo _("Usage Statistics Import");?></div>


  <?php

	#print errors if passed in

	if (isset($_GET['error'])){
		$errorNumber = $_GET['error'];
		switch ($errorNumber){
			case 1:
				echo "<font color='red'>" . _("Incorrect File format, must be .txt or .tsv!") . "</font><br /><br />";
				break;
			case 2:
				echo "<font color='red'>" . _("There was an error uploading the file.  Please verify the size is not over 5MB and try again!") . "</font><br /><br />";
				break;
			case 3:
				echo "<font color='red'>" . _("File has an incorrectly formatted name - try filename.txt or filename.tsv!") . "</font><br /><br />";
				break;
      case 4:
        echo "<font color='red'>" . _("Error with Setup:  This report format is not set up in layouts.ini.") . "</font><br /><br />";
        break;
		}
	}


  ?>
<div style='margin:7px;'>
    <form id="form1" name="form1" enctype="multipart/form-data" onsubmit="return validateForm()" method="post" action="uploadConfirmation.php">

    	<br />

        <b><?php echo _("File:");?></b><span id='span_error' style='color:red'></span><br /><input type="file" name="usageFile" id="usageFile" class='bigger' /><br /><br />

		<?php

		$layout = new Layout();
		echo "<b>" . _("Layout:") . "</b><br /> <select id='layoutID' name='layoutID'>";
	        foreach($layout->getLayouts as $lo) {
			echo "<option value='" . $lo['layoutID'] . "'>" . $lo['name'] . "</option>\n";
		}
		echo "</select>";
		?>

		<br /><br />

        <input type="checkbox" name="overrideInd" id="overrideInd" />&nbsp;<span class="smallText"><?php echo _("Override previous month verification");?></span><br /><br />
        <input type="submit" name="submitFile" id="submitFile" value="<?php echo _('Upload');?>" />
        <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />



    </form>
<br /><br />
<br /><br />
<hr />
<br />
<div class="bigBlueText"><?php echo _("Instructions:");?></div>
<ul class="smallerText">
<li><?php echo _("Save file as .txt files in tab delimited format");?></li>
<li><?php echo _("File may not be larger than 5MB");?></li>
<li><?php echo _("Ensure column headers conform to Counter's standards for the report type");?></li>
<li><?php echo _("More info: ");?><a href="http://www.projectcounter.org/code_practice.html" target="_blank">http://www.projectcounter.org/code_practice.html</a></li>
</ul>
</div>
<br /><br />


</td>
<td>

<div class="headerText" style='margin-bottom:9px;'><?php echo _("Recent Imports");?>&nbsp;&nbsp;&nbsp;<span id='span_feedback'></span></div>
<div id='div_recentImports'>
</div>

</td></tr>

</table>


<script type="text/javascript" src="js/import.js"></script>

<?php include 'templates/footer.php'; ?>
