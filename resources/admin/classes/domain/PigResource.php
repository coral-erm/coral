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

class PigResource extends Resource {

    public static function getPigSearchDetails() {
        // mysqli::real_escape_string() requires a connected database. DBService provides a connected database on when getInstance() is called.
        // $this->db cannot be accessed because this is a static function.
        $db = DBService::getInstance();

        $search = Resource::getSearch();

        $whereAdd = array();
        $searchDisplay = array();
        $config = new Configuration();


        //if name is passed in also search alias, organizations and organization aliases
        if ($search['name']) {
            $nameQueryString = $db->escapeString(strtoupper($search['name']));
            $nameQueryString = preg_replace("/ +/", "%", $nameQueryString);
            $nameQueryString = "'%" . $nameQueryString . "%'";

            if ($config->settings->organizationsModule == 'Y'){
                $dbName = $config->settings->organizationsDatabaseName;

                $whereAdd[] = "((UPPER(R.titleText) LIKE " . $nameQueryString . ") OR (UPPER(A.shortName) LIKE " . $nameQueryString . ") OR (UPPER(O.name) LIKE " . $nameQueryString . ") OR (UPPER(OA.name) LIKE " . $nameQueryString . ") OR (UPPER(RP.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RC.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RA.recordSetIdentifier) LIKE " . $nameQueryString . "))";

            }else{

                $whereAdd[] = "((UPPER(R.titleText) LIKE " . $nameQueryString . ") OR (UPPER(A.shortName) LIKE " . $nameQueryString . ") OR (UPPER(O.shortName) LIKE " . $nameQueryString . ") OR (UPPER(RP.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RC.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RA.recordSetIdentifier) LIKE " . $nameQueryString . "))";
            }

            $searchDisplay[] = "Name contains: " . $search['name'];
        }

        //if descriptionText is passed
        if ($search['descriptionText']) {
            $descriptionQueryString = $db->escapeString(strtoupper($search['descriptionText']));
            $descriptionQueryString = preg_replace("/ +/", "%", $descriptionQueryString);
            $descriptionQueryString = "'%" . $descriptionQueryString . "%'";
            $whereAdd[] = "(UPPER(R.descriptionText) LIKE " . $descriptionQueryString . ")";
            $searchDisplay[] = "Description contains: " . $search['descriptionText'];
        }

        //if providerText is passed
        if ($search['providerText']) {
            $providerQueryString = $db->escapeString(strtoupper($search['providerText']));
            $providerQueryString = preg_replace("/ +/", "%", $providerQueryString);
            $providerQueryString = "'%" . $providerQueryString . "%'";
            $whereAdd[] = "(UPPER(R.providerText) LIKE " . $providerQueryString . ")";
            $searchDisplay[] = "Provider contains: " . $search['providerText'];
        }


        //get where statements together (and escape single quotes)
        if (isset($search['resourceID']) && is_numeric($search['resourceID'])) {
            $whereAdd[] = "R.resourceID = " . (int) $search['resourceID'];
            $searchDisplay[] = "Resource ID: " . $search['resourceID'];
        }
        if ($search['resourceISBNOrISSN']) {
            $resourceISBNOrISSN = $db->escapeString(str_replace("-","",$search['resourceISBNOrISSN']));
            $whereAdd[] = "REPLACE(R.isbnOrISSN,'-','') = '" . $resourceISBNOrISSN . "'";
            $searchDisplay[] = "ISSN/ISBN: " . $search['resourceISBNOrISSN'];
        }
        if ($search['fund']) {
            $fund = $db->escapeString(str_replace("-","",$search['fund']));
            $whereAdd[] = "REPLACE(RPAY.fundName,'-','') = '" . $fund . "'";
            $searchDisplay[] = "Fund: " . $search['fund'];
        }

        if ($search['stepName']) {
            $status = new Status();
            $completedStatusID = $status->getIDFromName('complete');
            $whereAdd[] = "(R.statusID != $completedStatusID AND RS.stepName = '" . $db->escapeString($search['stepName']) . "' AND RS.stepStartDate IS NOT NULL AND RS.stepEndDate IS NULL)";
            $searchDisplay[] = "Routing Step: " . $search['stepName'];
        }

        // Return all results except the records with this statusID
        if (isset($search['statusID']) && is_numeric($search['statusID'])) {
            $whereAdd[] = "R.statusID <> " . (int) $search['statusID'];
            $status = new Status(new NamedArguments(array('primaryKey' => $search['statusID'])));
            $searchDisplay[] = "Status: " . $status->shortName;
        }

        if (isset($search['creatorLoginID']) && is_numeric($search['creatorLoginID'])) {
            $whereAdd[] = "R.createLoginID = " . (int) $search['creatorLoginID'];

            $createUser = new User(new NamedArguments(array('primaryKey' => $search['creatorLoginID'])));
            if ($createUser->firstName){
                $name = $createUser->lastName . ", " . $createUser->firstName;
            }else{
                $name = $createUser->loginID;
            }
            $searchDisplay[] = "Creator: " . $name;
        }

        if (isset($search['resourceFormatID']) && is_numeric($search['resourceFormatID'])) {
            $whereAdd[] = "R.resourceFormatID = " . (int) $search['resourceFormatID'];
            $resourceFormat = new ResourceFormat(new NamedArguments(array('primaryKey' => $search['resourceFormatID'])));
            $searchDisplay[] = "Resource Format: " . $resourceFormat->shortName;
        }

        if (isset($search['acquisitionTypeID']) && is_numeric($search['acquisitionTypeID'])) {
            $whereAdd[] = "RA.acquisitionTypeID = " . (int) $search['acquisitionTypeID'];
            $acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $search['acquisitionTypeID'])));
            $searchDisplay[] = "Acquisition Type: " . $acquisitionType->shortName;
        }


        if ($search['resourceNote']) {
            $whereAdd[] = "UPPER(RN.noteText) LIKE UPPER('%" . $db->escapeString($search['resourceNote']) . "%')";
            $searchDisplay[] = "Note contains: " . $search['resourceNote'];
        }

        if ($search['createDateStart']) {
            $whereAdd[] = "R.createDate >= STR_TO_DATE('" . $db->escapeString($search['createDateStart']) . "','%m/%d/%Y')";
            if (!$search['createDateEnd']) {
                $searchDisplay[] = "Created on or after: " . $search['createDateStart'];
            } else {
              $searchDisplay[] = "Created between: " . $search['createDateStart'] . " and " . $search['createDateEnd'];
            }
        }

        if ($search['createDateEnd']) {
            $whereAdd[] = "R.createDate <= STR_TO_DATE('" . $db->escapeString($search['createDateEnd']) . "','%m/%d/%Y')";
            if (!$search['createDateStart']) {
                  $searchDisplay[] = "Created on or before: " . $search['createDateEnd'];
            }
        }

        if ($search['startWith']) {
            $whereAdd[] = "TRIM(LEADING 'THE ' FROM UPPER(R.titleText)) LIKE UPPER('" . $db->escapeString($search['startWith']) . "%')";
            $searchDisplay[] = "Starts with: " . $search['startWith'];
        }

        //the following are not-required fields with dropdowns and have "none" as an option
        if ($search['resourceTypeID'] == 'none'){
            $whereAdd[] = "((R.resourceTypeID IS NULL) OR (R.resourceTypeID = '0'))";
            $searchDisplay[] = "Resource Type: none";
        }else if (is_numeric($search['resourceTypeID'])){
            $whereAdd[] = "R.resourceTypeID = " . (int) $search['resourceTypeID'];
            $resourceType = new ResourceType(new NamedArguments(array('primaryKey' => $search['resourceTypeID'])));
            $searchDisplay[] = "Resource Type: " . $resourceType->shortName;
        }


        if ($search['generalSubjectID'] == 'none'){
            $whereAdd[] = "((GDLINK.generalSubjectID IS NULL) OR (GDLINK.generalSubjectID = '0'))";
            $searchDisplay[] = "Resource Type: none";
        }else if (isset($search['generalSubjectID']) && is_numeric($search['generalSubjectID'])){
            $whereAdd[] = "GDLINK.generalSubjectID = " . (int) $search['generalSubjectID'];
            $generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $search['generalSubjectID'])));
            $searchDisplay[] = "General Subject: " . $generalSubject->shortName;
        }

        if ($search['detailedSubjectID'] == 'none'){
            $whereAdd[] = "((GDLINK.detailedSubjectID IS NULL) OR (GDLINK.detailedSubjectID = '0') OR (GDLINK.detailedSubjectID = '-1'))";
            $searchDisplay[] = "Resource Type: none";
        }else if (isset($search['detailedSubjectID']) && is_numeric($search['detailedSubjectID'])){
            $whereAdd[] = "GDLINK.detailedSubjectID = " . (int) $search['detailedSubjectID'];
            $detailedSubject = new DetailedSubject(new NamedArguments(array('primaryKey' => $search['detailedSubjectID'])));
            $searchDisplay[] = "Detailed Subject: " . $detailedSubject->shortName;
        }

        if ($search['noteTypeID'] == 'none'){
            $whereAdd[] = "(RN.noteTypeID IS NULL) AND (RN.noteText IS NOT NULL)";
            $searchDisplay[] = "Note Type: none";
        }else if (isset($search['noteTypeID']) && is_numeric($search['noteTypeID'])){
            $whereAdd[] = "RN.noteTypeID = " . (int) $search['noteTypeID'];
            $noteType = new NoteType(new NamedArguments(array('primaryKey' => $search['noteTypeID'])));
            $searchDisplay[] = "Note Type: " . $noteType->shortName;
        }


        if ($search['purchaseSiteID'] == 'none'){
            $whereAdd[] = "RPSL.purchaseSiteID IS NULL";
            $searchDisplay[] = "Purchase Site: none";
        }else if (isset($search['purchaseSiteID']) && is_numeric($search['purchaseSiteID'])){
            $whereAdd[] = "RPSL.purchaseSiteID = " . (int) $search['purchaseSiteID'];
            $purchaseSite = new PurchaseSite(new NamedArguments(array('primaryKey' => $search['purchaseSiteID'])));
            $searchDisplay[] = "Purchase Site: " . $purchaseSite->shortName;
        }


        if ($search['authorizedSiteID'] == 'none'){
            $whereAdd[] = "RAUSL.authorizedSiteID IS NULL";
            $searchDisplay[] = "Authorized Site: none";
        }else if (isset($search['authorizedSiteID']) && is_numeric($search['authorizedSiteID'])){
            $whereAdd[] = "RAUSL.authorizedSiteID = " . (int) $search['authorizedSiteID'];
            $authorizedSite = new AuthorizedSite(new NamedArguments(array('primaryKey' => $search['authorizedSiteID'])));
            $searchDisplay[] = "Authorized Site: " . $authorizedSite->shortName;
        }


        if ($search['administeringSiteID'] == 'none'){
            $whereAdd[] = "RADSL.administeringSiteID IS NULL";
            $searchDisplay[] = "Administering Site: none";
        }else if ($search['administeringSiteID']){
            $whereAdd[] = "RADSL.administeringSiteID = " . (int) $search['administeringSiteID'];
            $administeringSite = new AdministeringSite(new NamedArguments(array('primaryKey' => $search['administeringSiteID'])));
            $searchDisplay[] = "Administering Site: " . $administeringSite->shortName;
        }


        if ($search['authenticationTypeID'] == 'none'){
            $whereAdd[] = "R.authenticationTypeID IS NULL";
            $searchDisplay[] = "Authentication Type: none";
        }else if ($search['authenticationTypeID']){
            $whereAdd[] = "R.authenticationTypeID = " . (int) $search['authenticationTypeID'];
            $authenticationType = new AuthenticationType(new NamedArguments(array('primaryKey' => $search['authenticationTypeID'])));
            $searchDisplay[] = "Authentication Type: " . $authenticationType->shortName;
        }

        if ($search['catalogingStatusID'] == 'none') {
            $whereAdd[] = "(R.catalogingStatusID IS NULL)";
            $searchDisplay[] = "Cataloging Status: none";
        } else if ($search['catalogingStatusID']) {
            $whereAdd[] = "R.catalogingStatusID = " . (int) $search['catalogingStatusID'];
            $catalogingStatus = new CatalogingStatus(new NamedArguments(array('primaryKey' => $search['catalogingStatusID'])));
            $searchDisplay[] = "Cataloging Status: " . $catalogingStatus->shortName;
        }

        $orderBy = $search['orderBy'];


        $page = $search['page'];
        $recordsPerPage = $search['recordsPerPage'];

        return array("where" => $whereAdd, "page" => $page, "order" => $orderBy, "perPage" => $recordsPerPage, "display" => $searchDisplay);
    }

    public function searchPigQuery($whereAdd, $orderBy = '', $limit = '', $count = false) {
        $config = new Configuration();
        $status = new Status();

        if ($config->settings->organizationsModule == 'Y'){
            $dbName = $config->settings->organizationsDatabaseName;

            $orgJoinAdd = "LEFT JOIN " . $dbName . ".Organization O ON O.organizationID = ROL.organizationID
                           LEFT JOIN " . $dbName . ".Alias OA ON OA.organizationID = ROL.organizationID";

        }else{
            $orgJoinAdd = "LEFT JOIN Organization O ON O.organizationID = ROL.organizationID";
        }

        $savedStatusID = intval($status->getIDFromName('saved'));
        //also add to not retrieve saved records
        $whereAdd[] = "R.statusID != " . $savedStatusID;

        if (is_array($whereAdd) && count($whereAdd) > 0) {
            $whereStatement = " WHERE " . implode(" AND ", $whereAdd);
        }else{
            $whereStatement = "";
        }

        if ($count) {
            $select = "SELECT COUNT(DISTINCT R.resourceID) count";
            $groupBy = "";
        } else {
            $select = "SELECT R.resourceID, RT.shortName resourceTypeID, R.titleText, AT.shortName acquisitionType, R.createLoginID, CU.firstName, CU.lastName, R.createDate, R.providerText, R.descriptionText, R.resourceURL, S.shortName status,
                              GROUP_CONCAT(DISTINCT A.shortName ORDER BY A.shortName DESC SEPARATOR '<br />') aliases";
            $groupBy = "GROUP BY R.resourceID";
        }

    $referenced_tables = array();

    $table_matches = array();

    // Build a list of tables that are referenced by the select and where statements in order to limit the number of joins performed in the search.
    preg_match_all("/[A-Z]+(?=[.][A-Z]+)/i", $select, $table_matches);
    $referenced_tables = array_unique($table_matches[0]);

    preg_match_all("/[A-Z]+(?=[.][A-Z]+)/i", $whereStatement, $table_matches);
    $referenced_tables = array_unique(array_merge($referenced_tables, $table_matches[0]));

    // These join statements will only be included in the query if the alias is referenced by the select and/or where.
    $conditional_joins = explode("\n", "LEFT JOIN ResourceFormat RF ON R.resourceFormatID = RF.resourceFormatID
                                    LEFT JOIN ResourceType RT ON R.resourceTypeID = RT.resourceTypeID
                                    LEFT JOIN AcquisitionType AT ON RA.acquisitionTypeID = AT.acquisitionTypeID
                                    LEFT JOIN Status S ON R.statusID = S.statusID
                                    LEFT JOIN User CU ON R.createLoginID = CU.loginID
                                    LEFT JOIN ResourcePurchaseSiteLink RPSL ON R.resourceID = RPSL.resourceID
                                    LEFT JOIN ResourceAuthorizedSiteLink RAUSL ON R.resourceID = RAUSL.resourceID
                                    LEFT JOIN ResourceAdministeringSiteLink RADSL ON R.resourceID = RADSL.resourceID
                                    LEFT JOIN ResourcePayment RPAY ON R.resourceID = RPAY.resourceID
                                    LEFT JOIN ResourceNote RN ON R.resourceID = RN.resourceID
                                    LEFT JOIN ResourceStep RS ON R.resourceID = RS.resourceID");

        $additional_joins = array();

        foreach($conditional_joins as $join) {
            $match = array();
            preg_match("/[A-Z]+(?= ON )/i", $join, $match);
            $table_name = $match[0];
            if (in_array($table_name, $referenced_tables)) {
        $additional_joins[] = $join;
      }
        }

        $query = $select . "
                            FROM Resource R
                                LEFT JOIN Alias A ON R.resourceID = A.resourceID
                                LEFT JOIN ResourceAcquisition RA ON R.resourceID = RA.resourceID
                                LEFT JOIN ResourceOrganizationLink ROL ON R.resourceID = ROL.resourceID
                                " . $orgJoinAdd . "
                                LEFT JOIN ResourceRelationship RRC ON RRC.relatedResourceID = R.resourceID
                                LEFT JOIN ResourceRelationship RRP ON RRP.resourceID = R.resourceID
                                LEFT JOIN ResourceSubject RSUB ON R.resourceID = RSUB.resourceID
                                LEFT JOIN Resource RC ON RC.resourceID = RRC.resourceID
                                LEFT JOIN Resource RP ON RP.resourceID = RRP.relatedResourceID
                                LEFT JOIN GeneralDetailSubjectLink GDLINK ON RSUB.generalDetailSubjectLinkID = GDLINK.generalDetailSubjectLinkID
                  " . implode("\n", $additional_joins) . "
                                  " . $whereStatement . "
                                  " . $groupBy;

        if ($orderBy) {
            $query .= "\nORDER BY " . $orderBy;
        }

        if ($limit) {
            $query .= "\nLIMIT " . $limit;
        }

        return $query;
    }

    //returns array based on search
    public function searchPig($whereAdd, $orderBy, $limit){
        $query = $this->searchPigQuery($whereAdd, $orderBy, $limit, false);

        $result = $this->db->processQuery($query, 'assoc');

        $searchArray = array();
        $resultArray = array();

        //need to do this since it could be that there's only one result and this is how the dbservice returns result
        if (isset($result['resourceID'])){

            foreach (array_keys($result) as $attributeName) {
                $resultArray[$attributeName] = $result[$attributeName];
            }

            array_push($searchArray, $resultArray);
        }else{
            foreach ($result as $row) {
                $resultArray = array();
                foreach (array_keys($row) as $attributeName) {
                    $resultArray[$attributeName] = $row[$attributeName];
                }
                array_push($searchArray, $resultArray);
            }
        }

        return $searchArray;
    }

    public function searchPigCount($whereAdd) {
        $query = $this->searchPigQuery($whereAdd, '', '', true);
        $result = $this->db->processQuery($query, 'assoc');

        //echo $query;

        return $result['count'];
    }
}
