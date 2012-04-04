<?php
/**
 * Blog Connector English Language Translation
 *
 * @package TGSBlogConnector
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010-2012
 * @link http://www.thinkglobalschool.org/
 * 
 */

$english = array(
	// General
	'blogconnector' => 'Blog Connector',

	// Page titles 

	// Labels
	'blogconnector:label:externalblog' => 'External Blog Settings',
	'blogconnector:label:title' => 'Blog Title',
	'blogconnector:label:url' => 'Blog URL',
	'blogconnector:label:find' => 'Find',
	'blogconnector:label:findfeeds' => 'Find Feed(s)',
	'blogconnector:label:manual' => 'Manual Entry',
	'blogconnector:label:connect' => 'Connect',
	'blogconnector:label:currenttitle' => 'Connected Blog Title',
	'blogconnector:label:currenturl' => 'Connected Blog URL',
	'blogconnector:label:currentconnectiontitle' => 'Current Connection',
	'blogconnector:label:newconnectiontitle' => 'Update Connection',

	// River

	// Messages
	'blogconnector:success:foundfeeds' => 'Found the following feed(s). Select one below, enter a title for your Blog and click \'Connect\':',
	'blogconnector:success:savefeed' => 'Successfully saved the blog feed',
	'blogconnector:error:emptytitle' => 'Title cannot be empty',
	'blogconnector:error:emptyurl' => 'URL Cannot be empty',
	'blogconnector:error:nofeeds' => 'Could not find any RSS feeds at given URL. Try again, or enter RSS URL manually.',
	'blogconnector:error:savefeed' => 'There was an error saving the blog feed',
	'blogconnector:error:invalidfeed' => 'There was a problem with the feed URL you supplied.'

	// Other content
);

add_translation('en',$english);

