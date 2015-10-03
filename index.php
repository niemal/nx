<?php

/**
 * Main entrypoint for NX ANALYTICS
 *
 *
 * Include this file on your PHP script:
 *     <?php require('path-to-nx-analytics/index.php'); ?>
 *
 * Or include a script tag in your HTML. Ajax will call this script from the client.
 *     <script src="path-to-nx-analytics/js"></script>
 *
 * The admin panel is at "/admin" and the install script is at "/install".
 **/

define('NX-ANALYTICS', true);

if (!isset($_GET['nx-route'])) { // The script was loaded server-side.

	require_once('src/nx.php');
	$nx = new NX();
	$nx->init();

} else { // The script was requested from a browser.
	$method = $_GET['nx-route'];
	
	switch ($method) {
		case 'js':
			echo 'The javascript is not here yet. Sorry.';
			break;

		case 'admin':
			echo 'The admin panel doesnt exist. Yet.';
			break;

		case 'install':
			require_once('src/install.php');
			break;

		default:
			echo 'You shouldnt be here...';
			break;
	}
}
