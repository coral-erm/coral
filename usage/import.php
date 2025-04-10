<?php
$pageTitle = _('Import');
include 'templates/header.php';
?>
<main id="main-content">
  <article>
    <h2><?php echo _('Usage Statistics Import'); ?></h2>
  <?php

	#print errors if passed in

	if (isset($_GET['error'])){
		$errorNumber = $_GET['error'];
		switch ($errorNumber){
      case UPLOAD_ERR_INI_SIZE:
        $message = _("The uploaded file exceeds the upload_max_filesize directive in php.ini");
        break;
      case UPLOAD_ERR_FORM_SIZE:
        $message = _("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form");
        break;
      case UPLOAD_ERR_PARTIAL:
        $message = _("The uploaded file was only partially uploaded");
        break;
      case UPLOAD_ERR_NO_FILE:
        $message = _("No file was uploaded");
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
        $message = _("Missing a temporary folder");
        break;
      case UPLOAD_ERR_CANT_WRITE:
        $message = _("Failed to write file to disk");
        break;
      case UPLOAD_ERR_EXTENSION:
        $message = _("File upload stopped by extension");
        break;
			case 21:
				$message = _("Incorrect File format, must be .txt!");
				break;
			case 23:
				$message = _("File has an incorrectly formatted name - try filename.txt!");
				break;
        case 24:
            $message = _("The archive directory is not writable. Please check permissions.");
            break;
      default:
        $message = _("Unknown upload error");
        break;
		}
    echo "<p class='error'>" . $message . "</p>";
	}

  ?>   
    <form id="form1" name="form1" enctype="multipart/form-data" onsubmit="return validateForm()" method="post" action="uploadConfirmation.php">
      <p>
        <label for="usageFile"><?php echo _("File:");?></label>
        <span id='span_error' class="error"></span>
        <input type="file" name="usageFile" id="usageFile" aria-describedby="span_error"/>
      </p>

      <p>
        <label for="layoutID"><?php echo _("Layout:"); ?></label>
        <select id="layoutID" name="layoutID">
          <?php
          $layout = new Layout();
          foreach($layout->getLayouts as $lo) {
            echo "<option value='" . $lo['layoutID'] . "'>" . $lo['name'] . "</option>\n";
          }
          ?>
        </select>
      </p>
      <p>
        <input type="checkbox" name="overrideInd" id="overrideInd" />
        <label for="overrideInd"><?php echo _("Override previous month verification");?></label>
      </p>
      <h3><?php echo _("Instructions:");?></h3>
      <ul>
        <li><?php echo _("Save file as .txt files in tab delimited format");?></li>
        <li><?php echo _("File may not be larger than 5MB");?></li>
        <li><?php echo _("Ensure column headers conform to Counter's standards for the report type");?></li>
        <li><?php echo _("More info: ");?><a href="http://www.projectcounter.org/code_practice.html" <?php echo getTarget(); ?>>http://www.projectcounter.org/code_practice.html</a></li>
      </ul>
      <p>      
        <input type="submit" name="submitFile" id="submitFile" value="<?php echo _('Upload');?>" />
        <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
      </p>
    </form>
     
    <h3><?php echo _("Recent Imports");?></h3>
    <p id='span_feedback' class="msg"></p>
    <div id='div_recentImports'></div>
  </article>
</main>

<script type="text/javascript" src="js/import.js"></script>

<?php include 'templates/footer.php'; ?>
</body>
</html>