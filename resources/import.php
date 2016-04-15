<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.2
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
session_start();
include_once 'directory.php';
//print header
$pageTitle=_('Resources import');
include 'templates/header.php';
?><div id="importPage"><h1><?php echo _("CSV File import");?></h1><?php
// CSV configuration
$required_columns = array('titleText' => 0, 'resourceURL' => 0, 'resourceAltURL' => 0, 'parentResource' => 0, 'organization' => 0, 'role' => 0);
if ($_POST['submit']) {
  $delimiter = $_POST['delimiter'];
  $uploaddir = 'attachments/';
  $uploadfile = $uploaddir . basename($_FILES['uploadFile']['name']);
  if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadfile)) {  
    print '<p>'._("The file has been successfully uploaded.").'</p>';
  
  // Let's analyze this file
  if (($handle = fopen($uploadfile, "r")) !== FALSE) {
    if (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
      $columns_ok = true;
      foreach ($data as $key => $value) {
        $available_columns[$value] = $key;
      } 
    } else {
      $error = _("Unable to get columns headers from the file");
    }
  } else {
    $error = _("Unable to open the uploaded file");
  }
  } else {
    $error = _("Unable to upload the file");
  }
  if ($error) {
    print "<p>"._("Error: ").$error.".</p>";
  } else {
    print "<p>"._("Please choose columns from your CSV file:")."</p>";
    print "<form action=\"import.php\" method=\"post\">";
    foreach ($required_columns as $rkey => $rvalue) {
      print "<label for=\"$rkey\">" . $rkey . "</label><select name=\"$rkey\" id=\"$rkey\">";
      print '<option value=""></option>';
      foreach ($available_columns as $akey => $avalue) {
        print "<option value=\"$avalue\"";
        if ($rkey == $akey) print ' selected="selected"';
        print ">$akey</option>";
      } 
      print '</select><br />';
    }
    print "<input type=\"hidden\" name=\"delimiter\" value=\"$delimiter\" />";
    print "<input type=\"hidden\" name=\"uploadfile\" value=\"$uploadfile\" />";
    print "<input type=\"submit\" name=\"matchsubmit\" id=\"matchsubmit\" /></form>";
  }
