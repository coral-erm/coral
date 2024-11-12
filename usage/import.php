<?php

$pageTitle=_('Home');

include 'templates/header.php';

?>

<table class="headerTable">

<tr style='vertical-align:top;'>
<td style="padding-right:10px;" id="import-file">

<div class="headerText" style='margin:5px 5px 9px 3px;'><?= _("Usage Statistics Import");?></div>


  <?php

	#print errors if passed in
  $errorMessage = FALSE;
	if (isset($_GET['error'])){
		$errorNumber = $_GET['error'];
		switch ($errorNumber){
      case 1:
        $errorMessage =  _("Selected Layout not found in CORAL Database.");
        break;
      case 2:
        $errorMessage =  _("Selected Layout not found in layouts.ini file.");
        break;
      case 3:
        $errorMessage =  _("Incorrect file extension - must be a .txt. or .tsv file.");
        break;
      case 4:
        $errorMessage =  _("Incorrect file mimetype. File must have a mime type of text/plain.");
        break;
      case 5:
        $errorMessage =  _("The counterstore directory is not writable by CORAL.");
        break;
      case 6:
        $errorMessage =  _("Error moving file into the counterstore directory.");
      default:
        $errorMessage =  _("Unknown upload error");
        break;
		}
	}

  if (isset($_GET['fileerror'])){
    $fileErrorCode = $_GET['fileerror'];
    //Technically we're receiving a number from fileerror - this number corresponds with the standard PHP Error Codes. We're using the error code names for the case statements to help make it readable.
    switch ($fileErrorCode){
      case UPLOAD_ERR_INI_SIZE:
        $errorMessage =  _("The uploaded file exceeds the upload_max_filesize directive in php.ini");
        break;
      case UPLOAD_ERR_FORM_SIZE:
        $errorMessage =  _("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form");
        break;
      case UPLOAD_ERR_PARTIAL:
        $errorMessage =  _("The uploaded file was only partially uploaded");
        break;
      case UPLOAD_ERR_NO_FILE:
        $errorMessage =  _("No file was uploaded");
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
        $errorMessage =  _("Missing a temporary folder");
        break;
      case UPLOAD_ERR_CANT_WRITE:
        $errorMessage =  _("Failed to write file to disk");
        break;
      case UPLOAD_ERR_EXTENSION:
        $errorMessage =  _("File upload stopped by extension");
        break;
      default:
        $errorMessage =  _("Unknown upload error");
        break;
    }
  }
  if($errorMessage){echo "<p style='color:red'>{$errorMessage}</p>";}


  ?>
<div style='margin:7px;'>
    <style>
      #form1 {
        margin-bottom: 2rem;
      }
      #form1 > select {
        margin-bottom: 1rem;
      }
      label {
        font-weight: bold;
      }
      label.checkBox {
        font-weight: normal;
        font-size: small;
        margin-left: 0.3rem;
      }
      label.ownLine {
        display:block;
        margin-top: 0.5rem;
        margin-bottom: 0.25rem;
      }
      input[type="submit"] {
        margin-top: 1rem;
        display:block;
      }
    </style>
    <form id="form1" name="form1" enctype="multipart/form-data" onsubmit="return validateForm()" method="post" action="uploadConfirmation.php">
      <label for="usageFile" class="ownLine"><?= _("File:");?></label>
      <input type="file" name="usageFile" id="usageFile" accept="text/plain,.tsv,.txt" class="bigger" />
		  <label for="layoutID" class="ownLine"><?= _("Layout:"); ?></label>
      <select id="layoutID" name="layoutID">
        <?php 
          $layout = new Layout();
          foreach($layout->getLayouts as $layoutInfo){
            echo "<option value='{$layoutInfo['layoutID']}'>{$layoutInfo['name']}</option>";
          }
        ?>
      </select>
      <input type="checkbox" name="overrideInd" id="overrideInd" /><label for="overrideInd" class="checkBox"><?= _("Override previous month verification");?></label>
      <input type="submit" name="submitFile" id="submitFile" value="<?= _('Upload Report File');?>" />
      <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
    </form>
<hr />
<figure>
  <figcaption class="bigBlueText"><?= _("Instructions:"); ?></figcaption>
  <ul class="smallerText">
    <li><?= _("Save file as .txt files in tab delimited format");?></li>
    <li><?= _("File extension must be .txt or .tsv file."); ?></li>
    <li><?= _("CORAL is able to process files no larger than 5MB. Your server may have a lower upload limit.");?></li>
    <li><?= _("Ensure column headers conform to Counter's standards for the report type. Note: at present CORAL expects these headers to be in Row 1 of the file.");?></li>
    <li><?= _("More info about COUNTER's Code of Practice at: ");?><a href="https://www.countermetrics.org/code-of-practice/" target="_blank">https://www.countermetrics.org/code-of-practice/</a></li>
  </ul>
</figure>
<br /><br />


</td>
<td>

<div class="headerText" style='margin-bottom:9px;'><?= _("Recent Imports");?>&nbsp;&nbsp;&nbsp;<span id='span_feedback'></span></div>
<div id='div_recentImports'>
</div>

</td></tr>

</table>


<script type="text/javascript" src="js/import.js"></script>

<?php include 'templates/footer.php'; ?>
