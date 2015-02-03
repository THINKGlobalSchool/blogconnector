<?php
/**
 * Blog Connector
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 * 
 */

elgg_register_event_handler('init', 'system', 'blogconnector_init');

function blogconnector_init() {
	// Blogconnector library
	elgg_register_library('elgg:blogconnector', elgg_get_plugins_path() . 'blogconnector/lib/blogconnector.php');
	elgg_load_library('elgg:blogconnector');

	// Register JS
	$bc_js = elgg_get_simplecache_url('js', 'blogconnector/blogconnector');
	elgg_register_js('elgg.blogconnector', $bc_js);

	// Register the barge JS
	$bc_css = elgg_get_simplecache_url('css', 'blogconnector/css');
	elgg_register_css('elgg.blogconnector', $bc_css);

	// Page setup to add blog connector to user settings
	elgg_register_event_handler('pagesetup', 'system', 'blogconnector_pagesetup');

	// Cron hook for blog polling
	$polling = elgg_get_plugin_setting('pollingfrequency', 'blogconnector');
	if (!$polling) {
		$polling = 'daily';
	}
	elgg_register_plugin_hook_handler('cron', $polling, 'blogconnector_polling_cron');

	// Blog connector page handler
	elgg_register_page_handler('blogconnector', 'blogconnector_page_handler');
	
	// URL Handler
	elgg_register_plugin_hook_handler('entity:url', 'object', 'blogconnector_url_handler');

	// Actions
	$action_base = elgg_get_plugins_path() . 'blogconnector/actions/blogconnector';
	elgg_register_action("blogconnector/connect", "$action_base/connect.php");
	elgg_register_action("blogconnector/disconnect", "$action_base/disconnect.php");

	// Register for search
	elgg_register_entity_type('object', 'connected_blog_activity');
	
	// Ajax view whitelist
	elgg_register_ajax_view('blogconnector/remote_feeds');
}

/**
 * Blog connector page handler
 *
 * @param array $page Array of url parameters
 * @return bool
 */
function blogconnector_page_handler($page) {
	elgg_load_js('elgg.blogconnector');
	elgg_load_css('elgg.blogconnector');

	switch ($page[0]) {
		case 'settings':
		default:
			gatekeeper();
			$params = blogconnector_get_usersettings_content();
			break;
		case 'test':
			if (elgg_is_admin_logged_in()) {
				echo "<pre>";
				elgg_push_context('blogconnector_log');
				blogconnector_polling_cron(null, null, null, null);
				elgg_pop_context();
				echo "</pre>";
			} else {
				forward();
			}
			return TRUE;
			break;
		case 'delete':
			if (elgg_is_admin_logged_in()) {
				// Delete all items and river items
				set_time_limit(0);
				$connected_blogs = new ElggBatch('elgg_get_entities', array(
					'type' => 'object',
					'subtype' => 'connected_blog_activity',
					'limit' => 0,
				));
			
				echo "<pre>";
				$count = 0;
				foreach ($connected_blogs as $blog) {
					$blog->delete();
					elgg_delete_river(array(
						'object_guid' => $blog->guid,
					));
					$count++;
				}
				echo "Deleted $count blog(s)";
				echo "</pre>";
			} else {
				forward();
			}
			return TRUE;
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

/**
 * Returns the URL from a connected blog entity
 *
 * @param string $hook   'entity:url'
 * @param string $type   'object'
 * @param string $url    The current URL
 * @param array  $params Hook parameters
 * @return string
 */
function blogconnector_url_handler($hook, $type, $url, $params) {
	$entity = $params['entity'];

	// Check that the entity is a photo object
	if (!elgg_instanceof($entity, 'object', 'connected_blog_activity')) {
		return;
	}

	return $entity->item_permalink;
}