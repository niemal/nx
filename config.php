<?php
if (!defined('NX-ANALYTICS')) die('Go away.');

$nx_config = [
	/**
	 * This will determine which mode of NX ANALYTICS will be enabled
	 *	- "simple" will log URL, user agent and referer. Very light.
	 *	- "advanced" will log and track IP addresses (including those behind proxies),
	 *		parse user agents, and log referers. Overall better for data manipulation.
	 **/
	'mode' => 'advanced',

	/* This is the hostname for a MySQL server. */
	'hostname' => 'localhost',
	
	/* Used to auth to the database. */
	'user' => 'username',
	'pass' => 'p4ssw0rd',
	
	/* Name of the default database used in nx analytics. */
	'database' => 'analytics'
];
