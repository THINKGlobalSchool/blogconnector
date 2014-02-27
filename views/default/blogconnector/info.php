<?php
/**
 * Blog Connector Info
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.org/
 * 
 * @uses $vars['connections']
 */

$user = elgg_get_logged_in_user_entity();
$blog_connections = elgg_extract('connections', $vars);

// Labels
$blog_title_label = elgg_echo('blogconnector:label:connectedtitle');
$blog_site_label = elgg_echo('blogconnector:label:connectedsite');
$blog_url_label = elgg_echo('blogconnector:label:connectedurl');
$blog_connected_label = elgg_echo('blogconnector:label:connecteddate');
$blog_updated_label = elgg_echo('blogconnector:label:updateddate');
$na =  elgg_echo('blogconnector:label:na');

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

$blog_url_link = $blog_url ? elgg_view('output/url', array(
	'text' => $blog_url, 
	'value' => $blog_url,
	'target' => '_blank',
)) : $na;

$blog_updated = ($blog_updated != $blog_connected) ? date("F j, Y", $blog_updated) : $na;
$blog_connected = $blog_connected ? date("F j, Y", $blog_connected) : $na;

$blog_disconnect = elgg_view('output/url', array(
	'href' => elgg_normalize_url('action/blogconnector/disconnect'),
	'text' => elgg_echo('blogconnector:label:disconnect'),
	'title' => elgg_echo('blogconnector:label:disconnect'),
	'class' => 'elgg-button elgg-button-action centered',
	'style' => 'display: block; width: 20%;',
	'is_action' => true,
	'is_trusted' => true,
));

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
	        	<td>$blog_url_link</td>
	        </tr>
			<tr>
	        	<td><strong>$blog_connected_label</strong></td>
	        	<td>$blog_connected</td>
	        </tr>
			<tr>
	        	<td><strong>$blog_updated_label</strong></td>
	        	<td>$blog_updated</td>
	        </tr>
	        <tr>
	        	<td colspan='2'>$blog_disconnect</td>
	        </tr>
		</table>
	</div>
HTML;

$content_title = elgg_echo('blogconnector:label:currentconnectiontitle');
$content_module = elgg_view_module('info', $content_title, $content);

echo $content_module;