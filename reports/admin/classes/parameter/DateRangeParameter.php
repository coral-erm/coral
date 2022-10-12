<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DateRangeParameter
 *
 * @author bgarcia
 */
class DateRangeParameter extends DropdownParameter implements ParameterInterface {

    public function __construct($reportID,$db,$dbData,$value=null) {
        parent::__construct($reportID, $db, $dbData,$value);

        if (isset($this->value['y0']) && isset($this->value['y1']) && $this->value['y0']===$this->value['y1']) {
            $this->addWhereClause = "(mus.year={$this->value['y0']} AND "
            . DateRangeParameter::monthRangeSameYearSQL($this->value['m0'], $this->value['m1']) . ")";
        } else {
			if ( isset($this->value['y0']) && isset($this->value['y1']) && isset($this->value['m0']) && isset($this->value['m1'])){
				 
            $this->addWhereClause = "((mus.year={$this->value['y0']} AND month BETWEEN {$this->value['m0']} AND 12)";
            for ($y=$this->value['y0']+1; $y<$this->value['y1']; ++$y) {
                $this->addWhereClause .= " OR mus.year=$y";
            }
            $this->addWhereClause .=  " OR (mus.year={$this->value['y1']} AND "
            . DateRangeParameter::monthRangeSameYearSQL(1, $this->value['m1']) . "))";
			
			}
        }
    }

    public function value() {
        if ($this->reportID===null) {
            return array('m0'=>null,'y0'=>null,'m1'=>null,'y1'=>null);
        } else if (! isset($_REQUEST["prm_$this->id"])) {
            $years = $this->getSelectValues($this->parentID);
			if($years){
            $y0 = min($years);
            $y1 = max($years);
            return array('m0'=>1,'y0'=>$y0['val'],'m1'=>12,'y1'=>$y1['val']);
			}
        } else {
            return $this->decode($_REQUEST["prm_$this->id"]);
        }
    }

    public function process() {
        if ($this->value !== null) {
            Parameter::$report->addWhere[$this->addWhereNum] .= " AND $this->addWhereClause";
            Parameter::$report->applyDateRange($this->value);
            FormInputs::addVisible("prm_$this->id",$this->encode($this->value));
            $this->value = $this->value['m0'] . '/' . $this->value['y0'] . '-'
                . $this->value['m1'] . '/' . $this->value['y1'];
            Parameter::$display .= $this->description();
        }
    }

    public function description() {
        return "<b>{$this->prompt}:</b> '$this->value'<br/>";
    }

    private static function monthRangeSameYearSQL($start,$end) {
        if ($start===$end)
            return "month=$start";
        return "month BETWEEN $start AND $end";
    }

    private function validateRange($range) {
        if ( isset($range['y0'],$range['y1'],$range['m0'],$range['m1']) // not missing params
            && ($range['m0']>=1 && $range['m0']<=12) // m0 is valid
            && ($range['m1']>=1 && $range['m1']<=12) // m1 is valid
            && (strlen("{$range['y0']}")==4)  // y0 is valid
            && (strlen("{$range['y1']}")==4)  // y1 is valid
            && ($range['y0']<=$range['y1']) // y0<=y1
            && ($range['y0']!=$range['y1'] || $range['m0']<=$range['m1']) // m0<=m1 when y0=y1
            ) {
                return true;
        } else {
            $fields = array('m0','y0','m1','y1');
            foreach ($fields as $f) {
                if (!isset($range[$f]))
                    $range[$f] = 'NULL';
            }
            throw new InvalidArgumentException("invalid date range: {$range['m0']}/{$range['y0']} -> {$range['m1']}/{$range['y1']}");
        }
    }

    public function encode($range) { // for HTTP GET
        if ($this->validateRange($range)) { // throws InvalidArgumentException
            return sprintf('%02u%04u%02u%04u',$range['m0'],$range['y0'],$range['m1'],$range['y1']);
        }
    }

    public function decode($data) { // for HTTP GET
        if (!is_string($data) || strlen($data)!==12) {
            throw new InvalidArgumentException("unable to decode: \"$data\"");
        }

        $parsed = sscanf($data,'%02u%04u%02u%04u');
        $range = array('m0'=>$parsed[0], 'y0'=>$parsed[1],'m1'=>$parsed[2], 'y1'=>$parsed[3]);

        if ($this->validateRange($range)) { // throws InvalidArgumentException
            return $range;
        }
    }

    private function formCommon() {
        $varname_parentID = "prm_$this->parentID";
        $parentID = 0;
        if (isset($_GET[$varname_parentID])) {
            $parentID = $_GET[$varname_parentID];
        }

        $months = array('','January','February','March','April','May','June','July','August','September','October','November','December');
        $years = $this->getSelectValues($parentID);
        echo "<div id='div_parm_$this->id' class='param'>
            <input type='hidden' id='daterange' name='prm_$this->id' />";
        for ($i=0;$i<2;$i++) {
            $mOpts = "";
            $yOpts = "";
            $legendtxt = ($i)?'Through date':'From date';
            for ($mi=1;$mi<count($months);$mi++) {
                $sel = ($this->value["m$i"]==$mi)?'selected="selected"':'';
                $mOpts .= "<option value='$mi' $sel>{$months[$mi]}</option>";
            }

            foreach ($years as $y) {
                $sel = ($this->value["y$i"]==$y['cde'])?'selected="selected"':'';
                $yOpts .= "<option value=\"{$y['cde']}\" $sel>{$y['val']}</option>";
            }
            echo "<br />
                <fieldset>
                    <legend>$legendtxt:</legend>
                    <select id='date{$i}m' class='opt'>
                        $mOpts
                    </select>
                    <select id='date{$i}y' class='opt'>
                        $yOpts
                    </select>
                </fieldset>";
        }
        echo "</div>";
    }

	public function form() {
        $this->formCommon();
    }

    public function ajaxGetChildUpdate() {
        $this->formCommon();
    }

    public static function getMonthsUsed(array $range) {
        $used = array();

        $miny = intval($range['y0']);
        $maxy = intval($range['y1']);

        for ($y=$miny; $y<=$maxy; ++$y) {
            if (count($used)===12) {
                return $used;
            }

            $minm = intval(($y===$miny)? $range['m0'] : 1);
            $maxm = intval(($y===$maxy)? $range['m1'] : 12);

            for ($m=$minm; $m<=$maxm; ++$m) {
                $used[$m] = true;
                if (count($used)===12) {
                    return $used;
                }
            }
        }
        return $used;
    }
}
