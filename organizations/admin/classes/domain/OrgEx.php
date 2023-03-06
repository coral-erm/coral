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

class OrgEx extends DatabaseObject {


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
    CoralSession::set('OrgSearch', $search);
  }



  public static function resetSearch() {
    OrgEx::setSearch(array());
  }



  public static function getSearch() {
    
    if (!CoralSession::get('OrgSearch')) {
      
      OrgEx::resetSearch();
    }
    return CoralSession::get('OrgSearch');
  }



  public static function getSearchDetails() {
    
    // A successful mysqli_connect must be run before mysqli_real_escape_string will function.  Instantiating a Organization model will set up the connection
    $Organization = new OrgEx();
    $search = OrgEx::getSearch();
    $whereAdd = array();
    $searchDisplay = array();
    $config = new Configuration();
    

    //if name is passed in also search alias, organizations and organization aliases
    if ($search['organizationName']) {
      $nameQueryString = $Organization->db->escapeString(strtoupper($search['organizationName']));
      $nameQueryString = preg_replace("/ +/", "%", $nameQueryString);
      $nameQueryString = "'%" . $nameQueryString . "%'";

      $whereAdd[] = "((UPPER(O.name) LIKE " . $nameQueryString . ") OR (UPPER(A.name) LIKE " . $nameQueryString . "))"; //OR (UPPER(O.shortName) LIKE " . $nameQueryString . ") OR (UPPER(RP.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RC.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RA.recordSetIdentifier) LIKE " . $nameQueryString . "))";

      $searchDisplay[] = _("Name contains: ") . $search['organizationName'];
    }

    if ($search['contactName']) {
      $nameQueryString = $Organization->db->escapeString(strtoupper($search['contactName']));
      $nameQueryString = preg_replace("/ +/", "%", $nameQueryString);
      $nameQueryString = "'%" . $nameQueryString . "%'";

      $whereAdd[] = "((UPPER(C.name) LIKE " . $nameQueryString . "))";// OR (UPPER(A.shortName) LIKE " . $nameQueryString . ") OR (UPPER(O.shortName) LIKE " . $nameQueryString . ") OR (UPPER(RP.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RC.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RA.recordSetIdentifier) LIKE " . $nameQueryString . "))";

      $searchDisplay[] = _("Contact name contains: ") . $search['contactName'];
    }
    //get where statements together (and escape single quotes)

    if ($search['creatorLoginID']) {
      $whereAdd[] = "R.createLoginID = '" . $Organization->db->escapeString($search['creatorLoginID']) . "'";

      $createUser = new User(new NamedArguments(array('primaryKey' => $search['creatorLoginID'])));
      if ($createUser->firstName) {
        $name = $createUser->lastName . ", " . $createUser->firstName;
      }else{
        $name = $createUser->loginID;
      }
      $searchDisplay[] = _("Creator: ") . $name;
    }

    if ($search['OrganizationFormatID']) {
      $whereAdd[] = "R.OrganizationFormatID = '" . $Organization->db->escapeString($search['OrganizationFormatID']) . "'";
      $OrganizationFormat = new OrganizationFormat(new NamedArguments(array('primaryKey' => $search['OrganizationFormatID'])));
      $searchDisplay[] = _("Organization Format: ") . $OrganizationFormat->shortName;
    }

    if ($search['acquisitionTypeID']) {
      $whereAdd[] = "RA.acquisitionTypeID = '" . $Organization->db->escapeString($search['acquisitionTypeID']) . "'";
      $acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $search['acquisitionTypeID'])));
      $searchDisplay[] = _("Acquisition Type: ") . $acquisitionType->shortName;
    }


    if ($search['OrganizationNote']) {
      $whereAdd[] = "(UPPER(RNA.noteText) LIKE UPPER('%" . $Organization->db->escapeString($search['OrganizationNote']) . "%') AND RNA.tabName <> 'Product') OR (UPPER(RNR.noteText) LIKE UPPER('%" . $Organization->db->escapeString($search['OrganizationNote']) . "%') AND RNR.tabName = 'Product')";
      $searchDisplay[] = _("Note contains: ") . $search['OrganizationNote'];
    }

    if ($search['createDateStart']) {
      $whereAdd[] = "R.createDate >= STR_TO_DATE('" . $Organization->db->escapeString($search['createDateStart']) . "','%m/%d/%Y')";
      if (!$search['createDateEnd']) {
        $searchDisplay[] = _("Created on or after: ") . $search['createDateStart'];
      } else {
        $searchDisplay[] = _("Created between: ") . $search['createDateStart'] . " and " . $search['createDateEnd'];
      }
    }

    if ($search['createDateEnd']) {
      $whereAdd[] = "R.createDate <= STR_TO_DATE('" . $Organization->db->escapeString($search['createDateEnd']) . "','%m/%d/%Y')";
      if (!$search['createDateStart']) {
        $searchDisplay[] = _("Created on or before: ") . $search['createDateEnd'];
      }
    }

    if ($search['startWith']) {
      $whereAdd[] = "TRIM(LEADING 'THE ' FROM UPPER(R.shortName)) LIKE UPPER('" . $Organization->db->escapeString($search['startWith']) . "%')";
      $searchDisplay[] = _("Starts with: ") . $search['startWith'];
    }

    //the following are not-required fields with dropdowns and have "none" as an option
    $oName = $search['organizationRoleID'];
    if ($oName == 'none') {
      $whereAdd[] = "((ORP.organizationRoleID IS NULL) OR (ORP.organizationRoleID = '0'))";
      $searchDisplay[] = _("Organization: none");
    }else if ($oName) {
      $whereAdd[] = "ORP.organizationRoleID = '" . $Organization->db->escapeString($oName) . "'";    
      $organizationType = new OrganizationRole(new NamedArguments(array('primaryKey' => $oName)));
      $searchDisplay[] = _("Organization Role: ") . $organizationType->name;
      
    }

    

    $orderBy = $search['orderBy'];


    $page = $search['page'];
    $recordsPerPage = $search['recordsPerPage'];
    return array("where" => $whereAdd, "page" => $page, "order" => $orderBy, "perPage" => $recordsPerPage, "display" => $searchDisplay);
  }



  public function searchQuery($whereAdd, $orderBy = '', $limit = '', $count = false) {
    $config = new Configuration();
    $status = new Status();
    
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
    if (isset($result['OrganizationID'])) { $result = [$result]; }
    foreach ($result as $row) {
      $row = static::addIdsToOrganizationsRow($row);
      array_push($searchArray, $row);
    }
    return $searchArray;
  }



  private static function addIdsToOrganizationsRow($row) {
    $Organization = new OrgEx(new NamedArguments(array('primaryKey' => $row['OrganizationID'])));
    $isbnOrIssns = $Organization->getIsbnOrIssn();
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



  //returns array based on search for excel output (export.php)
  public function export($whereAdd, $orderBy) {
    $distinct_Organization_id_query = "SELECT DISTINCT(organizationID) AS Organization_id FROM Organization;";
    $distinct_Organization_ids_assoc_array = $this->db->processQuery($distinct_Organization_id_query, 'assoc');
    $distinct_Organization_ids = array_map(function($value) {
      return $value["Organization_id"];
    }, $distinct_Organization_ids_assoc_array);

    $config = new Configuration();
    if ($config->settings->organizationsModule == 'Y') {
      $dbName = $config->settings->organizationsDatabaseName;
      $orgJoinAdd = "LEFT JOIN $dbName.Organization O ON O.organizationID = R.organizationID";
    }else{
      $orgJoinAdd = "  LEFT JOIN Organization O ON O.organizationID = R.organizationID";
    }

    $whereStatement = "WHERE " . implode(" AND ", $whereAdd);

    //now actually execute query
    if(strlen($whereStatement) < 7){
      $whereStatement = "";
    }
    $query = "
SELECT
  O.organizationID ,
  O.name,
  O.createDate,
  O.updateDate,

  A.name aName,

  PO.name pName,

  OrR.shortName,

  C.name cName,
  C.title,
  C.addressText,
  C.phoneNumber,
  C.emailAddress



FROM Organization O
  LEFT JOIN OrganizationHierarchy OH ON OH.organizationID = O.organizationID
  LEFT JOIN Organization PO ON PO.organizationID = OH.parentOrganizationID
  LEFT JOIN OrganizationRoleProfile ORP ON ORP.organizationID = O.organizationID
  LEFT JOIN OrganizationRole OrR ON OrR.organizationRoleID = ORP.organizationRoleID
  LEFT JOIN Contact C ON C.organizationID = O.organizationID
  LEFT JOIN Alias A ON A.organizationID = O.organizationID
  LEFT JOIN ContactRoleProfile CRP ON CRP.contactID = C.contactID
  LEFT JOIN ContactRole CR ON CR.contactRoleID = CRP.contactRoleID 
  $whereStatement
  Order BY
    O.name
  ; ";

    // This was determined by trial and error
    
    $CHUNK_SIZE = 10000;
    //echo $query;
    $searchArray = [];
    $slice_offset = 0;
    $Organization_id_chunk = array_slice($distinct_Organization_ids, $slice_offset, $CHUNK_SIZE);
    while (count($Organization_id_chunk) > 0) {
      
      $list_of_ids = implode(",", $Organization_id_chunk);
      $chunked_query = str_replace("LIST_OF_IDS",$list_of_ids,$query);
      $result = $this->db->processQuery(stripslashes($chunked_query), 'assoc');

      //need to do this since it could be that there's only one result and this is how the dbservice returns result
      foreach ($result as $row) {
        array_push($searchArray, $row);
      }

      if (is_string($searchArray[0])) {

        $result = [$result];
        $searchArray = [];
        foreach ($result as $row) {
          array_push($searchArray, $row);
        }
      }
      
      
      //
      $slice_offset += $CHUNK_SIZE;
      $Organization_id_chunk = array_slice($distinct_Organization_ids, $slice_offset, $CHUNK_SIZE);
    }
    
    return $searchArray;
  }





  
}
?>
