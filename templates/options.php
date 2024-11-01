<div class="wrap">
	<div class="icon32" id="icon-options-general"><br></div>
	<h2>Sidebars Options</h2>
	
	<?php settings_errors(); ?>
	
	<form action="<?php echo $this->_request_uri; ?>-options" method="post">
		<?php
        wp_nonce_field('wp-sidebars', '_nonce');
        $options = get_option('ai_sidebars_data');
        ?>
        
		<table class="form-table">
			<tbody>
				<tr valign="top">
                    <th scope="row">Sidebar Locations</th>
                    <td>
                        <span class="description">Select the locations your sidebars will be placed in.</span>
                        <p><?php echo ai_location_checkbox($options['locations']); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Post Types</th>
                    <td>
                        <span class="description">Select the post types for Sidebars to manage.</span>
                        <p><?php echo ai_post_type_checkbox($options['post_types']); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Default Sidebar</th>
                    <td>
                        <input type="text" size="20" value="<?php echo esc_attr( $options['default_id'] ); ?>" name="default_id">
                        <span class="description">Provide the ID of a default sidebar to use when no dynamic sidebars exists.</span>
                    </td>
                </tr>
			</tbody>
        </table>
		
		<?php submit_button(); ?>
	</form>
</div>
