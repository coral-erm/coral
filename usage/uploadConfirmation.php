<?php
/*
**************************************************************************************************************************
** CORAL Usage Statistics Module
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

ini_set("auto_detect_line_endings", true);
include_once 'directory.php';

$util = new Utility();

function cleanValue($value) {
  //strip everything after (Subs from Title
  if (strpos($value,' (Subs') !== false) {
    $value = substr($value, 0, strpos($value, ' (Subs'));
  }
  //remove " quotes
  $value = str_replace("\"","",$value);

  // set value to &nbsp; if value is empty
  $value = (($value == '') || ($value == ' ')) ? "&nbsp;" : $value;
  return trim($value);
}

$importLogID = filter_input(INPUT_GET, 'importLogID', FILTER_VALIDATE_INT);
$fromSushi = !empty($importLogID) && $importLogID > 0;

// The page data differs for sushi and manual uploads. This adds clarity to what is rendered onto the page
$page = array(
  'title' => '',
  'reportName' => '',
  'formValues' => array(),
  'status' => array(),
  'errors' => array(),
  'warnings' => array(),
);

//this file has been created from SUSHI
if ($fromSushi) {

  $importLog = new ImportLog(new NamedArguments(array('primaryKey' => $_GET['importLogID'])));
  $layout = new Layout();
  $layout->getByLayoutCode($importLog->layoutCode);

  // read file
  $file_handle = $util->utf8_fopen_read($importLog->fileName, true);

  // page values
  $page['title'] = _('SUSHI Import Confirmation');
  $page['reportName'] = $layout->name;
  $page['formValues']['importLogID'] = $importLogID;
  $page['formValues']['overrideInd'] = 'Y';
  $page['warnings'][] = _("File has been imported from SUSHI. The default behavior for imported SUSHI files is to overwrite previously imported data. If this is incorrect, please contact a system administrator.");

} else {

  //came from file import

  // before assessing file, check that the layoutID is valid
  $layoutID = filter_input(INPUT_POST, 'layoutID', FILTER_VALIDATE_INT);

  $layout = new Layout(new NamedArguments(array('primaryKey' => $layoutID)));

  if (!$layout->name) {
    header( 'Location: import.php?error=4' ) ;
    exit();
  }

  #read layouts ini file to get the available layouts
  $layoutsArray = parse_ini_file("layouts.ini", true);
  $layoutKey = $layoutsArray['ReportTypes'][$layout->layoutCode];
  if (empty($layoutKey) || empty($layoutsArray[$layoutKey])) {
    header( 'Location: import.php?error=4' ) ;
    exit();
  }

  $columnsToCheck = $layoutsArray[$layoutKey]['columnToCheck'];
  $layoutColumns = $layoutsArray[$layoutKey]['columns'];

  // check file validity

  // get fileinfo
  $pathInfo = pathinfo($_FILES['usageFile']['name']);

  // check the extension is valid
  if (!in_array(strtolower($pathInfo['extension']), array('txt','tsv'))) {
    header( 'Location: import.php?error=1' ) ;
    exit();
  }

  // check that the doc is the correct mimetype
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mtype = finfo_file($finfo, $_FILES['usageFile']['tmp_name']);
  finfo_close($finfo);
  if ($mtype != 'text/plain') {
    header( 'Location: import.php?error=1' ) ;
    exit();
  }

  // store the file
  // TODO: In the following code, uploading the file repeatedly will overwrite the archive/ file...until the next day.
  // This is slightly odd because the file is saved but never imported
  $targetPath = BASE_DIR . "counterstore/" . $pathInfo['filename'] . $pathInfo['extension'];

  if(move_uploaded_file($_FILES['usageFile']['tmp_name'], $targetPath)) {
    $page['status'][] = _("The file "). $pathInfo['basename'] ._(" has been uploaded successfully.")."<br />"._("Please confirm the following data:")."<br />";
  } else{
    header( 'Location: import.php?error=2' ) ;
  }

	// file upload was OK, now we can read the file to output for confirmation

  // read this file
  $file_handle = $util->utf8_fopen_read($targetPath, false);

  // get first line of file
  $firstLine = stream_get_line($file_handle, 10000000, "\n");
  $firstArray = array_map(function($v) {
    return cleanValue($v);
  }, explode("\t",$firstLine));

  $missingColumns = [];
  foreach($columnsToCheck as $position => $check) {
    if ($check != $firstArray[$position]) {
      $missingColumns[] =  '<li>'
        . _("Looking for ") . $check . _(" in column ") . $position . _(" but found ") . $firstArray[$position]
        .'</li>';
    }
  }

  if (!empty($missingColumns)) {
    $page['errors'][] = _('Error with Format') . ': ' ._("Report format is set to ") . $layout->name .
      _(" but does not match the column names listed in layouts.ini for this format") . '<br>'
      . implode('<br>', $missingColumns)
      . '<br><br>' . _("Expecting columns: ") . implode(', ', $columnsToCheck) . '<br><br>' . _("Found columns: ")
      . implode(',', $firstArray) . '<br><br>'
      . _("If problems persist you can copy an existing header that works into this file.");
  }



  $page['title'] = _('Upload Process Confirmation');
  $page['reportName'] = $layout->name;
  $page['formValues']['file'] = $targetPath;
  $page['formValues']['layoutID'] = $layoutID;

  if (isset($_POST['overrideInd'])){
    $page['warnings'][] = _("File is flagged to override verifications of previous month data.  If this is incorrect use 'Cancel' to fix.");
    $page['formValues']['overrideInd'] = 1;
  }else{
    $page['formValues']['overrideInd'] = 0;
  }
}

// for the header
$pageTitle = $page['title'];


include 'templates/header.php';

?>

<script language="javascript">
	function updateSubmit(){
		document.confirmForm.submitForm.disabled=true;
		document.confirmForm.submitForm.value=_("Processing Contents...");
		document.confirmForm.submit();
	}
</script>

<main id="main-content">
  <article>
    <h2><?php echo $page['title']; ?></h2>
			
    
      <!-- ERRORS -->
      <?php if(!empty($page['errors'])): ?>
        <ul class="error">
        <?php foreach($page['errors'] as $text): ?>
          <li><?php echo $text; ?></li>
        <?php endforeach; ?>
        </ul>
        <?php exit(); ?>
      <?php endif; ?>

      <!-- STATUSES -->
      <?php if(!empty($page['status'])): ?>
        <ul class="msg">
        <?php foreach($page['status'] as $text): ?>
          <li><?php echo $text; ?></li>
        <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <!-- WARNINGS -->
      <?php if(!empty($page['warnings'])): ?>
        <ul class="warning">
        <?php foreach($page['warnings'] as $text): ?>
          <li><?php echo $text; ?></li>
        <?php endforeach; ?>
        </ul>
      <?php endif; ?>


      <!-- REPORT NAME -->
      <h3><?php echo _('Report Format'); ?>: <?php echo $page['reportName']; ?></h3>
        <?php if(!$fromSushi): ?>
          <p><?php echo _('If this is incorrect, please use \'Cancel\' to go back and fix the headers of the file.'); ?></p>
        <?php endif; ?>
      
      <p class="actions">
        <input type="submit" name="submitForm" id="submitFormTop" value="<?php echo _('Confirm');?>" onclick="updateSubmit();" class="submit-button primary" />
        <input type="button" value="<?php echo _('Cancel');?>" onClick="history.back();" class='cancel-button secondary'>
      </p>
      <hr>
      <table class="table-border">


			<?php

        $headerSet = FALSE;
        // If this is not a sushi report, need to render headers
        $i = 0;
        if (!$fromSushi) {
          echo '<thead><tr><th>' . implode('</th><th>', $firstArray) . '</th></tr></thead>';
          $headerSet = TRUE;
          $i++;
        }

        echo '<tbody>';
        //This will call on a lineLimit setting and set everything appropriately.
        $limitLines = ($config->settings->lineLimit == "Y"); //The lineLimit setting exists and is set to "Y"
        $lineLimit = ($limitLines) ? intval($config->settings->lineLimitAmount) : FALSE; //Get an integer value for the amount.
        $plural = ($lineLimit > 1) ? "s" : "";
        $lineLimitExists = ($lineLimit > 0); //The line limit exists and is greater than 0 (strings return 0)
        $checkLimit = ($limitLines && $lineLimitExists); //There is a valid line limit and limit lines is set to Y
        
        while (!feof($file_handle)) {
          //Check if you need to check for a line limit and, if that limit is set and surpassed, stop producing the list.
          if($checkLimit && $i>$lineLimit){break;}
          //get each line out of the file handler
          $line = stream_get_line($file_handle, 10000000, "\n");
          //set delimiter
          $del = "\t";
          $lineArray = explode($del,$line);

          // If this is not a sushi report skip the first line if it begins with "Total"
          if (!$fromSushi && strtolower(substr($lineArray[0], 0, 5)) == 'total') {
            continue;
          }

          echo '<tr>';
          foreach($lineArray as $value){
            //Clean some of the data
            $display = cleanValue($value);
            if ($headerSet == FALSE) {
              echo '<th>' . strtoupper($display) .'</th>';
              $headerSet = TRUE;
            } else {
              echo "<td>$display</td>";
            }
          }
          echo '</tr>';
          $i++;
        }
        fclose($file_handle);
			?>
        </tbody>
        <?php if($checkLimit){
          echo "<tfoot><tr>Row limit set to {$lineLimit} Row{$plural}. Further rows not presently shown.</tr></tfoot>";
        } ?>
      </table>

      <form id="confirmForm" name="confirmForm" enctype="multipart/form-data" method="post" action="uploadComplete.php">
        <!-- JR1 override warning -->
        <?php if(!$fromSushi && in_array($layout->layoutCode, array('JR1_R4','JR1a_R4'))): ?>
          <div class="msg">
            <p><?php echo _('Reporting period totals in COUNTER Release 4 JR1 reports refer to the reporting period only, which may be shorter than the current calendar year to date. By default, reporting period totals are not imported from JR1 reports in TSV format. Select the following option if you want to overwrite the reporting period totals with the data in this report for the titles specified.'); ?></p>
            <p>
              <label for="storeJR1Totals">
                <input type="checkbox" id="storeJR1Totals" name="storeJR1Totals" value="Y">
                <?php echo _('Update reporting period totals'); ?>
              </label>
            </p>
          </div>
        <?php endif; ?>
        <?php foreach($page['formValues'] as $key => $value): ?>
				  <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
        <?php endforeach; ?>
        <hr>
        <p class="actions">
          <input type="submit" name="submitForm" id="submitFormBottom" value="<?php echo _('Confirm');?>" onclick="updateSubmit();" class="submit-button primary" />
          <input type="button" value="<?php echo _('Cancel');?>" onClick="history.back();" class='cancel-button secondary'>
        </p>
			</form>
</article>
</main>
<?php include 'templates/footer.php'; ?>
</body>
</html>