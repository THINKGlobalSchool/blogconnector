<?php
/**
 * Blog Connector User Setting Connect Action
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.org/
 * 
 */
$user = elgg_get_logged_in_user_entity();

// Only one blog at the moment
//$blog_connections = unserialize(elgg_get_plugin_user_setting('blog_connections', $user->getGUID(), 'blogconnector'));

// Update blog connections
if (!elgg_set_plugin_user_setting('blog_connections', null, $user->getGUID(), 'blogconnector')) {
	register_error(elgg_echo('blogconnector:error:disconnect'));
} else {
	system_message(elgg_echo('blogconnector:success:disconnect'));
}

forward(REFERER);
