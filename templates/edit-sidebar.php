<div class="wrap">
	<div id="icon-themes" class="icon32"><br /></div>
    <h2>Edit Sidebar</h2>
	<?php $this->get_notice(); ?>

	<form method="post" action="<?php echo $this->_request_uri; ?>">
        <input type="hidden" name="id" value="<?php echo ai_set_value('id', $sidebar['id']); ?>" />
        <input type="hidden" name="action" value="edit_sidebar" />
		<?php wp_nonce_field('wp-sidebars', '_nonce'); ?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="name">ID</label></th>
				<td>
                    <input type="text" class="regular-text" value="<?php echo $sidebar['id']; ?>" disabled="disabled" />
                    <span class="description">ID cannot be changed.</span>
                </td>
			</tr>
            <tr valign="top" class="form-field">
				<th scope="row"><label for="name">Name</label></th>
				<td><input type="text" name="name" id="name" value="<?php echo ai_set_value('name', $sidebar['name']); ?>" size="35" /></td>
			</tr>
            <tr valign="top" class="form-field">
                <th scope="row"><label for="description">Description</label></th>
                <td>
                    <textarea name="description" id="description" rows="5" cols="50" style="width: 97%;"><?php echo ai_set_value('description', $sidebar['description']); ?></textarea>
                    <p class="description">Sidebar description.</p>
                </td>
            </tr>
		</table>
		<br />
        
		<h3>Advanced Settings</h3>
        <p>Do not edit these settings if you are unsure with what they do. To read more about sidebar registration, view the <a href="http://codex.wordpress.org/Function_Reference/register_sidebar" target="_blank">register_sidebar</a> documenations at WordPress.org.</p>
		<table class="form-table">
			<tr valign="top" class="form-field">
				<th scope="row"><label for="before_widget">Before Widget</label></th>
				<td>
                    <input type="text" name="before_widget" id="before_widget" value="<?php echo ai_set_value('before_widget', $sidebar['before_widget']); ?>" size="35" />
					<p class="description">Text to place before every widget.</p>
				</td>
			</tr>
            <tr valign="top" class="form-field">
				<th scope="row"><label for="after_widget">After Widget</label></th>
				<td>
                    <input type="text" name="after_widget" id="after_widget" value="<?php echo ai_set_value('after_widget', $sidebar['after_widget']); ?>" size="35" />
					<p class="description">Text to place after every widget.</p>
				</td>
			</tr>
            <tr valign="top" class="form-field">
				<th scope="row"><label for="before_title">Before Title</label></th>
				<td>
                    <input type="text" name="before_title" id="before_title" value="<?php echo ai_set_value('before_title', $sidebar['before_title']); ?>" size="35" />
					<p class="description">Text to place before every title.</p>
				</td>
			</tr>
            <tr valign="top" class="form-field">
				<th scope="row"><label for="after_title">After Title</label></th>
				<td>
                    <input type="text" name="after_title" id="after_title" value="<?php echo ai_set_value('after_title', $sidebar['after_title']); ?>" size="35" />
					<p class="description">Text to place after every title.</p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>