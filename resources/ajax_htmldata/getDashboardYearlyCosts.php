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
    $fundID = $_POST['fundID'];
    $organizationID = $_POST['organizationID'];
    $roleID = $_POST['roleID'];
    $groupBy = $_POST['groupBy'];

    $dashboard = new Dashboard();
    $query = $dashboard->getQueryYearlyCosts($resourceTypeID, $startYear, $endYear, $acquisitionTypeID, $orderTypeID, $subjectID, $costDetailsID, $fundID, $organizationID, $roleID, $groupBy);
    $results = $dashboard->getResults($query);
    if ($groupBy == "GS.shortName") $groupBy = "generalSubject";
    $costDetails = new CostDetails();
    $costDetailsArray = $costDetails->allAsArray();

    echo "<table id='dashboard_table' class='dataTable display cell-border' style='width:840px;margin-top:0'>";
    echo "<thead><tr>";
    echo "<th>" . _("Name") . "</th>";
    echo "<th>" . _("Resource Type") . "</th>";
    echo "<th>" . _("Subject") . "</th>";
    echo "<th>" . _("Acquisition Type") . "</th>";
    echo "<th>" . _("Fund") . "</th>";
    echo "<th>" . _("Library Number") . "</th>";
    echo "<th>" . _("Organizations") . "</th>";
    for ($i = $startYear; $i <= $endYear; $i++) {
        foreach ($costDetailsArray as $costDetail) {
            if (is_array($costDetailsID) && !in_array($costDetail['costDetailsID'], $costDetailsID)) continue;
            echo "<th>" . $costDetail['shortName'] . " / $i</th>";
        }
    }
    echo ("<th>" . _("All cost details") . "</th>");
    echo "</tr></thead>";
    echo "<tbody>";
    $count = sizeof($results);
    $currentCount = 1;
    foreach ($results as $result) {
        if ($result['resourceID'] != null) {
            echo "<tr>";
            echo '<td><a href="resource.php?resourceID=' . $result['resourceID'] . '">' . $result['titleText'] . "</a></td>";
            echo "<td>" . $result['resourceType'] . "</td>";
            $subject = $result['generalSubject'] && $result['detailedSubject'] ? 
                $result['generalSubject'] . " / " . $result['detailedSubject'] : 
                $result['generalSubject'] . $result['detailedSubject'];
            echo "<td>" . $subject . "</td>";
            echo "<td>" . $result['acquisitionType'] . "</td>";
            echo "<td>" . $result['fundName'] . "</td>";
            echo "<td>" . $result['libraryNumber'] . "</td>";
            echo "<td>" . $result['organizationName'] . "</td>";
            for ($i = $startYear; $i <= $endYear; $i++) {
                foreach ($costDetailsArray as $costDetail) {
                    if (is_array($costDetailsID) && !in_array($costDetail['costDetailsID'], $costDetailsID)) continue;
                    echo "<td>" . $result[$costDetail['shortName'] . " / $i"] . "</td>";
                }
            }
            echo ("<td>" . $result['costDetailsSum']. "</td>");
            echo "</tr>";
        } else {
            if ($currentCount == $count) {
                echo "<tr class='dashboard_total'><td><b>";
                echo  _("Total");
            } else {
                echo "<tr class='dashboard_subtotal'><td><b>";
                echo _("Sub-Total:") . " " . $result[$groupBy];
            }
            echo "</b></td><td></td><td></td><td></td><td></td><td></td><td></td>";
            for ($i = $startYear; $i <= $endYear; $i++) {
                foreach ($costDetailsArray as $costDetail) {
                    if ($costDetailsID) {
                        foreach ($costDetailsID as $costDetailID) {
                            if ($costDetailID && $costDetail['costDetailsID'] != $costDetailID) continue;
                            echo "<td><b>" . $result[$costDetail['shortName'] . " / $i"] . "</b></td>";
                        }
                    } else {
                        echo "<td><b>" . $result[$costDetail['shortName'] . " / $i"] . "</b></td>";
                    }
                }
            }
            echo "<td><b>" . $result['costDetailsSum'] . "</b></td>";
            echo "</tr></tfoot>";
        }
        $currentCount++;
    }
    echo "</tbody>";
    echo "</table>";

?>
