<?php
/**
 * Blog Connector Remote Feeds View
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.org/
 * 
 */
elgg_load_library('elgg:blogconnector');

$url = get_input('url', FALSE);

// @TODO This is my 'fancy' way.. needs testing on live server. 
$feeds = blogconnector_get_remote_alternate_links($url);

if (count($feeds) > 1) {
	$list = "<ul class='blogconnector-feed-list'>";
	
	foreach ($feeds as $feed) {
		$feed_link = "<a target='_blank' href='{$feed['url']}'>{$feed['title']}</a>";
		$feed_radio = "<input class='blogconnector-feed-select' type='radio' name='feed_url_lookup' value='{$feed['url']}' />";
		
		$list .= "<li class='clearfix'><div class='feed-link left'>$feed_link</div><div class='feed-radio right'>$feed_radio</div></li>";
	}
	
	$list .= "</ul>";
	
	$feeds_label = elgg_echo('blogconnector:success:foundfeeds');
	
	$connect_button = elgg_view('input/submit', array(
		'id' => 'blogconnector-connect-lookup-button',
		'name' => 'lookup_connect',
		'value' => elgg_echo('blogconnector:label:connect'),
	));

	$title_label = elgg_echo('blogconnector:label:title');
	$title_input = elgg_view('input/text', array(
		'name' => 'feed_title_lookup',
		'id' => 'blogconnector-blog-title',
		'value' => '', 
	));
	
	$content = <<<HTML
		<div class='blogconnector-found-feeds-list'>
			<label>$feeds_label</label>
			$list
		</div><br />
		<div>
			<label>$title_label</label>
			$title_input
		</div>
		<br />
		<div class='elgg-foot'>
			$connect_button
		</div>
HTML;
	
} else {
	$no_feeds_label = elgg_echo('blogconnector:error:nofeeds');
	$content = <<<HTML
		<div class='blogconnector-no-feeds'>
			<label>$no_feeds_label</label>
		</div>
HTML;
}
echo $content;