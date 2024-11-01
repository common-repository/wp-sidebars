<?php
/*
Plugin Name: WP Sidebars
Plugin URI: http://wordpress.org/extend/plugins/wp-sidebars/
Description: Create sidebars (widget areas) on-the-fly and selectively choose the sidebar to use for a given page. This plugin requires WordPress 3 or greater.
Version: 0.6.6
Author: Daniel Hong <Amagine, Inc.>
Author URI: http://amagine.net/
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Include our dependants
require_once 'libraries/ai-validation.php';
require_once 'libraries/helpers.php';

/**
 * The sidebar class
 */
class AI_Sidebars {
    private $_update_notice = '';
    private $_request_uri;

    public function __construct()
    {
        global $wp_version, $pagenow;

		// We need at least WordPress 3.0
		if (version_compare($wp_version, '3.0', '<')) {
			add_action('admin_notices', array($this, 'wp_version_warning'));
			return false;
		}

        $this->_request_uri = admin_url($pagenow . '?page=wp-sidebars');

        if (is_admin()) {
			// Add link to the Appearance menu
			add_action('admin_menu', array($this, 'add_admin_menu'));

            // Add our admin JavaScript file
            if ($pagenow == 'themes.php') {
                add_action('admin_print_footer_scripts', array($this, 'add_admin_javascript'), 99);
            }
		}
        
        // Register our options
        add_action('admin_init', array($this, 'register_options'));

        // Register the sidebars
        add_action('widgets_init', array($this, 'register_sidebars'));

        // Add hooks into the edit screens
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_meta_box'), 10, 2);
    }

    /**
     * Add admin menu items
     */
    public function add_admin_menu()
    {
        // Add management page to appearance section
        add_theme_page('Sidebars', 'Sidebars', 'manage_options', 'wp-sidebars', array($this, 'management_page'));
        
        // Add options page to settings section
        add_options_page('Sidebars Options', 'Sidebars', 'manage_options', 'wp-sidebars-options', array($this, 'options_page'));
    }

    public function add_admin_javascript()
    {
        echo '<script src="' . plugins_url() . '/wp-sidebars/templates/admin.js"></script>';
    }
    
    /**
     * Register our option into WP options table
     */
    public function register_options()
    {
        register_setting('ai_sidebars', 'ai_sidebars_data' );
    }

    /**
     * Register our sidebars
     */
    public function register_sidebars()
    {
        $sidebars = $this->get_data('sidebars');
        
        if (! empty($sidebars)) {
            // Get the checksum
            $checksum = $this->get_data('checksum');

            // Make sure that the sidebar data we have matches the checksum
            if ($checksum == md5(serialize($sidebars))) {
                // Strip slashes in our array values
                array_walk_recursive($sidebars, 'ai_array_value_stripslashes');
                
                foreach ($sidebars as $sidebar) {
                    register_sidebar($sidebar);
                }
            }
        }
    }

    /**
     * Adds a meta box to pages
     */
    public function add_meta_box()
    {
        $post_types = $this->get_data('post_types', array());
        
        // Need to add a meta box for each of the selected post types
        foreach ($post_types as $post_type) {
            add_meta_box('ai_sidebars_meta_box', __('Sidebars'), array($this, 'sidebar_meta_box'), $post_type, 'side');
        }
    }

    /**
     * The meta box code
     * @global <type> $post
     */
    public function sidebar_meta_box()
    {
        global $post;
        $sidebar_ids = get_post_meta($post->ID, 'ai_post_sidebars', true);
        
        $locations = $this->get_data('locations');
        $sidebars = $this->get_data('sidebars', array());
        
        if (empty($locations)) {
            echo '<p>No sidebar locations have been selected. 
                Select sidebar locations on the 
                <a href="' . admin_url('options-general.php?page=wp-sidebars-options') . '">options page</a>.</p>';
            return;
        }
        
        if (empty($sidebars)) {
            echo '<p>No sidebars have been added. 
                <a href="' . admin_url('themes.php?page=wp-sidebars') . '">Click here to add some sidebars!</a>.</p>';
            return;
        }
        
        require_once 'templates/meta-box.php';
    }

    /**
     * Save the meta box value to the post
     * This method hooks into save_post
     */
    public function save_meta_box($post_id, $post)
    {
        // Verify nonce
        if (! isset($_POST['ai_nonce']) || ! wp_verify_nonce($_POST['ai_nonce'], 'supersecretstring')) {
            return $post_id;
        }
        
        // If doing autosave, return
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        
        $post_types = $this->get_data('post_types', array());

        // Check if current post type is in our array
        if (in_array($post->post_type, $post_types)) {
            if (isset($_POST['ai_location']) && ! empty($_POST['ai_location'])) {
                // Save the sidebar id to the pages meta
                update_post_meta($post->ID, 'ai_post_sidebars', $_POST['ai_location']);
            }
        }
    }

    /**
     * Display a notice about incompatible WP version
     */
    public function wp_version_warning()
    {
        echo '<div class="error fade"><p>Sidebars require WordPress version 3.0 or greater. Disable the plugin, then update your WordPress installation before re-activating.</p></div>';
    }

