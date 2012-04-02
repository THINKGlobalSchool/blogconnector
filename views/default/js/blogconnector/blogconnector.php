<?php
/**
 * Blog Connector JS
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010-2012
 * @link http://www.thinkglobalschool.org/
 * 
 */
?>
//<script>

elgg.provide('elgg.blogconnector');

// Init 
elgg.blogconnector.init = function() {
	console.log('elgg.blogconnector');
}

elgg.register_hook_handler('init', 'system', elgg.blogconnector.init);