<?php
if (!defined('NX-ANALYTICS')) die('Go away.');

/**
 * Install script for NX ANALYTICS
 * The user shall tell us information about the database and their website.
 **/

$nx = [
	'installed' => false,
	'success-installing' => false,
	'error' => false,
	'error-h2' => 'Error',
	'error-text' => ''
];

// Check if there is a config file
if (file_exists(dirname(__FILE__).'/config.json')){
	$nx['installed'] = true;
	$nx['error'] = true;
	$nx['error-text'] = 'It seems like NX ANALYTICS is already installed in this system. If you continue, <b>config.php</b> will be overwritten.';
}


// Check if the install form was submitted
if (isset($_POST['submit'])) {
	$data = [];
	foreach($_POST as $k => $v){
		if ($k == 'submit') continue;
		$data[$k] = (isset($_POST[$k]) && !empty($v)) ? $v : -1;
		if($data[$k] === -1){
			$nx['error'] = true;
			$nx['error-text'] = 'The provided form is missing information: ' . $k;
			break;
		}
	}

	// Do we just assume all the data is safe? Really?
	// I'm not risking this. Not going to write a `config.php`.
	$data = json_encode($data, 128);
	file_put_contents(dirname(__FILE__) . '/config.json', $data);
	$nx['success-installing'] = true;
}


?>
<!DOCTYPE html><html>
<head>
	<meta charset="utf-8">
	<title>Install | NX ANALYTICS</title>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<link rel="stylesheet" href="assets/install.css">
</head>
<body>

	<div id="layout">
		<div id="main">
			<div class="header">
				<h1>Installation</h1>
				<h2>Welcome to NX ANALYTICS!</h2>
			</div>


			<?php if ($nx['success-installing'] === true) { ?>
			<div class="content glass">
				<div class="article">
					<h2>Congratulations!</h2>
					<p>NX ANALYTICS was installed successfully! Now head over to the <a href="?admin">admin panel</a>.</p>
				</div>
			</div>
			<?php } else { ?>
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="content glass">
				<div class="article">
					<?php if($nx['error'] === true) { ?>
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
					<p>We also need to know the credentials for your MySQL install. A database will be created by this script.</p>

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
					<p>Where do we store the data? What installation is the best for your website?</p>
					<p>A <b>simple</b> install will log URLs, user agents and referers - very light and fast. <br/> An <b>advanced</b> install will log and track IP addresses (including those behind proxies), save and parse user agents, URLs and referers. This will allow the user to manipulate data in many ways, providing great details and powerful filters.</p>

					<div class="pure-form pure-form-aligned">
						<fieldset>
							<div class="pure-control-group">
								<label for="nx-mode">Mode</label>
								<select required name="nx-mode">
									<option>simple</option>
									<option>advanced</option>
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