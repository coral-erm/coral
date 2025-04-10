<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DropdownParameter
 *
 * @author bgarcia
 */
class DropdownParameter extends Parameter implements ParameterInterface {

    public function value() {
        if(!isset($_REQUEST["prm_$this->id"]))
            return null;

        $val = trim($_REQUEST["prm_$this->id"]);
        if ($val==='')
            return null;

        return $val;
    }

    public function process() {
        if ($this->value !== null) {
            Parameter::$report->addWhere[$this->addWhereNum] .= " AND " . preg_replace('/PARM/',$this->value,$this->addWhereClause);
            FormInputs::addVisible("prm_$this->id", $this->value);
            Parameter::$display .= $this->description();
        } else if (!$this->requiredInd) {
            Parameter::$display .= "<b>{$this->prompt}:</b> all<br/>";
        }
    }

    // TODO: add a button; <select> cannot trigger an action on change
    private function formCommon($parentID) {
        echo "<div id='div_parm_$this->id'>
              <br />
              <label for='prm_$this->id'>$this->prompt</label>";
        echo "<select name='prm_$this->id' id='prm_$this->id' class='opt' ";
        if ($this->isParent()) {
            echo "onchange='updateChildren($this->reportID,$this->id);'>";
        } else {
            echo ">";
        }

        if (!$this->requiredInd) {
            echo "<option value=''";
            if ($this->value===null || $this->value==='')
                echo "selected='selected'";
            echo ">All</option>";
        }

        $firstrow = true;
        foreach ( $this->getSelectValues($parentID) as $value ) {
            if ($firstrow && $this->requiredInd)
                Parameter::$ajax_parmValues[$this->id] = $value;
            echo "<option value='{$value['cde']}'";
            if ($this->value!==null && $this->value==$value['cde']) {
                echo " selected='selected'";
            }
            echo ">" . $value['val'] . "</option>";
            $firstrow = false;
        }

        echo "</select></div>";
    }

    public function form() {
        if (isset(Parameter::$ajax_parmValues[$this->parentID])) {
            $this->formCommon(Parameter::$ajax_parmValues[$this->parentID]);
        } else {
            $this->formCommon(0);
        }
    }

    public function ajaxGetChildUpdate() {
        if (isset($_GET['reportParameterVal'])) {
            $this->formCommon($_GET['reportParameterVal']);
        } else {
            $this->formCommon(0);
        }
    }
}
