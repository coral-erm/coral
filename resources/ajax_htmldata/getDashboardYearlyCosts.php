<?php

    include_once 'directory.php';


    $startYear = $_POST['startYear'];
    if (!$startYear) $startYear = date('Y');

    $endYear = $_POST['endYear'];
    if (!$endYear) $endYear = date('Y');

    $resourceTypeID = $_POST['resourceTypeID'];
    $acquisitionTypeID = $_POST['acquisitionTypeID'];
    $orderTypeID = $_POST['orderTypeID'];
    $subjectID = $_POST['subjectID'];
    $costDetailsID = $_POST['costDetailsID'];
    $groupBy = $_POST['groupBy'];

    $dashboard = new Dashboard();
    $query = $dashboard->getQueryYearlyCosts($resourceTypeID, $startYear, $endYear, $acquisitionTypeID, $orderTypeID, $subjectID, $costDetailsID, $groupBy);
    $results = $dashboard->getResults($query);
    if ($groupBy == "GS.shortName") $groupBy = "generalSubject";
    $costDetails = new CostDetails();
    $costDetailsArray = $costDetails->allAsArray();

    echo "<table id='dashboard_table' class='dataTable' style='width:840px;margin-top:0'>";
    echo "<thead><tr>";
    echo "<th>" . _("Name") . "</th>";
    echo "<th>" . _("Resource Type") . "</th>";
    echo "<th>" . _("General Subjects") . "</th>";
    echo "<th>" . _("Detailed Subjects") . "</th>";
    echo "<th>" . _("Acquisition Type") . "</th>";
    echo "<th>" . _("Library Number") . "</th>";
    for ($i = $startYear; $i <= $endYear; $i++) {
        foreach ($costDetailsArray as $costDetail) {
            if ($costDetailsID && $costDetail['costDetailsID'] != $costDetailsID) continue;
            echo "<th>" . $costDetail['shortName'] . " / $i</th>";
        }
    }
    echo "</tr></thead>";
    echo "<tbody>";
    $count = sizeof($results);
    $currentCount = 1;
    foreach ($results as $result) {
        if ($result['resourceID'] != null) {
            echo "<tr>";
            echo '<td><a href="resource.php?resourceID=' . $result['resourceID'] . '">' . $result['titleText'] . "</a></td>";
            echo "<td>" . $result['resourceType'] . "</td>";
            echo "<td>" . $result['generalSubjects'] . "</td>";
            echo "<td>" . $result['detailedSubjects'] . "</td>";
            echo "<td>" . $result['acquisitionType'] . "</td>";
            echo "<td>" . $result['libraryNumber'] . "</td>";
            for ($i = $startYear; $i <= $endYear; $i++) {
                foreach ($costDetailsArray as $costDetail) {
                    if ($costDetailsID && $costDetail['costDetailsID'] != $costDetailsID) continue;
                    echo "<td>" . $result[$costDetail['shortName'] . " / $i"] . "</td>";
                }
            }
            echo "</tr>";
        } else {
            echo "<tr><td colspan='6'><b>";
            if ($currentCount == $count) { echo  _("Total"); } else { echo _("Sub-Total:") . " " . $result[$groupBy]; }
            echo "</b></td>";
            for ($i = $startYear; $i <= $endYear; $i++) {
                foreach ($costDetailsArray as $costDetail) {
                    if ($costDetailsID && $costDetail['costDetailsID'] != $costDetailsID) continue;
                    echo "<td><b>" . $result[$costDetail['shortName'] . " / $i"] . "</b></td>";
                }
            }
            echo "</tr></tfoot>";
        }
        $currentCount++;
    }
    echo "</tbody>";
    echo "</table>";

?>
