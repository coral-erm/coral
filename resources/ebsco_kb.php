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


//used for creating a "sticky form" for back buttons
//except we don't want it to retain if they press the 'index' button
//check what referring script is

CoralSession::set('ref_script', empty($currentPage) ? null : $currentPage);
$ebscoKb = EbscoKbService::getInstance();
$search = $ebscoKb::getSearch();
$limitLabel = '';
$limitName = '';

if(!empty($search['vendorId'])){
    $vendor = $ebscoKb->getVendor($search['vendorId']);
    $limitLabel = 'from vendor';
    $limitName = $vendor->vendorName;

    if(!empty($search['packageId'])){
        $package = $ebscoKb->getPackage($search['vendorId'], $search['packageId']);
        $limitLabel = 'from package';
        $limitName = $package->packageName;

    }
}

include_once __DIR__.'/css/ebscoKbCss.php';

//print header
$pageTitle=_('EBSCO Knowledge Base');
include 'templates/header.php';
?>

<main id="main-content">
    <article>
        <div id='div_searchResults'></div>
    </article>
            <aside id="side" class="block-form" role="search">
                <form method="get" action="ajax_htmldata.php?action=getSearchEbscoKb" id="ebscoKbSearchForm">
                    <?php foreach(array('orderby','offset','count', 'vendorId', 'packageId', 'type') as $hidden): ?>
                        <input
                            type="hidden"
                            name="search[<?php echo _($hidden); ?>]"
                            id="search<?php echo ucfirst($hidden); ?>"
                            value="<?php echo $search[$hidden]; ?>"
                            data-default="<?php echo EbscoKbService::$defaultSearchParameters[$hidden]; ?>"
                        >
                    <?php endforeach; ?>

                    <!-- TODO: eliminate inline styles -->
                    <table class='noBorder' id='title-option'>
                        <tr><td style='text-align:left;width:75px;' align='left'>
                                <span style='font-size:130%;font-weight:bold;'><?php echo _("EBSCO Knowledge Base Search");?></span><br />
                                <button type="button" class='btn newSearch' title="<?php echo _("new search");?>"><?php echo _("new search");?></button>
                            </td>
                            <td><div id='div_feedback'>&nbsp;</div>
                            </td></tr>
                    </table>
                    <table class='borderedFormTable' style="width:150px; color: white;">
                        <tr>
                            <td class='searchRow'>
                                <label for='selectType'><strong><?php echo _("Search For");?></strong></label>
                                <br />
                                <select name='selectType' id='selectType' style='width:150px'>
                                    <?php
                                        foreach(EbscoKbService::$queryTypes as $key => $type){
                                          $selected = $search['type'] == $key ? 'selected' : '';
                                          echo "<option value='$key' $selected>".$type['selectDisplay']."</option>";
                                        }
                                    ?>
                                </select>
                                <div id="limitBy" style="margin-top: 1em; font-size: .75em;">
                                    <strong><?php echo _($limitLabel);?></strong>
                                    <br />
                                    <button type="button" id="removeLimit" class="btn">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <span id="limitName"><?php echo $limitName; ?></span>
                                </div>
                            </td>
                        </tr>

                        <tr class="ebsco-toggle-option titles-option packages-option vendors-option">
                            <td class='searchRow'>
                                <div class="ebsco-toggle-option titles-option">
                                    <label for="searchfield"><?php echo _("Search field"); ?></label>
                                    <select
                                        name='search[searchfield]'
                                        id='searchfield'
                                        data-default="<?php echo EbscoKbService::$defaultSearchParameters['searchfield']; ?>">
                                        <?php
                                        foreach(EbscoKbService::$titleSearchFieldOptions as $key => $type){
                                            $selected = $search['searchfield'] == $key ? 'selected' : '';
                                            echo "<option value='$key' $selected>$type</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        for='searchSearch'
                                        class="ebsco-toggle-option titles-option">
                                        <strong><?php echo _("contains");?></strong>
                                    </label>
                                    <label
                                            for='searchSearch'
                                            class="ebsco-toggle-option packages-option vendors-option">
                                        <strong><?php echo _("Name (contains)");?></strong>
                                    </label>
                                    <br />
                                    <input type="text" id="searchSearch" name="search[search]" style="width:145px" value="<?php echo isset($search['search']) ? $search['search'] : ''; ?>">
                                    <br />
                                </div>
                            </td>
                        </tr>

                        <tr class="ebsco-toggle-option titles-option packages-option">
                            <td class='searchRow'>
                                <label for='searchSelection'><strong><?php echo _("Selection");?></strong></label>
                                <br />
                                <select
                                    name='search[selection]'
                                    id='searchSelection'
                                    style='width:150px'
                                    data-default="<?php echo EbscoKbService::$defaultSearchParameters['selection']; ?>">
                                    <?php
                                    foreach(EbscoKbService::$selectionOptions as $key => $type){
                                        $selected = $search['selection'] == $key ? 'selected' : '';
                                        echo "<option value='$key' $selected>$type</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr class="ebsco-toggle-option titles-option">
                            <td class='searchRow'>
                                <label for='searchResourceType'><strong><?php echo _("Resource Type");?></strong></label>
                                <br />
                                <select
                                        name='search[resourcetype]'
                                        id='searchResourceType'
                                        style='width:150px'
                                        data-default="<?php echo EbscoKbService::$defaultSearchParameters['resourcetype']; ?>">
                                    <?php
                                    foreach(EbscoKbService::$titleResourceTypeOptions as $key => $type){
                                        $selected = $search['resourcetype'] == $key ? 'selected' : '';
                                        echo "<option value='$key' $selected>$type</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr class="ebsco-toggle-option packages-option">
                            <td class='searchRow'>
                                <label for='searchContentType'><b><?php echo _("Content Type");?></b></label>
                                <br />
                                <select
                                        name='search[contenttype]'
                                        id='searchContentType'
                                        style='width:150px'
                                        data-default="<?php echo EbscoKbService::$defaultSearchParameters['contenttype']; ?>">
                                    <?php
                                    foreach(EbscoKbService::$packageContentTypeOptions as $key => $type){
                                        $selected = $search['contenttype'] == $key ? 'selected' : '';
                                        echo "<option value='$key' $selected>$type</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td class='searchRow'>
                                <button
                                    type="submit"
                                    class="searchButton btn">
                                    <?php echo _("go!");?>
                                </button>
                            </td>
                        </tr>
                    </table>
                </form>
        </aside>
</main>

<?php
include 'templates/footer.php';
?>
<script type='text/javascript'>
<?php
  //used to default to previously selected values when back button is pressed
  //if the startWith is defined set it so that it will default to the first letter picked

  if ((CoralSession::get('res_pageStart')) && ($reset != 'Y')){
	  echo "pageStart = '" . CoralSession::get('res_pageStart') . "';";
  }

  if ((CoralSession::get('res_recordsPerPage')) && ($reset != 'Y')){
	  echo "recordsPerPage = '" . CoralSession::get('res_recordsPerPage') . "';";
  }

  if ((CoralSession::get('res_orderBy')) && ($reset != 'Y')){
	  echo "orderBy = \"" . CoralSession::get('res_orderBy') . "\";";
  }
?>
</script>
<script src="js/ebsco_kb.js"></script>
</body>
</html>
