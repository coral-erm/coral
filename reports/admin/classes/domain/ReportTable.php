<?php

class ReportTable {
    public static $maxRows;
    public $columnData;

    protected $report;
    protected $reportResult;

    public function __construct(Report $report, DBResult $reportResult) {
        $this->report = $report;
        $this->reportResult = $reportResult;

        $i = 0;
        $hasSubtotal = false;
        foreach ($this->reportResult->fetchFields() as $fld) {
			$i++;
            if (!$hasSubtotal
                && ($fld==='outlier_flag'||$fld==='YTD_TOTAL')) {

                $_fields[$i] = 'QUERY_TOTAL';
                $_fields[] = $fld;
                $hasSubtotal = true;
                $i++;
            } else {
                $_fields[$i] = $fld;
            }
        }

        $this->columnData = $this->report->getColumnData();
        $this->columnData['name'] = $_fields;

        if ($this->performSubtotalFlag = count($this->columnData['sum'])>0) {
            $this->totalSumArray = array();
            foreach ($this->fields() as $f) {
                $this->totalSumArray[$f] = 0;
            }
        }
    }

    public function displayHeader($outputType) {
        echo "<thead><tr>";
        foreach ( $this->fields() as $i=>$field ) {
            echo "<th>" . ucwords(strtolower(strtr($field, '_', ' ')));
            if ($outputType === 'web') {
                echo "<div><button type='button' class='btn' 
                    onclick=\"sortRecords('$i', 'asc');\"> <img src='images/arrowdown";
                if ($this->report->sortData['column'] == $i && $this->report->sortData['order'] === 'asc') {
                    echo '_sel';
                }
                echo ".png' border=0 alt='ascending' /></button>&nbsp; <button type='button' class='btn' 
                    onclick=\"sortRecords('$i', 'desc');\"> <img src='images/arrowup";
                if ($this->report->sortData['column'] == $i && $this->report->sortData['order'] === 'desc') {
                    echo '_sel';
                }
                echo ".png' alt='descending'/></button></div>";
            }
            echo "</th>";
        }
        echo "</tr></thead>";
    }

    public function prepareBody($outputType) {
        $this->numRows = 0;
        $tblBody = "<tbody>";
        while ($currentRow = $this->reportResult->fetchRowPersist(MYSQLI_ASSOC) ) {
            if (isset($currentRow['platformID']))
                ReportNotes::addPlatform($currentRow['platformID']);
            if (isset($currentRow['publisherPlatformID']))
                ReportNotes::addPublisher($currentRow['publisherPlatformID']);

            $colnum = 1;
            $subtotal = 0;
            $rowOutput = "<tr class='data'>";
            foreach ( ReportTable::filterRow($currentRow)
                as $field => $value ) {


                if ($this->performSubtotalFlag && isset($this->columnData['sum'][$field])) {
                    // get the numbers out for summing
                    if ($field==='QUERY_TOTAL') {
                        $value = $subtotal;
                    } else {
                        $subtotal += $value;
                    }
                    $this->totalSumArray[$field] += $value;
                }

                $rowOutput .= $this->formatColumn($outputType,$currentRow,$field,$value);

                // end if display columns is Y
                ++$colnum;
            } // end loop through columns
            $rowOutput .= "</tr>";
            ++$this->numRows;

            if (! $this->report->onlySummary || $outputType!=='web')
                $tblBody .= $rowOutput;
        }
        $tblBody .= "</tbody>";
        return $tblBody;
    }

    public function displayFooter($startRow, $outputType) {
        echo "<tfoot>";
        if (!$this->numRows) {
            echo "<tr class='data'><td colspan=" . $this->nfields() . "><i>" . _("Sorry, no rows were returned.") . "</i></td></tr>";
        } else {
            if (/*$outputType != 'xls' &&*/ $this->performSubtotalFlag) {

                $rowParms = array();
                $total = null;
                foreach ($this->fields() as $field) {
                    if (isset($this->columnData['sum'][$field],$this->totalSumArray[$field])) {
                        $total = $this->sumColumn($field, $this->totalSumArray, $this->numRows);
                    }
                    $rowParms[] = ($total===null||$total==='')?'&nbsp;':$total;
                    $total = null;
                }

                $rowParms[0] = "Total for Report";
                echo ReportTable::formatTotalsRow($rowParms);
            }

            if (!$this->report->onlySummary || $outputType!=='web') {
                echo "<tr><td colspan=" . $this->nfields() . " align='right'><i>Showing rows ",$startRow," to ";
                if ((ReportTable::$maxRows > 0) && ($this->numRows > ReportTable::$maxRows)) {
                    echo ReportTable::$maxRows . " of " . ReportTable::$maxRows;
                } else {
                    echo "$this->numRows of $this->numRows";
                }
                echo '</i></td></tr>';
            }
        }
        echo '</tfoot>';
    }

