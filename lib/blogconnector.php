<?php
/**
 * Blog Connector Helper Library
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010-2012
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

	// Get the form
	$form = elgg_view_form('blogconnector/connect');

	$params = array(
		'content' => $form,
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
	
	// Use PHP DOM parser
	$dom = new DOMDocument;
	libxml_use_internal_errors(false);
	$dom->strictErrorChecking = false;
	$dom->loadHTML($content);

	// Use xpath because the DOM parser can't find <link> tags (why?)
	$xpath = new DOMXPath($dom);
	
	// Select rel='alternate' and atom/rss feed links
	$query = "//link[@rel='alternate'][@type='application/rss+xml' or @type='application/atom+xml']";
	
	// Query DOMXPath
	$links = $xpath->query($query);

	// New array for results
	$results = array();
	
	foreach ($links as $idx => $link) {
		$results[$idx]['title'] = $link->getAttribute('title');
		$results[$idx]['url'] = $link->getAttribute('href');
	}
	
	return $results;
}