<?php
/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
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


class Html {

  public function nameToID($str) {
    $str = preg_replace('/[^a-zA-Z0-9]/', ' ', $str);
    $str = explode(' ', $str);
    $str = array_map('ucfirst', $str);
    $str = lcfirst(implode('', $str));
    return $str;
  }

  public function humanize($str) {
    $str = trim($str);
    $str = preg_replace('/ID$/i', '', $str);
    $str = preg_replace('/[A-Z]+/', " $0", $str);
    $str = preg_replace('/[^a-z0-9\s+]/i', '', $str);
    $str = preg_replace('/\s+/', ' ', $str);
    $str = explode(' ', $str);
    $str = array_map('ucwords', $str);
    return implode(' ', $str);
  }

  public function label_tag($for, $name = null, $required = false) {
    if ($name === null) {
      $name = (new Html())->humanize($for);
    }

    if ($required) {
      $required_text = '<span class="required">*</span>';
    } else {
      $required_text = '';
    }
    // TODO: i18n (":" and required "*" may not be appropriate)
    return '<label for="'. htmlspecialchars($for).'">'.htmlspecialchars($name).':'.$required_text.'</label>';
  }

  public function hidden_field_tag($name, $value, $options = array()) {
    $default_id = (new Html())->nameToID($name);
    $default_options = array('id' => $default_id);
    $options = array_merge($default_options, $options);

    return '<input type="hidden" id="'.htmlspecialchars($options['id']).'" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value). '" />';
  }

  public function hidden_search_field_tag($name, $value, $options = array()) {
    return (new Html())->hidden_field_tag("search[".$name."]", $value, $options);
  }

  public function text_field_tag($name, $value, $options = array()) {
    $default_id = (new Html())->nameToID($name);
    $default_options = array('id' => $default_id, 'class' => 'changeInput');
    $options = array_merge($default_options, $options);

    return '<input type="text" id="'.htmlspecialchars($options['id']).'" name="'.htmlspecialchars($name).'" class="'.htmlspecialchars($options['class']).'" value="'.htmlspecialchars($value). '" /><p id="span_error_'.htmlspecialchars($options['id']).'" class="error"></p>';
  }

  public function text_field($field, $object, $options = array()) {
    return (new Html())->text_field_tag($field, $object->$field, $options);
  }

  public function text_search_field_tag($name, $value, $options = array()) {
    $default_options = array('class' => '');
    $options = array_merge($default_options, $options);
    return (new Html())->text_field_tag("search[".$name."]", $value, $options);
  }



  public function select_field($field, $object, $collection, $options = array()) {
    $default_options = array();
    $options = array_merge($default_options, $options);

    $str = '<select id="'.$field.'" name="'.$field.'""><option></option>';
    foreach ($collection as $item) {
      if (is_subclass_of($item, 'DatabaseObject')) {
        $key = $item->getPrimaryKeyName();
        $value = $item->$key;
        $name = $item->shortName;
      } else {
        $value = $item;
        $name = $item;
      }
      if ($value == $object->$field) {
        $str .= '<option value="'.htmlspecialchars($value).'" selected="selected">'.htmlspecialchars($name).'</option>';
      } else {
        $str .= '<option value="'.htmlspecialchars($value).'">'.htmlspecialchars($name).'</option>';
      }
    }
    $str .= '</select><p id="span_error_'.$field.'" class="error"></p>';
    return $str;
  }
}
