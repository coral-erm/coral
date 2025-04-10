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
$util = new Utility();

if ($config->settings->pIG != 'Y') { die(); }

switch ($_GET['action']) {

    case 'getPigSearchResources':

        Resource::setSearch($_POST['search']);

        $queryString = "";
            foreach ($_POST['search'] as $key => $value) {
                $queryString = $queryString . "&" . $key . "=" . urlencode($value);
            }
        echo "<span style='float:right;'><a href='" . "pig_htmldata.php?action=getPigSearchResources" . $queryString . "'>HTML Feed</a>";
        echo "&nbsp;";
        echo "<a class='thickbox' href='" . 'javascript:alert("Copy the URL below:\n\nhttp://' . $_SERVER['SERVER_NAME'] . str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['SCRIPT_NAME']) . 'pig_htmldata.php?action=getPigSearchResources' . $queryString . '")' . "'><img title='PIG url' alt='PIG url' src='images/pig_balloon_icon.png'/></a>";
        echo "&nbsp;&nbsp;";
        echo "<a href='" . "pig_xmldata.php?action=getPigSearchResources" . $queryString . "'>XML Feed</a>";
        echo "&nbsp;";
        echo "<a class='thickbox' href='" . 'javascript:alert("Copy the URL below:\n\nhttp://' . $_SERVER['SERVER_NAME'] . str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['SCRIPT_NAME']) . 'pig_xmldata.php?action=getPigSearchResources' . $queryString . '")' . "'><img title='PIG XML url' alt='PIG XML url' src='images/pig_XML_icon.png'/></a></span>";

        $queryDetails = PigResource::getPigSearchDetails();
        $whereAdd = $queryDetails["where"];

        $page = $queryDetails["page"];
        $orderBy = $queryDetails["order"];
        $recordsPerPage = $queryDetails["perPage"];

        //numbers to be displayed in records per page dropdown
            $recordsPerPageDD = array(10,25,50,100);

            //determine starting rec - keeping this based on 0 to make the math easier, we'll add 1 to the display only
            //page will remain based at 1
            if ($page == '1'){
                $startingRecNumber = 0;
            }else{
                $startingRecNumber = ($page * $recordsPerPage) - $recordsPerPage;
            }


            //get total number of records to print out and calculate page selectors
            $resourceObj = new PigResource();
            $totalRecords = $resourceObj->searchPigCount($whereAdd);

            //reset pagestart to 1 - happens when a new search is run but it kept the old page start
            if ($totalRecords < $startingRecNumber){
                $page = 1;
                $startingRecNumber = 1;
            }

            $limit = $startingRecNumber . ", " . $recordsPerPage;

            $resourceArray = array();
            $resourceArray = $resourceObj->searchPig($whereAdd, $orderBy, $limit);

            if (count($resourceArray) == 0){
                echo "<br /><br /><i>Sorry, no requests fit your query</i>";
                $i=0;
            }else{
                //maximum number of pages to display on screen at one time
                $maxDisplay = 25;

                $displayStartingRecNumber = $startingRecNumber + 1;
                $displayEndingRecNumber = $startingRecNumber + $recordsPerPage;

                if ($displayEndingRecNumber > $totalRecords){
                    $displayEndingRecNumber = $totalRecords;
                }

                //div for displaying record count
                echo "<span style='float:left; font-weight:bold; width:650px;'>Displaying " . $displayStartingRecNumber . " to " . $displayEndingRecNumber . " of " . $totalRecords . " Resource Records</span>";


                //print out page selectors as long as there are more records than the number that should be displayed
                if ($totalRecords > $recordsPerPage){
                    echo "<div style='vertical-align:bottom;text-align:left;clear:both;'>";

                    //print starting <<
                    if ($page == 1){
                        echo "<span class='smallerText'><<</span>&nbsp;";
                    }else{
                        $prevPage = $page - 1;
                        echo "<a href='javascript:void(0);' id='" . $prevPage . "' class='setPage smallLink' alt='previous page' title='previous page'><<</a>&nbsp;";
                    }


                    //now determine the starting page - we will display 3 prior to the currently selected page
                    if ($page > 3){
                        $startDisplayPage = $page - 3;
                    }else{
                        $startDisplayPage = 1;
                    }

                    $maxPages = ($totalRecords / $recordsPerPage) + 1;

                    //now determine last page we will go to - can't be more than maxDisplay
                    $lastDisplayPage = $startDisplayPage + $maxDisplay;
                    if ($lastDisplayPage > $maxPages){
                        $lastDisplayPage = ceil($maxPages);
                    }

                    for ($i=$startDisplayPage; $i<$lastDisplayPage;$i++){

                        if ($i == $page){
                            echo "<span class='smallerText'>" . $i . "</span>&nbsp;";
                        }else{
                            echo "<a href='javascript:void(0);' id='" . $i . "' class='setPage smallLink'>" . $i . "</a>&nbsp;";
                        }

                    }

                    $nextPage = $page + 1;
                    //print last >> arrows
                    if ($nextPage >= $maxPages){
                        echo "<span class='smallerText'>>></span>&nbsp;";
                    }else{
                        echo "<a href='javascript:void(0);' id='" . $nextPage . "' class='setPage smallLink' alt='next page' title='next page'>>></a>&nbsp;";
                    }

                    echo "</div>";


                }else{
                    echo "<div style='vertical-align:bottom;text-align:left;clear:both;'>&nbsp;</div>";
                }


                ?>
                <table class='dataTable' style='width:727px'>
                <tr>
                <?php if ($_POST['search']['titleTextckbox'] == 'ON') { ?>
                <th><table class='noBorderTable' style='width:100%'><tr><td>Name</td><td style='width:10px;'><a href='javascript:setOrder("R.titleText","asc");'><img src='images/arrowup.gif' border=0></a></td><td style='width:10px;'><a href='javascript:setOrder("R.titleText","desc");'><img src='images/arrowdown.gif' border=0></a></td></tr></table></th>
                <?php } if ($_POST['search']['providerTextckbox'] == 'ON') { ?>
                <th><table class='noBorderTable' style='width:100%'><tr><td>Publisher</td><td style='width:10px;'><a href='javascript:setOrder("R.providerText","asc");'><img src='images/arrowup.gif' border=0></a></td><td style='width:10px;'><a href='javascript:setOrder("CU.loginID","desc");'><img src='images/arrowdown.gif' border=0></a></td></tr></table></th>
                <?php } if ($_POST['search']['descriptionTextckbox'] == 'ON') { ?>
                <th><table class='noBorderTable' style='width:100%'><tr><td>Description</td><td style='width:10px;vertical-align:top;'>&nbsp;</td><td style='width:10px;vertical-align:top;'></td></tr></table></th>
                <?php } if ($_POST['search']['generalSubjectckbox'] == 'ON') { ?>
                <th><table class='noBorderTable' style='width:100%'><tr><td>General Subject</td><td style='width:10px;vertical-align:top;'>&nbsp;</td><td style='width:10px;vertical-align:top;'></td></tr></table></th>
                <?php } if ($_POST['search']['resourceTypeckbox'] == 'ON') { ?>
                <th><table class='noBorderTable' style='width:100%'><tr><td>Resource Type</td><td style='width:10px;vertical-align:top;'>&nbsp;</td><td style='width:10px;vertical-align:top;'></td></tr></table></th>
                <?php } ?>
                </tr>

                <?php

                $i=0;
                foreach ($resourceArray as $resource){
                    $i++;
                    if ($i % 2 == 0){
                        $classAdd="";
                    }else{
                        $classAdd="class='alt'";
                    }
                    echo "<tr>";
                    if ($_POST['search']['titleTextckbox'] == 'ON') {
                        if (strlen($resource['resourceURL']) > 0) {
                            echo "<td $classAdd><a href='" . $resource['resourceURL'] ."'>" . $resource['titleText'] . "</a></td>";
                        } else {
                            echo "<td $classAdd>" . $resource['titleText'] . "</td>";
                        }
                    }
                    if ($_POST['search']['providerTextckbox'] == 'ON') {
                        echo "<td $classAdd>" . $resource['providerText'] . "</td>";
                    }
                    if ($_POST['search']['descriptionTextckbox'] == 'ON') {
                        echo "<td $classAdd>" . $resource['descriptionText'] . "</td>";
                    }
                    if ($_POST['search']['generalSubjectckbox'] == 'ON') {
                        //get subjects for this tab
                        $sanitizedInstance = array();
                        $generalDetailSubjectIDArray = array();
                        $resourceRecord = new Resource(new NamedArguments(array('primaryKey' => $resource['resourceID'])));

                            foreach ($resourceRecord->getGeneralDetailSubjectLinkID() as $instance) {
                                foreach (array_keys($instance->attributeNames) as $attributeName) {
                                    $sanitizedInstance[$attributeName] = $instance->$attributeName;
                                }

                                $sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;
                                array_push($generalDetailSubjectIDArray, $sanitizedInstance);

                            }

                            if (is_array($generalDetailSubjectIDArray) && count($generalDetailSubjectIDArray) > 0) {

                                $generalSubjectID = 0;
                                echo "<td nowrap='nowrap' $classAdd><ul>";

                                    foreach ($generalDetailSubjectIDArray as $generalDetailSubjectID){
                                        $generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $generalDetailSubjectID[generalSubjectID])));

                                        if ($generalDetailSubjectID['generalSubjectID'] != $generalSubjectID) {
                                            echo "<li>" . $generalSubject->shortName . "</li>";
                                        }

                                        $generalSubjectID = $generalDetailSubjectID['generalSubjectID'];
                                    }
                                echo "</td></ul>";
                            } else {
                                echo "<td $classAdd>&nbsp;</td>";
                            }

                    }
                    if ($_POST['search']['resourceTypeckbox'] == 'ON') {
                        echo "<td $classAdd>" . $resource['resourceTypeID'] . "</td>";
                    }
                    echo "</tr>";
                }

                ?>
                </table>

                <table style='width:100%;margin-top:4px'>
                <tr>
                <td style='text-align:left'>
                <?php
                //print out page selectors
                if ($totalRecords > $recordsPerPage){

                    //print starting <<
                    if ($page == 1){
                        echo "<span class='smallerText'><<</span>&nbsp;";
                    }else{
                        $prevPage = $page - 1;
                        echo "<a href='javascript:void(0);' id='" . $prevPage . "' class='setPage smallLink' alt='previous page' title='previous page'><<</a>&nbsp;";
                    }


                    //now determine the starting page - we will display 3 prior to the currently selected page
                    if ($page > 3){
                        $startDisplayPage = $page - 3;
                    }else{
                        $startDisplayPage = 1;
                    }

                    $maxPages = ($totalRecords / $recordsPerPage) + 1;

                    //now determine last page we will go to - can't be more than maxDisplay
                    $lastDisplayPage = $startDisplayPage + $maxDisplay;
                    if ($lastDisplayPage > $maxPages){
                        $lastDisplayPage = ceil($maxPages);
                    }

                    for ($i=$startDisplayPage; $i<$lastDisplayPage;$i++){

                        if ($i == $page){
                            echo "<span class='smallerText'>" . $i . "</span>&nbsp;";
                        }else{
                            echo "<a href='javascript:void(0);' id='" . $i . "' class='setPage smallLink'>" . $i . "</a>&nbsp;";
                        }

                    }

                    $nextPage = $page + 1;
                    //print last >> arrows
                    if ($nextPage >= $maxPages){
                        echo "<span class='smallerText'>>></span>&nbsp;";
                    }else{
                        echo "<a href='javascript:void(0);' id='" . $nextPage . "' class='setPage smallLink' alt='next page' title='next page'>>></a>&nbsp;";
                    }
                }
                ?>
                </td>
                <td style="text-align:right">
                <select id='numberRecordsPerPage' name='numberRecordsPerPage' style='width:50px;'>
                    <?php
                    foreach ($recordsPerPageDD as $i){
                        if ($i == $recordsPerPage){
                            echo "<option value='" . $i . "' selected>" . $i . "</option>";
                        }else{
                            echo "<option value='" . $i . "'>" . $i . "</option>";
                        }
                    }
                    ?>
                </select>
                <span class='smallText'>records per page</span>
                </td>
                </tr>
                </table>

                <?php
            }

        break;


    default:
        if (empty($action))
            return;
       printf(_("Action %s not set up!"), $action);
       break;


}
