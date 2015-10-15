<?php
	if (!defined('NX-ANALYTICS')) die('Go away.');

	require_once('src/stats.php');
	$stats = new SIMPLE($nx->db);

	$last_week = $stats->last_weeks_visits();
	$recent_visits = $stats->most_recent_uris();
	$uri_visits = $stats->get_uris(true, 5);
	$top_browsers = $stats->browsers();
?>
<!DOCTYPE html><html>
<head>
	<?php echo _headMeta('Dashboard | NX'); ?>
	<link rel="stylesheet" href="assets/chartist.min.css">
	<link rel="stylesheet" href="assets/utils.css">
	<link rel="stylesheet" href="assets/admin.css">
</head>

<body>
	<div id="layout">
		<?php echo _navbar(); ?>

		<div id="main">
			<div class="header">
				<h1>Dashboard</h1>
				<h2>Mode: <i><?php echo $nx->config['nx-mode']; ?></i></h2>
			</div>

			<div class="content u-center">
				<div class="content-inner glass">
					<div class="article">
						<h2>Last week's visits</h2>
						<p>Total: <b><?php echo $last_week['total'] ?></b></p>
					</div>
					<div id="chart-visits"></div>
				</div>
			</div>

			<div class="content u-center">
				<div class="content-inner glass">
					<div class="article u-center">
						<h2>Most recent visited URIs</h2>
					</div>
					<?php if(!empty($recent_visits)) { ?>
					<table class="pure-table pure-table-bordered width-full">
						<thead>
							<tr>
								<th>URI</th>
								<th>URL</th>
								<th>Time</th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($recent_visits as $visit) { ?>
							<tr>
								<td><?php echo $visit['uri']; ?></td>
								<?php
									if(strlen($visit['url']) > 51){
										echo '<td data-tooltip="' . htmlspecialchars($visit['url']) . '">' . substr($visit['url'], 0, 50) . '...</td>';
									} else {
										echo '<td>'.$visit['url'].'</td>';
									}
								?>
								<td><?php echo $visit['time']; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php } else { ?>
					<p>There are no visitors!</p>
					<?php } ?>
				</div>
			</div>


			<div class="pure-g">

				<div class="content pure-u-1 pure-u-md-1-2 u-center">
					<div class="content-inner glass">
						<div class="article">
							<h2>Top 5 visited servers</h2>
						</div>
						<?php if(!empty($uri_visits)) { ?>
						<table class="pure-table pure-table-bordered width-full">
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
						<?php } else { ?>
						<p>There are no visitors!</p>
						<?php } ?>
					</div>
				</div>

				<div class="content pure-u-1 pure-u-md-1-2 u-center">
					<div class="content-inner glass">
						<div class="article">
							<h2>Web browsers</h2>
						</div>

						<?php if(!empty($top_browsers)) { ?>
						<table class="pure-table pure-table-bordered width-full">
							<thead>
								<tr>
									<th>Browser</th>
									<th>#</th>
								</tr>
							</thead>

							<tbody>
								<?php foreach ($top_browsers as $row) { ?>
								<tr>
									<td><?php echo $row['ua']; ?></td>
									<td><?php echo $row['n']; ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<?php } else { ?>
						<p>There are no browsers to show you. :(</p>
						<?php } ?>
					</div>
				</div>

				<div class="content pure-u-1 pure-u-md-1-2">
					<div class="content-inner glass">
						<div class="article u-center">
							<h2>Operating systems</h2>
						</div>

						<?php $rows = $stats->operating_systems(); ?>
						<?php if(!empty($rows)) { ?>
						<table class="pure-table pure-table-bordered width-full">
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
						<?php } else { ?>
						<p>We don't have any Operating Systems to show you. :(</p>
						<?php } ?>
					</div>
				</div>

				<div class="content pure-u-1 pure-u-md-1-2">
					<div class="content-inner glass">
						<div class="article u-center">
							<h2>Web browser engines</h2>
						</div>

						<?php $rows = $stats->render_engines(); ?>
						<?php if(!empty($rows)) { ?>
						<table class="pure-table pure-table-bordered width-full">
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
						<?php } else { ?>
						<p>We didn't find any Engines.</p>
						<?php } ?>
					</div>
				</div>

			</div>

		</div>

	</div>

	<script src="assets/tooltip.min.js"></script>
	<script>
		var datatooltip = document.querySelectorAll('[data-tooltip]'); // TODO remove this, its slow
		for(var j=0; j<datatooltip.length; j++){
			datatooltip[j].onmouseover = function(e){
				tooltip.show(this.getAttribute('data-tooltip'));
			};
			datatooltip[j].onmouseout = function(e){
				tooltip.hide();
			}
		}
	</script>
	<script src="assets/chartist.min.js"></script>
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

			var chart = new Chartist.Line('#chart-visits', data, options);
		}(this, this.document));
	</script>
</body>
</html>