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

include_once 'directory.php';


//set referring page
CoralSession::set('ref_script', $currentPage = '');

$pageTitle=_('Administration');
include 'templates/header.php';

$config = new Configuration;

//ensure user has admin permissions
if ($user->isAdmin()){

  $adminMenuLinks = [
    ["translatableTitle" => _("Access Method"),         "className" => "AdminLink", "id" => "AccessMethod"],
    ["translatableTitle" => _("Acquisition Type"),      "className" => "AdminLink", "id" => "AcquisitionType"],
    ["translatableTitle" => _("Administering Site"),    "className" => "AdminLink", "id" => "AdministeringSite"],
    ["translatableTitle" => _("Alias Type"),            "className" => "AdminLink", "id" => "AliasType"],
    ["translatableTitle" => _("Attachment Type"),       "className" => "AdminLink", "id" => "AttachmentType"],
    ["translatableTitle" => _("Authentication Type"),   "className" => "AdminLink", "id" => "AuthenticationType"],
    ["translatableTitle" => _("Authorized Site"),       "className" => "AdminLink", "id" => "AuthorizedSite"],
    ["translatableTitle" => _("Cataloging Status"),     "className" => "AdminLink", "id" => "CatalogingStatus"],
    ["translatableTitle" => _("Cataloging Type"),       "className" => "AdminLink", "id" => "CatalogingType"],
    ["translatableTitle" => _("Contact Role"),          "className" => "AdminLink", "id" => "ContactRole"],
    ["translatableTitle" => _("Currency"),              "className" => "CurrencyLink"],
    ["translatableTitle" => _("Downtime Type"),         "className" => "AdminLink", "id" => "DowntimeType"],
    ["translatableTitle" => _("EBSCO Kb Config"),       "className" => "EbscoKbConfigLink"],
    ["translatableTitle" => _("External Login Type"),   "className" => "AdminLink", "id" => "ExternalLoginType"],
    ["translatableTitle" => _("Funds"),                 "className" => "FundLink"],
    ["translatableTitle" => _("Import Configuration"),  "className" => "ImportConfigLink"],
    ["translatableTitle" => _("Export Configuration"),  "className" => "ExportConfigLink"],
    ["translatableTitle" => _("License Status"),        "className" => "AdminLink", "id" => "LicenseStatus"],
    ["translatableTitle" => _("Note Type"),             "className" => "AdminLink", "id" => "NoteType"],
    ["translatableTitle" => _("Order Type"),            "className" => "AdminLink", "id" => "OrderType"],
    ["translatableTitle" => _("Purchasing Site"),       "className" => "AdminLink", "id" => "PurchaseSite"],
    ["translatableTitle" => _("Resource Format"),       "className" => "AdminLink", "id" => "ResourceFormat"],
    ["translatableTitle" => _("Resource Type"),         "className" => "AdminLink", "id" => "ResourceType"],
    ["translatableTitle" => _("Storage Location"),      "className" => "AdminLink", "id" => "StorageLocation"],
    ["translatableTitle" => _("Subjects"),              "className" => "SubjectsAdminLink"],
    ["translatableTitle" => _("User Limit"),            "className" => "AdminLink", "id" => "UserLimit"]
  ];
  if ($config->settings->enhancedCostHistory == 'Y'){
    $adminMenuLinks[] = ["translatableTitle" => _("Cost Details"), "className" => "AdminLink", "id" => "CostDetails"];
  }
  if ($config->settings->enableAlerts == 'Y'){
    $adminMenuLinks[] = ["translatableTitle" => _("Alert Settings"), "className" => "AlertAdminLink"];
  }
  if ($config->settings->organizationsModule == 'N'){
    $adminMenuLinks[] = ["translatableTitle" => _("Organization Role"), "className" => "AdminLink", "id" => "OrganizationRole"];
    $adminMenuLinks[] = ["translatableTitle" => _("Organizations"), "className" => "AdminLink", "id" => "Organization"];
  }
  // Sort the links according to their title (I do not believe that this is locale aware)
  usort($adminMenuLinks, function ($item1, $item2) {
    return strcmp($item1["translatableTitle"], $item2["translatableTitle"]);
  });

  // We want these links at the top of the list all the time
  array_unshift($adminMenuLinks,
    ["translatableTitle" => _("Users"),                 "className" => "UserAdminLink"],
    ["translatableTitle" => _("Workflow / User Group"), "className" => "WorkflowAdminLink"]
  );
  ?>

  <table class='headerTable'>
    <tr>
      <td style='margin:0;padding:0;text-align:left;'>

        <table style='width:100%; margin:0;padding:0;'>
          <tr style='vertical-align:top'>
            <td>
              <span class="headerText"><?php echo _("Administration");?></span>
              <br />
            </td>
          </tr>
        </table>

        <table style='width:700px; text-align:left; vertical-align:top;'>
          <tr>
            <td style='width:170px;vertical-align:top;'>
              <table class='adminMenuTable' style='width:170px;'>
                <?php
                for ($i = 0; $i < count($adminMenuLinks); $i++) {
                  $l = $adminMenuLinks[$i];
                  $idText = array_key_exists("id", $l) ? "id='".$l["id"]."'" : "";
                  echo "<tr><td><div class='adminMenuLink'>";
                  echo "<a href='javascript:void(0);' $idText class='".$l["className"]."'>";
                  echo $l["translatableTitle"];
                  echo "</a>";
                  echo "</div></td></tr>";
                }
                ?>
              </table>
            </td>
            <td class='adminRightPanel' style='width:530px;margin:0;'>
              <div style='margin-top:5px;' id='div_AdminContent'>
              <img src = "images/circle.gif" /><?php echo _("Loading...");?>
              </div>
              <div style='margin-top:5px;' class='smallDarkRedText' id='div_error'></div>
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>
  <br />
  <script type="text/javascript" src="js/admin.js"></script>

<?php

//end else for admin
}else{
  echo _("You do not have permissions to access this screen.");
}

include 'templates/footer.php';
?>


