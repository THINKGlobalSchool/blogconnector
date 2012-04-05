<?php
/**
 * Blog Connector Info
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010-2012
 * @link http://www.thinkglobalschool.org/
 * 
 */

$user = elgg_get_logged_in_user_entity();

// Labels
$blog_title_label = elgg_echo('blogconnector:label:connectedtitle');
$blog_site_label = elgg_echo('blogconnector:label:connectedsite');
$blog_url_label = elgg_echo('blogconnector:label:connectedurl');
$blog_connected_label = elgg_echo('blogconnector:label:connecteddate');
$blog_updated_label = elgg_echo('blogconnector:label:updateddate');
$na =  elgg_echo('blogconnector:label:na');

// Get user blog connections
$blog_connections = unserialize(elgg_get_plugin_user_setting('blog_connections', $user->getGUID(), 'blogconnector'));

if (is_array($blog_connections)) {
	$blog_title = $blog_connections[0]['title'];
	$blog_site = $blog_connections[0]['permalink'];
	$blog_url = $blog_connections[0]['url'];
	$blog_connected = $blog_connections[0]['connected'];
	$blog_updated = $blog_connections[0]['last_update'];
}

$blog_title = $blog_title ? $blog_title : $na;

$blog_site = $blog_site ? elgg_view('output/url', array(
	'text' => $blog_site, 
	'value' => $blog_site,
	'target' => '_blank',
)) : $na;

$blog_url = $blog_url ? elgg_view('output/url', array(
	'text' => $blog_url, 
	'value' => $blog_url,
	'target' => '_blank',
)) : $na;

$blog_updated = ($blog_updated != $blog_connected) ? date("F j, Y", $blog_updated) : $na;
$blog_connected = $blog_connected ? date("F j, Y", $blog_connected) : $na;

$content = <<<HTML
	<div class='blogconnector-current-connection'>
		<table class='elgg-table'>
	        <tr>
	        	<td><strong>$blog_title_label</strong></td>
	        	<td>$blog_title</td>
	        </tr>
			<tr>
	        	<td><strong>$blog_site_label</strong></td>
	        	<td>$blog_site</td>
	        </tr>
	        <tr>
	        	<td><strong>$blog_url_label</strong></td>
	        	<td>$blog_url</td>
	        </tr>
			<tr>
	        	<td><strong>$blog_connected_label</strong></td>
	        	<td>$blog_connected</td>
	        </tr>
			<tr>
	        	<td><strong>$blog_updated_label</strong></td>
	        	<td>$blog_updated</td>
	        </tr>
		</table>
	</div>
HTML;

$content_title = elgg_echo('blogconnector:label:currentconnectiontitle');
$content_module = elgg_view_module('info', $content_title, $content);

echo $content_module;