    /**
     * Returns postback or form error notices
     */
    public function get_notice($echo = TRUE)
    {
        if (! $echo) {
            return (! empty($this->_update_notice)) ? $this->_update_notice : '';
        }

        echo $this->_update_notice;
    }

    public function set_notice($str, $class)
    {
        if (stristr($str, '<p>') === FALSE) {
            $str = '<p><strong>' . $str . '</strong></p>';
        }
        $this->_update_notice = '<div class="' . $class . ' fade">' . $str . '</div>';
    }
    
    /**
     * Get data from our options array
     * 
     * Keys include: sidebars, checksum, locations, default_id, post_types
     * 
     * @param type $key 
     */
    public function get_data($key, $default = null)
    {
        $data = get_option('ai_sidebars_data');
        
        if (! empty($data) && is_array($data) && key_exists($key, $data)) {
            return $data[$key];
        }
        
        return $default;
    }

    /**
	 * Displays the management page and handles postback
	 */
	public function management_page()
    {
        $template_file = 'manage-sidebar.php';

        // Handle the postback
		if ($_POST && check_admin_referer('wp-sidebars', '_nonce')) {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'add_sidebar' :
                        $this->_add_sidebar();
                        break;

                    case 'edit_sidebar' :
                        $this->_edit_sidebar();
                        break;

                    case 'set_default_id' :
                        $this->_set_default_sidebar_id();
                        break;
                }
            }
		}
        elseif (isset($_GET['action']) && isset($_GET['id'])) {
            switch ($_GET['action']) {
                case 'edit' :
                    $sidebars = $this->get_data('sidebars');
                    
                    // Check if the requested sidebar is valid
                    if (array_key_exists($_GET['id'], $sidebars)) {
                        $sidebar = $sidebars[$_GET['id']];
                        $template_file = 'edit-sidebar.php';
                    }
                    break;

                case 'delete' :
                    $this->_delete_sidebar($_GET['id']);
                    break;
            }
        }

        // Create nonce key for delete validation
        $nonce = wp_create_nonce('ai_sidebar_delete');

        // Get the sidebar widget collection
        // We'll use this to get the widget count for our sidebars
        $sidebar_widgets = wp_get_sidebars_widgets();
        
        // Get the sidebars
        $sidebars = $this->get_data('sidebars');
        
		require_once 'templates/' . $template_file;
	}
    
    public function options_page()
    {
        if ($_POST && check_admin_referer('wp-sidebars', '_nonce')) {
            $options_data = get_option('ai_sidebars_data', array());

            // Make the updates
            $options_data['locations'] = isset($_POST['locations']) ? $_POST['locations'] : array();
            $options_data['post_types'] = isset($_POST['post_types']) ? $_POST['post_types'] : array();
            $options_data['default_id'] = isset($_POST['default_id']) ? $_POST['default_id'] : '';

            // Push changes back into WP
            update_option('ai_sidebars_data', $options_data);
            
            add_settings_error('Sidebars Options', 'wp-sidebar-opt', 'Settings saved.', 'updated');
		}
        
        require_once 'templates/options.php';
    }

    /**
     * Method to add a new sidebar
     * @return <type>
     */
    private function _add_sidebar()
    {
        // Validate the fields
        $validation = new AI_Validation();
        $validation->add_field('name', 'Name', 'required|alpha_numeric[\s\-]');
        $validation->add_field('id', 'ID', 'required|alpha_numeric[_\-]|min_length[5]|max_length[30]');

        if (! $validation->validate()) {
            $this->set_notice($validation->errors, 'error');
            return FALSE;
        }

        $sidebar_id = $this->_post_value('id');
        
        // Get the sidebars
        $sidebars = $this->get_data('sidebars', array());

        // If the id already exists, show warning
        if (array_key_exists($sidebar_id, $sidebars)) {
            $this->set_notice('A sidebar with the given ID already exists. Please use a different sidebar ID.', 'error');
            return FALSE;
        }
        
        // Save the sidebar settings to our existing sidebar array
        $sidebars[$sidebar_id] = array(
            'name'          => $this->_post_value('name'),
            'id'            => $this->_post_value('id'),
            'description'   => $this->_post_value('description'),
            'before_widget' => $this->_post_value('before_widget'),
            'after_widget'  => $this->_post_value('after_widget'),
            'before_title'  => $this->_post_value('before_title'),
            'after_title'   => $this->_post_value('after_title'),
        );

        // Update our options table and refresh the sidebars
        $this->_update_options('sidebars', $sidebars);
        
        // Set success status
        $this->set_notice('New sidebar was added successfully.', 'updated');

        // Unset POST so that posted back values are not shown again
        unset($_POST);
    }

    /**
     * Method to edit an existing sidebar
     * @return <type>
     */
    private function _edit_sidebar()
    {
        // Validate the fields
        $validation = new AI_Validation();
        $validation->add_field('name', 'Name', 'required|alpha_numeric[\s\-]');
        $validation->add_field('id', 'ID', 'required|alpha_numeric[_\-]|min_length[5]|max_length[30]');

        if (!$validation->validate()) {
            $this->set_notice($validation->errors, 'error');
            return FALSE;
        }

        $sidebar_id = $this->_post_value('id');
        $sidebars = $this->get_data('sidebars', array());

        // Make sure id exists
        if (array_key_exists($sidebar_id, $sidebars)) {
            // Update the sidebar
            $sidebars[$sidebar_id]['name'] = $this->_post_value('name');
            $sidebars[$sidebar_id]['before_widget'] = $this->_post_value('before_widget');
            $sidebars[$sidebar_id]['after_widget'] = $this->_post_value('after_widget');
            $sidebars[$sidebar_id]['before_title'] = $this->_post_value('before_title');
            $sidebars[$sidebar_id]['after_title'] = $this->_post_value('after_title');
            $sidebars[$sidebar_id]['description'] = $this->_post_value('description');

            // Update our options table and refresh the sidebars
            $this->_update_options('sidebars', $sidebars);

            // Set success status
            $this->set_notice('Sidebar was updated successfully.', 'updated');

            // Unset POST so that posted back values are not shown again
            unset($_POST);
        }
    }

    /**
     * Method to delete a sidebar
     * @param <type> $sidebar_id
     * @return <type>
     */
    private function _delete_sidebar($sidebar_id)
    {
        $nonce = (isset($_GET['nonce'])) ? $_GET['nonce'] : '';

        if (! wp_verify_nonce($nonce, 'ai_sidebar_delete')) {
            $this->set_notice('The nonce key could not be validated.', 'error');
            return false;
        }

        $sidebars = $this->get_data('sidebars', array());

        // Make sure id exists
        if (array_key_exists($sidebar_id, $sidebars)) {
            // Remove the sidebar
            unset($sidebars[$sidebar_id]);

            // Update our options table and refresh the sidebars
            $this->_update_options('sidebars', $sidebars);

            // Set success status
            $this->set_notice('Sidebar was deleted successfully.', 'updated');
        }
    }

    /**
     * Sets the default sidebar id to use in the case
     * of when a page does not have a sidebar defined
     */
    private function _set_default_sidebar_id()
    {
        update_option('ai_default_sidebar_id', $this->_post_value('default_sidebar_id'));

        // Set success status
        $this->set_notice('Default sidebar ID was added successfully.', 'updated');
    }

    /**
     * Common update procedures
     * 
     * @param   string  $key
     * @param   mixed   $data 
     */
    private function _update_options($key, $data)
    {
        $options_data = get_option('ai_sidebars_data', array());
        
        // Update the data for the given key
        $options_data[$key] = $data;
        
        // If key is sidebar, we also update the checksum
        if ($key == 'sidebars') {
            $options_data['checksum'] = md5(serialize($data));
        }
        
        // Push changes back into WP
        update_option('ai_sidebars_data', $options_data);
    }

    /**
     * Short to get a posted value
     * Value is returned trimed with tags removed and entities decoded
     * 
     * @param   string  $key
     * @return  string
     */
    private function _post_value($key)
    {
        return (isset($_POST[$key])) ? html_entity_decode(trim($_POST[$key]), ENT_QUOTES) : '';
    }
}

