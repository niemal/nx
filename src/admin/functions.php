<?php
	require_once('src/NX.php');

	/**
	 * @param
	 *  NX class object.
	 *
	 * @return
	 *  Empty string if the user is not logged in. Username otherwise.
	 **/
	function is_logged($nx)
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$ua = $_SERVER['HTTP_USER_AGENT'];

		$fingerprint = hash('sha256', $nx->config['salt'] . $ip . $ua);
		$result = $nx->db->query("SELECT user, logged_at_time, logged_time FROM sessions WHERE fingerprint='$fingerprint';");

		if( $result->num_rows > 0 && !empty($_COOKIE['session']) ) {
			$row = $result->fetch_assoc();
			$cookie = hash('sha256', $ua . $row['user'] . $nx->config['salt']);

			if( (time() - $row['logged_at_time']) < $row['logged_time'] && $_COOKIE['session'] === $cookie)
				return $row['user'];
			else {
				$nx->db->query("DELETE FROM sessions WHERE fingerprint='$fingerprint';");
				setcookie('session', '', time() - $row['logged_time'], '/');
			}
		}
		else if( !empty($_COOKIE['session']) )
			setcookie('session', '', -1, '/');
		else if( $result->num_rows > 0 ) {
			$row = $result->fetch_assoc();
			$sql = "DELETE FROM sessions WHERE fingerprint='$fingerprint';";
			$nx->db->query($sql);
		}
 
		return '';
	}


	/**
	 * @param
	 *  NX class object, username, password and the amount of logged-in time in seconds.
	 *
	 * @return
	 *  Boolean indicating the outcome.
	 **/
	function try_to_login($nx, $user, $pass, $logged_time)
	{
		$user = $nx->db->real_escape_string($user);
		$pass = hash('sha256', $nx->config['salt'].$pass);

		$res = $nx->db->query("SELECT id FROM admin WHERE user='$user' AND pass='$pass' LIMIT 1;");

		if ($res->num_rows === 0)
			return false;
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
			$ua = $_SERVER['HTTP_USER_AGENT'];
			$now = time();
			$fingerprint = hash('sha256', $nx->config['salt'] . $ip . $ua);

			$nx->db->query("INSERT INTO sessions (user, fingerprint, logged_time, logged_at_time)
								 VALUES ('$user', '$fingerprint', $logged_time, $now);");
			$cookie = hash('sha256', $ua . $user . $nx->config['salt']);
			setcookie('session', $cookie, $now + $logged_time);

			return true;
		}
	}


	/**
	 * @param
	 *  NX class object, username.
	 *
	 * @return
	 *  Void. It just attempts to log out the user.
	 **/
	function logout($nx, $user)
	{
		$user = $nx->db->real_escape_string($user);
		setcookie('session', '', -1, '/');
		$nx->db->query("DELETE FROM sessions WHERE user='$user';");
	}