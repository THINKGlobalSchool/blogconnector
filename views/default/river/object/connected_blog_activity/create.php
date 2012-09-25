<?php
/**
 * Blog Connector Blog Activity River Item
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010-2012
 * @link http://www.thinkglobalschool.org/
 * 
 */

$object = $vars['item']->getObjectEntity();

$owner = $object->getOwnerEntity();
$owner_link = elgg_view('output/url', array(
	'text' => $owner->name,
	'href' => $owner->getURL(),
));

$blog_link = elgg_view('output/url', array(
	'text' => $object->blog_title,
	'href' => $object->blog_permalink, 
));

$item_link = elgg_view('output/url', array(
	'text' => $object->title,
	'href' => $object->item_permalink,
));

$summary = elgg_echo('river:create:object:connected_blog_activity_river', array($owner_link, $item_link, $blog_link));

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'summary' => $summary,
));