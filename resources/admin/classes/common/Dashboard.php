<?php
class Dashboard {

    public function getQuery($resourceTypeID, $year, $acquisitionTypeID, $orderTypeID, $subjectID, $costDetailsID, $groupBy) {
        $query = "SELECT
                        R.resourceID,
                        R.titleText,
                        RT.shortName AS resourceType,
                        AT.shortName AS acquisitionType,
                        OT.shortName AS orderType,
                        CD.shortName AS costDetails,
                        GS.shortName AS generalSubject,
                        DS.shortName AS detailedSubject,
                        RA.libraryNumber AS libraryNumber,
                        SUM(ROUND(COALESCE(RP.paymentAmount, 0) / 100, 2)) as paymentAmount
                        ";

        $query .= "
                 FROM Resource R
                    LEFT JOIN ResourceAcquisition RA ON RA.resourceID = R.resourceID
                    LEFT JOIN ResourcePayment RP ON RP.resourceAcquisitionID = RA.resourceAcquisitionID
                    LEFT JOIN ResourceType RT ON RT.resourceTypeID = R.resourceTypeID
                    LEFT JOIN AcquisitionType AT ON AT.acquisitionTypeID = RA.acquisitionTypeID
                    LEFT JOIN OrderType OT ON OT.orderTypeID = RP.orderTypeID
                    LEFT JOIN CostDetails CD ON CD.costDetailsID = RP.costDetailsID
                    LEFT JOIN ResourceSubject RS ON RS.resourceID = R.resourceID
                    LEFT JOIN GeneralDetailSubjectLink GDSL ON GDSL.generalDetailSubjectLinkID = RS.generalDetailSubjectLinkID
                    LEFT JOIN GeneralSubject GS ON GS.generalSubjectID = GDSL.generalSubjectID
                    LEFT JOIN DetailedSubject DS ON DS.detailedSubjectID = GDSL.detailedSubjectID
                ";

        $query .= " WHERE RP.year=$year";
        if (is_array($resourceTypeID))    $query .= " AND (R.resourceTypeID = " . join(" OR R.resourceTypeID = ", $resourceTypeID) . ")";
        if (is_array($acquisitionTypeID)) $query .= " AND (RA.acquisitionTypeID = " . join(" OR RA.acquisitionTypeID = ", $acquisitionTypeID) . ")";
        if (is_array($orderTypeID))       $query .= " AND (RP.orderTypeID = " . join(" OR RP.orderTypeID = ", $orderTypeID) . ")";
        if (is_array($costDetailsID))     $query .= " AND (RP.costDetailsID = " . join(" OR RP.costDetailsID = ", $costDetailsID) . ")";
        if (is_array($subjectID)) {
            foreach ($subjectID as $sid) {
                if (substr($sid, 0, 1) == "d") {
                    $query .= " AND GDSL.detailedSubjectID = " . substr($sid, 1);
                } else {
                    $query .= " AND GDSL.generalSubjectID = $sid";
                }
            }
        }
        $query .= " GROUP BY ";
        if ($groupBy != '') $query .= "$groupBy, ";
        $query .= "resourceID WITH ROLLUP";
        return $query;
    }

