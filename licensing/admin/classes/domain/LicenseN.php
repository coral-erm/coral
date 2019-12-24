<?php

/*
**************************************************************************************************************************
** CORAL Licenses Module v. 1.2
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

class LicenseN extends DatabaseObject {


  protected function overridePrimaryKeyName() {}


    
  public static function setSearch($search) {
  $config = new Configuration;
  echo var_dump($search);
    echo "we get into setSeach at some point$ ".$config->settings->defaultsort."$\n";
    if ($config->settings->defaultsort) {
      $orderBy = $config->settings->defaultsort;
    } else {
      $orderBy = "R.createDate DESC, TRIM(LEADING 'THE ' FROM UPPER(R.shortName)) asc";
    }
    $defaultSearchParameters = array(
    "orderBy" => $orderBy,
    "page" => 1,
    "recordsPerPage" => 25,
    );
    foreach ($defaultSearchParameters as $key => $value) {
      if (!isset($search[$key])) {
        $search[$key] = $value;
      }
    }
    foreach ($search as $key => $value) {
      $search[$key] = trim($value);
    }
    CoralSession::set('licenseSearch', $search);
  }



  public static function resetSearch() {
    LicenseN::setSearch(array());
  }



  public static function getSearch() {
    if (!CoralSession::get('licenseSearch')) {
      LicenseN::resetSearch();
    }
    return CoralSession::get('licenseSearch');
  }



  public static function getSearchDetails() {
    // A successful mysqli_connect must be run before mysqli_real_escape_string will function.  Instantiating a license model will set up the connection
    $license = new LicenseN();

    $search = LicenseN::getSearch();

    $whereAdd = array();
    $searchDisplay = array();
    $config = new Configuration();


    //if name is passed in also search alias, organizations and organization aliases
    if ($search['shortName']) {
      $nameQueryString = $license->db->escapeString(strtoupper($search['shortName']));
      $nameQueryString = preg_replace("/ +/", "%", $nameQueryString);
      $nameQueryString = "'%" . $nameQueryString . "%'";

      if ($config->settings->organizationsModule == 'Y') {
        $dbName = $config->settings->organizationsDatabaseName;
        echo "what is this: ".$dbName."\n";
        $whereAdd[] = "((UPPER(R.shortName) LIKE " . $nameQueryString . "))"; // OR (UPPER(A.shortName) LIKE " . $nameQueryString . ") OR (UPPER(O.name) LIKE " . $nameQueryString . ") OR (UPPER(OA.name) LIKE " . $nameQueryString . ") OR (UPPER(RP.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RC.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RA.recordSetIdentifier) LIKE " . $nameQueryString . "))";

      }else{

        $whereAdd[] = "((UPPER(R.shortName) LIKE " . $nameQueryString . "))";// OR (UPPER(A.shortName) LIKE " . $nameQueryString . ") OR (UPPER(O.shortName) LIKE " . $nameQueryString . ") OR (UPPER(RP.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RC.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RA.recordSetIdentifier) LIKE " . $nameQueryString . "))";

      }
      $searchDisplay[] = _("Name contains: ") . $search['shortName'];
    }

    //get where statements together (and escape single quotes)

    if ($search['stepName']) {
      $status = new Status();
      $completedStatusID = $status->getIDFromName('complete');
      $whereAdd[] = "(R.statusID != $completedStatusID AND RS.stepName = '" . $license->db->escapeString($search['stepName']) . "' AND RS.stepStartDate IS NOT NULL AND RS.stepEndDate IS NULL)";
      $searchDisplay[] = _("Workflow Step: ") . $search['stepName'];
    }




    if ($search['statusID']) {
      $whereAdd[] = "R.statusID = '" . $license->db->escapeString($search['statusID']) . "'";
      $status = new Status(new NamedArguments(array('primaryKey' => $search['statusID'])));
      $searchDisplay[] = _("Status: ") . $status->shortName;
    }

    if ($search['creatorLoginID']) {
      $whereAdd[] = "R.createLoginID = '" . $license->db->escapeString($search['creatorLoginID']) . "'";

      $createUser = new User(new NamedArguments(array('primaryKey' => $search['creatorLoginID'])));
      if ($createUser->firstName) {
        $name = $createUser->lastName . ", " . $createUser->firstName;
      }else{
        $name = $createUser->loginID;
      }
      $searchDisplay[] = _("Creator: ") . $name;
    }

    if ($search['licenseFormatID']) {
      $whereAdd[] = "R.licenseFormatID = '" . $license->db->escapeString($search['licenseFormatID']) . "'";
      $licenseFormat = new LicenseFormat(new NamedArguments(array('primaryKey' => $search['licenseFormatID'])));
      $searchDisplay[] = _("License Format: ") . $licenseFormat->shortName;
    }

    if ($search['acquisitionTypeID']) {
      $whereAdd[] = "RA.acquisitionTypeID = '" . $license->db->escapeString($search['acquisitionTypeID']) . "'";
      $acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $search['acquisitionTypeID'])));
      $searchDisplay[] = _("Acquisition Type: ") . $acquisitionType->shortName;
    }


    if ($search['licenseNote']) {
      $whereAdd[] = "(UPPER(RNA.noteText) LIKE UPPER('%" . $license->db->escapeString($search['licenseNote']) . "%') AND RNA.tabName <> 'Product') OR (UPPER(RNR.noteText) LIKE UPPER('%" . $license->db->escapeString($search['licenseNote']) . "%') AND RNR.tabName = 'Product')";
      $searchDisplay[] = _("Note contains: ") . $search['licenseNote'];
    }

    if ($search['createDateStart']) {
      $whereAdd[] = "R.createDate >= STR_TO_DATE('" . $license->db->escapeString($search['createDateStart']) . "','%m/%d/%Y')";
      if (!$search['createDateEnd']) {
        $searchDisplay[] = _("Created on or after: ") . $search['createDateStart'];
      } else {
        $searchDisplay[] = _("Created between: ") . $search['createDateStart'] . " and " . $search['createDateEnd'];
      }
    }

    if ($search['createDateEnd']) {
      $whereAdd[] = "R.createDate <= STR_TO_DATE('" . $license->db->escapeString($search['createDateEnd']) . "','%m/%d/%Y')";
      if (!$search['createDateStart']) {
        $searchDisplay[] = _("Created on or before: ") . $search['createDateEnd'];
      }
    }

    if ($search['startWith']) {
      $whereAdd[] = "TRIM(LEADING 'THE ' FROM UPPER(R.shortName)) LIKE UPPER('" . $license->db->escapeString($search['startWith']) . "%')";
      $searchDisplay[] = _("Starts with: ") . $search['startWith'];
    }

    //the following are not-required fields with dropdowns and have "none" as an option
    $orgID = $_SESSION['licenseSearch']['organizationID'];
    if ($orgID == 'none') {
      $whereAdd[] = "((R.organizationID IS NULL) OR (R.organizationID = '0'))";
      $searchDisplay[] = _("Organization: none");
    }else if ($orgID) {
      $whereAdd[] = "R.organizationID = '" . $license->db->escapeString($orgID) . "'";    
      $organizationType = new Organization(new NamedArguments(array('primaryKey' => $orgID)));
      $searchDisplay[] = _("Organization: ") . $organizationType->name;
      
    }

    $conID = $_SESSION['licenseSearch']['consortiumID'];
    if ($conID == 'none') {
      $whereAdd[] = "((R.consortiumID IS NULL) OR (R.consortiumID = '0'))";
      $searchDisplay[] = _("Consortium: none");
    }else if ($conID) {
      $whereAdd[] = "R.consortiumID = '" . $license->db->escapeString($conID) . "'";
      $generalSubject = new Consortium(new NamedArguments(array('primaryKey' => $conID)));
      $searchDisplay[] = _("Consortium: ") . $generalSubject->shortName;
    }

    $staID = $_SESSION['licenseSearch']['statusID'];
    if ($staID == 'none') {
      $whereAdd[] = "((R.statusID IS NULL) OR (R.statusID = '0'))";
      $searchDisplay[] = _("Status: none");
    }else if ($staID) {
      $whereAdd[] = "R.statusID = '" . $license->db->escapeString($staID) . "'";
      $generalSubject = new Status(new NamedArguments(array('primaryKey' => $staID)));
      $searchDisplay[] = _("Status: ") . $generalSubject->shortName;
    }

    $docID = $_SESSION['licenseSearch']['documentTypeID'];
    if ($docID == 'none') {
      $whereAdd[] = "((D.documentTypeID IS NULL) OR (D.documentTypeID = '0'))";
      $searchDisplay[] = _("Document: none");
    }else if ($docID) {
      $whereAdd[] = "D.documentTypeID = '" . $license->db->escapeString($docID) . "'";
      $generalSubject = new Consortium(new NamedArguments(array('primaryKey' => $docID)));
      $searchDisplay[] = _("Document: ") . $generalSubject->shortName;
    }

    $expID = $_SESSION['licenseSearch']['expressionTypeID'];
    if ($expID == 'none') {
      $whereAdd[] = "((E.expressionTypeID IS NULL) OR (E.expressionTypeID = '0'))";
      $searchDisplay[] = _("Expression: none");
    }else if ($expID) {
      $whereAdd[] = "E.expressionTypeID = '" . $license->db->escapeString($expID) . "'";
      $generalSubject = new ExpresionType(new NamedArguments(array('primaryKey' => $expID)));
      $searchDisplay[] = _("Expression Type: ") . $generalSubject->shortName;
    }

    $quID = $_SESSION['licenseSearch']['qualifierID'];
    if ($quID == 'none') {
      $whereAdd[] = "((Q.qualifierID IS NULL) OR (Q.qualifierID = '0'))";
      $searchDisplay[] = _("QualifierID: none");
    }else if ($quID) {
      $whereAdd[] = "Q.qualifierID = '" . $license->db->escapeString($quID) . "'";
      $generalSubject = new ExpresionType(new NamedArguments(array('primaryKey' => $quID)));
      $searchDisplay[] = _("Qualifier: ") . $generalSubject->shortName;
    }


    $orderBy = $search['orderBy'];


    $page = $search['page'];
    $recordsPerPage = $search['recordsPerPage'];
    return array("where" => $whereAdd, "page" => $page, "order" => $orderBy, "perPage" => $recordsPerPage, "display" => $searchDisplay);
  }



  public function searchQuery($whereAdd, $orderBy = '', $limit = '', $count = false) {
    $config = new Configuration();
    $status = new Status();
    if ($config->settings->organizationsModule == 'Y') {
      $dbName = $config->settings->organizationsDatabaseName;

      $orgJoinAdd = "
        
        LEFT JOIN $dbName.Organization O ON O.organizationID = R.organizationID";
    }else{
      $orgJoinAdd = "
        LEFT JOIN Organization O ON O.organizationID = R.organizationID";
    }

    $savedStatusID = intval($status->getIDFromName('saved'));
    //also add to not retrieve saved records
    $whereAdd[] = "R.statusID != " . $savedStatusID;

    if (count($whereAdd) > 0) {
      $whereStatement = "WHERE\n  " . implode(" AND ", $whereAdd);
    }else{
      $whereStatement = "";
    }

    if ($count) {
      $select = "SELECT COUNT(DISTINCT R.licenseID) count";
      $groupBy = "";
    } else {
      $select = "SELECT
                  R.licenseID,
                  R.shortName,
                  GROUP_CONCAT(DISTINCT AT.shortName SEPARATOR ' / ') as acquisitionType,
                  R.createLoginID,
                  CU.firstName,
                  CU.lastName,
                  R.createDate,
                  S.shortName status,
                  GROUP_CONCAT(DISTINCT A.shortName, I.isbnOrIssn ORDER BY A.shortName DESC SEPARATOR '<br />') aliases";
      $groupBy = "GROUP BY R.licenseID";
    }

    $referenced_tables = array();

    $table_matches = array();

    // Build a list of tables that are referenced by the select and where statements in order to limit the number of joins performed in the search.
    $table_matching_regex = "/\b[A-Z]+(?=[.][A-Z]+)/iu";
    preg_match_all($table_matching_regex, $select, $table_matches);
    $referenced_tables_select = array_unique($table_matches[0]);

    preg_match_all($table_matching_regex, $whereStatement, $table_matches);
    $referenced_tables = array_unique(array_merge($referenced_tables_select, $table_matches[0]));

    // These join statements will only be included in the query if the alias is referenced by the select and/or where.
    $conditional_joins = explode("\n", "LEFT JOIN LicenseFormat RF ON R.licenseFormatID = RF.licenseFormatID
                  LEFT JOIN LicenseType RT ON R.licenseTypeID = RT.licenseTypeID
                  LEFT JOIN AcquisitionType AT ON RA.acquisitionTypeID = AT.acquisitionTypeID
                  LEFT JOIN Status S ON R.statusID = S.statusID
                  LEFT JOIN User CU ON R.createLoginID = CU.loginID
                  LEFT JOIN LicensePurchaseSiteLink RPSL ON RA.licenseAcquisitionID = RPSL.licenseAcquisitionID
                  LEFT JOIN LicenseAuthorizedSiteLink RAUSL ON RA.licenseAcquisitionID = RAUSL.licenseAcquisitionID
                  LEFT JOIN LicenseAdministeringSiteLink RADSL ON RA.licenseAcquisitionID = RADSL.licenseAcquisitionID
                  LEFT JOIN LicenseNote RNA ON RA.licenseAcquisitionID = RNA.entityID
                  LEFT JOIN LicenseNote RNR ON R.licenseID = RNR.entityID
                  LEFT JOIN LicensePayment RPAY ON RA.licenseAcquisitionID = RPAY.licenseAcquisitionID
                  LEFT JOIN LicenseStep RS ON RA.licenseAcquisitionID = RS.licenseAcquisitionID
                  LEFT JOIN IsbnOrIssn I ON R.licenseID = I.licenseID
                  ");

    $additional_joins = array();
    foreach($conditional_joins as $join) {
      // drop the last line of $conditional_joins which is empty
      if (trim($join) == "") { break; }

      $match = array();
      preg_match("/[A-Z]+(?= ON )/i", $join, $match);
      $table_name = $match[0];
      if (in_array($table_name, $referenced_tables)) {
        $additional_joins[] = $join;
      }
    }

	// Technically this is not the most efficient solution:
	// e.g. if RRP is needed but R is not, this solution will still do both joins,
	// these seem like rare enough edge cases (which involve expected slowdown in
	// the query anyway) that it's not worth cluttering up this code further.
    $table_join_requirements = [
      [ // Organization Tables
        "required_tables" => ["ROL", "O", "OA"],
        "join_rule" => $orgJoinAdd
      ],
      [ // Subject Tables
        "required_tables" => ["RSUB", "GDLINK"],
        "join_rule" => "LEFT JOIN LicenseSubject RSUB ON R.licenseID = RSUB.licenseID
                        LEFT JOIN GeneralDetailSubjectLink GDLINK ON RSUB.generalDetailSubjectLinkID = GDLINK.generalDetailSubjectLinkID"
      ],
      [ // Related License PARENT Tables
        "required_tables" => ["RRP", "RP"],
        "join_rule" => "LEFT JOIN LicenseRelationship RRP ON RRP.licenseID = R.licenseID
                        LEFT JOIN License RP ON RP.licenseID = RRP.relatedlicenseID"
      ],
      [ // Related License CHILD Tables
        "required_tables" => ["RRC", "RC"],
        "join_rule" => "LEFT JOIN LicenseRelationship RRC ON RRC.relatedlicenseID = R.licenseID
                        LEFT JOIN License RC ON RC.licenseID = RRC.licenseID"
      ]
    ];
    foreach ($table_join_requirements as $table_join) {
      foreach ($table_join["required_tables"] as $table) {
        if (in_array($table, $referenced_tables)) {
          $additional_joins[] = $table_join["join_rule"];
          break;
        }
      }
    }

    $query = $select . "
                FROM License R
                  LEFT JOIN Alias A ON R.licenseID = A.licenseID
                  LEFT JOIN LicenseAcquisition RA ON R.licenseID = RA.licenseID
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
  public function search($whereAdd, $orderBy, $limit) {
    $query = $this->searchQuery($whereAdd, $orderBy, $limit, false);

    $result = $this->db->processQuery($query, 'assoc');

    $searchArray = array();

    //need to do this since it could be that there's only one result and this is how the dbservice returns result
    if (isset($result['licenseID'])) { $result = [$result]; }
    foreach ($result as $row) {
      $row = static::addIdsToLicensesRow($row);
      array_push($searchArray, $row);
    }
    return $searchArray;
  }



  private static function addIdsToLicensesRow($row) {
    $license = new LicenseN(new NamedArguments(array('primaryKey' => $row['licenseID'])));
    $isbnOrIssns = $license->getIsbnOrIssn();
    $row['isbnOrIssns'] = [];
    foreach ($isbnOrIssns as $isbnOrIssn) {
      array_push($row['isbnOrIssns'], $isbnOrIssn->isbnOrIssn);
    }
    return $row;
  }



  public function searchCount($whereAdd) {
    $query = $this->searchQuery($whereAdd, '', '', true);
    $result = $this->db->processQuery($query, 'assoc');

    return $result['count'];
  }



  //used for A-Z on search (index)
  public function getAlphabeticalList() {
    $alphArray = array();
    $result = $this->db->query("SELECT DISTINCT UPPER(SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1)) letter, COUNT(SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1)) letter_count
                FROM License R
                GROUP BY UPPER(SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1))
                ORDER BY 1;");

    while ($row = $result->fetch_assoc()) {
      $alphArray[$row['letter']] = $row['letter_count'];
    }

    return $alphArray;
  }

  //returns array based on search for excel output (export.php)
  public function export($whereAdd, $orderBy) {

    $distinct_license_id_query = "SELECT DISTINCT(licenseID) AS license_id FROM License;";
    $distinct_license_ids_assoc_array = $this->db->processQuery($distinct_license_id_query, 'assoc');
    $distinct_license_ids = array_map(function($value) {
      return $value["license_id"];
    }, $distinct_license_ids_assoc_array);

    $config = new Configuration();

    if ($config->settings->organizationsModule == 'Y') {
      $dbName = $config->settings->organizationsDatabaseName;
      $orgJoinAdd = "LEFT JOIN $dbName.Organization O ON O.organizationID = R.organizationID";
    }else{
      $orgJoinAdd = "  LEFT JOIN Organization O ON O.organizationID = R.organizationID";
    }



    $status = new Status();
    //also add to not retrieve saved records
    $savedStatusID = intval($status->getIDFromName('saved'));


    $whereStatement = "WHERE " . implode(" AND ", $whereAdd);

    //now actually execute query
    if(strlen($whereStatement) < 7){
      $whereStatement = "";
    }
    $query = "
SELECT
  R.licenseID,
  R.shortName,
  R.createDate,
  CON.shortName conName,
  ST.shortName staName,
  O.name orgName,
  D.shortName docName,
  D.effectiveDate,
  D.expirationDate,
  D.documentURL,
  ET.shortName expName

FROM License R
  LEFT JOIN Consortium CON ON CON.consortiumID = R.consortiumID
  LEFT JOIN Status ST ON ST.statusID = R.statusID
  LEFT JOIN Document D ON D.licenseID = R.licenseID
  LEFT JOIN Expression E ON E.documentID = D.documentID
  LEFT JOIN ExpressionType ET ON ET.expressionTypeID = E.expressionTypeID
  LEFT JOIN Qualifier Q ON Q.expressionTypeID = E.expressionTypeID
  $orgJoinAdd
  $whereStatement
  ORDER BY
    R.licenseID,
    R.shortName
  
  ";

    // This was determined by trial and error
    $CHUNK_SIZE = 10000;

    $searchArray = [];
    $slice_offset = 0;
    $license_id_chunk = array_slice($distinct_license_ids, $slice_offset, $CHUNK_SIZE);
    while (count($license_id_chunk) > 0) {

      $list_of_ids = implode(",", $license_id_chunk);
      $chunked_query = str_replace("LIST_OF_IDS",$list_of_ids,$query);
      $result = $this->db->processQuery(stripslashes($chunked_query), 'assoc');
      //need to do this since it could be that there's only one result and this is how the dbservice returns result
      if (isset($result['licenseID'])) {
        $result = [$result];
      }
      foreach ($result as $row) {
        array_push($searchArray, $row);
      }

      $slice_offset += $CHUNK_SIZE;
      $license_id_chunk = array_slice($distinct_license_ids, $slice_offset, $CHUNK_SIZE);
    }

    return $searchArray;
  }





  
}
?>
