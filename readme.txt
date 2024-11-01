=== WP Sidebars ===
Contributors: danielhong, amagine
Tags: widget, sidebar, page
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 0.6.6

== Description ==

WP Sidebars allows you to generated dynamic sidebars (widget areas) on-the-fly. Quickly generate sidebars for your site. Use the sidebar selector on the page edit screen to select a sidebar for any page. Custom post types are supported.

= Usage =

1. Create a sidebar
1. Add widgets to the sidebar
1. In your templates, use `ai_dynamic_sidebar` in place of `dynamic_sidebar`
4. Configure options in Settings > Sidebars

= Function Reference =

`ai_dynamic_sidebar` takes two optional parameters:

1. `$sidebar_id`. Provide an ID to a specific sidebar. The default is NULL
1. `$location`. Provide a sidebar location, either: left, right, footer. The default is 'left'

To display sidebars dynamically based on the selection for the given page, pass NULL to `$sidebar_id`.

You can also check if a given location has any sidebars. Use `ai_has_sidebar` and pass in the either 'left', 'right', or 'footer'. Returns TRUE if given location has sidebars, FALSE if no sidebars.

== Installation ==

= Install =

1. Unzip the `wp-sidebars.zip` file.
1. Upload the the `wp-sidebars` folder to your `wp-contents/plugins` folder.

= Activate =

1. In your WordPress administration, go to the Plugins page
1. Activate the Sidebars plugin
1. Go to Appearance > Sidebars to create and manage your sidebars

If you find any bugs or have any ideas, please email us.

= Requirements =

* PHP 5.1 or above
* WordPress 3.0 or above

== Screenshots ==

1. Manage sidebars
2. Adding widgets to your sidebars
3. Sidebar options