    public function getQueryYearlyCosts($resourceTypeID, $startYear, $endYear, $acquisitionTypeID, $orderTypeID, $subjectID, $costDetailsID, $groupBy) {
     $query = "SELECT
                        R.resourceID,
                        R.titleText,
                        RT.shortName AS resourceType,
                        AT.shortName AS acquisitionType,
                        CD.shortName AS costDetails,
                        GS.shortName AS generalSubject,
                        DS.shortName AS detailedSubject,
                        RA.libraryNumber AS libraryNumber
                        ";

        $costDetails = new CostDetails();
        $costDetailsArray = $costDetails->allAsArray();
        $sum_parts = array();
        for ($i = $startYear; $i <= $endYear; $i++) {
            foreach ($costDetailsArray as $costDetail) {

                if (is_array($costDetailsID) && !in_array($costDetail['costDetailsID'], $costDetailsID)) continue;

                $sum_query = " SUM(if(RP.year = $i";
                $sum_query .= " AND RP.costDetailsID = " . $costDetail['costDetailsID'];

                $sum_query .= ", ROUND(COALESCE(RP.paymentAmount, 0) / 100, 2), 0)) AS `" . $costDetail['shortName'] . " / $i`";
                $sum_parts[] = $sum_query;
            }
        }
        $query_sum = join(",", $sum_parts);
        if ($query_sum) $query .= "," . $query_sum;

        $query .= "
                 FROM Resource R
                    LEFT JOIN ResourceAcquisition RA ON RA.resourceID = R.resourceID
                    LEFT JOIN ResourcePayment RP ON RP.resourceAcquisitionID = RA.resourceAcquisitionID
                    LEFT JOIN ResourceType RT ON RT.resourceTypeID = R.resourceTypeID
                    LEFT JOIN AcquisitionType AT ON AT.acquisitionTypeID = RA.acquisitionTypeID
                    LEFT JOIN CostDetails CD ON CD.costDetailsID = RP.costDetailsID
                    LEFT JOIN ResourceSubject RS ON RS.resourceID = R.resourceID
                    LEFT JOIN GeneralDetailSubjectLink GDSL ON GDSL.generalDetailSubjectLinkID = RS.generalDetailSubjectLinkID
                    LEFT JOIN GeneralSubject GS ON GS.generalSubjectID = GDSL.generalSubjectID
                    LEFT JOIN DetailedSubject DS ON DS.detailedSubjectID = GDSL.detailedSubjectID
                ";

        $query_parts = array();
        if (is_array($resourceTypeID))    $query_parts[] = " (R.resourceTypeID = " . join(" OR R.resourceTypeID = ", $resourceTypeID) . ")";
        if (is_array($acquisitionTypeID)) $query_parts[] = " (RA.acquisitionTypeID = " . join(" OR RA.acquisitionTypeID = ", $acquisitionTypeID) . ")";
        if (is_array($orderTypeID))       $query_parts[] = " (RP.orderTypeID = " . join(" OR RP.orderTypeID = ", $orderTypeID) . ")";
        if (is_array($costDetailsID))     $query_parts[] = " (RP.costDetailsID = " . join(" OR RP.costDetailsID = ", $costDetailsID) . ")";
        if (is_array($subjectID)) {
            foreach ($subjectID as $sid) {
                if (substr($sid, 0, 1) == "d") {
                    $query_parts[] = " GDSL.detailedSubjectID = " . substr($sid, 1);
                } else {
                    $query_parts[] = " GDSL.generalSubjectID = $sid";
                }
            }
        }
        $query_where .= join(" AND ", $query_parts);
        if ($query_where) $query .= " WHERE " . $query_where;

        $query .= " GROUP BY ";
        if ($groupBy != '') $query .= "$groupBy, ";
        $query .= "resourceID WITH ROLLUP";
        return $query;
    }

    public function getResults($query) {
        $this->db = new DBService;
        $result = $this->db->processQuery($query, 'assoc');
        if (isset($result['resourceID'])) { $result = [$result]; }
        return $result;

    }

    public function displayExportParameters($resourceTypeID, $startYear, $endYear, $acquisitionTypeID, $orderTypeID, $subjectID, $costDetailsID, $groupBy) {
        $resourcesFilters = array();
        if (is_array($resourceTypeID)) {
            $resourceTypes = array_map(function ($a) { $o = new ResourceType(new NamedArguments(array('primaryKey' => $a))); return $o->shortName; }, $resourceTypeID);
            $resourceFilters[] = _("Resource Type(s)") . ": " . join(" / ", $resourceTypes);
        }
        if (is_array($subjectID)) {
            $subjects = array_map(function ($a) {
                if (substr($a, 0, 1) == "d") {
                    $subject = new DetailedSubject(new NamedArguments(array('primaryKey' => substr($a, 1))));
                } else {
                    $subject = new GeneralSubject(new NamedArguments(array('primaryKey' => $a)));
                }
                return $subject->shortName;
            }, $subjectID);
            $resourceFilters[] = _("Subject(s)") . ": " . join(" / ", $subjects);
        }
        if (is_array($acquisitionTypeID)) {
            $acquisitionTypes = array_map(function ($a) { $o = new AcquisitionType(new NamedArguments(array('primaryKey' => $a))); return $o->shortName; }, $acquisitionTypeID);
            $resourceFilters[] = _("Acquisition Type(s)") . ": " . join(" / ", $acquisitionTypes);
        }

        $paymentFilters = array();
        if (is_array($orderTypeID)) {
            $orderTypes = array_map(function ($a) { $o = new OrderType(new NamedArguments(array('primaryKey' => $a))); return $o->shortName; }, $orderTypeID);
            $paymentFilters[] = _("Order Type(s)") . ": " . join(" / ", $orderTypes);
        }
        if (is_array($costDetailsID)) {
            $costDetails = array_map(function ($a) { $o = new CostDetails(new NamedArguments(array('primaryKey' => $a))); return $o->shortName; }, $costDetailsID);
            $paymentFilters[] = _("Cost Details") . ": " . join(" / ", $costDetails);
        }

        echo _("Filters on resources") . ":\r\n";
        if (count($resourceFilters) > 0) {
            echo join(" / ", $resourceFilters) . "\r\n";
        } else {
            echo _("none") . "\r\n";
        }

        echo _("Filters on payments") . ":\r\n";
        if (count($paymentFilters) > 0) {
            echo join(" / ", $paymentFilters) . "\r\n";
        } else {
            echo _("none") . "\r\n";
        }

        if ($startYear && $endYear) {
            echo _("Start year") . ": $startYear\r\n";
            echo _("End year") . ": $endYear\r\n";
        } else {
            echo _("Year") . ": $startYear\r\n";
        }

        if ($groupBy) {
            echo _("Group by") . ": " . $groupBy . "\r\n";
        }

    }