    public function fields() {
        return $this->columnData['name'];
    }

    public function nfields() {
        return count($this->columnData['name']);
    }

    public static function filterRow(array $row) {
        $row_tmp = array();
        $hasSubtotal = false;
        foreach ( $row as $field => $data ) {
            // stop displaying columns once we hit title ID or platform ID
            if (($field === 'titleID') || ($field === 'platformID')) {
                break;
            }

            if (!$hasSubtotal
                && ($field==='outlier_flag'||$field==='YTD_TOTAL')) {

                $row_tmp['QUERY_TOTAL'] = "&nbsp;";
                $hasSubtotal = true;
            }

            $row_tmp[$field] = $data;
            if ($data === '') {
                $row_tmp[$field] = "&nbsp;";
            }
        }
        return $row_tmp;
    }

    public function formatColumn($outputType, array $currentRow, $field, $value) {
        $colOutput = "";

        $value = str_replace(" & "," &amp; ",$value);

        if ($outputType === 'web'
            && ($value !== '&nbsp;') && $field === 'TITLE'
        ) {
            if ($this->report->id != '1') {
                $value .= "<br/><span class='smallerText'><a ". getTarget() ." href=\"report.php?reportID=1&prm_4=" . ($this->report->showUnadjusted ? 'Y' : 'N');
                if (isset($currentRow['titleID'])) {
                    $value .= "&titleID={$currentRow['titleID']}&outputType=web\">" . _("view related titles") . "</a></span>";
                } else {
                    $value .= "&outputType=web\">" . _("view related titles") . "</a></span>";
                }
            }
            // echo link resolver link
            if ((($currentRow['PRINT_ISSN']) || ($currentRow['ONLINE_ISSN'])) && isset($this->report->baseURL)) {
                $value .= "<br/><span class='smallerText'><a target=\"_BLANK\" href=\"" . $this->report->getLinkResolverLink($currentRow) . "\">" . _("view in link resolver") . "</a></span>";
            }
        }
        if (isset($currentRow[$field . '_OVERRIDE']) || (isset($currentRow[$field . '_OUTLIER']) && $currentRow[$field . '_OUTLIER'] > 0)) {
            if (!$this->report->showUnadjusted && isset($currentRow[$field . '_OVERRIDE'])) {
                $colOutput .= "<td class='overriden'>" . $currentRow[$field . '_OVERRIDE'] . "</td>";
            } else {
                if ($this->report->showUnadjusted) {
                    if ($currentRow[$field . '_OUTLIER'] >= 4) {
                        $tmp_outlier_color = Color::$levels[$currentRow[$field . '_OUTLIER'] - 3];
                    } else {
                        $tmp_outlier_color = Color::$levels[$currentRow[$field . '_OUTLIER']];
                    }
                    $colOutput .= "<td class='$tmp_outlier_color[1]'>$value</td>";
                    unset($tmp_outlier_color);
                } else {
                    $colOutput .= "<td class='flagged'>$value</td>";
                }
            }
        } else if (isset($currentRow['MERGE_IND'])) {
            $colOutput .= "<td class='merged'>$value</td>";
        } else {
            $colOutput .= "<td>$value</td>";
        }
        return $colOutput;
    }

    public static function formatTotalsRow(array $row) {
        $str = null;
        $cspan = 0;
        $spanval = '';
        foreach($row as $name=>$val) {
            if ($str===null) {
                $cspan++;
                $spanval = $val;
                $str = "";
            } else if ($val==="&nbsp;"||$val==="") {
                $cspan++;
            } else {
                if($cspan>1) {
                    $str .= "<td class='sum' colspan='$cspan'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$spanval</td>";
                    $cspan = 0;
                    $spanval = '';
                }
                $str .= "<td class='sum'>$val</td>";
            }
        }
        if ($cspan) {
            $str .= "<td class='sum' colspan='$cspan'>$spanval</td>";
        }
        return "<tr class='data'>$str</tr>";
    }

    public function sumColumn($field, $totalSum, $rowcount) {
        $sumType = $this->columnData['sum'][$field];
        if ($sumType === 'dollarsum') {
            return money_format($totalSum[$field]);
        } else if ($sumType === 'sum') {
            $total = number_format($totalSum[$field]);
            if (!$total)
                $total = '-';
            return $total;
        } else if ($sumType === 'avg') {
            return ($rowcount > 0) ? (number_format($totalSum[$field] / $rowcount) . '%') : '';
        }
        return '';
    }
}
