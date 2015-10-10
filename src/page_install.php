<?php
if (!defined('NX-ANALYTICS')) die('Go away.');
error_reporting(E_ALL); ini_set('display_errors', 1);

/**
 * Install script for NX ANALYTICS
 * The user shall tell us information about the database and their website.
 **/

$nx = [
	'skip-install' => false,
	'installed' => false,
	'success' => false,
	'error' => false,
	'error-h2' => 'Error',
	'error-text' => ''
];

// Check if there is a config file
if (file_exists(dirname(__FILE__) . '/config.php')) {
	$nx['installed'] = true;
}


// Check if the install form was submitted
if (isset($_POST['submit'])) {
	$required_data = [
		'admin-user',
		'admin-pass',
		'admin-email',
		'db-user',
		'db-pass',
		'db-host',
		'db-port',
		'nx-mode',
		'nx-errors',
		'nx-db'
	];
	$parsed_data = [];

	foreach($required_data as $k){
		$parsed_data[$k] = (isset($_POST[$k]) && !empty($_POST[$k]) ? ''.$_POST[$k] : -1);
		$parsed_data[$k] = str_replace("'", "\\'", $parsed_data[$k]);

		if($parsed_data[$k] === -1){
			$nx['error'] = true;
			$nx['error-text'] = 'The uploaded form is missing information: ' . $k;
			break;
		} else if( strlen($parsed_data[$k]) < 4 || strlen($parsed_data[$k]) > 32 ){
			$nx['error'] = true;
			$nx['error-text'] = 'The element ' . $k . ' has incorrect length. Allowed range is between 4 and 32.';
			break;
		}
	}

	if ($nx['error'] === false) {
		$db = new mysqli(
				$parsed_data['db-host'],
				$parsed_data['db-user'],
				$parsed_data['db-pass'],
				"",
				$parsed_data['db-port']
		);

		if ($db->connect_error) {
			$nx['error'] = true;
			$nx['error-text'] = 'Failed to establish MySQL connection, please check your credentials.';
		} else {
			$db->query("CREATE DATABASE `" .$parsed_data['nx-db']. "`;");

			if ($db->error) {
				$nx['error'] = true;
				$nx['error-text'] = 'Failed to create database. You sure it doesn\'t exist already?';
			} else {
				$db->select_db($parsed_data['nx-db']);
				$db->query("CREATE TABLE IF NOT EXISTS admin (
								id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
								user TINYTEXT NOT NULL,
								pass TINYTEXT NOT NULL,
								email TINYTEXT NOT NULL,
								regdate INT NOT NULL
				);");
				$db->query("CREATE TABLE IF NOT EXISTS sessions (
				                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				                user TINYTEXT NOT NULL,
				                fingerprint TINYTEXT NOT NULL,
				                logged_time INT NOT NULL,
				                logged_at_time INT NOT NULL
				);");


				$db->query("CREATE TABLE IF NOT EXISTS refs (
								id INT UNSIGNED NOT NULL,
								ref TINYTEXT DEFAULT NULL,
								times INT NOT NULL
				);");
				switch ($parsed_data['nx-mode']) {
					case 'simple':
						$db->query("CREATE TABLE IF NOT EXISTS simple (
										id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
										uri TINYTEXT NOT NULL,
										ua TINYTEXT NOT NULL,
										url TINYTEXT NOT NULL,
										ref TINYTEXT NOT NULL,
										visits INT NOT NULL,
										date INT NOT NULL,
										ts INT NOT NULL
						);");
						break;
					case 'advanced':
						$db->query("CREATE TABLE IF NOT EXISTS advanced (
										id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
										ip TINYTEXT NOT NULL,
										ua TINYTEXT NOT NULL,
										xff TINYTEXT DEFAULT NULL
						);");
						$db->query("CREATE TABLE IF NOT EXISTS urls (
										id INT UNSIGNED NOT NULL,
										url TINYTEXT NOT NULL,
										visits INT NOT NULL,
										time INT NOT NULL
						);");
						break;
				}

				$user =& $parsed_data['admin-user'];
				$pass =& $parsed_data['admin-pass'];
				$email =& $parsed_data['admin-email'];
				$now = time();

				$parsed_data['salt'] = 'LyUrA4aCPhd7I717';
				$pass = hash('sha256', $parsed_data['salt'].$pass);

				$db->query("INSERT INTO admin (user, pass, email, regdate)
				                 VALUES ('$user', '$pass', '$email', $now);");

				unset($parsed_data['admin-user']);
				unset($parsed_data['admin-pass']);
				unset($parsed_data['admin-email']);

				$json = json_encode($parsed_data, 128);
				$output = <<<TEXT
<?php
if (!defined('NX-ANALYTICS')) die('Go away.');

\$nx_config = <<<JSON
$json
JSON;

TEXT;

				file_put_contents(dirname(__FILE__) . '/config.php', $output);
				$nx['success'] = true;
			}
		}
	}
}


?>
<!DOCTYPE html><html>
<head>
	<meta charset="utf-8">
	<title>Install | NX</title>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<link rel="stylesheet" href="assets/install.css">
</head>

<body>
	<div id="layout">
		<div id="main">
			<div class="header">
				<h1>Installation</h1>
				<h2>Welcome to <b>nx analytics</b>.</h2>
			</div>


			<?php if ($nx['success'] === true) { ?>
			<div class="content glass">
				<div class="article">
					<h2>Congratulations!</h2>
					<p><b>nx analytics</b> has been installed successfully! You can now head over to the <a href="?admin">admin panel</a>.</p>
				</div>
			</div>
			<?php } else if ($nx['installed'] === true) { ?>
			<div class="content glass">
				<div class="article">
					<h2>Already installed!</h2>
					<p>It seems like <b>nx analytics</b> is already installed on this system. <br/> If you wish to re-install please delete your <b>config.php</b> and refresh this page.</p>
				</div>
			</div>
			<?php } else { ?>
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="content glass">
				<div class="article">
					<?php if ($nx['error'] === true) { ?>
					<h2><?php echo $nx['error-h2']; ?></h2>
					<p><?php echo $nx['error-text']; ?></p>
					<?php } else { ?>
					<h2>Installing</h2>
					<p>We have detected that your installation requires setting up. We'll start by asking some things:</p>
					<?php } ?>
				</div>

				<div class="article">
					<h2>Who are you?</h2>
					<p>First up, we need to know some things about you.</p>

					<div class="pure-form pure-form-aligned">
						<fieldset>
							<div class="pure-control-group">
								<label for="admin-user">Username</label>
								<input required name="admin-user" type="text" placeholder="admin" pattern=".{4,32}">
							</div>

							<div class="pure-control-group">
								<label for="admin-pass">Password</label>
								<input required name="admin-pass" type="password" placeholder="pass" pattern=".{4,64}">
							</div>

							<div class="pure-control-group">
								<label for="admin-email">Email</label>
								<input required name="admin-email" type="email" placeholder="awesome-admin@me.com">
							</div>
						</fieldset>
					</div>
				</div>

				<div class="article">
					<h2>About your servers</h2>
					<p>We also need to know the credentials of your MySQL install. A database will be created by this script.</p>

					<div class="pure-form pure-form-aligned">
						<fieldset>
							<div class="pure-control-group">
								<label for="db-user">Username</label>
								<input required name="db-user" type="text" placeholder="user">
							</div>

							<div class="pure-control-group">
								<label for="db-pass">Password</label>
								<input required name="db-pass" type="password" placeholder="secur3-p4ssw0rd">
							</div>

							<div class="pure-control-group">
								<label for="db-host">Hostname</label>
								<input required name="db-host" type="text" placeholder="localhost" value="localhost">
							</div>

							<div class="pure-control-group">
								<label for="db-port">Port</label>
								<input required name="db-port" type="text" placeholder="3306" value="3306" pattern="[0-9]{1,5}">
							</div>
						</fieldset>
					</div>
				</div>

				<div class="article">
					<h2>Configuring NX</h2>
					<p>Where do we store the data? What type of installation is the best for your website?</p>
					<p>A <b>simple</b> installation will log URLs, user agents and referers - very light and fast. <br/> An <b>advanced</b> one will log and track IP addresses (including those behind proxies), save and parse user agents, URLs and referers. This will allow the user to manipulate data in many ways, providing great details and powerful filters.</p>
					<p>The error handling will determine how NX lets you know about errors. Choosing <b>show</b> will print all errors to the HTML, while <b>hide</b> will make the script fail silently and redirect errors to <a href="http://stackoverflow.com/a/5127884/4301778">your server's error log</a>.</p>

					<div class="pure-form pure-form-aligned">
						<fieldset>
							<div class="pure-control-group">
								<label for="nx-mode">Mode</label>
								<select required name="nx-mode">
									<option value="simple">simple</option>
									<option value="advanced">advanced</option>
								</select>
							</div>

							<div class="pure-control-group">
								<label for="nx-errors">Errors</label>
								<select required name="nx-errors">
									<option value="show">show</option>
									<option value="hide">hide (be careful!)</option>
								</select>
							</div>

							<div class="pure-control-group">
								<label for="nx-db">Database name</label>
								<input required name="nx-db" type="text" value="nx-analytics">
							</div>
						</fieldset>
					</div>
				</div>

				<div class="article">
					<h2>Ready?</h2>
					<p>Let's go!</p>
					<p><button type="submit" name="submit">Submit</button></p>
				</div>
			</form>
			<?php } ?>
		</div>
	</div>

</body>
</html>