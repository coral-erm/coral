<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReportParameterFactory
 *
 * @author bgarcia
 */
class ParameterFactory {
    public static function makeParam($reportID,$reportParameterID) {
        $parm = null;
        $db = DBService::getInstance();
        $result = $db
            ->selectDB(Config::$database->name)
            ->query("SELECT rp.*, rpm.parentReportParameterID
            FROM ReportParameter rp, ReportParameterMap rpm
            WHERE rp.reportParameterID = '$reportParameterID' LIMIT 1")
            ->fetchRow(MYSQLI_ASSOC);

        if ($result['parameterDisplayPrompt'] === 'Provider / Publisher') {
                $parm = new ProviderPublisherParameter($reportID,$db,$result);
        } else if ($result['parameterDisplayPrompt'] === 'Provider') {
            $parm = new ProviderParameter($reportID,$db,$result);
        }else if ($result['parameterDisplayPrompt'] === 'Publisher') {
            $parm = new PublisherParameter($reportID,$db,$result);
        } else if($result['parameterTypeCode']==='chk'){
            if ($result['parameterDisplayPrompt']==="Do not adjust numbers for use violations") {
                $parm = new CheckUnadjustedParameter($reportID,$db,$result);
            } else {
                $parm = new CheckboxParameter($reportID,$db,$result);
            }
        } else if ($result['parameterTypeCode']==='dd') {
            if ($result['parameterAddWhereClause'] === 'limit') {
                $parm = new LimitParameter($reportID,$db,$result);
            } else if ($result['parameterDisplayPrompt'] === 'Year') {
                $parm = new YearParameter($reportID,$db,$result);
            } else if ($result['parameterDisplayPrompt'] === 'Date Range') {
                $parm = new DateRangeParameter($reportID,$db,$result);
            } else {
                $parm = new DropdownParameter($reportID,$db,$result);
            }
        } else if ($result['parameterTypeCode']==='ms') {
            if ($result['parameterDisplayPrompt'] === 'Provider / Publisher'
                || $result['parameterDisplayPrompt'] === 'Provider'
                || $result['parameterDisplayPrompt'] === 'Publisher') {
                $parm = new ProviderPublisherDropdownParameter($reportID,$db,$result);
            } else {
                $parm = new MultiselectParameter($reportID,$db,$result);
            }
        } else if ($result['parameterTypeCode']==='txt') {
            $parm = new TextParameter($reportID,$db,$result);
        } else {
            $parm = new Parameter($reportID,$db,$result);
        }

        return $parm;
    }
}
