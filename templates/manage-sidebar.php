<div class="wrap nosubsub">
    <div id="icon-themes" class="icon32"><br /></div>
    <h2>Sidebars</h2>
    <?php $this->get_notice(); ?>
    <?php
    // Our static messages
    if (isset($_GET['message'])) {
        switch ($_GET['message']) {
            case '1' :
                echo '<div class="updated fade"><p><strong>Sidebar was updated successfully.</strong></p></div>';
                break;
        }
    }
    ?>
    <div id="col-container">
        <div id="col-right">
            <div class="col-wrap">
                <table class="widefat tag fixed" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col" id="name" class="manage-column" style="padding-left:12px;">Name</th>
                            <th scope="col" id="description" class="manage-column">Description</th>
                            <th scope="col" id="widgets" class="manage-column column-posts num">Widgets</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th scope="col" class="manage-column" style="padding-left:12px;">Name</th>
                            <th scope="col" class="manage-column">Description</th>
                            <th scope="col" class="manage-column column-posts num">Widgets</th>
                        </tr>
                    </tfoot>
                    <tbody id="the-list">
                    <?php if (! empty($sidebars)) :
                        $count = 0;
                        // Sort by key
                        ksort($sidebars);

                        foreach ($sidebars as $sidebar) :
                            $count++;
                            $alt = ($count % 2 == 0) ? ' class="alternate"' : '';
                    ?>
                        <tr id="tag-7"<?php echo $alt; ?>>
                            <td style="padding-left:12px;"><strong><a class="row-title" href="<?php echo $this->_request_uri; ?>&action=edit&id=<?php echo $sidebar['id']; ?>" title="Edit <?php echo $sidebar['name']; ?>"><?php echo $sidebar['name']; ?></a></strong><br />
                                <div class="row-actions">
                                    <span><a href="widgets.php">Add Widgets</a> | </span>
                                    <span class='edit'><a href="<?php echo $this->_request_uri; ?>&action=edit&id=<?php echo $sidebar['id']; ?>">Edit</a> | </span>
                                    <span class="delete"><a class="delete_sidebar" href="<?php echo $this->_request_uri; ?>&action=delete&id=<?php echo $sidebar['id']; ?>&nonce=<?php echo $nonce; ?>">Delete</a></span>
                                </div>
                            </td>
                            <td><?php echo $sidebar['description']; ?></td>
                            <td class="column-posts num"><a href="widgets.php"><?php echo (isset($sidebar_widgets[$sidebar['id']])) ? count($sidebar_widgets[$sidebar['id']]) : 0; ?></a></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>

                <br class="clear" />
                <div class="form-wrap">
                    <p><strong>Note:</strong><br />
                        Deleting a sidebar will cause pages that are using the sidebar to revert to the default sidebar specified.
                        If a default sidebar is not specified, a sidebar may not be shown depending on the setup of your theme.
                        Be sure to update the sidebar selection for those pages.
                        <a href="<?php echo admin_url('options-general.php?page=wp-sidebars-options'); ?>">Manage Sidebars options here.</a></p>
                </div>
            </div>
        </div>
        <!-- /col-right -->

        <div id="col-left">
            <div class="col-wrap">
                <div class="form-wrap">
                    <h3>Add New Sidebar</h3>
                    <form id="add_sidebars" method="post" action="<?php echo $this->_request_uri; ?>">
                        <input type="hidden" name="action" value="add_sidebar" />
                        <input type="hidden" name="adv_settings" value="<?php echo ai_set_value('adv_settings'); ?>" />
                        <?php wp_nonce_field('wp-sidebars', '_nonce'); ?>
                        
                        <div class="form-field form-required">
                            <label for="name">Name</label>
                            <input name="name" id="name" type="text" value="<?php echo ai_set_value('name'); ?>" size="40" aria-required="true" />
                        </div>
                        <div class="form-field form-required">
                            <label for="id">ID</label>
                            <input name="id" id="id" type="text" value="<?php echo ai_set_value('id'); ?>" size="40" aria-required="true" />
                            <p>The "ID" is how WordPress will reference this sidebar. You may use the auto-generated ID, or specify your own. The ID must only contain alpha-numeric, dashes, or underscore characters.</p>
                        </div>
                        <div class="form-field">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="5" cols="40"><?php echo ai_set_value('description'); ?></textarea>
                            <p>Sidebar description.</p>
                        </div>
                        
                        <div class="form-field">
                            <a href="#" class="adv_settings">View Advanced Settings &rarr;</a>
                        </div>

                        <div id="adv_settings" class="hidden">
                            <div class="form-field">
                                <label for="before_widget">Before Widget</label>
                                <input name="before_widget" id="before_widget" type="text" value="<?php echo ai_set_value('before_widget', '<li id="%1$s" class="%2$s">'); ?>" size="40" />
                                <p>Text to place before every widget.</p>
                            </div>
                            <div class="form-field">
                                <label for="after_widget">After Widget</label>
                                <input name="after_widget" id="after_widget" type="text" value="<?php echo ai_set_value('after_widget', '</li>'); ?>" size="40" />
                                <p>Text to place after every widget.</p>
                            </div>
                            <div class="form-field">
                                <label for="before_title">Before Title</label>
                                <input name="before_title" id="before_title" type="text" value="<?php echo ai_set_value('before_title', '<h4>'); ?>" size="40" />
                                <p>Text to place before every title.</p>
                            </div>
                            <div class="form-field">
                                <label for="after_title">After Title</label>
                                <input name="after_title" id="after_title" type="text" value="<?php echo ai_set_value('after_title', '</h4>'); ?>" size="40" />
                                <p>Text to place after every title.</p>
                            </div>
                        </div>
                        <p class="submit">
                            <input type="submit" class="button" name="submit" id="submit" value="Add Sidebar" />
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <!-- /col-left -->

    </div>
    <!-- /col-container -->
</div>
<!-- /wrap -->