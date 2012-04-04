<?php
/**
 * Blog Connector User Setting Connect Action
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010-2012
 * @link http://www.thinkglobalschool.org/
 * 
 */
$user = elgg_get_logged_in_user_entity();

require_once(elgg_get_plugins_path() . 'blogconnector/vendors/simplepie.inc');

// Which form sent us here..
$manual_connect = get_input('manual_connect');
$lookup_connect = get_input('lookup_connect');

if ($manual_connect) {
	$feed_url = get_input('feed_url_manual');
	$feed_title = get_input('feed_title_manual');
} else if ($lookup_connect) {
	$feed_url = get_input('feed_url_lookup');
	$feed_title = get_input('feed_title_lookup');
}

if (empty($feed_url)) {
	register_error(elgg_echo('blogconnector:error:emptyurl'));
	forward(REFERER);
}

if (empty($feed_title)) {
	register_error(elgg_echo('blogconnector:error:emptytitle'));
	forward(REFERER);
}

// Check feed using simplepie
$feed = new SimplePie();
$feed->set_feed_url($feed_url);
$feed->enable_cache(FALSE);
$feed->init();

// If we have an error initting the feed, display an error (log the real error)
if ($feed_error = $feed->error()) {
    error_log($feed_error);
	register_error(elgg_echo('blogconnector:error:invalidfeed'));
	forward(REFERER);
}

// Try to save feed URL & Title
if (!elgg_set_plugin_user_setting('externalblog_url', $feed_url, $user->getGUID(), 'blogconnector') ||
	!elgg_set_plugin_user_setting('externalblog_title', $feed_title, $user->getGUID(), 'blogconnector')) {
	register_error(elgg_echo('blogconnector:error:savefeed'));
} else {
	system_message(elgg_echo('blogconnector:success:savefeed'));
}

forward(REFERER);
