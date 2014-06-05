<?php
/**
 * Blog Connector JS
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.org/
 * 
 */
?>
//<script>

elgg.provide('elgg.blogconnector');

// Init 
elgg.blogconnector.init = function() {
	// Delegate click handler for find feed button
	$(document).on('click', '#blogconnector-find-button', elgg.blogconnector.findClick)

	// Delegate click handler for connect feed button
	$(document).on('click', '#blogconnector-connect-lookup-button', elgg.blogconnector.lookupConnectClick);

	// Delegate click handler for manual connect feed button
	$(document).on('click', '#blogconnector-connect-manual-button', elgg.blogconnector.manualConnectClick);
	
	// Delegate click handler radio button selects
	$(document).on('click', '.blogconnector-feed-select', elgg.blogconnector.feedSelectClick);

	// Delegate click handler for connect menu items
	$(document).on('click', '.blogconnector-connect-menu-item', elgg.blogconnector.connectMenuClick);

	// Delegate click handler for admin menu items
	$(document).on('click', '.blogconnector-admin-menu-item', elgg.blogconnector.adminMenuClick);
	
	// Delegate click handler for admin run cron
	$(document).on('click', '#blogconnector-run-cron', elgg.blogconnector.adminRunCronClick);
	
	// Delegate click handler for admin delete
	$(document).on('click', '#blogconnector-delete-entities', elgg.blogconnector.adminDeleteClick);
}

// Click handler for find feeds
elgg.blogconnector.findClick = function(event) {
	// Clear existing feed list
	$("#blogconnector-found-feeds-container").html('');

	// Grab remote url and clean/trim it
	var remote_url = elgg.blogconnector.cleanRemoteURL($('#blogconnector-url').val());

	// Check for non empty URL..
	if (!remote_url) {
		elgg.register_error(elgg.echo('blogconnector:error:emptyurl'));
	} else {
		var $_this = $(this).clone();

		var local_url = elgg.get_site_url() + 'ajax/view/blogconnector/remote_feeds?url=' + remote_url;
		
		// Show loader
		$(this).replaceWith("<div class='elgg-ajax-loader' id='blogconnector-ajax-loader'></div>");
		
		// Hit our feed scraper and try to load feeds	
		elgg.get(local_url, {
			success: function(data) {
				$("#blogconnector-found-feeds-container").html(data);
				$("#blogconnector-ajax-loader").replaceWith($_this);
			},
			error: function() {
				console.log('error');
				$("#blogconnector-ajax-loader").replaceWith($_this);
			}
		});
	}
	event.preventDefault();
}

// Click handler for lookup connect click 
elgg.blogconnector.lookupConnectClick = function(event) {
	var title = $('#blogconnector-blog-title').val();
	var url = $('input[name=feed_url_lookup]:checked').val();
	
	// Check title
	if (!title) {
		elgg.register_error(elgg.echo('blogconnector:error:emptytitle'));
		return false;
	}

	// Check url
	if (!url) {
		elgg.register_error(elgg.echo('blogconnector:error:emptyurl'));
		return false; 
	}
}

// Click handler for manual connect click 
elgg.blogconnector.manualConnectClick = function(event) {
	var title = $('input[name=feed_title_manual]').val();
	var url = $('input[name=feed_url_manual]').val();

	// Check title
	if (!title) {
		elgg.register_error(elgg.echo('blogconnector:error:emptytitle'));
		return false;
	}

	// Check url
	if (!url) {
		elgg.register_error(elgg.echo('blogconnector:error:emptyurl'));
		return false;
	}
}

// Click handler for feed select click 
elgg.blogconnector.feedSelectClick = function(event) {
	// @TODO load from actual feed	
	// Get default feed title
	var title = $(this).closest('li').find('div.feed-link a').html();
	
	// Set title
	$('#blogconnector-blog-title').val(title);
}

// Connect menu click handler
elgg.blogconnector.connectMenuClick = function(event) {
	$('.blogconnector-connect-menu-item').parent().removeClass('elgg-state-selected');
	$(this).parent().addClass('elgg-state-selected');

	$('.blogconnector-menu-container').hide();
	$($(this).attr('href')).show();
	
	event.preventDefault();
}

// Admin menu click handler
elgg.blogconnector.adminMenuClick = function(event) {
	$('.blogconnector-admin-menu-item').parent().removeClass('elgg-state-selected');
	$(this).parent().addClass('elgg-state-selected');

	$('.blogconnector-menu-container').hide();
	
	// Hide 'save' button
	if ($(this).attr('href') != "#blogconnector-admin-settings") {
		$('form#blogconnector-settings').find('input.elgg-button-submit').hide();
	} else {
		$('form#blogconnector-settings').find('input.elgg-button-submit').show();
	}
	
	$($(this).attr('href')).show();
	
	event.preventDefault();
}

// Admin run cron click handler 
elgg.blogconnector.adminRunCronClick = function(event) {
	elgg.get(elgg.get_site_url() + "blogconnector/test", {
		success: function(data) {
			$("#blogconnector-cron-output").html(data);
		},
		error: function() {
			$("#blogconnector-cron-output").html("There was an error loading output");
		}
	});
}

// Admin delete click handler 
elgg.blogconnector.adminDeleteClick = function(event) {
	elgg.get(elgg.get_site_url() + "blogconnector/delete", {
		success: function(data) {
			$("#blogconnector-delete-output").html(data);
		},
		error: function() {
			$("#blogconnector-delete-output").html("There was an error loading output");
		}
	});
}

// Clean up/fix remote url
elgg.blogconnector.cleanRemoteURL = function(url) {
	// Trim string 
	url = $.trim(url);

	// Get outta here if string is empty
	if (url.length == 0) {
		return false;
	}
	
	// Check for protocol
	if (!url.startsWith('http://') && !url.startsWith('https://')) {
		// None, add HTTP
		url = 'http://' + url;
	}

	// Trim trailing slashes	
	if (url.endsWith('/')) {
		url = url.substring(0, url.length - 1);
	}
	
	// Finally encode and return the url
	return encodeURIComponent(url);
}

String.prototype.startsWith=function(A){return this.length>=A.length&&this.substr(0,A.length)==A;};
String.prototype.endsWith=function(A){return this.length>=A.length&&this.substr(this.length-A.length)==A;};

elgg.register_hook_handler('init', 'system', elgg.blogconnector.init);