<?php
/**
 * Blog Connector
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010-2012
 * @link http://www.thinkglobalschool.org/
 * 
 */

elgg_register_event_handler('init', 'system', 'blogconnector_init');

function blogconnector_init() {
	// Contest library
	elgg_register_library('elgg:blogconnector', elgg_get_plugins_path() . 'blogconnector/lib/blogconnector.php');

	// Register JS
	$bc_js = elgg_get_simplecache_url('js', 'blogconnector/blogconnector');
	elgg_register_simplecache_view('js/blogconnector/blogconnector');
	elgg_register_js('elgg.blogconnector', $bc_js);

	// Register the barge JS
	$bc_css = elgg_get_simplecache_url('css', 'blogconnector/css');
	elgg_register_simplecache_view('css/blogconnector/css');
	elgg_register_css('elgg.blogconnector', $bc_css);

	//elgg_load_css('elgg.blogconnector');
	//elgg_load_js('elgg.blogconnector');	
}