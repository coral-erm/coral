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

function escape_csv($fields)
{
  $f = fopen('php://memory', 'r+');
  if (fputcsv($f, $fields) === false) {
    $failure_string = "Failed to generate csv with row\n".var_export($fields, true);
    throw new Exception($failure_string, 1);
  }
  rewind($f);
  $csv_line = stream_get_contents($f);
  return rtrim($csv_line);
}

function array_to_csv_row($array) {
  return escape_csv($array)."\r\n";
}
$queryDetails = OrgEx::getSearchDetails();
$whereAdd = $queryDetails["where"];
$searchDisplay = $queryDetails["display"];
$orderBy = $queryDetails["order"];

//get the results of the query into an array
$resourceObj = new OrgEx();
$resourceArray = array();
$resourceArray = $resourceObj->export($whereAdd, $orderBy);

$replace = array("/", "-");
$excelfile = "organization_export_" . str_replace( $replace, "_", format_date( date( 'Y-m-d' ) ) ).".csv";
header("Pragma: public");
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=\"" . $excelfile . "\"");
$columns = [
  ["header" => _("Organization ID"),                    "sqlColumn" => "organizationID",                   "getValueFromRow" => function($r) { return $r['organizationID']; }],
  ["header" => _("Organization Name"),                  "sqlColumn" => "name",                   "getValueFromRow" => function($r) { return $r['name']; }],
  ["header" => _("Create Date"),                        "sqlColumn" => "createDate",                     "getValueFromRow" => function($r) { return format_date($r['createDate']); }],
  ["header" => _("Update Date"),                        "sqlColumn" => "updateDate",                     "getValueFromRow" => function($r) { return format_date($r['updateDate']); }],
  ["header" => _("Alias Name"),                         "sqlColumn" => "aName",                     "getValueFromRow" => function($r) { return $r['aName']; }],
  ["header" => _("Parent Name"),                        "sqlColumn" => "pName",                     "getValueFromRow" => function($r) { return $r['pName']; }],
  ["header" => _("Organization Role"),                  "sqlColumn" => "shortName",                 "getValueFromRow" => function($r) { return $r['shortName']; }],
  ["header" => _("Contact Name"),                       "sqlColumn" => "cName",                     "getValueFromRow" => function($r) { return $r['cName']; }],
  ["header" => _("Contact Title"),                      "sqlColumn" => "title",               "getValueFromRow" => function($r) { return $r['title']; }],
  ["header" => _("Contact Address"),                    "sqlColumn" => "addressText",                  "getValueFromRow" => function($r) { return $r['addressText']; }],
  ["header" => _("Contact Phone"),                      "sqlColumn" => "phoneNumber",              "getValueFromRow" => function($r) { return $r['phoneNumber']; }],
  ["header" => _("Contact Email"),                      "sqlColumn" => "emailAddress",              "getValueFromRow" => function($r) { return $r['emailAddress']; }]];
$availableColumns = array_filter($columns, function($c) use ($resourceArray) {
  return array_key_exists($c["sqlColumn"], $resourceArray[0]);
});
$columnHeaders = array_map(function($c) { return $c["header"]; }, $availableColumns);

echo "# " . _("Organization Record Export") . " " . format_date( date( 'Y-m-d' )) . "\r\n";
if (!$searchDisplay) {
  $searchDisplay = array(_("All Organization Records"));
}
echo "# " . implode('; ', $searchDisplay) . "\r\n";
echo array_to_csv_row($columnHeaders);
foreach($resourceArray as $resource) {
  echo array_to_csv_row(array_map(function($column) use ($resource) {
    return $column["getValueFromRow"]($resource);
  }, $availableColumns));
}
?>
