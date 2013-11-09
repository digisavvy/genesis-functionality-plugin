<?php
/*
Plugin Name: 	Genesis Functionality Plugin
Plugin URI: 	http://www.rvamedia.com/wp-plugins/genesis-functionality-plugin
Description: 	Genesis specific code snippets that are independent of your current child theme.
Author: 		RVA Media, LLC
Author URI: 	http://www.rvamedia.com
Version: 		1.0.0
License: 		GNU General Public License v2.0 or later
License URI: 	http://www.opensource.org/licenses/gpl-license.php
*/

/*
	Copyright 2013	 Rick R. Duncan	 (email : rick@rvamedia.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

	A WORD OR TWO ABOUT SUPPORT
	We created this plugin to scratch our own itch, and are happy to offer the code to the community in
	the spirit of open source. We are not able to provide support. If however, you find a legitimate bug
	please let me know; we take those seriously and will fix them.

	On the other hand, if you’re just having trouble using this plugin, or making it fit your specific needs,
	then we ask kindly that you solve the problem yourself, hire a developer, or get help from the
	extremely helpful Genesis community.
*/


//* Run activation hook, then test for Genesis install and see if HTML5 is activated
register_activation_hook( __FILE__, 'activation_hook' );


//* Confirm site is using Genesis Framework
function activation_hook() {

	if ( 'genesis' != basename( TEMPLATEPATH ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( sprintf( __( 'Sorry, but to activate this plugin the <a href="%s">Genesis Framework</a> is required.'), 'http://www.rvamedia.com/go/get-genesis' ) );
	}

}


//* If user has Genesis, run our custom code
add_action( 'after_setup_theme', 'process_customizations' );


/** ---:[ place your custom code below this line ]:--- */


//* Function to hold our Genesis customizations
function process_customizations(){

	//* Remove unwanted Genesis page layouts
	genesis_unregister_layout( 'content-sidebar-sidebar' );
	genesis_unregister_layout( 'sidebar-sidebar-content' );
	genesis_unregister_layout( 'sidebar-content-sidebar' );


	//* Remove unwanted Genesis widgets
	function remove_genesis_widgets() {
		unregister_widget( 'Genesis_User_Profile_Widget'    );
	}
	add_action( 'widgets_init', 'remove_genesis_widgets', 11 );


	//* Remove Site Tag Line (Setting -> General -> Tagline)
	remove_action( 'genesis_site_description', 'genesis_seo_site_description' );


	//* Remove Site Title (Setting -> General -> Site Title)
	//remove_action( 'genesis_site_title', 'genesis_seo_site_title' );


	//* Customize default text inside of search box
	function rvam_search_text( $text ) {
		return esc_attr( 'Search our store...' );

	}
	add_filter( 'genesis_search_text', 'rvam_search_text' );


	//* Customize default text inside of the search box button
	function rvam_search_button_text( $text ) {
	    return esc_attr( 'Search' );
	}
	add_filter( 'genesis_search_button_text', 'rvam_search_button_text' );


	//* Customize the display of the Genesis breadcrumbs
	function rvam_custom_breadcrumb_args( $args ) {
		$args['home'] = 'Home';                                    	// home text
		$args['sep'] = ' &raquo; ';                                	// the seperator between links
		$args['list_sep'] = ', ';                                  	// Genesis 1.5 and later
		$args['prefix'] = '<div class="breadcrumb">';              	// the breadcrumbs container
		$args['suffix'] = '</div>';
		$args['heirarchial_attachments'] = true;                   	// Genesis 1.5 and later
		$args['heirarchial_categories'] = true;                    	// Genesis 1.5 and later
		$args['display'] = true;
		$args['labels']['prefix'] = '';								// You are here:
		$args['labels']['author'] = 'Archives for ';               	// the author archives
		$args['labels']['category'] = 'Archives for ';             	// the category archives (Genesis 1.6 and later)
		$args['labels']['tag'] = 'Archives for ';                  	// the tag archives
		$args['labels']['date'] = 'Archives for ';                 	// the date archives
		$args['labels']['search'] = 'Search for ';                 	// the search archives
		$args['labels']['tax'] = 'Archives for ';                  	// taxonomy archives
		$args['labels']['post_type'] = 'Archives for ';            	// custom post type archives
		$args['labels']['404'] = 'Not found: ';                    	// 404 breadcrumbs       (Genesis 1.5 and later)

    	return $args;

	}
	add_filter( 'genesis_breadcrumb_args', 'rvam_custom_breadcrumb_args' );


	//* Customize the entire footer
	remove_action( 'genesis_footer', 'genesis_do_footer' );
	function rvam_custom_footer() { ?>
		<p>&copy; Copyright 2013 <a href="http://www.rvamedia.com">RVA Media, LLC</a> &middot; All Rights Reserved</p>
		<?php
	}
	add_action( 'genesis_footer', 'rvam_custom_footer' );


	if( function_exists( 'genesis_html5' ) && genesis_html5() ) {
		//* Filters & Actions for HTML5 versions of child themes


		//* Remove the entry meta in the entry header
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );


		//* Remove the entry meta in the entry footer
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );


		//* Modify the 'Leave a Comment' title in comments
		add_filter( 'comment_form_defaults', 'rvam_comment_form_html5' );


		//* Remove the author box on single posts
		//remove_action( 'genesis_after_entry', 'genesis_do_author_box_single', 8 );

	}
	else {
		//* Filters & Actions for XHTML versions of child themes


		//* Remove the post info
		remove_action( 'genesis_before_post_content', 'genesis_post_info' );


		// Remove post meta
		remove_action('genesis_after_post_content', 'genesis_post_meta');


		//* Modify the speak your mind title in comments
		add_filter( 'comment_form_defaults', 'rvam_comment_form_xhtml' );


		//* Remove the author box on single posts
		//remove_action( 'genesis_after_post', 'genesis_do_author_box_single' );

	}

}


function rvam_comment_form_html5( $fields ) {

	$fields['comment_notes_before'] = ''; 												//Removes Email Privacy Notice
   	$fields['title_reply'] = __( 'Share Your Comments & Feedback:', 'customtheme' ); 	//Changes The Form Headline
	$fields['label_submit'] = __( 'Leave a Comment', 'customtheme' ); 					//Changes The Submit Button Text
	$fields['comment_notes_after'] = ''; 												//Removes Form Allowed Tags Box

    return $fields;

}


function rvam_comment_form_xhtml( $defaults ) {

	$defaults['title_reply'] = __( 'Leave a Comment' );

	return $defaults;

}



/** ---:[ place your custom code above this line ]:--- */

/**
 * Removes the plugin from repo update checks to avoid errant updates.
 *
 * @since 1.0.0
 * @link http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 *
 * @param array $r The request data
 * @param string $url The URL which is being pinged for updates
 * @return array $r The amended request data
 */
function rvam_hide_plugin_updates( $r, $url ) {

	/** If the URL is not from the WordPress API, return the request */
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r;

	/** Unset our plugin if it exists */
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[plugin_basename( __FILE__ )] );
	unset( $plugins->active[array_search( plugin_basename( __FILE__ ), $plugins->active )] );
	$r['body']['plugins'] = serialize( $plugins );

	/** Return the request */
	return $r;

}
add_filter( 'http_request_args', 'rvam_hide_plugin_updates', 5, 2 );