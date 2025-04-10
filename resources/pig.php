<?php

/*
**************************************************************************************************************************
** CORAL PIG Add-On v. 1.0
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

$config = new Configuration();
if ($config->settings->pIG != 'Y') { die(_("Public Interface Generator is not enabled on this Coral installation. Please edit configuration.ini if you want to enable it.")); }

//print header
$pageTitle='PIG Home';
$statusID = 4;  // This is the statusID for records we DO NOT want to view, example Archived;

include 'templates/pig_header.php';

//used for creating a "sticky form" for back buttons
//except we don't want it to retain if they press the 'index' button
//check what referring script is

if ($_SESSION['ref_script'] != "pig.php"){
    Resource::resetSearch();
}

// If we are coming in from a searchbox form
if (strlen($_GET['name']) > 0) {
    $search = array(
        "orderBy" => urldecode($_GET['orderBy']),
        "page" => urldecode($_GET['page']),
        "recordsPerPage" => urldecode($_GET['recordsPerPage']),
        "startWith" => urldecode($_GET['startWith']),
        "name" => urldecode($_GET['name']),
        "descriptionText" => urldecode($_GET['descriptionText']),
        "providerText" => urldecode($_GET['providerText']),
        "generalSubjectID" => urldecode($_GET['generalSubjectID']),
        "resourceTypeID" => urldecode($_GET['resourceTypeID']),
        "statusID" => $statusID,
        );

} else {
    $search = Resource::getSearch();
}

//set referring page
CoralSession::set('ref_script', empty($currentPage) ? null : $currentPage);

$html = new Html();

?>

<div style='text-align:left;'>
<table class="headerTable" style="background-image:url('images/header.gif');background-repeat:no-repeat;">
<tr style='vertical-align:top;'>
<td style="width:155px;padding-right:10px;">
  <form method="get" action="pig_ajax_htmldata.php?action=getPigSearchResources" id="resourcePigSearchForm">
    <?php
    foreach(array('statusID', 'orderBy','page','recordsPerPage','startWith') as $hidden) {
        echo $html->hidden_search_field_tag($hidden, $search[$hidden]);
    }
    ?>

    <table class='noBorder'>
    <tr><td style='text-align:left;width:75px;' align='left'>
    <span style='font-size:130%;font-weight:bold;'>Search</span><br />
    <button type="button" class='btn newSearch'>new search</button>
    </td>
    <td><div id='div_feedback'>&nbsp;</div>
    </td></tr>
    </table>

    <table class='borderedFormTable' style="width:150px">

    <tr>
    <td class='searchRow'><label for='searchName'><b>Name (contains)</b></label>
    <br />
    <?php echo $html->text_search_field_tag('name', $search['name']); ?>
    <br />
    <div id='div_searchName' style='<?php if (!$search['name']) echo "display:none;"; ?>margin-left:123px;'><input type='button' name='btn_searchName' value='go!' class='searchButton' /></div>
    </td>
    </tr>



    <tr>
    <td class='searchRow'><label for='searchDescriptionText'><b>Description (contains)</b></label>
    <br />
    <?php echo $html->text_search_field_tag('descriptionText', $search['descriptionText']); ?>
    <br />
    <div id='div_searchDescriptionText' style='<?php if (!$search['descriptionText']) echo "display:none;"; ?>margin-left:123px;'><input type='button' name='btn_searchDescriptionText' value='go!' class='searchButton' /></div>
    </td>
    </tr>


    <tr>
    <td class='searchRow'><label for='searchProviderText'><b>Publisher (contains)</b></label>
    <br />
    <?php echo $html->text_search_field_tag('providerText', $search['providerText']); ?><br />
    <div id='div_searchProviderText' style='<?php if (!$search['providerText']) echo "display:none;"; ?>margin-left:123px;'><input type='button' name='btn_searchProviderText' value='go!' class='searchButton' /></div>
    </td>
    </tr>

    <tr>
    <td class='searchRow'><label for='searchGeneralSubjectID'><b>General Subject</b></label>
    <br />
    <select name='search[generalSubjectID]' id='searchGeneralSubjectID' style='width:150px'>
    <option value=''>All</option>

    <?php

        if ($search['generalSubjectID'] == "none"){
            echo "<option value='none' selected>(none)</option>";
        }else{
            echo "<option value='none'>(none)</option>";
        }


        $display = array();
        $generalSubject = new GeneralSubject();

        foreach($generalSubject->allAsArray() as $display) {
            if ($search['generalSubjectID'] == $display['generalSubjectID']){
                echo "<option value='" . $display['generalSubjectID'] . "' selected>" . $display['shortName'] . "</option>";
            }else{
                echo "<option value='" . $display['generalSubjectID'] . "'>" . $display['shortName'] . "</option>";
            }
        }

    ?>
    </select>
    </td>
    </tr>


    <tr>
    <td class='searchRow'><label for='searchResourceTypeID'><b><?php echo _("Resource Type"); ?></b></label>
    <br />
    <select name='search[resourceTypeID]' id='searchResourceTypeID' style='width:150px'>
    <option value=''>All</option>

    <?php

        if ($search['resourceTypeID'] == "none"){
            echo "<option value='none' selected>(none)</option>";
        }else{
            echo "<option value='none'>(none)</option>";
        }


        $display = array();
        $resourceType = new ResourceType();

        foreach($resourceType->allAsArray() as $display) {
            if ($search['resourceTypeID'] == $display['resourceTypeID']){
                echo "<option value='" . $display['resourceTypeID'] . "' selected>" . $display['shortName'] . "</option>";
            }else{
                echo "<option value='" . $display['resourceTypeID'] . "'>" . $display['shortName'] . "</option>";
            }
        }

    ?>
    </select>
    </td>
    </tr>
    <tr>
        <td class='searchRow'><b><?php echo _("Display Column"); ?></b><br>
            <ul class="unstyled">
            <li><label><input type="checkbox" id="titleTextckbox" name="search[titleTextckbox]" value="ON" checked><?php echo _("Name"); ?></label></li>
            <li><label><input type="checkbox" id="providerTextckbox" name="search[providerTextckbox]" value="ON" checked><?php echo _("Publisher"); ?></label></li>
            <li><label><input type="checkbox" id="descriptionTextckbox" name="search[descriptionTextckbox]" value="ON" checked><?php echo _("Description"); ?></label></li>
            <li><label><input type="checkbox" id="generalSubjectckbox" name="search[generalSubjectckbox]" value="ON" checked><?php echo _("General Subject"); ?></label></li>
            <li><label><input type="checkbox" id="resourceTypeckbox" name="search[resourceTypeckbox]" value="ON"><?php echo _("Resource Type"); ?></label></li>
            </ul>
        </td>
    </tr>

    </table>
    </div>

  </form>

</td>
<td>
<div id='div_searchResults'></div>
</td></tr>
</table>
</div>
<br />
<script type="text/javascript" src="js/pig.js"></script>
<script type='text/javascript'>
<?php
    //used to default to previously selected values when back button is pressed
    //if the startWith is defined set it so that it will default to the first letter picked
    if ((isset($_SESSION['res_startWith'])) && ($reset != 'Y')){
      echo "startWith = '" . $_SESSION['res_startWith'] . "';";
      echo "$(\"#span_letter_" . $_SESSION['res_startWith'] . "\").removeClass('searchLetter').addClass('searchLetterSelected');";
    }

    if ((isset($_SESSION['res_pageStart'])) && ($reset != 'Y')){
      echo "pageStart = '" . $_SESSION['res_pageStart'] . "';";
    }

    if ((isset($_SESSION['res_recordsPerPage'])) && ($reset != 'Y')){
      echo "recordsPerPage = '" . $_SESSION['res_recordsPerPage'] . "';";
    }

    if ((isset($_SESSION['res_orderBy'])) && ($reset != 'Y')){
      echo "orderBy = \"" . $_SESSION['res_orderBy'] . "\";";
    }

    echo "</script>";

    //print footer
    include 'templates/footer.php';
?>
