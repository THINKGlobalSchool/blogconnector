<?php
/**
 * Blog Connector Connect Form
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010-2012
 * @link http://www.thinkglobalschool.org/
 * 
 */

$user = elgg_get_logged_in_user_entity();

// Simple tab interface for switching between feed lookup and manual entry
elgg_register_menu_item('blogconnector-connect-menu', array(
	'name' => 'find_feeds',
	'text' => elgg_echo('blogconnector:label:findfeeds'),
	'href' => '#blogconnector-lookup',
	'priority' => 0,
	'item_class' => 'elgg-state-selected',
	'class' => 'blogconnector-connect-menu-item',
));

elgg_register_menu_item('blogconnector-connect-menu', array(
	'name' => 'manual_entry',
	'text' => elgg_echo('blogconnector:label:manual'),
	'href' => '#blogconnector-manual',
	'priority' => 1,
	'class' => 'blogconnector-connect-menu-item',
));

$menu = elgg_view_menu('blogconnector-connect-menu', array(
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
));

$title_label = elgg_echo('blogconnector:label:title');
$title_input = elgg_view('input/text', array(
	'name' => 'feed_title_manual',
	'value' => '', 
));

$url_label = elgg_echo('blogconnector:label:url');
$url_manual_input = elgg_view('input/text', array(
	'name' => 'feed_url_manual',
	'value' => '',
));

$url_lookup_input = elgg_view('input/text', array(
	'id' => 'blogconnector-url',
	'name' => 'find_feed_url',
	'value' => '',
));

$find_input = elgg_view('input/submit', array(
	'id' => 'blogconnector-find-button',
	'name' => 'find',
	'value' => elgg_echo('blogconnector:label:find'),
));

$connect_input = elgg_view('input/submit', array(
	'name' => 'manual_connect',
	'value' => elgg_echo('blogconnector:label:connect'),
	'id' => 'blogconnector-connect-manual-button',
));

$content = <<<HTML
	$current_module
	<div>
		$menu
	</div><br />
	<div id='blogconnector-lookup' class='blogconnector-menu-container'>
		<div>
			<label>$url_label</label><br />
			$url_lookup_input
		</div><br />
		<div>
			$find_input
		</div><br />
		<div id='blogconnector-found-feeds-container'>
		</div>
	</div>
	<div style='display: none;' id='blogconnector-manual' class='blogconnector-menu-container'>
		<div>
			<label>$title_label</label><br />
			$title_input
		</div><br />
		<div>
			<label>$url_label</label><br />
			$url_manual_input
		</div><br />
		<div>
			$connect_input
		</div>
	</div>
HTML;

$content_title = elgg_echo('blogconnector:label:newconnectiontitle');
$content_module = elgg_view_module('info', $content_title, $content);
echo $content_module;