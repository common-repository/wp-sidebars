<?php
/**
 * Helper functions
 * 
 * @package    AI Sidebars
 * @author     Daniel Hong <Amagine, Inc.>
 * @copyright  (c) 2011 Amagine, Inc.
 */

if (! function_exists('ai_set_value')) {
    /**
     * Easy way to get a postback value for display in a form field
     * 
     * @param <type> $key
     * @param <type> $default
     * @return <type>
     */
    function ai_set_value($key, $default = '')
    {
        $retval = (isset($_POST[$key])) ? $_POST[$key] : $default;
        return htmlentities(stripslashes(trim($retval)), ENT_QUOTES);
    }
}

if (! function_exists('ai_array_value_stripslashes')) {
    /**
     * Our array_walk callback function
     * This strips slashes in the array values
     */
    function ai_array_value_stripslashes(&$val, $key)
    {
        $val = stripslashes($val);
    }
}

if (! function_exists('ai_location_checkbox')) {
    /**
     * Generates the checkboxes for sidebar locations
     * 
     * @param type $selected 
     */
    function ai_location_checkbox($selected = array())
    {
        $locations = array(
            'left' => 'Left',
            'right' => 'Right',
            'footer' => 'Footer',
        );

        $retval = '';

        foreach ($locations as $key => $value) {
            $checked = (is_array($selected) && in_array($value, $selected)) ? ' checked="checked"' : '';
            $retval .= '<label for="cb_' . $value . '">
                <input type="checkbox" name="locations[]" value="' . $value . '" id="cb_' . $value . '"' . $checked . '>&nbsp;'
                . $value .
            '</label>&nbsp;&nbsp;';
        }

        return $retval;
    }
}

if (! function_exists('ai_post_type_checkbox')) {
    /**
     * Generates a checkbox of all the public post types
     * 
     * @param type $selected
     * @return string 
     */
    function ai_post_type_checkbox($selected = array())
    {
        $retval = '';
        $post_types = get_post_types(array('public' => true, 'show_ui' => true), 'object');

        foreach ($post_types as $key => $post_type) {
            $checked = (is_array($selected) && in_array($key, $selected)) ? ' checked="checked"' : '';
            $retval .= '<label for="cb_' . $key . '">
                <input type="checkbox" name="post_types[]" value="' . $key . '" id="cb_' . $key . '"' . $checked . '>&nbsp;'
                . $post_type->labels->name .
            '</label>&nbsp;&nbsp;';
        }

        return $retval;
    }
}