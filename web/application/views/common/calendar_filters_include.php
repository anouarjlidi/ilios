<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file calendar_filters_include.php
 *
 * An assortment of form-builder/templating functions for the search/filter widget on the calendar/dashboard pages.
 * @todo the only "real" helper function here is <code>generateCheckboxElementsFromArray</code>.
 * @todo Move this to the templating helper script, convert the rest of this mess into a template script.
 * @todo [ST 2013/11/27]
 */

/**
 * Generates and returns an HTML form displaying given filtering options.
 *
 * @param array $filtersData An associative array containing the form content.
 * @return string The generated markup.
 */
function generateCalendarFiltersFormContent ($filtersData) {

    // Header Div
    $content = '<div class="hd">';
    $content .= $filtersData['calendar_filters_title'];
    $content .= '</div>';

    // Body Div
    $content .= '<div class="bd">' . '<form method="GET" action="#">';

    $content .= '<div>';
    $content .= '<strong>' . $filtersData['calendar_filters_title'] . '</strong>';


    // Generate drop down box for academic year
    $content .= '<span style="float: right; padding-left: 5px;">'.$filtersData['academic_year_title'].'&nbsp;';

    $current_year = date("Y");
    // If current month is before September, set last academic year as default.  We should probably make this
    // customizable, so that each school can define its own academic year cut-off date.
    if (date("m") < 9) {
        $current_year--;
    }
    $academic_years_array = range($current_year - 5, $current_year + 1);
    $content .= '<select id="calendar_filters_academic_year_select" style="width: 100px;">';
    foreach ($academic_years_array as $year) {
        $nextyear = $year + 1;
        $content .= '<option value="'.$year.'" ';
        if ($current_year == $year)
            $content .= 'selected="selected" ';
        $content .= '>'.$year."-".$nextyear."</option>\n";
    }
    $content .= '</select>';

    $content .= '</span>';

    // Search by topic / course toggle
    $content .= '<span style="float: right;">'
        . '<a id="search_by_course_toggle" href="#">'
        . $filtersData['search_by_course_text'] . '</a>'

        . '<a id="search_by_topic_toggle" href="#" style="display: none;">'
        . $filtersData['search_by_topic_text']. '</a>';
    $content .= '</span>';

    $content .= '<div class="clear"></div>';

    $content .= '</div>';

    $content .= '<div><div style="border-style: solid; border-width: medium thin thin; border-color: chocolate grey grey; margin-top: 8px; padding: 5px; background-color: AliceBlue;">';

    // Generate 'Search by Topic/Detail' panel (div)
    $content .= '<div id="search_by_topic_panel">';

    $content .= 'Show Topic: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_topic_list" class="calendar_filters_checkbox_list" style="height: 5em;">';
    $content .= generateCheckboxElementsFromArray($filtersData['discipline_titles']);
    $content .= '</div><br />';

    $content .= 'Show Session Type: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_topic_session_type_list" class="calendar_filters_checkbox_list" style="height:5em;">';
    $content .= generateCheckboxElementsFromArray($filtersData['session_type_titles']);
    $content .= '</div><br />';

    $content .= 'Show Course Level: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_course_level_list" class="calendar_filters_checkbox_list" style="height:5em;">';
    $content .= generateCheckboxElementsFromArray($filtersData['course_levels']);
    $content .= '</div><br />';

    $content .= 'Show Program / Cohort: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_program_cohort_list" class="calendar_filters_checkbox_list" style="height:5em;">';
    $content .= generateCheckboxElementsFromArray($filtersData['program_cohort_titles']);
    $content .= '</div><br />';

    $content .= '</div><!-- div-search_by_topic_panel -->';

    // Generate 'Search by Course' panel (div)
    $content .= '<div id="search_by_course_panel" style="display: none;">';

    // Since course titles are not unique, we will use the whole string instead of the corresponding course id.
    $course_array = array_values(array_unique($filtersData['course_titles']));

    $content .= 'Show Course: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_course_list" class="calendar_filters_checkbox_list">';
    $content .= empty($course_array) ? '' : generateCheckboxElementsFromArray(array_combine($course_array, $course_array));
    $content .= '</div><br />';

    $content .= 'Show Session Type: ';
    $content .= '<span style="float: right;">';
    $content .= '<a class="select_all_toggle" href="javascript:void(0);" >select all + </a>';
    $content .= '<a class="clear_all_toggle" href="javascript:void(0);" style="display: none;">clear all - </a>';
    $content .= '</span>';
    $content .= '<div id="calendar_filters_course_session_type_list" class="calendar_filters_checkbox_list" style="height: 10em;">';
    $content .= generateCheckboxElementsFromArray($filtersData['session_type_titles']);
    $content .= '</div><br />';

    $content .= '</div><!-- div-search_by_course_panel -->';

    $content .= '</div></div>';

    $content .= <<<EOF
        <div style="padding: 10px 0; overflow:hidden;">
        <div style="float:left">
        <input id="calendar_filters_showmyactivities" type="radio" name="showallactivities" onclick="ilios.ui.radioButtonSelected(this);" />
        <label id="calendar_filters_showmyactivities_label" for="calendar_filters_showmyactivities">show my schedule only </label><br />
        <input id="calendar_filters_showallactivities" type="radio" name="showallactivities" onclick="ilios.ui.radioButtonSelected(this);" checked />
        <label id="calendar_filters_showallactivities_label" for="calendar_filters_showallactivities" style="font-weight:bold;" >show all activities for selection</label>
        </div>
EOF;

    $content .= '<div style="float:right;"> <br />'
        /* .       '<input id="calendar_filters_search_button" type="button" value="Search" />' */
        /* .       '<input id="calendar_filters_clear_button" type="button" value="Clear" />' */
        . '<span id="calendar_filters_button_group" class="yui-buttongroup">'
        . ' <span id="calendar_filters_search_button" class="yui-button">'
        . '   <span class="first-child">'
        . '     <button type="button">Search</button>'
        . '   </span>'
        . ' </span>'
        . ' <span id="calendar_filters_clear_button" class="yui-button">'
        . '   <span class="first-child">'
        . '     <button type="button">Clear</button>'
        . '   </span>'
        . ' </span>'
        . '</span>'
        .       '</div>';

    $content .= '</div>';

    $content .= '</form>';
    $content .= '</div>';
    $content .= '<div class="ft"></div>';

    return $content;
}
