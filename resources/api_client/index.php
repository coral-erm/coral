<?php
require 'vendor/autoload.php';
$server = "http://coral.local/resources/api/";
$user = $_SERVER['REMOTE_USER'] ? $_SERVER['REMOTE_USER'] : 'API';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
<link rel="stylesheet" href="pure-min.css">
</head>
<h1>Simple Resources module API client</h1>
<h2>Propose a resource</h2>
<?php
if ($_POST['submitProposeResourceForm']) {
    $fieldNames = array("user", "titleText", "descriptionText", "providerText", "resourceURL", "resourceAltURL", "noteText", "resourceTypeID", "resourceFormatID", "acquisitionTypeID", "administeringSiteID", "homeLocationNote", "licenseRequired", "existingLicense", "publicationYear", "edition", "holdLocation", "patronHold", "CMRanking", "subjectCoverage", "audience", "frequency", "access", "contributingFactors", "ripCode", "fund", "cost");
    $headers = array("Accept" => "application/json");
    $body = array();
    foreach ($fieldNames as $fieldName) {
        $body[$fieldName] = $_POST[$fieldName];
    }
    $response = Unirest\Request::post($server . "proposeResource/", $headers, $body);
    if ($response->body->resourceID) {
        echo "<p>The resource was correctly submitted (resource " . $response->body->resourceID . ")</p>";
        ?>
        <ul>
            <li>Title: <?php echo $_POST['titleText']; ?></li>
            <li>Description: <?php echo $_POST['descriptionText']; ?></li>
            <li>Provider: <?php echo $_POST['providerText']; ?></li>
            <li>URL: <?php echo $_POST['resourceURL']; ?></li>
            <li>URL Alt: <?php echo $_POST['resourceAltURL']; ?></li>
            <li>Publication year or subscription start date: <?php echo $_POST['publicationYear']; ?></li>
            <li>Edition: <?php echo $_POST['edition']; ?></li>
            <li>Hold location (patron pickup library for item held): <?php echo $_POST['holdLocation']; ?></li>
            <li>Patron hold (patrons' name, email): <?php echo $_POST['patronHold']; ?></li>
            <li>Rip code (serials): <?php echo $_POST['ripCode']; ?></li>
            <li>Fund code: <?php echo $_POST['fund']; ?></li>
            <li>Cost: <?php echo $_POST['cost']; ?></li>
            <?php $formatResponse = Unirest\Request::post($server . "getResourceFormat/" . $_POST['resourceFormatID']); ?>
            <li>Format: <?php echo $formatResponse->body; ?></li>

            <?php 
            if ($_POST['acquisitionTypeID']) {
                $ATResponse = Unirest\Request::post($server . "getAcquisitionType/" . $_POST['acquisitionTypeID']); ?>
                <li>Acquisition Type: <?php echo $ATResponse->body; ?></li>
            <?php } ?>

            <?php $RTResponse = Unirest\Request::post($server . "getResourceType/" . $_POST['resourceTypeID']); ?>
            <li>Resource Type: <?php echo $RTResponse->body; ?></li>

            <?php 
                if ($_POST['administeringSiteID']) {
                    echo "<li>Library: ";
                    foreach($_POST['administeringSiteID'] as $as) {
                        $ASResponse = Unirest\Request::post($server . "getAdministeringSite/" . $as);
                        $libraries[] = $ASResponse->body; 
                    }
                    echo implode(' / ', $libraries) . "</li>";
                }
            ?>
            <li>License required?: <?php echo $_POST['licenseRequired']; ?></li>
            <li>Existing license?: <?php echo $_POST['existingLicense']; ?></li>
            <li>Home Location: <?php echo $_POST['homeLocationNote']; ?></li>
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
      if ($response->code == 500) {
        echo "<p>This service encountered an error.</p>";
      }
  } else {
?>
<form name="proposeResourceForm" action="index.php" method="POST" class="pure-form pure-form-aligned" style="margin:50px">
<fieldset>
<legend>Product</legend>
<div class="pure-control-group">
<label for="titleText">Title: </label><input name="titleText" type="text" /><br />
</div>
<div class="pure-control-group">
<label for="descriptionText">Description: </label><textarea name="descriptionText"></textarea><br />
</div>
<div class="pure-control-group">
<label for="providerText">Provider: </label><input name="providerText" type="text" /><br />
</div>
<div class="pure-control-group">
<label for="resourceURL">URL: </label><input name="resourceURL" type="text" /><br />
</div>
<div class="pure-control-group">
<label for="resourceAltURL">URL Alt: </label><input name="resourceAltURL" type="text" /><br />
</div>
<div class="pure-control-group">
<label for="publicationYear">Publication year or subscription start date: </label><input name="publicationYear" type="text" /><br />
</div>
<div class="pure-control-group">
<label for="edition">Edition: </label><input name="edition" type="text" /><br />
</div>
<div class="pure-control-group">
<label for="holdLocation">Hold location (patron pickup library for item held)</label><input name="holdLocation" type="text" /><br />
</div>
<div class="pure-control-group">
<label for="patronHold">Patron hold (patrons' name, email)</label><input name="patronHold" type="text" /><br />
</div>
<div class="pure-control-group">
<label for="ripCode">RIP code (serials)</label><input name="ripCode" type="text" /><br />
</div>
<div class="pure-control-group">
<label for="fund">Fund code</label><input name="fund" type="text" /><br />
</div>
<div class="pure-control-group">
<label for="cost">Cost</label><input name="cost" type="text" /><br />
</div>
</fieldset>

<fieldset>
<legend>Format</legend>
<?php getResourceFormatsAsDropdown($server); ?>
</fieldset>

<fieldset>
<legend>Acquisition Type</legend>
<?php getAcquisitionTypesAsRadio($server); ?>
</fieldset>

<fieldset>
<legend>Resource Type</legend>
<?php getResourceTypesAsDropdown($server); ?>
</fieldset>

<fieldset>
<legend>Library</legend>
<?php getAdministeringSitesAsDropdown($server); ?>
</fieldset>

<fieldset>
<legend>License required?</legend>
<input type="radio" name="licenseRequired" value="Yes" />Yes
<input type="radio" name="licenseRequired" value="No" />No
<input type="radio" name="licenseRequired" value="Don't know" checked="checked" />Don't know
</fieldset>

<fieldset>
<legend>Existing license?</legend>
<input type="radio" name="existingLicense" value="Yes" />Yes
<input type="radio" name="existingLicense" value="No" />No
<input type="radio" name="existingLicense" value="Don't know" checked="checked" />Don't know
</fieldset>



<fieldset>
<legend>Home Location</legend>
<select name="homeLocationNote">
<option value="Stacks">Stacks</option>
<option value="References">References</option>
<option value="Reserves">Reserves</option>
<option value="Online">Online</option>
<option value="Teach DVD">Teach DVD</option>
<option value="Circulating DVD">Circulating DVD</option>
<option value="Media (Branch)">Media (Branch)</option>
<option value="Other">Other (please specify it in Notes)</option>
</select>
</fieldset>

<fieldset>
<legend>Notes</legend>
<label for="noteText">Include any additional information</label>
<textarea name="noteText"></textarea><br />
</fieldset>

<h2>The following fields are for collection managers' decision use.</h2>
<fieldset>
<legend>CM ranking</legend>
<select name="CMRanking">
<option value="1">High</option>
<option value="2">Medium</option>
<option value="3">Low</option>
</select>

<div class="pure-control-group">
<label for="subjectCoverage">Subject coverage: </label><textarea name="subjectCoverage"></textarea><br />
</div>

<div class="pure-control-group">
<label for="audience">Audience: </label><textarea name="audience"></textarea><br />
</div>

<div class="pure-control-group">
<label for="frequency">Frequency and language: </label><textarea name="frequency"></textarea><br />
</div>

<div class="pure-control-group">
<label for="access">Access via indexes: </label><textarea name="access"></textarea><br />
</div>

<div class="pure-control-group">
<label for="contributingFactors">Contributing factors: </label><textarea name="contributingFactors"></textarea><br />
</div>

</fieldset>

<input type="hidden" name="user" value="<?php echo $user; ?>">

<input type="submit" name="submitProposeResourceForm" />
</form>
<?php
}
}

function getResourceTypesAsDropdown($server) {
    $response = Unirest\Request::post($server . "getResourceTypes/", $headers, $body);
    echo '<select name="resourceTypeID">';
    foreach ($response->body as $resourceType) {
        echo ' <option value="' . $resourceType->resourceTypeID  . '">' . $resourceType->shortName . "</option>";
    }
    echo '</select>';
}

function getAcquisitionTypesAsRadio($server) {
    $response = Unirest\Request::post($server . "getAcquisitionTypes/", $headers, $body);
    foreach ($response->body as $resourceType) {
        if (strtolower($resourceType->shortName) == "approved" || strtolower($resourceType->shortName) == "need approval") {
            echo ' <input type="radio" name="acquisitionTypeID" value="' . $resourceType->acquisitionTypeID . '">' . $resourceType->shortName;
        }
    }
}

function getResourceFormatsAsDropdown($server) {
    $response = Unirest\Request::post($server . "getResourceFormats/", $headers, $body);
    echo '<select name="resourceFormatID">';
    foreach ($response->body as $resourceType) {
        echo ' <option value="' . $resourceType->resourceFormatID . '">' . $resourceType->shortName . "</option>";
    }
    echo '</select>';
}

function getAdministeringSitesAsDropdown($server) {
    $response = Unirest\Request::post($server . "getAdministeringSites/", $headers, $body);
    echo '<select name="administeringSiteID[]" multiple="multiple">';
    foreach ($response->body as $resourceType) {
        echo ' <option value="' . $resourceType->administeringSiteID . '">' . $resourceType->shortName;
    }
    echo '</select>';
}



?>
</html>
