<?php

/*
**************************************************************************************************************************
** CORAL Organizations Module
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

// Define the MODULE base directory, ending with |/|.
define('BASE_DIR', dirname(__FILE__) . '/');

require_once "../common/common_directory.php";

/**
 * Multibyte capable wordwrap
 *   Found in comments on php.net by Sam B
 * @param string $str
 * @param int $width
 * @param string $break
 * @return string
 */
function mb_wordwrap($str, $width=74, $break="\r\n")
{
    // Return short or empty strings untouched
    if(empty($str) || mb_strlen($str, 'UTF-8') <= $width)
        return $str;

    $br_width  = mb_strlen($break, 'UTF-8');
    $str_width = mb_strlen($str, 'UTF-8');
    $return = '';
    $last_space = false;

    for($i=0, $count=0; $i < $str_width; $i++, $count++)
    {
        // If we're at a break
        if (mb_substr($str, $i, $br_width, 'UTF-8') == $break)
        {
            $count = 0;
            $return .= mb_substr($str, $i, $br_width, 'UTF-8');
            $i += $br_width - 1;
            continue;
        }

        // Keep a track of the most recent possible break point
        if(mb_substr($str, $i, 1, 'UTF-8') == " ")
        {
            $last_space = $i;
        }

        // It's time to wrap
        if ($count > $width)
        {
            // There are no spaces to break on!  Going to truncate :(
            if(!$last_space)
            {
                $return .= $break;
                $count = 0;
            }
            else
            {
                // Work out how far back the last space was
                $drop = $i - $last_space;

                // Cutting zero chars results in an empty string, so don't do that
                if($drop > 0)
                {
                    $return = mb_substr($return, 0, -$drop);
                }

                // Add a break
                $return .= $break;

                // Update pointers
                $i = $last_space + ($br_width - 1);
                $last_space = false;
                $count = 0;
            }
        }

        // Add character from the input string to the output
        $return .= mb_substr($str, $i, 1, 'UTF-8');
    }
    return $return;
}

function buildSelectableHours($fieldNameBase,$defaultHour=8) {
    $label = _('Hour');
    $html = "<select name=\"{$fieldNameBase}[hour]\" aria-label='{$label}' aria-describedby='span_error_{$fieldNameBase}'>";
    for ($hour=1;$hour<13;$hour++) {
        $html .= "<option".(($hour == $defaultHour) ? ' selected':'').">{$hour}</option>";
    }
    $html .= '</select>';
    return $html;
}

function buildSelectableMinutes($fieldNameBase,$intervals=4) {
    $label = _('Minute');
    $html = "<select name=\"{$fieldNameBase}[minute]\" aria-label='{$label}' aria-describedby='span_error_{$fieldNameBase}'>";
    for ($minute=0;$minute<=($intervals-1);$minute++) {
        $html .= "<option>".sprintf("%02d",$minute*(60/$intervals))."</option>";
    }
    $html .= '</select>';
    return $html;
}

function buildSelectableMeridian($fieldNameBase) {
    $label = _('AM/PM');
    return "<select name=\"{$fieldNameBase}[meridian]\" aria-label='{$meridian}' aria-describedby='span_error_{$fieldNameBase}'>
                    <option>AM</option>
                    <option>PM</option>
                </select>";
}

function buildTimeForm($fieldNameBase,$defaultHour=8,$minuteIntervals=4) {
    return buildSelectableHours($fieldNameBase,$defaultHour).buildSelectableMinutes($fieldNameBase,$minuteIntervals).buildSelectableMeridian($fieldNameBase);
}

?>
