<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DefaultMultiselectParameter
 *
 * @author bgarcia
 */
class MultiselectParameter extends Parameter implements ParameterInterface {

    public function value() {
        if(!isset($_REQUEST["prm_$this->id"]))
            return null;

        if (!isset($_REQUEST['useHidden']) || $_REQUEST['useHidden'] == null) {
            return implode("', '", explode(',', str_replace('\\\\', ',', $_REQUEST["prm_$this->id"])));
        } else {
            return trim($_REQUEST["prm_$this->id"]);
        }
    }

    private function formCommon($options) {
        echo "
<span style='margin-left:-90px'>
    <div id='div_show_$this->id' style='float:left;margin-bottom: 5px'>
        <button type='button' class='btn' onclick=\"toggleLayer('div_$this->id','block');
           toggleLayer('div_show_$this->id','none');\">-Click to choose $this->prompt-</button>
    </div>
    <div id='div_$this->id' style='display:none;float:left;margin-bottom: 5px;'>
        <table class='noborder'>
            <tr>
                <td class='noborder'>
                <!-- TODO: a11y: is this the appropriate label? -->
                    <select name='prm_left_$this->id' id='prm_left_$this->id' class='opt' size='10'
                    multiple='multiple' aria-label='$this->prompt'>
                        $options
                    </select>
                </td>
                <td align='center' valign='middle' style='border:0px;'>
                    <input type='button' value='--&gt;' style='width:35px'
                        onclick='moveOptions(this.form.prm_left_$this->id, this.form.prm_right_$this->id);
                        placeInHidden(\",\",\"prm_right_$this->id\", \"prm_$this->id\");'/>
                    <input type='button' value='&lt;--' style='width:35px'
                        onclick='moveOptions(this.form.prm_right_$this->id, this.form.prm_left_$this->id);
                        placeInHidden(\",\",\"prm_right_$this->id\", \"prm_$this->id\");'/>
                </td>
                <td style='border:0px;'>
                    <!-- TODO: a11y: is this the appropriate label? -->
                    <select name='prm_right_$this->id' id='prm_right_$this->id' class='opt' size='10' multiple='multiple' aria-label='$this->prompt'>
                    </select>
                </td>
            </tr>

            <tr>
                <td style='border:0px;' colspan='3' align='left'>
                    <input type='hidden' name='prm_$this->id' id='prm_$this->id' value=''/>
                    <button type='button' class='btn' onclick=\"toggleLayer('div_$this->id','none');
                        toggleLayer('div_show_$this->id','block');\">-Hide $this->prompt-</button>
                </td>
            </tr>
        </table>
    </div>
</span>";

    }

    public function form() {
        $options = "";
        if (!$this->requiredInd) {
            $options .= "<option value='' selected='selected'>All</option>";
        }
        if (isset(Parameter::$ajax_parmValues[$this->parentID])) {
            foreach ( $this->getSelectValues(Parameter::$ajax_parmValues[$this->parentID]) as $value ) {
                $options .= "<option value='"
                    . strtr($value['cde'], ",'", "\\\\") . "'>" . $value['val']
                    . "</option>";
            }
        } else {
            foreach ( $this->getSelectValues(0) as $value ) {
                $options .= "<option value='"
                    . strtr($value['cde'], ",'", "\\\\") . "'>" . $value['val']
                    . "</option>";
            }
        }
        echo "<div id='div_parm_$this->id'>
                      <br />
                      <label for='prm_$this->id'>$this->prompt</label>";
        $this->formCommon($options);
        echo "</div>";
    }

    public function ajaxGetChildUpdate() {
        $options = "";
        if (!$this->requiredInd) {
            $options .= "<option value='' selected='selected'>All</option>";
        }
        if (isset($_GET['reportParameterVal'])) {
            foreach ( $this->getSelectValues($_GET['reportParameterVal']) as $value ) {
                $options .= "<option value='"
                    . strtr(str_replace("'", "\\'", $value['cde']), ',', "\\") . "'>" . $value['val']
                    . "</option>";
            }
        }
        $this->formCommon($options);
    }
}