// Instantiate the plugin class
$ai_sidebars = new AI_Sidebars();

/**
 * Our template tag to show the sidebars
 * This is used in place of WP dynamic_sidebar()
 * We also support displaying a specific sidebar
 *
 * @param   string  $sidebar_id The sidebar ID to show
 * @param   string  $location   Can be 'left', 'right', 'footer'
 */
function ai_dynamic_sidebar($sidebar_id = null, $location = 'left')
{
    global $post;
    
    // If a sidebar id was given, use that to show the sidebar
    if (! empty($sidebar_id)) {
        return dynamic_sidebar($sidebar_id);
    }

    // Otherwise, we try to get the sidebar defined for the currently viewing page
    $sidebar_ids = get_post_meta($post->ID, 'ai_post_sidebars', true);
    
    $location = strtolower($location);
    
    // Get id that corresponds to the location
    $sidebar_id = (isset($sidebar_ids[$location])) ? $sidebar_ids[$location] : '';

    // If no sidebar defined for the page, we check if a default ID was
    // provided in our AI Sidebars option page
    if (empty($sidebar_id)) {
        $sidebar_id = get_option('ai_default_sidebar_id');
    }

    // Display the sidebar using dynamic_sidebar()
    // If $sidebar_id is still empty at this point, we'll just let WP handle it
    return dynamic_sidebar($sidebar_id);
}

/**
 * Check whether the current page has a sidebar for the given location
 * 
 * @global type $post
 * @param type $location
 * @return type 
 */
function ai_has_sidebar($location)
{
    global $post;
    
    $location = strtolower($location);
    $sidebar_ids = get_post_meta($post->ID, 'ai_post_sidebars', true);
    
    if (isset($sidebar_ids[$location]) && ! empty($sidebar_ids[$location])) {
        return true;
    }
    return false;
}