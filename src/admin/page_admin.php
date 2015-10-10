<?php
	/**
	 * page_admin.php main code.
	 **/
	require_once('functions.php');

	$nx = new NX();
	$user = is_logged($nx);
	$logged = !empty($user);

	$err = [
		'error' => false,
		'error-h2' => 'Error',
		'error-text' => 'Invalid credential combination.'
	];

	if (!$logged) {
		if (isset($_POST['submit'])) {
			if (!isset($_POST['user']) || !isset($_POST['pass'])) {
				$err['error'] = true;
				$err['error-text'] = 'You forgot something.';
			} else if ( (strlen($_POST['user']) < 4 || strlen($_POST['user']) > 32) ||
						(strlen($_POST['pass']) < 4 || strlen($_POST['pass']) > 32) ) {
				$err['error'] = true;
				$err['error-text'] = 'Both username and password legnths must not be less than 4 and not higher than 32.';
			} else {
				if (isset($_POST['remember'])) $logged_time = 9999999999;
				else                           $logged_time = 600;

				$err['error'] = !try_to_login($nx, $_POST['user'], $_POST['pass'], $logged_time);
				if (!$err['error']) {
					$user = $_POST['user'];
					$logged = true;
				}
			}
		}
	} else if (isset($_GET['logout'])) {
		logout($nx, $user);
		header('HTTP/1.1 302 Moved Temporarily');
		header('Location: '. dirname($_SERVER['PHP_SELF']) .'/?admin');
	}


?>
<!DOCTYPE html><html>
<head>
<meta charset="utf-8">
	<title><?php if (!$logged) { ?>Login<?php } else { ?>Home<?php } ?> | NX</title>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">
	<link rel="stylesheet" href="assets/chartist.min.css">
	<link rel="stylesheet" href="assets/admin.css">
</head>

<body>
	<div id="layout">
	<?php
		if ($logged) {
			require_once('src/stats.php');

			$stats = new SIMPLE($nx->db);
	?>
		<a href="#menu" id="menuLink" class="menu-link"><span></span></a>

		<div id="menu" class="dark-glass">
			<div class="pure-menu">
				<a class="pure-menu-heading dark-glass" href="#">nx analytics</a>

				<ul class="pure-menu-list">
					<li class="pure-menu-item"><a href="#" class="pure-menu-link">Dashboard</a></li>
					<li class="pure-menu-item"><a href="#" class="pure-menu-link">Settings</a></li>
					<li class="pure-menu-item"><a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']).'&logout=1'; ?>" class="pure-menu-link">Logout</a></li>
				</ul>
			</div>
		</div>

		<div id="main">
			<div class="header">
				<h1>Dashboard</h1>
				<h2>Mode: <i><?php echo $nx->config['nx-mode']; ?></i></h2>
			</div>

			<div class="content glass">
				<div class="article">
					<h2 class="article-h2" style="text-align: center">Last week's visits</h2>
				</div>
				<?php $last_week = $stats->last_weeks_visits(); ?>
				<div class="ct-chart"></div>
				<p align="middle">Total: <b><?php echo $last_week['total'] ?></b></p>
			</div>


			<div class="pure-g" style="text-align: center">

				<div class="content glass pure-u-1 pure-u-md-1-5">
					<div class="article">
						<h2 class="article-h2" style="text-align: center">Most recent visited URIs</h2>
					</div>
					<?php $recent_visits = $stats->most_recent_uris(); ?>
					<table class="pure-table pure-table-bordered" style="width: 100%">
						<thead>
							<tr>
								<th>URI</th>
								<th>URL</th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($recent_visits as $visit) { ?>
							<tr>
								<td><?php echo $visit['uri']; ?></td>
								<td><?php echo $visit['url']; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>

				<div class="content glass pure-u-1 pure-u-md-1-5">
					<div class="article">
						<h2 class="article-h2" style="text-align: center">Top 5 visited URIs</h2>
					</div>
					<?php $uri_visits = $stats->top_5_uris(); ?>
					<table class="pure-table pure-table-bordered" style="width: 100%">
						<thead>
							<tr>
								<th>URI</th>
								<th>Visits</th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($uri_visits as $visit) { ?>
							<tr>
								<td><?php echo $visit['uri']; ?></td>
								<td><?php echo $visit['n']; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>

			</div>


			<div class="pure-g" style="text-align: center">

				<div class="content glass pure-u-1 pure-u-md-1-5">
					<div class="article">
						<h2 class="article-h2">Web browsers</h2>
					</div>

					<?php $rows = $stats->browsers(); ?>
					<table class="pure-table pure-table-bordered" style="width: 100%">
						<thead>
							<tr>
								<th>Browser</th>
								<th>#</th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($rows as $row) { ?>
							<tr>
								<td><?php echo $row['ua']; ?></td>
								<td><?php echo $row['n']; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>

				<div class="content glass pure-u-1 pure-u-md-1-5">
					<div class="article">
						<h2 class="article-h2">Operating systems</h2>
					</div>

					<?php $rows = $stats->operating_systems(); ?>
					<table class="pure-table pure-table-bordered" style="width: 100%">
						<thead>
							<tr>
								<th>OS</th>
								<th>#</th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($rows as $row) { ?>
							<tr>
								<td><?php echo $row['os']; ?></td>
								<td><?php echo $row['n']; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>

				<div class="content glass pure-u-1 pure-u-md-1-5">
					<div class="article">
						<h2 class="article-h2">Web browser engines</h2>
					</div>

					<?php $rows = $stats->render_engines(); ?>
					<table class="pure-table pure-table-bordered" style="width: 100%">
						<thead>
							<tr>
								<th>Engine</th>
								<th>#</th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($rows as $row) { ?>
							<tr>
								<td><?php echo $row['eng']; ?></td>
								<td><?php echo $row['n']; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>

			</div>

		</div>

	<script src="assets/chartist.min.js" type="text/javascript"></script>
	<script>
		(function (window, document) {

			var layout   = document.getElementById('layout'),
				menu     = document.getElementById('menu'),
				menuLink = document.getElementById('menuLink'),
				logout   = document.getElementById('logout');

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

			// last week's visits | chartist attempt
			var data = {
			  labels: <?php echo $last_week['labels']; ?>,
			  series: [
				<?php echo $last_week['series']; ?>
			  ]
			};

			var options = {
				axisY: {
					onlyInteger: true
				},
				lineSmooth: Chartist.Interpolation.simple({
					divisor: 2
				}),
				low: 0,
				showArea: true
			};

			var chart = new Chartist.Line('.ct-chart', data, options);
		}(this, this.document));
	</script>

		<?php } else { ?>
		<div id="main">
			<div class="header">
				<h1>Login</h1>
			</div>

			<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" class="content glass" style="text-align: center">
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
							<p>Remember me&nbsp;&nbsp;<input type="checkbox" name="remember" value="me"></p>
							<button class="pure-button button-xlarge" type="submit" name="submit">Login</button>
						</div>
					</fieldset>
				</div>
			</form>
		</div>
		<?php } ?>
	</div>
</body>
</html>