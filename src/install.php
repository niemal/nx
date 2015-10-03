<?php
if (!defined('NX-ANALYTICS')) die('Go away.');

$install_script_url = $_SERVER['REQUEST_URI'];

/**
 * Install script for NX ANALYTICS
 * The user shall tell us information about the database and their website.
 **/


// TODO add backend for POSTed data and write config.php

// remember to CHECK SERVER-SIDE EVERY VARIABLE
// clients can never be trusted

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

			<form action="<?php echo $install_script_url; ?>" method="post" class="content glass">
				<div class="article">
					<h2 class="article-h2">Title</h2>
					<p>Welcome and stuff</p>
				</div>

				<div class="article">
					<h2 class="article-h2">Who are you?</h2>
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
					<h2 class="article-h2">About your servers</h2>
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
					<h2 class="article-h2">Configuring NX</h2>
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
					<h2 class="article-h2">Ready?</h2>
					<p>well too bad there's no backend code lol</p>
				</div>
			</form>
		</div>
	</div>

</body>
</html>