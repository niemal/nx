<?php
if (!defined('NX-ANALYTICS')) die('Go away.');

/**
 * Login page
 * Just some HTML. Real code is at page_admin.php
 **/

?>
<!DOCTYPE html><html>
<head>
	<meta charset="utf-8">
	<title>Login | NX</title>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<link rel="stylesheet" href="assets/admin-login.css">
</head>

<body>
	<div id="layout">
		<div id="main">
			<div class="header">
				<h1>Login</h1>
			</div>


			<form action="?admin" method="post" class="content glass" style="text-align: center">
				<div class="article">
					<?php if ($err['error'] === true) { ?>
					<h2><?php echo $err['error-h2']; ?></h2>
					<p><?php echo $err['error-text']; ?></p>
					<?php } ?>
				</div>

				<div class="pure-form pure-form-aligned">
					<fieldset>
						<div class="pure-control-group">
							<input required name="user" type="text" placeholder="Username">
						</div>

						<div class="pure-control-group">
							<input required name="pass" type="password" placeholder="Password">
						</div>

						<div>
							<p><input type="checkbox" name="remember" value="me">&nbsp;Remember me</p>
							<button class="pure-button button-xlarge" type="submit" name="submit">Login</button>
						</div>
					</fieldset>
				</div>
			</form>
		</div>
	</div>

</body>
</html>