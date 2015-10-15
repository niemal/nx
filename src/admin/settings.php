<?php
	if (!defined('NX-ANALYTICS')) die('Go away.');

	$settings = [
		'success' => false,
		'success-text' => '',
		'error' => false,
		'error-h2' => 'Error',
		'error-text' => ''
	];

	if (isset($_POST['submit'])) {
		if (strlen($_POST['current_pw'])) {
			if (!(strlen($_POST['new_pw']) && strlen($_POST['new_pw_repeat']))) {
				$settings['error'] = true;
				$settings['error-text'] = 'You forgot the new password field(s).';
			} else if (strlen($_POST['current_pw']) < 4 || strlen($_POST['current_pw']) > 32 ||
			           strlen($_POST['new_pw']) < 4 || strlen($_POST['new_pw']) > 32 ||
			           strlen($_POST['new_pw_repeat']) < 4 || strlen($_POST['new_pw_repeat']) > 32) {
				$settings['error'] = true;
				$settings['error-text'] = 'Your password field(s) contain an invalid length. Range is minumum 4 characters and maximum 32.';
			} else if ($_POST['new_pw'] !== $_POST['new_pw_repeat']) {
				$settings['error'] = true;
				$settings['error-text'] = 'Your new password doesn\'t match your repeated one.';
			} else if (!change_pass($nx, $user, $_POST['current_pw'], $_POST['new_pw'])) {
				$settings['error'] = true;
				$settings['error-text'] = 'The password you entered is invalid.';
			} else {
				$settings['success'] = true;
				$settings['success-text'] = 'Your password has been changed successfully.';
			}
		}
	}
?><!DOCTYPE html><html>
<head>
	<?php echo _headMeta('Settings | NX'); ?>
	<link rel="stylesheet" href="assets/utils.css">
	<link rel="stylesheet" href="assets/admin.css">
</head>

<body>
	<div id="layout">
		<?php echo _navbar(); ?>

		<div id="main">
			<div class="header">
				<h1>Settings</h1>
			</div>

			<?php if ($settings['success']) { ?>
			<div class="content">
				<div class="content-inner glass article">
					<h2>Success!</h2>
					<p><?php echo $settings['success-text']; ?></p>
				</div>
			</div>
			<?php } else { ?>
			<form action="?admin/settings" method="post" class="content">
				<div class="content-inner glass">
					<?php if ($settings['error'] === true) { ?>
					<div class="article">
						<h2><?php echo $settings['error-h2']; ?></h2>
						<p><?php echo $settings['error-text']; ?></p>
					</div>
					<?php } ?>
					<div class="article">
						<h2>Change your password</h2>
					</div>

					<div class="pure-form pure-form-aligned">
						<fieldset>
							<div class="pure-control-group">
								<label for="current_pw">Current</label>
								<input name="current_pw" type="password" placeholder="password" maxlength="32">
							</div>

							<div class="pure-control-group">
								<label for="new_pw">New</label>
								<input name="new_pw" type="password" placeholder="password" maxlength="32">
							</div>

							<div class="pure-control-group">
								<label for="new_pw_repeat">Repeat new</label>
								<input name="new_pw_repeat" type="password" placeholder="password" maxlength="32">
							</div>
						</fieldset>
					</div>

					<button class="pure-button" type="submit" name="submit">Submit</button>
				</div>
			</form>
			<?php } ?>
		</div>
	</div>

	<script>
		(function (window, document) {

			var layout   = document.getElementById('layout'),
				menu     = document.getElementById('menu'),
				menuLink = document.getElementById('menuLink');

			// this is here because of old browsers
			function toggleClass(element, className) {
				var classes = element.className.split(/\s+/),
					length = classes.length,
					i = 0;

				for(; i < length; i++) {
				  if (classes[i] === className) {
					classes.splice(i, 1);
					break;
				  }
				}
				// The className is not found
				if (length === classes.length) {
					classes.push(className);
				}

				element.className = classes.join(' ');
			}

			menuLink.onclick = function (e) {
				var active = 'active';

				e.preventDefault();
				toggleClass(layout, active);
				toggleClass(menu, active);
				toggleClass(menuLink, active);
			};

		}(this, this.document));
	</script>
</body>
</html>