// Process
} elseif ($_POST['matchsubmit']) {
  $delimiter = $_POST['delimiter'];
  $deduping_config = explode(',', $config->settings->importISBNDedupingColumns); 
  $uploadfile = $_POST['uploadfile'];
   // Let's analyze this file
  if (($handle = fopen($uploadfile, "r")) !== FALSE) {
    $row = 0;
    $inserted = 0;
    $parentInserted = 0;
    $parentAttached = 0;
    $organizationsInserted = 0;
    $organizationsAttached = 0;
    $arrayOrganizationsCreated = array();
    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
      // Getting column names again for deduping
      if ($row == 0) {
        print "<h2>"._("Settings")."</h2>";
        print "<p>"._("Importing and deduping isbnOrISSN on the following columns: ") ;
        foreach ($data as $key => $value) {
          if (in_array($value, $deduping_config)) {
            $deduping_columns[] = $key;
            print $value . " ";
          }
        } 
        print ".</p>";
      } else {
        // Deduping
        unset($deduping_values);
        $resource = new Resource(); 
        $resourceObj = new Resource(); 
        foreach ($deduping_columns as $value) {
          $deduping_values[] = $data[$value];
        }
        $deduping_count = count($resourceObj->getResourceByIsbnOrISSN($deduping_values));
        if ($deduping_count == 0) {
          // Convert to UTF-8
          $data = array_map(function($row) { return mb_convert_encoding($row, 'UTF-8'); }, $data);
        
          // Let's insert data
          $resource->createLoginID    = $loginID;
          $resource->createDate       = date( 'Y-m-d' );
          $resource->updateLoginID    = '';
          $resource->updateDate       = '';
          $resource->titleText        = $data[$_POST['titleText']];
          $resource->resourceURL      = $data[$_POST['resourceURL']];
          $resource->resourceAltURL   = $data[$_POST['resourceAltURL']];
          $resource->providerText     = $data[$_POST['providerText']];
          $resource->statusID         = 1;
          $resource->save();
          $resource->setIsbnOrIssn($deduping_values);
          $inserted++;
          // Do we have to create an organization or attach the resource to an existing one?
          if ($data[$_POST['organization']]) {
            $organizationName = $data[$_POST['organization']];
            $organization = new Organization();
            $organizationRole = new OrganizationRole();
            $organizationID = false;
            // If we use the Organizations module
            if ($config->settings->organizationsModule == 'Y'){
              
              $dbName = $config->settings->organizationsDatabaseName;
              // Does the organization already exists?
              $query = "SELECT count(*) AS count FROM $dbName.Organization WHERE UPPER(name) = '" . str_replace("'", "''", strtoupper($organizationName)) . "'";
              $result = $organization->db->processQuery($query, 'assoc');
              // If not, we try to create it
              if ($result['count'] == 0) {
                $query = "INSERT INTO $dbName.Organization SET createDate=NOW(), createLoginID='$loginID', name='" . $organization->db->escapeString($organizationName) . "'";
                try {
                  $result = $organization->db->processQuery($query);
                  $organizationID = $result;
                  $organizationsInserted++;
                  array_push($arrayOrganizationsCreated, $organizationName);
                } catch (Exception $e) {
                  print "<p>"._("Organization ").$organizationName._(" could not be added.")."</p>";
                }
              // If yes, we attach it to our resource
              } elseif ($result['count'] == 1) {
                $query = "SELECT name, organizationID FROM $dbName.Organization WHERE UPPER(name) = '" . str_replace("'", "''", strtoupper($organizationName)) . "'";
                $result = $organization->db->processQuery($query, 'assoc');
                $organizationID = $result['organizationID'];
                $organizationsAttached++;
              } else {
                print "<p>"._("Error: more than one organization is called ").$organizationName._(". Please consider deduping.")."</p>";
              }
              if ($organizationID) {
                $dbName = $config->settings->organizationsDatabaseName;
                // Get role
                $query = "SELECT organizationRoleID from OrganizationRole WHERE shortName='" . $organization->db->escapeString($data[$_POST['role']]) . "'";
                $result = $organization->db->processQuery($query);
                // If role is not found, fallback to the first one.
                $roleID = ($result[0]) ? $result[0] : 1;
                // Does the organizationRole already exists?
                $query = "SELECT count(*) AS count FROM $dbName.OrganizationRoleProfile WHERE organizationID=$organizationID AND organizationRoleID=$roleID";
                $result = $organization->db->processQuery($query, 'assoc');
                // If not, we try to create it
                if ($result['count'] == 0) {
                  $query = "INSERT INTO $dbName.OrganizationRoleProfile SET organizationID=$organizationID, organizationRoleID=$roleID";
                  try {
                    $result = $organization->db->processQuery($query);
                    if (!in_array($organizationName, $arrayOrganizationsCreated)) {
                      $organizationsInserted++;
                      array_push($arrayOrganizationsCreated, $organizationName);
                    }
                  } catch (Exception $e) {
                    print "<p>"._("Unable to associate organization ").$organizationName._(" with its role.")."</p>";
                  }
                }
              }
            // If we do not use the Organizations module
            } else {
              // Search if such organization already exists
              $organizationExists = $organization->alreadyExists($organizationName);
              $parentID = null;
              if (!$organizationExists) {
                // If not, create it
                $organization->shortName = $organizationName;
                $organization->save();
                $organizationID = $organization->organizationID();
                $organizationsInserted++;
                array_push($arrayOrganizationsCreated, $organizationName);
              } elseif ($organizationExists == 1) {
                // Else, 
                $organizationID = $organization->getOrganizationIDByName($organizationName);
                $organizationsAttached++;
              } else {
                print "<p>"._("Error: more than one organization is called ").$organizationName._(" Please consider deduping.")."</p>";
              }
              // Find role
              $organizationRoles = $organizationRole->getArray();
              if (($roleID = array_search($data[$_POST['role']], $organizationRoles)) == 0) {
                // If role is not found, fallback to the first one.
                $roleID = '1';
              } 
            }
            // Let's link the resource and the organization.
            // (this has to be done whether the module Organization is in use or not)
            if ($organizationID && $roleID) {
              $organizationLink = new ResourceOrganizationLink();
              $organizationLink->organizationRoleID = $roleID;
              $organizationLink->resourceID = $resource->resourceID;
              $organizationLink->organizationID = $organizationID;
              $organizationLink->save();
            }
          }
        } elseif ($deduping_count == 1) {
          $resources = $resourceObj->getResourceByIsbnOrISSN($deduping_values);
          $resource = $resources[0];
        }
          // Do we have a parent resource to create?
          if ($data[$_POST['parentResource']] && ($deduping_count == 0 || $deduping_count == 1) ) {
            // Search if such parent exists
            $numberOfParents = count($resourceObj->getResourceByTitle($data[$_POST['parentResource']]));
            $parentID = null;
            if ($numberOfParents == 0) {
              // If not, create parent
              $parentResource = new Resource();
              $parentResource->createLoginID = $loginID;
              $parentResource->createDate    = date( 'Y-m-d' );
              $parentResource->titleText     = $data[$_POST['parentResource']];
              $parentResource->statusID      = 1;
              $parentResource->save();
              $parentID = $parentResource->resourceID;
              $parentInserted++;
            } elseif ($numberOfParents == 1) {
              // Else, attach the resource to its parent.
              $parentResource = $resourceObj->getResourceByTitle($data[$_POST['parentResource']]);
              $parentID = $parentResource[0]->resourceID;
              
              $parentAttached++; 
            }
            if ($numberOfParents == 0 || $numberOfParents == 1) {
              $resourceRelationship = new ResourceRelationship();
              $resourceRelationship->resourceID = $resource->resourceID;
              $resourceRelationship->relatedResourceID = $parentID;
              $resourceRelationship->relationshipTypeID = '1';  //hardcoded because we're only allowing parent relationships
              if (!$resourceRelationship->exists()) {
                $resourceRelationship->save();
              }
            }
          } 
        }
      $row++;
    }
    print "<h2>"._("Results")."</h2>";
    print "<p>" . ($row - 1) . _(" rows have been processed. ").$inserted._(" rows have been inserted.")."</p>";
    print "<p>".$parentInserted._(" parents have been created. ").$parentAttached._(" resources have been attached to an existing parent.")."</p>";
    print "<p>".$organizationsInserted._(" organizations have been created");
    if (count($arrayOrganizationsCreated) > 0) print " (" . implode(',', $arrayOrganizationsCreated) . ")";
    print ". $organizationsAttached"._(" resources have been attached to an existing organization.")."</p>";
  }
} else {
          
?>
<p><?php echo _("The first line of the CSV file must contain column names, and not data. These names will be used during the import process.");?></p>
<form enctype="multipart/form-data" action="import.php" method="post" id="importForm">
  <fieldset>
  <legend><?php echo _("File selection");?></legend>
  <label for="uploadFile"><?php echo _("CSV File");?></label>
  <input type="file" name="uploadFile" id="uploadFile" />
  </fieldset>
  <fieldset>
  <legend><?php echo _("Import options");?></legend>
  <label for="delimiter"><?php echo _("CSV delimiter");?></label>
  <select name="delimiter" id="delimiter">
    <option value=",">, <?php echo _("(comma)");?></option>
    <option value=";">; <?php echo _("(semicolon)");?></option>
    <option value="|">| <?php echo _("(pipe)");?></option>
  </select>
  </fieldset>
  <input type="submit" name="submit" value="<?php echo _("Upload");?>" />
</form>

<?php
}
?>
</div>
<?php
//print footer
include 'templates/footer.php';
?>
