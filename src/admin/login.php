<?php
	if (!defined('NX-ANALYTICS')) die('Go away.');
?><!DOCTYPE html><html>
<head>
	<?php echo _headMeta('Login | NX'); ?>
	<link rel="stylesheet" href="assets/utils.css">
	<link rel="stylesheet" href="assets/admin-login.css">
</head>

<body>
	<div id="layout">
		<div id="main">
			<div class="header">
				<h1>Login</h1>
			</div>

			<form action="?admin" method="post" class="content u-center">
				<div class="content-inner glass">
					<?php if ($err['error'] === true) { ?>
					<div class="article">
						<h2><?php echo $err['error-h2']; ?></h2>
						<p><?php echo $err['error-text']; ?></p>
					</div>
					<?php } ?>

					<div class="pure-form pure-form-aligned">
						<fieldset>
							<div class="pure-control-group">
								<input required name="user" type="text" placeholder="Username" maxlength="32">
							</div>

							<div class="pure-control-group">
								<input required name="pass" type="password" placeholder="Password" maxlength="32">
							</div>

							<p><input type="checkbox" name="remember" value="me">&nbsp;Remember me</p>
							<button class="pure-button button-xlarge" type="submit" name="submit">Login</button>
						</fieldset>
					</div>
				</div>
			</form>
		</div>
	</div>

</body>
</html>