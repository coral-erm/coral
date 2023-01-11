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
session_start();
//require 'minify.php';
//ob_start('minify_output');
ob_start();

include_once 'directory.php';


if (isset($_REQUEST['outputType'])) {
    $outputType = $_REQUEST['outputType'];
} else {
    $outputType = 'web';
}

$report = ReportFactory::makeReport($_REQUEST['reportID']);
Parameter::setReport($report);
//FormInputs::init() and ReportNotes::init(..) are called by Report constructor
FormInputs::addHidden('outputType',$outputType);
if (! isset($_REQUEST['reportID'])) {
    error_log("missing reportID; redirecting to index.php");
    header("location: index.php");
    exit();
}
if ($outputType === 'web' && isset($_REQUEST['startPage'])) {
    $startRow = $_REQUEST['startPage'];
} else {
    $startRow = 1;
}
if ($report->titleID) {
    Parameter::$display = '<b>Title:</b> ' . $report->getUsageTitle($report->titleID) . '<br/>';
}

// loop through parameters
foreach ( $report->getParameters() as $parm ) {
    $parm->process();
}
// if titleID was passed in, add that to addwhere
if (($report->id === '1') && ($report->titleID != '')) {
    $report->addWhere[1] .= " AND t.titleID = $report->titleID";
}

$pageTitle = $report->name;





///////////////////////////////////header (start)/////////////////////
if ($outputType === 'print') {
?>
    <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
    <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
            <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
            <title>
                <?php echo _("CORAL Usage Statistics Reporting") . ' - ' . $report->name ;?>
            </title>
            <link rel='stylesheet' href='css/print.css' type='text/css' media='screen' />
        </head>
        <body>
<?php
} else if ($outputType === 'web' || $outputType==='pop') {
    include 'templates/header.php';
} else {
    // required to allow downloads in IE 6 and 7
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Transfer-Encoding: binary");
    header("Content-type: application/vnd.ms-excel;");
    header("Content-Disposition: attachment; filename=" . strtr($report->name, ' ', '_') . ".xls");
?>
    <html>
        <head>
        </head>
        <body>
<?php
}
/////////////////////////////////////header (end)//////////////////
?>
            <center>
                <table class='noborder' style='width: 780px;'>
                    <tr>
                        <td class='noborder' align=center colspan='2'>
                            <table class='noborder' style='text-align: left;'>

<!--////////////////////logo/splash and param list (start)//////////////////-->
                                <tr>
<?php
if ($outputType === 'web') {
?>
                                    <td>
                                        <div id="main-title">
                                            <img src="images/title-icon-reports.png" />
                                            <span id="main-title-text">
                                                <?php echo _("Usage Reports"); ?>
                                            </span>
                                            <span id="powered-by-text">
                                                <?php echo _("Powered by");?><img src="images/logo-coral.jpg" />
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='noborder report-head-info-box' align=left valign='bottom'>
                                        <table class='noborder'>
                                            <tr valign='bottom'>
                                                <td class='head' style='padding: 5px; vertical-align: bottom;'>
                                                    <form name='viewreport' method='post' action='report.php<?php echo FormInputs::getVisible();?>'>
                                                        <?php echo FormInputs::getHidden();?>
                                                        <font size='+1'>
                                                            <?php echo $report->name;?>
                                                        </font>
                                                        &nbsp;
                                                        <a href="javascript:showPopup('report','<?php echo $report->id;?>');" title='<?php echo _("Click to show information about this report");?>' style='border: none'>
                                                            <img src='images/help.gif' style='border: none' alt='help'/>
                                                        </a>
                                                        <br/>
                                                        <?php echo Parameter::$display;?>
                                                        <a href="index.php<?php echo FormInputs::getVisible();?>">
                                                            <?php echo _("Modify Parameters");?>
                                                        </a>
                                                        &nbsp;
                                                        <a href='index.php'>
                                                            <?php echo _("Create New Report");?>
                                                        </a>
                                                        <br/>
                                                        <a href="javascript:viewReportOutput('xls');" style="border: none">
                                                            <img border='0' src="images/xls.gif" alt="xls" />
                                                        </a>
                                                        <a href="javascript:viewReportOutput('print');" style="border:none">
                                                            <img border='0' src="images/printer.gif" alt="print" />
                                                        </a>
                                                        <br />
                                                    </form>
                                                </td>
                                                <td class='head' align='right' valign='top'>
                                                    &nbsp;
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
<?php
} else {
?>
                                    <td class='head'>
                                        <font size='+1'>
                                            <?php echo $report->name;?>
                                        </font>
                                        <br/>
                                        <?php echo Parameter::$display;?>
                                        <br/>
                                    </td>
<?php
}
?>
                                </tr>
<?php
////////////////////////logo/splash and param list (end)/////////////////





///////////////////////////report tables (start)///////////////////////
$textAdd = (($report->id === '1') || ($report->id === '2')) ? _('By Month and Resource') : '';
for ($irep=0; $irep<2; $irep++) {
    if ($irep===1) {
        $textAdd = _("from an Archive") . " " . $textAdd;
    }
?>
                                <tr class='rtitle'>
                                    <td colspan='2' class='noborder'>
                                        <?php echo sprintf(_("Number of Successful Full-Text Article Requests %s"), $textAdd);?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2' class='noborder'>
                                        <table id='R<?php echo $irep;?>' class='table rep-res'>
<?php
    $allowSort = (!$report->onlySummary || $outputType!=='web');
    $reportTable = $report->run($irep===1,$allowSort); //ReportTable created by Report::run

    /* print table header */
    $reportTable->displayHeader($outputType);

    /* process and get table html */
    $tblBody = $reportTable->prepareBody($outputType);

    /* print table footer */
    $reportTable->displayFooter($startRow, $outputType);

    echo $tblBody;
?>
                                        </table>
                                    </td>
                                </tr>
<?php
}
////////////////////////////report tables (end)///////////////////////





