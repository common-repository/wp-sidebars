<?php wp_nonce_field( 'supersecretstring', 'ai_nonce' ); ?>

<?php foreach ($locations as $location) : ?>
<p><label><?php echo $location; ?> Area:</label><br />
    <select name="ai_location[<?php echo strtolower($location); ?>]" style="width:95%;">
        <option value="">(no sidebar)</option>
        <?php foreach ($sidebars as $sidebar) : ?>
            <option value="<?php echo $sidebar['id']; ?>" <?php echo ($sidebar_ids[strtolower($location)] == $sidebar['id']) ? 'selected="selected"' : ''; ?>><?php echo $sidebar['name']; ?></option>
        <?php endforeach; ?>
    </select>
</p>
<?php endforeach; ?>
