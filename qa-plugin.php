<?php

/*
	Question2Answer (c) Gideon Greenspan
	QA Invite Plugin (c) Amiya Sahu (developer.amiya@outlook.com)

	http://www.question2answer.org/

	
	File: qa-plugin/qa-invite/qa-plugin.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Initiates example page plugin


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/

/*
	Plugin Name: Invite 
	Plugin URI: http://www.amiyasahu.com/
	Plugin Description: Invite Others plugin via email 
	Plugin Version: 1.0
	Plugin Date: 2014-11-06
	Plugin Author: Amiya Sahu
	Plugin Author URI: http://www.amiyasahu.com/
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: 
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}

	if (!defined('INVITE_PLUGIN_DIR')) {
		define('INVITE_PLUGIN_DIR', dirname(__FILE__));
	}
	if (!defined('INVITE_PLUGIN_DIR_NAME')) {
		define('INVITE_PLUGIN_DIR_NAME', basename(dirname(__FILE__)));
	}

	require_once INVITE_PLUGIN_DIR . '/qa-invite-utils.php' ;
	require_once QA_INCLUDE_DIR . '/qa-util-string.php' ;
	qa_register_plugin_module('page', 'qa-invite-page.php', 'qa_invite_page', 'Invite Page');
	qa_register_plugin_phrases('qa-invite-lang-*.php', 'invite');
	

/*
	Omit PHP closing tag to help avoid accidental output
*/