////////////////////////legend (start)//////////////////
$outlier = $report->getOutliers();
?>
                                <tr>
                                    <td class='noborder' style='text-align: left;'>
                                        <br/>
                                        <br/>
<?php
// for excel
$outlier_cls = array('flagged','overriden','merged');
$rp_fldcnt = $reportTable->nfields();
$modcolcount = $rp_fldcnt - 2;
$nbsp6 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
$txt_merged = _("Multiple titles with the same print ISSN (generally multiple parts) have been merged together");

if ($outputType != 'xls') {
?>
                                        <table style='width: 350px; border-width: 1px'>
                                            <tr>
                                                <td colspan='2'>
                                                    <b><?php echo _("Key");?></b>
                                                </td>
                                            </tr>
<?php
    if (!$report->showUnadjusted) {
        $outlier_txt = array(_('not been adjusted'),_('been adjusted manually by Electronic Resources'),$txt_merged);
        for ($i=0;$i<3;$i++) {
?>
                                            <tr>
                                                <td class='<?php echo $outlier_cls[$i];?>'>
                                                    &nbsp;
                                                </td>
                                                <td>
                                                    <?php echo sprintf(_("Programmatically flagged as outlier based on previous 12 month average. The number has %s"), $outlier_txt[$i]);?>
                                                </td>
                                            </tr>
<?php
        }
    } else {
        for ($i=1;$i<=3;++$i) {
?>
                                            <tr>
                                                <td class='l<?php echo $i;?>'>
                                                    &nbsp;
                                                </td>
                                                <td>
                                                    <?php sprintf(_("Programmatically flagged as outlier using the following formula: Count is %d over %d%% of the previous 12 month average."), $outlier[$i]['count'], $outlier[$i]['percent']);?>
                                                </td>
                                            </tr>
<?php
        }
?>
                                            <tr>
                                                <td class='<?php echo $outlier_cls[2];?>'>
                                                    &nbsp;
                                                </td>
                                                <td>
                                                    <?php echo $txt_merged;?>.
                                                </td>
                                            </tr>
<?php
    }
    echo "</table>";
    // excel
} else {
    echo "<table style='border-width: 1px'>";
    if (!$report->showUnadjusted) {
        $html = array(
            _("Programmatically flagged as outlier based on previous 12 month average. The number has not been adjusted."),
            _("Programmatically flagged as outlier based on previous 12 month average. The number has been adjusted manually by Electronic Resources."),
            _("Multiple titles with the same print ISSN (generally multiple parts) have been merged together")
        );
        for ($i=0; $i<3; $i++) {
?>
                                            <tr>
                                                <td colspan='<?php echo $rp_fldcnt;?>'>
                                                    <table style='border: 0px;'>
                                                        <tr>
                                                            <td class='noborder'<?php if ($i===0) {echo " align=right><b>" . _("Color Background Key") . "</b>";} else {echo ">$nbsp6";}?>
                                                            </td>
                                                            <td class='<?php echo $outlier_cls[$i];?>'>
                                                                &nbsp;
                                                            </td>
                                                            <td class='noborder' colspan='$modcolcount'>
                                                                <?php echo $html[$i];?>.
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
<?php
        }
    } else {
        $html = array();
        for ($i=1;$i<=3;$i++) {
            $html[$i] = array(
                'col'=>Color::$levels[$i][2],
                'cnt'=>$outlier[$i]['count'],
                '%'=>$outlier[$i]['percent']);
        }
        $html_top_opt = array("align=right><b>" . _("Color Background Key") . "</b>",">$nbsp6",">$nbsp6");
        for ($i=1;$i<=3;++$i) {
?>
                                            <tr>
                                                <td colspan='<?php echo $rp_fldcnt;?>'>
                                                    <table style='border: 0px;'>
                                                        <tr>
                                                            <td class='noborder' <?php echo $html_top_opt[$i-1];?>
                                                            </td>
                                                            <td style='width: 20px;' bgcolor='{$html[$i]['col']}'>
                                                                &nbsp;
                                                            </td>
                                                            <td class='noborder' colspan='$modcolcount'>
                                                                <?php echo sprintf(_("Programmatically flagged as outlier using the following formula: Count is %d over %d%% of the previous 12 month average."), $html[$i]['cnt'], $html[$i]['%']);?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
<?php
        }
?>
                                            <tr>
                                                <td colspan='<?php echo $rp_fldcnt;?>'>
                                                    <table style='border: 0px;'>
                                                        <tr>
                                                            <td class='noborder'>
                                                                <?php echo $nbsp6;?>
                                                            </td>
                                                            <td class='<?php echo $outlier_cls[2];?>'>
                                                                &nbsp;
                                                            </td>
                                                            <td class='noborder' colspan='<?php echo $modcolcount;?>'>
                                                                <?php echo $txt_merged;?>.
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
<?php
    }
}
//////////////////////////legend (end)///////////////////

/* display any publisher or platform notes */
?>
                                            <tr>
                                                <td class='noborder' style='text-align: left;'>
                                                    <?php echo ReportNotes::displayNotes();?>
                                                </td>
                                            </tr>

                                        </table>
                                        <br/>
                                    </td>
                                    <td class='noborder'>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </center>

<!--////////////////////footer//////////////////-->
            <script type='text/javascript' src='js/report.js'></script>
<?php
if ($outputType === 'print') {
?>
            <script type="text/javascript">
                <!--
                window.print();
                //-->
            </script>
<?php
}

include 'templates/footer.php';
///////////////////footer (end)///////////////


ob_end_flush();
