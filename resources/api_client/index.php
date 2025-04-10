<?php
require 'vendor/autoload.php';
$server = "http://coral.local/resources/api/";
$user = $_SERVER['REMOTE_USER'] ? $_SERVER['REMOTE_USER'] : 'API';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
    <link rel="SHORTCUT ICON" href="../images/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
<!--<link rel="stylesheet" href="pure-min.css">-->
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-old-ie-min.css">
    <![endif]-->
    <!--[if gt IE 8]><!-->
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css">
    <!--<![endif]-->

    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="../css/thickbox.css" type="text/css" media="screen" />

    <link rel="stylesheet" href="local.css">
</head>
<body>
    <h1>Simple Resources module API client</h1>
    <h2>Propose a resource</h2>
<?php
$headers = array("Accept" => "application/json");
$body = array();
if (isset($_POST['submitProposeResourceForm'])) {
    $fieldNames = array("user", "titleText", "descriptionText", "isbn", "providerText", "resourceURL", "resourceAltURL", "noteText", "resourceTypeID", "resourceFormatID", "acquisitionTypeID", "administeringSiteID", "homeLocationNote", "licenseRequired", "existingLicense", "publicationYear", "edition", "holdLocation", "patronHold", "CMRanking", "subjectCoverage", "audience", "frequency", "access", "contributingFactors", "ripCode", "fund", "cost", "neededByDate");

    foreach ($fieldNames as $fieldName) {
        if (isset($_POST[$fieldName])) {
			$body[$fieldName] = $_POST[$fieldName];
		}
    }

    /*
     * If you would like to add any additional inputs to the form and have their
     * values added as description text on the resource, uncomment the following lines
     * and add each new input name to the $descriptionFields array.
     */
//    $descriptionFields = array("author");
//
//    foreach ($descriptionFields as $descField){
//        addToDescriptionText($body,$descField);
//    }

	if(!empty($body['neededByDate'])){
		$body['neededByDate'] = 'Needed by '.date("m/d/Y", strtotime($body['neededByDate']));
	} else if(isset($body['neededByDate'])){
		unset($body['neededByDate']);
	}

    $response = Unirest\Request::post($server . "proposeResource/", $headers, $body);
    if (isset($response->body->resourceID)) {
        echo "<p>The resource was correctly submitted (resource " . $response->body->resourceID . ")</p>";
        ?>
        <ul>
            <li>Title: <?php echo $_POST['titleText']; ?></li>
            <li>Description: <?php echo $_POST['descriptionText']; ?></li>
<!--            <li>Author: --><?php //echo $_POST['author']; ?><!--</li>-->
            <li>ISBN/ISSN: <?php echo $_POST['isbn']; ?></li>
            <li>Provider: <?php echo $_POST['providerText']; ?></li>
            <li>URL: <?php echo $_POST['resourceURL']; ?></li>
            <li>URL Alt: <?php echo $_POST['resourceAltURL']; ?></li>
            <li>Publication year or subscription start date: <?php echo $_POST['publicationYear']; ?></li>
            <li>Edition: <?php echo $_POST['edition']; ?></li>
            <li>Hold location: <?php echo $_POST['holdLocation']; ?></li>
            <li>Patron hold (patrons' name, email): <?php echo $_POST['patronHold']; ?></li>
            <li>Rip code (serials): <?php echo $_POST['ripCode']; ?></li>
			<?php
            if (!empty($_POST['fund'])) {
                $fundResponse = Unirest\Request::post($server . "getFund/" . $_POST['fund']);
                echo "<li>Fund shortName: " . $fundResponse->body . "</li>";
			}
			if (!empty($_POST['cost'])){
                echo "<li>Cost: $" . cost_to_integer($_POST['cost']) . "</li>";
			}

            $formatResponse = Unirest\Request::post($server . "getResourceFormat/" . $_POST['resourceFormatID']); ?>
            <li>Format: <?php echo $formatResponse->body; ?></li>

            <?php
            if (!empty($_POST['acquisitionTypeID'])) {
                $ATResponse = Unirest\Request::post($server . "getAcquisitionType/" . $_POST['acquisitionTypeID']); ?>
                <li>Acquisition Type: <?php echo $ATResponse->body; ?></li>
            <?php } ?>

            <?php $RTResponse = Unirest\Request::post($server . "getResourceType/" . $_POST['resourceTypeID']); ?>
            <li>Resource Type: <?php echo $RTResponse->body; ?></li>

            <?php
                if (isset($body['administeringSiteID'])) {
                    echo "<li>Library: ";
                    foreach($body['administeringSiteID'] as $as) {
                        $ASResponse = Unirest\Request::post($server . "getAdministeringSite/" . $as);
                        $libraries[] = $ASResponse->body;
                    }
                    echo implode(' / ', $libraries) . "</li>";
                }
            ?>
            <li>License required?: <?php echo $_POST['licenseRequired']; ?></li>
            <li>Existing license?: <?php echo $_POST['existingLicense']; ?></li>
            <li>Home Location: <?php echo $_POST['homeLocationNote']; ?></li>
			<?php
			if (isset($_POST['neededByDate']) && $_POST['neededByDate'] != "") {
				echo '<li>Urgent: Needed by: ' . $body['neededByDate'] . '.</li>';
			} ?>
            <li>Notes: <?php echo $_POST['noteText']; ?></li>
            <li>CM ranking: <?php echo $_POST['CMRanking']; ?></li>
            <li>Subject coverage: <?php echo $_POST['subjectCoverage']; ?></li>
            <li>Audience: <?php echo $_POST['audience']; ?></li>
            <li>Frequency and language: <?php echo $_POST['frequency']; ?></li>
            <li>Access via indexes: <?php echo $_POST['access']; ?></li>
            <li>Contributing factors: <?php echo $_POST['contributingFactors']; ?></li>
        </ul>
        <?php
    } else {
        echo "<p>The resource could not be submitted. (error: " . $response->body->error . ")</p>";
    }
    echo '<a href="index.php">Submit another resource</a>';
} else {
  // Checking if the API is up
  $response = Unirest\Request::get($server . "version/", $headers, $body);
  if ($response->code != 200) {
      if ($response->code == 403) {
        echo "<p>You are not authorized to use this service.</p>";
        echo $response->body;
      }
      elseif ($response->code == 500) {
        echo "<p>This service encountered an error.</p>";
      }
      else{
          echo "<p>There was an error.</p>";
      }
  } else {
?>
    <form name="proposeResourceForm" action="index.php" method="POST" class="pure-form pure-form-stacked">
        <h2><?php echo _('Product'); ?></h2>
            <div class="pure-g">
                <div class="pure-u-1">
                    <label for="titleText"><?php echo _('Title: *'); ?></label>
                    <input id="titleText" name="titleText" type="text" class="pure-u-1 pure-u-md-1-2" oninvalid="setCustomValidity('Please submit a title for the resource.')" required/>
                </div>
                <div class="pure-u-1">
                    <label for="descriptionText"><?php echo _('Description:'); ?> </label>
                    <textarea name="descriptionText" rows="5" class="pure-u-1 pure-u-md-1-2"></textarea>
                </div>
<!--                <div class="pure-u-1">-->
<!--                    <label for="author">Author: </label>-->
<!--                    <input name="author" type="text" class="pure-u-1 pure-u-md-1-3"/>-->
<!--                </div>-->
                <div class="pure-u-1">
                    <label for="isbn"><?php echo _('ISBN/ISSN:'); ?> </label>
                    <input name="isbn" type="text" class="pure-u-1 pure-u-md-1-3"/>
                </div>
                <div class="pure-u-1">
                    <label for="providerText"><?php echo _('Provider:'); ?> </label>
                    <input name="providerText" type="text" class="pure-u-1 pure-u-md-1-3"/>
                </div>
                <div class="pure-u-1">
                    <label for="resourceURL"><?php echo _('URL:'); ?> </label>
                    <input name="resourceURL" type="url" class="pure-u-1 pure-u-md-1-3"/>
                </div>
                <div class="pure-u-1">
                    <label for="resourceAltURL"><?php echo _('URL Alt:'); ?> </label>
                    <input name="resourceAltURL" type="url" class="pure-u-1 pure-u-md-1-3"/>
                </div>
                <div class="pure-u-1">
                    <label for="publicationYear"><?php echo _('Publication year or subscription start date:'); ?> </label>
                    <input name="publicationYear" type="text" class="pure-u-1 pure-u-md-1-5"/>
                </div>
                <div class="pure-u-1">
                    <label for="edition"><?php echo _('Edition:'); ?> </label>
                    <input name="edition" type="text" class="pure-u-1 pure-u-md-1-3"/>
                </div>
                <div class="pure-u-1">
                    <label for="holdLocation"><?php echo _('Hold location (patron pickup library for item held)'); ?></label>
                    <input name="holdLocation" type="text" class="pure-u-1 pure-u-md-1-3"/>
                </div>
                <div class="pure-u-1">
                    <label for="patronHold"><?php echo _("Patron hold (patrons' name, email)"); ?></label>
                    <input name="patronHold" type="text" class="pure-u-1 pure-u-md-1-3"/>
                </div>
                <div class="pure-u-1">
                    <label for="ripCode"><?php echo _('RIP code (serials)'); ?></label>
                    <input name="ripCode" type="text" class="pure-u-1 pure-u-md-1-4"/>
                </div>
                <div class="pure-u-1">
                    <label for="fund"><?php echo _('Fund code'); ?></label>
					<?php getFundCodesAsDropdown($server, $headers, $body); ?>
                </div>
                <div class="pure-u-1">
                    <label id="costLabel" for="cost"><?php echo _('Cost'); ?></label>
                    <input id="cost" name="cost" type="number" min="0" step="0.01" class="pure-u-1 pure-u-md-1-6"/>
                </div>
            </div>

            <div class="pure-g">
                <div id='urgentDiv' class="pure-u-1">
                    <label for="urgent" ><?php echo _('Urgent?'); ?></label>
                    <input id='urgent' type="checkbox" >
                    <label class="urgent" for="neededByDate" ><?php echo _('Needed By:'); ?></label>
                    <input class="urgent" type="date" id="neededByDate" name="neededByDate" >
                </div>
                <div class="pure-u-1">
                    <label for="resourceFormatID"><?php echo _('Format'); ?></label>
					<?php getResourceFormatsAsDropdown($server, $headers, $body); ?>
                </div>

                <div class="pure-u-1">
                    <p><?php echo _('Acquisition Type:'); ?></p>
					<?php getAcquisitionTypesAsRadio($server, $headers, $body); ?>
                </div>

                <div class="pure-u-1">
                    <label for="resourceTypeID"><?php echo _('Resource Type'); ?></label>
					<?php getResourceTypesAsDropdown($server, $headers, $body); ?>
                </div>

                <div class="pure-u-1">
                    <label for="administeringSiteID"><?php echo _('Library'); ?></label>
					<?php getAdministeringSitesAsDropdown($server, $headers, $body); ?>
                </div>

                <div class="pure-u-1 checkbox">
                    <label for="licenseRequired"><?php echo _('License required?'); ?></label>
                    <input type="radio" name="licenseRequired" value="Yes"/> <?php echo _('Yes'); ?> 
                    <input type="radio" name="licenseRequired" value="No"/> <?php echo _('No'); ?>
                    <input type="radio" name="licenseRequired" value="Don't know" checked="checked"/> <?php echo _("Don't know"); ?>
                </div>

                <div class="pure-u-1">
                    <label for="existingLicense"><?php echo _('Existing license?'); ?></label>
                    <input type="radio" name="existingLicense" value="Yes"/> <?php echo _('Yes'); ?>
                    <input type="radio" name="existingLicense" value="No"/> <?php echo _('No'); ?>
                    <input type="radio" name="existingLicense" value="Don't know" checked="checked"/> <?php echo _("Don't know"); ?>
                </div>

                <div class="pure-u-1">
                    <label for="homeLocationNote"><?php echo _('Home Location'); ?></label>
                    <select name="homeLocationNote">
                        <option value="Stacks"><?php echo _('Stacks'); ?></option>
                        <option value="References"><?php echo _('References'); ?></option>
                        <option value="Reserves"><?php echo _('Reserves'); ?></option>
                        <option value="Online"><?php echo _('Online'); ?></option>
                        <option value="Teach DVD"><?php echo _('Teach DVD'); ?></option>
                        <option value="Circulating DVD"><?php echo _('Circulating DVD'); ?></option>
                        <option value="Media (Branch)"><?php echo _('Media (Branch)'); ?></option>
                        <option value="Other"><?php echo _('Other (please specify it in Notes)'); ?></option>
                    </select>
                </div>
            </div>

            <div class="pure-g">
                <div class="pure-u-1">
                    <p><label for="noteText"><?php echo _('Notes'); ?></label></p>
                    <p class="form-text"><?php echo _('Include any additional information'); ?></p>
                    <textarea name="noteText" rows="5" class="pure-u-1 pure-u-md-1-2"></textarea><br/>
                </div>
            </div>


        <h2><?php echo _("The following fields are for collection managers' decision use."); ?></h2>
        
            <label for="CMRanking"><?php echo _('CM ranking'); ?></label>
            <select name="CMRanking">
                <option value="1"><?php echo _('High'); ?></option>
                <option value="2"><?php echo _('Medium'); ?></option>
                <option value="3"><?php echo _('Low'); ?></option>
            </select>

            <div class="pure-u-1">
                <label for="subjectCoverage"><?php echo _('Subject coverage:'); ?> </label><textarea name="subjectCoverage" id="subjectCoverage"></textarea><br />
            </div>

            <div class="pure-u-1">
                <label for="audience"><?php echo _('Audience:'); ?> </label><textarea name="audience"></textarea><br />
            </div>

            <div class="pure-u-1">
                <label for="frequency"><?php echo _('Frequency and language:'); ?> </label><textarea name="frequency"></textarea><br />
            </div>

            <div class="pure-u-1">
                <label for="access"><?php echo _('Access via indexes:'); ?> </label><textarea name="access"></textarea><br />
            </div>

            <div class="pure-u-1">
                <label for="contributingFactors"><?php echo _('Contributing factors:'); ?> </label><textarea name="contributingFactors"></textarea><br />
            </div>


        <input type="hidden" name="user" value="<?php echo $user; ?>">

        <button type="submit" class="primary" name="submitProposeResourceForm">
        <?php echo _('Submit'); ?>
        </button>
    </form>

<?php
}
}

function getResourceTypesAsDropdown($server, $headers, $body) {
    $response = Unirest\Request::post($server . "getResourceTypes/", $headers, $body);
    echo '<select name="resourceTypeID" class="pure-u-1 pure-u-md-1-4">';
    foreach ($response->body as $resourceType) {
        echo ' <option value="' . $resourceType->resourceTypeID  . '">' . $resourceType->shortName . "</option>";
    }
    echo '</select>';
}

function getAcquisitionTypesAsRadio($server, $headers, $body) {
    $response = Unirest\Request::post($server . "getAcquisitionTypes/", $headers, $body);
    foreach ($response->body as $AcquisitionType) {
        $default = (isset($AcquisitionType->shortName) && strtolower($AcquisitionType->shortName) == "approved")? ' checked':'' ;  //Replace 'approved' with your default
        if (strtolower($AcquisitionType->shortName) == "approved" || strtolower($AcquisitionType->shortName) == "needs approval") {
            echo ' <label for="acquisitionType'.$AcquisitionType->acquisitionTypeID.'" class="pure-radio"> ';
            echo ' <input id="acquisitionType'.$AcquisitionType->acquisitionTypeID.'" type="radio" name="acquisitionTypeID" value="' . $AcquisitionType->acquisitionTypeID . '" '.$default.'> ';
            echo $AcquisitionType->shortName . '</label>';
        }
    }
}

function getResourceFormatsAsDropdown($server, $headers, $body) {
    $response = Unirest\Request::post($server . "getResourceFormats/", $headers, $body);
    echo '<select name="resourceFormatID">';
    foreach ($response->body as $resourceFormat) {
        echo ' <option value="' . $resourceFormat->resourceFormatID . '">' . $resourceFormat->shortName . "</option>\n";
    }
    echo '</select>';
}

function getAdministeringSitesAsDropdown($server, $headers, $body) {
    $response = Unirest\Request::post($server . "getAdministeringSites/", $headers, $body);
    echo '<select id="administeringSiteID" name="administeringSiteID[]" multiple="multiple">';
    foreach ($response->body as $administeringSite) {
        echo ' <option value="' . $administeringSite->administeringSiteID . '">' . $administeringSite->shortName . "</option>\n";
    }
    echo '</select>';
}

function getFundCodesAsDropdown($server, $headers, $body) {
	$response = Unirest\Request::post($server . "getFundCodes/", $headers, $body);
	echo "<select name='fund' id='fund'>\n";
	echo "  <option value=''>unknown</option>\n";
	foreach ($response->body as $fund) {
        if (!is_null($fund->fundCode) && !is_null($fund->shortName) && is_null($fund->archived)){
			echo ' <option value="' . $fund->fundCode . '">' . $fund->shortName ."</option>\n";
		}
	}
	echo '</select>';
}

function addToDescriptionText(&$body, $inputField){
    if(isset($_POST[$inputField]) && $_POST[$inputField]!=""){
        if(isset($body['descriptionText'])){
            if($body['descriptionText']!= ""){
                $body['descriptionText'].="\n";
            }
        }else{
            $body['descriptionText']="";
        }
        $body['descriptionText'] .= ucfirst($inputField) . ": " . $_POST[$inputField];
    }
}

function cost_to_integer($price) {

	$price = preg_replace("/[^0-9\.]/", "", $price);

	$decimal_place = strpos($price,".");

	if (strpos($price,".") > 0) {
		$cents = '.' . substr($price, $decimal_place+1, 2);
		$price = substr($price,0,$decimal_place);
	}else{
		$cents = '.00';
	}

	$price = preg_replace("/[^0-9]/", "", $price);

	if (is_numeric($price . $cents)){
		return ($price . $cents);
	}else{
		return false;
	}
}

?>
</body>
</html>
