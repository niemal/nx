<?php

/**
 * Main entrypoint for browsers
 *
 *
 * Include NX ANALYTICS your PHP script:
 *     <?php require('src/nx.php'); ?>
 *
 * Or include a script tag in your HTML. Ajax will call this script from the client.
 *     <script src="path-to-nx-analytics/js"></script>
 *
 * The admin panel is at "/admin" and the install script is at "/install".
 **/

define('NX-ANALYTICS', true);

$method = key($_GET);//retrieve the first query argument name from the GET associative array - allows support for secondary arguments.
$method = preg_replace('/[^A-Za-z]/', '', $method);

if (file_exists('src/config.php') === false && $method !== 'install') {
	header('Location: ' . dirname($_SERVER['PHP_SELF']) . '/?install');
	die();
}

switch ($method) {
	/* Javascript code / client-side alternative to `require('src/nx.php')` */
	case 'js':
		echo 'The javascript is not here yet. Sorry.';
		break;

	/* Admin panel */
	case 'admin':
		require_once('src/admin/page_admin.php');
		break;

	/* Install script */
	case 'install':
		require_once('src/page_install.php');
		break;

	default:
		echo 'You shouldnt be here...';
		break;
}

