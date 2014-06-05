<?php
/**
 * Blog Connector Helper Library
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.org/
 * 
 */

/**
 * Get blog connector user settings content
 */
function blogconnector_get_usersettings_content() {
	elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
	$user = elgg_get_page_owner_entity();

	// Set the context to settings
	elgg_set_context('settings');

	$title = elgg_echo('blogconnector:label:externalblog');

	elgg_push_breadcrumb(elgg_echo('settings'), "settings/user/$user->username");
	elgg_push_breadcrumb($title);

	$blog_connections = unserialize(elgg_get_plugin_user_setting('blog_connections', $user->getGUID(), 'blogconnector'));
	
	if ($blog_connections) {
		// Get current connection info
		$content = elgg_view('blogconnector/info', array('connections' => $blog_connections));
	}

	// Get the form
	$content .= elgg_view_form('blogconnector/connect');

	$params = array(
		'content' => $content,
		'title' => $title,
	);

	return $params;
}

/**
 * Helper function to grab and parse remote HTML/XML data
 * 
 * @param string $url URL to grab data from
 */
function blogconnector_get_remote_alternate_links($url) {
	// Grab youtube xml data
	$content = file_get_contents($url);
	
	require_once(elgg_get_plugins_path() . 'blogconnector/vendors/simplepie.inc');
	$feed = new SimplePie();
	$feed->set_feed_url($url);
	$feed->enable_cache(FALSE);
	$feed->init();
	$links = $feed->get_all_discovered_feeds();
	
	$feed->__destruct(); // Just in case
	unset($feed);
	
	foreach ($links as $idx => $link) {
		$feed = new SimplePie();
		$feed->set_feed_url($link->url);
		$feed->init();
		
		$feed_type = blogconnector_get_friendly_type($feed->get_type());	
		
		$feed_title = $feed->get_title() . " [" . $feed_type . "]";
		
		$feed->__destruct(); // Just in case
		unset($feed);
		
		$results[$idx]['title'] = $feed_title;
		$results[$idx]['url'] = $link->url;
	}
	
	return $results;
}

/**
 * Get a human readable 'type' for given Simplepie feed type
 * 
 * @param int $type Type returned from Simplepie $feed->get_type()
 * @return string
 */
function blogconnector_get_friendly_type($type) {
	if ($type & SIMPLEPIE_TYPE_NONE) {
		$friendly_type = 'Unknown';
	} elseif ($type & SIMPLEPIE_TYPE_RSS_ALL) {
		$friendly_type = 'RSS';
	} elseif ($type & SIMPLEPIE_TYPE_ATOM_ALL) {
		$friendly_type = 'Atom';
	} elseif ($type & SIMPLEPIE_TYPE_ALL) {
		$friendly_type = 'Supported';
	}
	return $friendly_type;
}

/**
 * Helper function to grab all connected feeds
 * 
 * @return mixed array/false if none
 */
function blogconnector_get_connected_feeds() {
	// Build query to grab a list of users -> feed_urls
	$dbprefix = elgg_get_config('dbprefix');

	// Namespace for enternalblog_url user setting
	$setting_namespace = _elgg_namespace_plugin_private_setting('user_setting', 'blog_connections', 'blogconnector');

	$query = "SELECT p.entity_guid, p.value from {$dbprefix}private_settings p 
			  WHERE p.name = '{$setting_namespace}'";
	$result = get_data($query);

	$feeds = FALSE;

	if ($result) {
		$feeds = array();
		foreach ($result as $r) {
			$feeds[$r->entity_guid] = $r->value;
		}
	}
	
	return $feeds;
}

/**
 * Cron job for connected blog polling
 */
function blogconnector_polling_cron($hook, $type, $value, $params) {
	set_time_limit(0); // No timeout, just in case
	
	$ia = elgg_get_ignore_access();
	elgg_set_ignore_access(TRUE);

	require_once(elgg_get_plugins_path() . 'blogconnector/vendors/simplepie.inc');

	$log .= "Processing Connected Feeds\n";
	$log .= "--------------------------\n";

	// Grab all connected feeds
	$user_feeds = blogconnector_get_connected_feeds();
	
	// If we've got at least one user with feeds, start processing
	if (count($user_feeds) >= 1) {
		// Loop over each user with feed
		foreach ($user_feeds as $user_guid => $feeds_serialized) {
			$user = get_entity($user_guid);

			$feed_array = unserialize($feeds_serialized);

			$log .= "USER: {$user_guid} \n";

			// Loop over each feed for user
			foreach ($feed_array as $feed_idx => $feed) {
				$feed_url = $feed['url'];
				$feed_title = $feed['title'];
				$feed_permalink = $feed['permalink'];
				$feed_updated = $feed['last_update'];

				$log .= "-> FEED: [{$feed_url}]\n\n";
				
				// Init simplepie for feed
				$feed = new SimplePie();
				$feed->set_feed_url($feed_url);
				$feed->enable_cache(FALSE);
				$feed->init();

				// If we have an error initting the feed..
				if ($feed_error = $feed->error()) {
				    $log .= "   ERROR: {$feed_error}\n";
				} else {
					// Keep count of new posts
					$count = 0;

					// Loop over posts, only grabbing 50 most recent for sanity reasons
					foreach ($feed->get_items(0, 50) as $idx => $item) { 
						$item_date = $item->get_date("U");
						
						if ($idx == 0) {
							// Lazy storing last post
							$last_post = $item->get_date('U');
						}
						
						// If item posted after last updated, create new item
						if ($item_date > $feed_updated) {
							$count++;
							$log .= "   {$item->get_title()} - {$item->get_date()} ({$item_date})\n";

							// Create new entity
							$blog_activity = new ElggObject();
							$blog_activity->subtype = "connected_blog_activity";
							$blog_activity->owner_guid = $user_guid;
							$blog_activity->container_guid = $user_guid;
							$blog_activity->access_id = ACCESS_LOGGED_IN; // @TODO? 
							$blog_activity->title = $item->get_title();
							$blog_activity->time_created = $item_date;
							$blog_activity->blog_title = $feed_title;
							$blog_activity->blog_permalink = $feed_permalink;
							$blog_activity->item_permalink = $item->get_permalink();
							$blog_activity->item_id = $item->get_id();

							// Try to save
							if (!$blog_activity->save()) {
								$log .= "      !!! ERROR SAVING ENTITY !!!";
							} else {
								// Add to river
								$river = add_to_river(
									"river/object/connected_blog_activity/create", 
									"create",
									$user_guid, 
									$blog_activity->guid, 
									$blog_activity->access_id,
									$item_date
								);
								$log .= "      -> GUID: {$blog_activity->guid} - RIVER: {$river}\n\n";
							}			
						}
					}
					// End post foreach

					$log .= "\n   Found {$count} new post(s)!\n";
					
					if ($count > 0) {
						$log .= "\n   Last Updated Post: {$last_post}";

						// Set last updated
						$feed_array[$feed_idx]['last_update'] = $last_post;
						$log .= "\n   Set Feed Last Update: " . $feed_array[$feed_idx]['last_update'] . "\n";
					}
				}
				$feed->__destruct(); // Just in case
				unset($feed);
			}
			// End feed foreach

			// Update user blog connections
			elgg_set_plugin_user_setting('blog_connections', serialize($feed_array), $user_guid, 'blogconnector');

			$log .= "\n";
		}
		// End user foreach
	} else {
		$log .= 'No feeds';
	}
	
	if (elgg_in_context('blogconnector_log')) {
		echo $log;
	}

	elgg_set_ignore_access($ia);
	return $value;
}