    function getResourceTypesAsDropdown($currentID = null) {
        $display = array();
        $resourceType = new ResourceType();
        echo '<select multiple name="resourceTypeID[]" id="resourceTypeID" style="width:150px;">';
        foreach($resourceType->getAllResourceType() as $display) {
            if (isset($current) && $display['resourceTypeID'] == $current) {
                echo "<option value='" . $display['resourceTypeID'] . "' selected>" . $display['shortName'] . "</option>";
            } else {
                echo "<option value='" . $display['resourceTypeID'] . "'>" . $display['shortName'] . "</option>";
            }
        }
        echo '</select>';
    }

    function getAcquisitionTypesAsDropdown($currentID = null) {
        $display = array();
        $acquisitionType = new AcquisitionType();
        echo '<select multiple name="acquisitionTypeID[]" id="acquisitionTypeID" style="width:150px;">';
        foreach($acquisitionType->allAsArray() as $display) {
            if (isset($current) && $display['acquisitionTypeID'] == $current) {
                echo "<option value='" . $display['acquisitionTypeID'] . "' selected>" . $display['shortName'] . "</option>";
            } else {
                echo "<option value='" . $display['acquisitionTypeID'] . "'>" . $display['shortName'] . "</option>";
            }
        }
        echo '</select>';
    }

    function getOrderTypesAsDropdown($currentID = null) {
        $display = array();
        $orderType = new OrderType();
        echo '<select multiple name="orderTypeID[]" id="orderTypeID" style="width:150px;">';
        foreach($orderType->getAllOrderType() as $display) {
            if (isset($current) && $display['orderTypeID'] == $current) {
                echo "<option value='" . $display['orderTypeID'] . "' selected>" . $display['shortName'] . "</option>";
            } else {
                echo "<option value='" . $display['orderTypeID'] . "'>" . $display['shortName'] . "</option>";
            }
        }
        echo '</select>';
    }

    function getSubjectsAsDropdown($currentID = null) {
        $generalSubject = new GeneralSubject();
        $generalSubjectArray = $generalSubject->allAsArray();

        $detailedSubject = new DetailedSubject();
        $detailedSubjectArray = $detailedSubject->allAsArray();

        echo '<select multiple name="subjectID[]" id="subjectID" style="width:150px;">';
        foreach($generalSubjectArray as $ug) {
            $generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $ug['generalSubjectID'])));
            echo "<option value='" . $ug['generalSubjectID'] . "'>" . $ug['shortName'] . "</option>";
            foreach ($generalSubject->getDetailedSubjects() as $ds){
                echo "<option value='d" . $ds->detailedSubjectID . "'> -- " . $ds->shortName . "</option>";
            }
        }
        echo '</select>';
    }

    function getCostDetailsAsDropdown($currentID = null) {
        $display = array();
        $costDetails = new CostDetails();
        echo '<select multiple name="costDetailsID[]" id="costDetailsID" style="width:150px;">';
        foreach($costDetails->allAsArray() as $display) {
            if ($display['costDetailsID'] == $current) {
                echo "<option value='" . $display['costDetailsID'] . "' selected>" . $display['shortName'] . "</option>";
            } else {
                echo "<option value='" . $display['costDetailsID'] . "'>" . $display['shortName'] . "</option>";
            }
        }
        echo '</select>';

    }



}
?>
