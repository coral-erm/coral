<?php

    include_once 'directory.php';


    $year = $_POST['year'];
    if (!$year) $year = date('Y');
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
    $query = $dashboard->getQuery($resourceTypeID, $year, $acquisitionTypeID, $orderTypeID, $subjectID, $costDetailsID, $fundID, $organizationID, $roleID, $groupBy);
    $results = $dashboard->getResults($query);
    if ($groupBy == "GS.shortName") $groupBy = "generalSubject";
    echo "<table id='dashboard_table' class='dataTable display cell-border' style='width:840px;margin-top:0'>";
    echo "<thead><tr>";
    echo "<th>" . _("Name") . "</th>";
    echo "<th>" . _("Resource Type") . "</th>";
    echo "<th>" . _("Subject") . "</th>";
    echo "<th>" . _("Acquisition Type") . "</th>";
    echo "<th>" . _("Fund") . "</th>";
    echo "<th>" . _("Library number") . "</th>";
    echo "<th>" . _("Organizations") . "</th>";
    echo "<th>" . _("Payment amount") . "</th>";
    echo "</tr></thead>";
    echo "<tbody>";
    $count = sizeof($results);
    $i = 1;
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
            echo "<td>" . $result['paymentAmount'] . "</td>";
            echo "</tr>";
        } else {
            if ($i == $count) {
                echo "<tr class='dashboard_total'><td><b>";
                echo  _("Total");
            } else {
                echo "<tr class='dashboard_subtotal'><td><b>";
                echo _("Sub-Total:") . " " . $result[$groupBy];
            }
            echo "</b></td><td></td><td></td><td></td><td></td><td></td><td></td>";
            echo "<td><b>" . $result['paymentAmount']  . "</b></td>";
            echo "</tr>";
        }
    $i++;
    }
    echo "</tbody>";
    echo "</table>";

?>
