<?php

/**
 * Main entrypoint for browsers
 * Do _NOT_ require this file in your webpage
 *
 *
 * Include NX ANALYTICS your PHP script:
 *     <?php require('src/nx.php'); ?>
 *
 * Or include a script tag in your HTML. Ajax will call this script from the client.
 *     <script src="path-to-nx-analytics/js"></script>
 *
 * The admin panel is at "/?admin" and the install script is at "/?install".
 **/

define('NX-ANALYTICS', true);

$route = key($_GET);
$route = preg_replace('/[^A-Za-z0-9\/]/', '', $route);
$route = explode('/', $route);

if(!isset($route[0])) $route[0] = '';
if(!isset($route[1])) $route[1] = '';

if (file_exists('src/config.php') === false && $route[0] !== 'install') {
	header('Location: ' . dirname($_SERVER['PHP_SELF']) . '/?install');
	die();
}

switch ($route[0]) {
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
		require_once('src/NX.php');
		$nx = new NX();
		$nx->init();
		break;
}

