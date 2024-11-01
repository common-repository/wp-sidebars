jQuery(function($) {
    $('.adv_settings').click(function(e) {
        e.preventDefault();

        var $div = $('#adv_settings'),
            $this = $(this),
            visibility = 'hidden';

        if ($div.is(':hidden')) {
            $div.slideDown();
            $this.html('&larr; Close Advanced Settings');
            visibility = 'visible';
        }
        else {
            $div.slideUp();
            $this.html('View Advanced Settings &rarr;');
        }

        // Flag that advanced settings are hidden
        // so on post back we can show or hide it
        $("input[name='adv_settings']").val(visibility);
    });

    // Check if advanced settings are visible after a postback
    if ($("input[name='adv_settings']").val() == 'visible') {
        $('.adv_settings').click();
    }

    $addSidebarForm = $('#add_sidebars');

    // Suggest a sidebar id
    $('#name', $addSidebarForm).bind('blur keyup', function() {
        var $id = $('#id', $addSidebarForm),
            name = $(this).val();

        // Remove all characters except alphanum and spaces
        var id = name.replace(/[^a-zA-Z0-9\s]/g, '').toLowerCase();

        // Remove multiple spaces with just one
        id = id.replace(/\s+/g, ' ');

        // Next trim off any leading/trailing spaces
        id = trim(id);

        // Replace spaces with dashes
        id = id.replace(/[\s]/g, '-');

        $id.val(id.substr(0, 30));
    });

    // Deleting a sidebar with simple confirmation
    $('.delete_sidebar').click(function() {
       if (! confirm('Are you sure you want to delete the sidebar?')) {
           return false;
       }
       return true;
    });
});

function trim(str) {
	str = str.replace(/^\s+/, '');
	for (var i = str.length - 1; i >= 0; i--) {
		if (/\S/.test(str.charAt(i))) {
			str = str.substring(0, i + 1);
			break;
		}
	}
	return str;
}
