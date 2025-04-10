<?php
function searchForShortName($shortName, $array)
{
  foreach ($array as $key => $val) {
    if (strtolower($val['shortName']) == strtolower($shortName)) {
      return $key;
      break;
    }
  }
  return null;
}

include_once 'directory.php';
$pageTitle = _('ONIX-PL Import');
include 'templates/header.php';
?>
<main id="main-content">
	<article>
<div id="onixImportPage"><h2><?php echo _("ONIX-PL Import");?></h2>
<?php
	if (isset($_POST['submit']))
	{
		$expressionTypeInserted = 0;
		$qualifierInserted = 0;

    $uploaddir = 'attachments/';
    $uploadfile = $uploaddir . basename($_FILES['uploadFile']['name']);
    if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadfile)) {
      print '<p>' . _("The file has been successfully uploaded.") . '</p>';
      // Let's analyze this file
      if (($xml = simplexml_load_file($uploadfile)) !== FALSE) {
        //get all expression types
        $expressionTypeArray = array();
        $expressionTypeObj = new ExpressionType();
        $expressionTypeArray = $expressionTypeObj->allAsArray();

        $textArray = array();
        $licenseAgreement = "";
        foreach ($xml->LicenseDocumentText->TextElement as $licenseText) {
          $licenseAgreement .= "<p>" . preg_replace('~\R~u', "<br/>\n", (string)$licenseText->Text) . "</p>\n\n";
          if ((string)$licenseText['id'] !== "") {
            $textArray[(string)$licenseText['id']] = "<p>" . preg_replace('~\R~u', "<br/>", (string)$licenseText->Text) . "</p>";
          }
        }

        //Save License Agreement to documents directory, create license, attachment, and attachmentFile records and get IDs
        $filename = trim($xml->LicenseDetail->Description);
        print $xml->LicenseDetail->Description;
        $licenseFile = fopen("documents/" . $filename . ".html", "wb") or die (_("Unable to create file for license."));
        fwrite($licenseFile, "<html>\n<head>\n<meta charset='utf-8'/>\n</head>\n</body>");
        fwrite($licenseFile, $licenseAgreement);
        fwrite($licenseFile, "</body>\n</html>");
        fclose($licenseFile);
        $licenseObj = new License();
        $licenseObj->shortName = $filename;
        $licenseObj->setOrganization($_POST['organizationID'], $_POST['organizationName']);
        $licenseObj->save();
        $licenseID = $licenseObj->primaryKey;
        $documentObj = new Document();
        $documentObj->shortName = $filename;
        $documentObj->documentTypeID = 3;
        $documentObj->licenseID = $licenseID;
        $documentObj->documentURL = $filename . ".html";
        $documentObj->save();
        $documentID = $documentObj->primaryKey;

        if ($_POST['usageTerms'] === "on") {
          if (isset($xml->UsageTerms->Usage)) {
            foreach ($xml->UsageTerms->Usage as $usage) {
              //get the expressionTypeID -- create expressionType if necessary
              $expression = preg_replace('/^onixPL\:/s', '', $usage->UsageType);
              $index = searchForShortName($expression, $expressionTypeArray);
              if ($index !== null) {
                $expressionTypeID = $expressionTypeArray[$index]['expressionTypeID'];
                $expressionTypeObj = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));
              } else {
                $expressionTypeObj = new ExpressionType();
                $expressionTypeObj->shortName = $expression;
                $expressionTypeObj->noteType = "Internal";
                $expressionTypeObj->save();
                $expressionTypeID = $expressionTypeObj->primaryKey;
                $expressionTypeArray = $expressionTypeObj->allAsArray();
              }
              $expressionTypeInserted++;
              //get the qualifierID -- create qualifier if necessary
              $expressionQualifiers = $expressionTypeObj->getQualifiers();
              $qualifier = preg_replace('/^onixPL\:/s', '', $usage->UsageStatus);
              $qualifierID = -1;
              foreach ($expressionQualifiers as $expressionQualifier) {
                if (strtolower($expressionQualifier->shortName) == strtolower($qualifier)) {
                  $qualifierID = $expressionQualifier->qualifierID;
                  break;
                }
              }
              if ($qualifierID === -1) {
                $qualifierObj = new Qualifier();
                $qualifierObj->expressionTypeID = $expressionTypeID;
                $qualifierObj->shortName = $qualifier;
                $qualifierObj->save();
                $qualifierID = $qualifierObj->primaryKey;
              }
              $qualifierInserted++;
              $expressionObj = new Expression();
              $expressionObj->documentID = $documentID;
              $expressionObj->expressionTypeID = $expressionTypeID;
              $expressionText = "";
              foreach ($usage->LicenseTextLink as $licenseTextLink) {
                $expressionText .= $textArray[(string)$licenseTextLink["href"]] . "\n\n";
              }
              $expressionObj->documentText = $expressionText;
              $expressionObj->lastUpdateDate = "0000-00-00 00:00:00";
              $expressionObj->productionUseInd = "0";
              $expressionObj->save();
              $expressionID = $expressionObj->primaryKey;
              $expressionQualifierProfileObj = new ExpressionQualifierProfile();
              $expressionQualifierProfileObj->expressionID = $expressionID;
              $expressionQualifierProfileObj->qualifierID = $qualifierID;
              $expressionQualifierProfileObj->save();
            }
          }
        }
        if ($_POST['supplyTerms'] === "on") {
          if (isset($xml->SupplyTerms->SupplyTerm)) {
            foreach ($xml->SupplyTerms->SupplyTerm as $supplyTerm) {
              //get the expressionTypeID -- create expressionType if necessary
              $expression = preg_replace('/^onixPL\:/s', '', $supplyTerm->SupplyTermType);
              $index = searchForShortName($expression, $expressionTypeArray);
              if ($index !== null) {
                $expressionTypeID = $expressionTypeArray[$index]['expressionTypeID'];
                $expressionTypeObj = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));
              } else {
                $expressionTypeObj = new ExpressionType();
                $expressionTypeObj->shortName = $expression;
                $expressionTypeObj->noteType = "Internal";
                $expressionTypeObj->save();
                $expressionTypeID = $expressionTypeObj->primaryKey;
                $expressionTypeArray = $expressionTypeObj->allAsArray();
              }
              $expressionTypeInserted++;

              $expressionObj = new Expression();
              $expressionObj->documentID = $documentID;
              $expressionObj->expressionTypeID = $expressionTypeID;
              $expressionText = "";
              foreach ($supplyTerm->LicenseTextLink as $licenseTextLink) {
                $expressionText .= $textArray[(string)$licenseTextLink["href"]] . "\n\n";
              }
              $expressionObj->documentText = $expressionText;
              $expressionObj->lastUpdateDate = "0000-00-00 00:00:00";
              $expressionObj->productionUseInd = "0";
              $expressionObj->save();
              $expressionID = $expressionObj->primaryKey;
            }
          }
        }
        if ($_POST['continuingAccessTerms'] === "on") {
          if (isset($xml->ContinuingAccessTerms->ContinuingAccessTerm)) {
            foreach ($xml->ContinuingAccessTerms->ContinuingAccessTerm as $continuingAccessTerm) {
              //get the expressionTypeID -- create expressionType if necessary
              $expression = preg_replace('/^onixPL\:/s', '', $continuingAccessTerm->ContinuingAccessTermType);
              $index = searchForShortName($expression, $expressionTypeArray);
              if ($index !== null) {
                $expressionTypeID = $expressionTypeArray[$index]['expressionTypeID'];
                $expressionTypeObj = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));
              } else {
                $expressionTypeObj = new ExpressionType();
                $expressionTypeObj->shortName = $expression;
                $expressionTypeObj->noteType = "Internal";
                $expressionTypeObj->save();
                $expressionTypeID = $expressionTypeObj->primaryKey;
                $expressionTypeArray = $expressionTypeObj->allAsArray();
              }
              $expressionTypeInserted++;

              $expressionObj = new Expression();
              $expressionObj->documentID = $documentID;
              $expressionObj->expressionTypeID = $expressionTypeID;
              $expressionText = "";
              foreach ($continuingAccessTerm->LicenseTextLink as $licenseTextLink) {
                $expressionText .= $textArray[(string)$licenseTextLink["href"]] . "\n\n";
              }
              $expressionObj->documentText = $expressionText;
              $expressionObj->lastUpdateDate = "0000-00-00 00:00:00";
              $expressionObj->productionUseInd = "0";
              $expressionObj->save();
              $expressionID = $expressionObj->primaryKey;
            }
          }
        }
        if ($_POST['paymentTerms'] === "on") {
          if (isset($xml->PaymentTerms->PaymentTerm)) {
            foreach ($xml->PaymentTerms->PaymentTerm as $paymentTerm) {
              //get the expressionTypeID -- create expressionType if necessary
              $expression = preg_replace('/^onixPL\:/s', '', $paymentTerm->PaymentTermType);
              $index = searchForShortName($expression, $expressionTypeArray);
              if ($index !== null) {
                $expressionTypeID = $expressionTypeArray[$index]['expressionTypeID'];
                $expressionTypeObj = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));
              } else {
                $expressionTypeObj = new ExpressionType();
                $expressionTypeObj->shortName = $expression;
                $expressionTypeObj->noteType = "Internal";
                $expressionTypeObj->save();
                $expressionTypeID = $expressionTypeObj->primaryKey;
                $expressionTypeArray = $expressionTypeObj->allAsArray();
              }
              $expressionTypeInserted++;

              $expressionObj = new Expression();
              $expressionObj->documentID = $documentID;
              $expressionObj->expressionTypeID = $expressionTypeID;
              $expressionText = "";
              foreach ($paymentTerm->LicenseTextLink as $licenseTextLink) {
                $expressionText .= $textArray[(string)$licenseTextLink["href"]] . "\n\n";
              }
              $expressionObj->documentText = $expressionText;
              $expressionObj->lastUpdateDate = "0000-00-00 00:00:00";
              $expressionObj->productionUseInd = "0";
              $expressionObj->save();
              $expressionID = $expressionObj->primaryKey;
            }
          }
        }
        if ($_POST['generalTerms'] === "on") {
          if (isset($xml->GeneralTerms->GeneralTerm)) {
            foreach ($xml->GeneralTerms->GeneralTerm as $generalTerm) {
              //get the expressionTypeID -- create expressionType if necessary
              $expression = preg_replace('/^onixPL\:/s', '', $generalTerm->GeneralTermType);
              $index = searchForShortName($expression, $expressionTypeArray);
              if ($index !== null) {
                $expressionTypeID = $expressionTypeArray[$index]['expressionTypeID'];
                $expressionTypeObj = new ExpressionType(new NamedArguments(array('primaryKey' => $expressionTypeID)));
              } else {
                $expressionTypeObj = new ExpressionType();
                $expressionTypeObj->shortName = $expression;
                $expressionTypeObj->noteType = "Internal";
                $expressionTypeObj->save();
                $expressionTypeID = $expressionTypeObj->primaryKey;
                $expressionTypeArray = $expressionTypeObj->allAsArray();
              }
              $expressionTypeInserted++;

						$expressionObj = new Expression();
						$expressionObj->documentID = $documentID;
						$expressionObj->expressionTypeID = $expressionTypeID;
						$expressionText = "";
						foreach($generalTerm->LicenseTextLink as $licenseTextLink)
						{
							$expressionText .= $textArray[(string)$licenseTextLink["href"]] . "\n\n";
						}
						$expressionObj->documentText = $expressionText;
						$expressionObj->lastUpdateDate = "0000-00-00 00:00:00";
						$expressionObj->productionUseInd = "0";
						$expressionObj->save();
						$expressionID = $expressionObj->primaryKey;
					}
				}
				echo "<p>" . $expressionTypeInserted . _(" Expression Type(s) Created") . "</p>";
				echo "<p>" . $qualifierInserted . _(" Qualifiers Created") . "</p>";
			}
			else
			{
				$error = _("Cannot create XML object");
			}
		}
		else
		{
			$error = _("Unable to upload the file");
		}
		if ($error)
		{
			print "<p class='error'>"._("Error: ").$error.".</p>";
		}
		else
		{
		}
	}
	elseif (isset($_POST['matchsubmit']))
	{
	}
	else
	{
?>
		<form enctype="multipart/form-data" action="onix_import.php" method="post" id="importForm">
				<h3><?php echo _("File selection");?></h3>
				<label for="uploadFile"><?php echo _("XML File");?></label>
				<input type="file" name="uploadFile" id="uploadFile" />

        <fieldset>
				<legend><?php echo _("Import options");?><legend>
				<ul id='importOptions' class="unstyled">
					<li><input id='usageTerms' name='usageTerms' type='checkbox'><label for='usageTerms'><?php echo _("Usage Terms");?></label></li>
					<li><input id='supplyTerms' name='supplyTerms' type='checkbox'><label for='supplyTerms'><?php echo _("Supply Terms");?></label></li>
					<li><input id='continuingAccessTerms' name='continuingAccessTerms' type='checkbox'><label for='continuingAccessTerms'><?php echo _("Continuing Access Terms");?></label></li>
					<li><input id='paymentTerms' name='paymentTerms' type='checkbox'><label for='paymentTerms'><?php echo _("Payment Terms");?></label></li>
					<li><input id='generalTerms' name='generalTerms' type='checkbox'><label for='generalTerms'><?php echo _("General Terms");?></label></li>
					<li><label for="organizationName" class="formText">
						<?php echo _("Publisher / Provider:");?>
					</label>
					<p id='span_error_organizationName' class='error'></p>
					<?php
					  if(!isset($organizationName)){
						  $organizationName = "";
					  }
					?>					
					<input type='text' id='organizationName' name='organizationName' value="<?php echo $organizationName; ?>" aria-describedby="span_error_organizationName" />
					<input type='hidden' id='licenseOrganizationID' name='licenseOrganizationID' value='<?php echo $license->organizationID; ?>'>
					<p id='span_error_organizationNameResult' class='warning'></p>
					</li>
				</ul>
      </fieldset>
				<p>
					<input type="submit" name="submit" value="<?php echo _("Upload");?>" class="submit-button primary" />
				</p>	
			</form>
		</div>
	</article>
</main>
<?php
	}
	include 'templates/footer.php';
?>
<script src="js/onix_import.js"></script>
</body>
</html>
