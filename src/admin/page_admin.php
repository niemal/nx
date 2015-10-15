<?php
	/**
	 * page_admin.php
	 * handles includes depending on is_logged() and $route
	 **/
	require_once('functions.php');

	$nx = new NX();
	$user = is_logged($nx);
	$logged = !empty($user);

	$err = [
		'error' => false,
		'error-h2' => 'Error',
		'error-text' => 'Invalid username or password.'
	];

	if (!$logged && isset($_POST['submit'])) {
		if (!isset($_POST['user']) || !isset($_POST['pass'])) {
			$err['error'] = true;
			$err['error-text'] = 'You forgot something.';
		} else if ( (strlen($_POST['user']) < 4 || strlen($_POST['user']) > 32) ||
					(strlen($_POST['pass']) < 4 || strlen($_POST['pass']) > 32) ) {
			$err['error'] = true;
			$err['error-text'] = 'Both username and password lengths must not be less than 4 and not higher than 32.';
		} else {
			if (isset($_POST['remember'])) $logged_time = 9999999999;
			else                           $logged_time = 7200;

			$err['error'] = !try_to_login($nx, $_POST['user'], $_POST['pass'], $logged_time);
			if (!$err['error']) {
				$user = $_POST['user'];
				$logged = true;
			}
		}
	}

	require('_html.php');
	if($logged === false) {
		require('login.php');
	} else {
		switch($route[1]) {
			case '':
			case 'dashboard':
				require('dashboard.php');
				break;

			case 'statistics':
				require('statistics.php');
				break;

			case 'settings':
				require('settings.php');
				break;

			case 'logout':
				logout($nx, $user);
				header('HTTP/1.1 302 Moved Temporarily');
				header('Location: '. dirname($_SERVER['PHP_SELF']) .'/?admin');
				break;

			default:
				// fallback to login i guess?
				require('login.php');
				break;
		}
	}
