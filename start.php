<?php
/**
 * Blog Connector
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010-2012
 * @link http://www.thinkglobalschool.org/
 * 
 */

elgg_register_event_handler('init', 'system', 'blogconnector_init');

function blogconnector_init() {
	// Contest library
	elgg_register_library('elgg:blogconnector', elgg_get_plugins_path() . 'blogconnector/lib/blogconnector.php');

	// Register JS
	$bc_js = elgg_get_simplecache_url('js', 'blogconnector/blogconnector');
	elgg_register_simplecache_view('js/blogconnector/blogconnector');
	elgg_register_js('elgg.blogconnector', $bc_js);
	
	// Register google JS
	elgg_register_js('google.jsapi', "https://www.google.com/jsapi", 'head', 1);

	// Register the barge JS
	$bc_css = elgg_get_simplecache_url('css', 'blogconnector/css');
	elgg_register_simplecache_view('css/blogconnector/css');
	elgg_register_css('elgg.blogconnector', $bc_css);

	// Page setup to add blog connector to user settings
	elgg_register_event_handler('pagesetup', 'system', 'blogconnector_pagesetup');

	// Blog connector page handler
	elgg_register_page_handler('blogconnector', 'blogconnector_page_handler');

	// Actions
	$action_base = elgg_get_plugins_path() . 'blogconnector/actions/blogconnector';
	elgg_register_action("blogconnector/connect", "$action_base/connect.php");
	
	// Ajax view whitelist
	elgg_register_ajax_view('blogconnector/remote_feeds');

	//elgg_load_css('elgg.blogconnector');
	//elgg_load_js('elgg.blogconnector');	
}

/**
 * Blog connector page handler
 *
 * @param array $page Array of url parameters
 * @return bool
 */
function blogconnector_page_handler($page) {
	elgg_load_library('elgg:blogconnector');
	elgg_load_js('google.jsapi');
	elgg_load_js('elgg.blogconnector');
	elgg_load_css('elgg.blogconnector');

	switch ($page[0]) {
		case 'settings':
		default:
			gatekeeper();
			$params = blogconnector_get_usersettings_content();
			break;
	}

	$body = elgg_view_layout('one_sidebar', $params);

	echo elgg_view_page($params['title'], $body);

	return true;  
}

/**
 * Blog connector settings sidebar menu
 */
function blogconnector_pagesetup() {
	if (elgg_get_context() == "settings" && elgg_get_logged_in_user_guid()) {
		$user = elgg_get_logged_in_user_entity();

		$params = array(
			'name' => 'blog_connector',
			'text' => elgg_echo('blogconnector:label:externalblog'),
			'href' => "blogconnector/settings",
		);
		elgg_register_menu_item('page', $params);
	}
}