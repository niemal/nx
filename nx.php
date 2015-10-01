<?php
/**
 * This file may be included in PHP files as it can handle back-end
 * type of analytics. However, it can also serve as a stand-alone file
 * which javascript code must reach.
 **/

	function nx_init()
	{
		/* These values will be interpreted/parsed with/from install.php file. */
		$hostname = 'localhost';
		$username = 'username';
		$password = 'password';
		$database = 'nx_analytics';

		$db = new mysqli($hostname, $username, $password, $database);

		if ($db->connect_errno) {
			echo "Connection failed to establish: " .
				$db->connect_errno . ' | ' . $db->connect_error;
			die();
		}

		$ip  = $db->real_escape_string($_SERVER['REMOTE_ADDR']);
		$ua  = $db->real_escape_string($_SERVER['HTTP_USER_AGENT']);
		$url = $db->real_escape_string($_SERVER['REQUEST_URI']);
		$ref = $db->real_escape_string($_SERVER['HTTP_REFERER']);
		$xff = $db->real_escape_string($_SERVER['X_FORWARDED_FOR']);
		$now = time();

		$res = $db->query("SELECT id FROM core WHERE ip='$ip' AND ua='$ua' AND xff='$xff' LIMIT 1;");

		if ($res->num_rows === 0) {
			/* First time this client makes a contact, treat it differently. */
			$db->query("INSERT INTO core (ip, ua, xff)
			                 VALUES ('$ip', '$ua', '$xff');");

			$id = $db->insert_id;

			$db->query("INSERT INTO urls (id, url, visits, time)
			                 VALUES ('$id', '$url', 1, $now);");
			$db->query("INSERT INTO refs (id, ref, times)
			                 VALUES ('$id', '$ref', 1);");
		} else {
			$id = $res['id'];

			/* Check if this referer exists on the client's history. */
			$res = $db->query("SELECT id FROM refs WHERE id='$id' AND ref='$ref' LIMIT 1;");
			if ($res->num_rows === 0) {
				/* First time this client contacts with this referer, note it down. */
				$db->query("INSERT INTO refs (id, ref, times)
					             VALUES ('$id', '$ref', 1);");
			} else {
				/* Increment the times column. */
				$db->query("UPDATE refs SET times=times+1 WHERE id='$id';");
			}

			/* Check if the client has been to this url before. */
			$res = $db->query("SELECT id FROM urls WHERE id='$id' AND url='$url' LIMIT 1;");
			if ($res->num_rows === 0) {
				/* First time this client contacts this specific url, make a new entry. */
				$db->query("INSERT INTO urls (id, url, visits, time)
				                 VALUES ('$id', '$url', 1, '$now');")
			} else {
				/* Increment the visits column. */
				$db->query("UPDATE urls SET visits=visits+1 WHERE id='$id' AND url='$url';");
			}
		}
 	}


 	nx_init();