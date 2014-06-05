<?php
/**
 * Blog Connector Settings
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.org/
 * 
 */
elgg_load_js('elgg.blogconnector');

// Simple tab interface for switching between feed lookup and manual entry
elgg_register_menu_item('blogconnector-admin-menu', array(
	'name' => 'bc_settings',
	'text' => elgg_echo('blogconnector:label:admin:settings'),
	'href' => '#blogconnector-admin-settings',
	'priority' => 0,
	'item_class' => 'elgg-state-selected',
	'class' => 'blogconnector-admin-menu-item',
));

elgg_register_menu_item('blogconnector-admin-menu', array(
	'name' => 'bc_connections',
	'text' => elgg_echo('blogconnector:label:admin:connections'),
	'href' => '#blogconnector-admin-connections',
	'priority' => 1,
	'class' => 'blogconnector-admin-menu-item',
));

elgg_register_menu_item('blogconnector-admin-menu', array(
	'name' => 'bc_test_cron',
	'text' => elgg_echo('blogconnector:label:admin:test'),
	'href' => '#blogconnector-admin-test',
	'priority' => 2,
	'class' => 'blogconnector-admin-menu-item',
));

elgg_register_menu_item('blogconnector-admin-menu', array(
	'name' => 'bc_delete',
	'text' => elgg_echo('blogconnector:label:admin:delete'),
	'href' => '#blogconnector-admin-delete',
	'priority' => 3,
	'class' => 'blogconnector-admin-menu-item',
));

$menu = elgg_view_menu('blogconnector-admin-menu', array(
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
));

$polling = $vars['entity']->pollingfrequency;

if (!$polling) {
	$polling = 'daily';
}

// Grab all connected feeds
$user_feeds = blogconnector_get_connected_feeds();

$feed_table = "<table class='elgg-table'><thead><tr><th>User</th><th>Feed</th></tr></thead><tbody>";

if (count($user_feeds) >= 1) {
	foreach ($user_feeds as $user_guid => $feeds_serialized) {
		$user = get_entity($user_guid);

		$feed_array = unserialize($feeds_serialized);

		foreach ($feed_array as $feed) {
			$feed_url = $feed['url'];
			
			$feed_table .= "<tr>";
			$feed_table .= "<td>{$user->name}</td>";
			$feed_table .= "<td><a href='{$feed_url}' target='_blank'>{$feed_url}</a></td>";
			$feed_table .= "</tr>";
		}
	}
} else {
	$feed_table .= "<tr><td colspan='2'>No connected blogs</td></tr>";
}

$feed_table .= "</tbody></table>";

$polling_label = elgg_echo('blogconnector:label:pollingfrequency');
$polling_input = elgg_view('input/dropdown', array(
		'name' => 'params[pollingfrequency]',
		'options_values' => array(
			'minute' => 'minute',
			'fiveminute' => 'fiveminute',
			'fifteenmin' => 'fifteenmin',
			'halfhour' => 'halfhour',
			'hourly' => 'hourly',
			'daily' => 'daily',
			'weekly' => 'weekly',
			'monthly' => 'monthly',
			'yearly' => 'yearly',
	),
	'value' => $polling,
));

$run_cron_input = elgg_view('input/button', array(
	'value' => elgg_echo('blogconnector:label:admin:runcron'),
	'id' => 'blogconnector-run-cron',
));

$delete_input = elgg_view('input/button', array(
	'value' => elgg_echo('blogconnector:label:admin:delete'),
	'id' => 'blogconnector-delete-entities',
));

$content = <<<HTML
	<div>
		$menu
	</div>
	<div id='blogconnector-admin-settings' class='blogconnector-menu-container'>
		<label>$polling_label</label>
		$polling_input
	</div>
	<div style='display: none;' id='blogconnector-admin-connections' class='blogconnector-menu-container'>
		$feed_table
	</div>
	<div style='display: none;' id='blogconnector-admin-test' class='blogconnector-menu-container'>
		$run_cron_input
		<div id='blogconnector-cron-output'>
		</div>
	</div>
	<div style='display: none;' id='blogconnector-admin-delete' class='blogconnector-menu-container'>
		$delete_input
		<div id='blogconnector-delete-output'>
		</div>
	</div>
HTML;

echo $content;