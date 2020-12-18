<?php

/*
 * *************************************************************************************************************************
 * * CORAL Usage Statistics Reporting Module v. 1.0
 * *
 * * Copyright (c) 2010 University of Notre Dame
 * *
 * * This file is part of CORAL.
 * *
 * * CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * *
 * * CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * *
 * * You should have received a copy of the GNU General Public License along with CORAL. If not, see <http://www.gnu.org/licenses/>.
 * *
 * *************************************************************************************************************************
 */
abstract class Report implements ReportInterface {
    public $db;
    public $dbname;

    public $id;
    public $name;
    public $addWhere = array('','');
    public $sortData = array('order'=>'asc','column'=>1);
    public $titleID = null;
    public $baseURL = null;
    public $showUnadjusted = false;
    public $onlySummary = false;

    public abstract function sql($isArchive);

    public function applyDateRange(array $dateRange) {
        // defaults to no action if not overriden
    }

    public function __construct($id){
        $this->db = DBService::getInstance();
        $result = $this->db
            ->selectDB(Config::$database->name)
            ->query("SELECT reportName, reportDatabaseName FROM Report WHERE reportID = '$id' LIMIT 1")
            ->fetchRow(MYSQLI_ASSOC);

        $this->id = $id;
        $this->name = $result['reportName'];
        $this->dbname = $result['reportDatabaseName'];

        ReportNotes::init($this->dbname);

        if (isset($_REQUEST['titleID']) && $_REQUEST['titleID']!==null && $_REQUEST['titleID']!=='') {
            $this->titleID = $_REQUEST['titleID'];
            FormInputs::addVisible('titleID',$this->titleID);
        }

        if (isset($_REQUEST['sortColumn'])) {
            $this->sortData['column'] = $_REQUEST['sortColumn'];
        }

        if (isset($_REQUEST['sortOrder'])) {
            $this->sortData['order'] = $_REQUEST['sortOrder'];
        }

        FormInputs::addVisible('reportID',$this->id);
        FormInputs::addHidden('useHidden',1);
        FormInputs::addHidden('sortColumn',$this->sortData['column']);
        FormInputs::addHidden('sortOrder',$this->sortData['order']);

        Config::init();
        if (Config::$settings->baseURL) {
            if (strpos(Config::$settings->baseURL, '?') > 0) {
                $this->baseURL = Config::$settings->baseURL . '&';
            } else {
                $this->baseURL = Config::$settings->baseURL .'?';
            }
        }
    }

    public function run($isArchive, $allowSort){
        $sql = $this->sql($isArchive);
        if ($allowSort)
            $sql .= " ORDER BY {$this->sortData['column']} {$this->sortData['order']}";

        $this->db->selectDB(Config::$database->{$this->dbname});
        $reportArray = $this->db->query($sql);
        return new ReportTable($this, $reportArray);
    }

    // returns outlier array for display at the bottom of reports
    public function getOutliers(){
        Config::init();
        $outlier = array();
        foreach ( $this->db
                ->selectDB(Config::$database->{$this->dbname})
                ->query("SELECT outlierLevel, overageCount, overagePercent FROM Outlier ORDER BY 2")
                ->fetchRows(MYSQLI_ASSOC) as $outlierArray ){
            $outlier[$outlierArray['outlierLevel']]['count'] = $outlierArray['overageCount'];
            $outlier[$outlierArray['outlierLevel']]['percent'] = $outlierArray['overagePercent'];
            $outlier[$outlierArray['outlierLevel']]['level'] = $outlierArray['outlierLevel'];
        }
        return $outlier;
    }

    // returns associated parameters
    public function getParameters(){
        // set database to reporting database name
        Config::init();

        $objects = array();
        foreach ( $this->db
                ->selectDB(Config::$database->name)
                ->query("SELECT reportParameterID
                    FROM ReportParameterMap
                    WHERE reportID = '$this->id'
                    ORDER BY 1")
                ->fetchRows(MYSQLI_ASSOC) as $row ){
            $objects[] = ParameterFactory::makeParam($this->id,$row['reportParameterID']);
        }
        $objects[] = new CheckSummaryOnlyParameter($this->id);
        return $objects;
    }

    // removes associated parameters
    public function getColumnData(){
        // set database to reporting database name
        Config::init();
        $this->db->selectDB(Config::$database->name);

        $sumColsArray = array();
        foreach($this->db
                ->query("SELECT reportColumnName, reportAction
                        FROM ReportSum
                        WHERE reportID = '$this->id'"
                    )
                ->fetchRows(MYSQLI_ASSOC) as $row ){
            $sumColsArray[$row['reportColumnName']] = $row['reportAction'];
        }

        return array('sum'=>$sumColsArray);
    }

    // return the title of the ejournal for this report
    public function getUsageTitle($titleID){
        Config::init();
        $row = $this->db
            ->selectDB(Config::$database->{$this->dbname})
            ->query("SELECT title FROM Title WHERE titleID = '$titleID'")
            ->fetchRow(MYSQLI_ASSOC);
        return $row['title'];
    }

    public function getLinkResolverLink(&$row) {
        if ($row['PRINT_ISSN']) {
            if (($row['ONLINE_ISSN'])) {
                return "{$this->baseURL}rft.issn={$row['PRINT_ISSN']}&rft.eissn={$row['ONLINE_ISSN']}";
            } else {
                return "{$this->baseURL}rft.issn={$row['PRINT_ISSN']}";
            }
        } else {
            return "{$this->baseURL}rft.eissn={$row['ONLINE_ISSN']}";
        }
    }


}
?>
