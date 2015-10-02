<?php

class NX
{
	public $db;
	public $mode;
	public $config;

	function __construct()
	{
		require_once('config.php');

		$this->config =& $nx_config;
		$this->mode   =& $this->config['mode'];

		$this->db = new mysqli($this->config['hostname'],
		                       $this->config['user'],
		                       $this->config['pass'],
		                       $this->config['database']);

		if ($this->db->connect_errno) {
			echo "Connection failed to establish: " .
				$this->db->connect_errno . ' | ' . $this->db->connect_error;
			die();
		}
	}

	function init()
	{
		(($this->mode === 'simple') ? $this->simple() : $this->advanced());
	}

	function simple()
	{
		$now = time();
		$url = $this->db->real_escape_string($_SERVER['REQUEST_URI']);
		$ref = $this->db->real_escape_string($_SERVER['HTTP_REFERER']);
		$ua  = $this->db->real_escape_string($_SERVER['HTTP_USER_AGENT']);

		$res = $this->db->query("SELECT id FROM simple
		               WHERE ua='$ua' AND url='$url' AND ref='$ref' LIMIT 1;");

		if ($res->num_rows === 0) {
			/* First time this case happens, treat it appropriately. */
			$this->db->query("INSERT INTO simple (ua, url, ref, visits, time)
			                       VALUES ('$ua', '$url', '$ref', 1, $now);");
		} else {
			/* It has happened before, note it down. */
			$id =& $res['id'];
			$this->db->query("UPDATE simple SET visits=visits+1
			                                WHERE id='$id';");
		}
	}

	function advanced()
	{
		$now = time();
		$ip  = $this->db->real_escape_string($_SERVER['REMOTE_ADDR']);
		$url = $this->db->real_escape_string($_SERVER['REQUEST_URI']);
		$ref = $this->db->real_escape_string($_SERVER['HTTP_REFERER']);
		$ua  = $this->db->real_escape_string($_SERVER['HTTP_USER_AGENT']);
		$xff = $this->db->real_escape_string($_SERVER['X_FORWARDED_FOR']);

		$res = $this->db->query("SELECT id FROM core
		               WHERE ip='$ip' AND ua='$ua' AND xff='$xff' LIMIT 1;");

		if ($res->num_rows === 0) {
			/* First time this client makes a contact, treat it differently. */
			$this->db->query("INSERT INTO core (ip, ua, xff)
			                       VALUES ('$ip', '$ua', '$xff');");

			$id =& $this->db->insert_id;

			$this->db->query("INSERT INTO urls (id, url, visits, time)
			                       VALUES ('$id', '$url', 1, $now);");
			$this->db->query("INSERT INTO refs (id, ref, times)
			                       VALUES ('$id', '$ref', 1);");
		} else {
			$id =& $res['id'];

			/* Check if this referer exists on the client's history. */
			$res = $this->db->query("SELECT id FROM refs
			               WHERE id='$id' AND ref='$ref' LIMIT 1;");
			if ($res->num_rows === 0) {
				/* First time this client contacts with this referer,
				note it down. */
				$this->db->query("INSERT INTO refs (id, ref, times)
				                       VALUES ('$id', '$ref', 1);");
			} else {
				/* Increment the times column. */
				$this->db->query("UPDATE refs SET times=times+1
				                              WHERE id='$id';");
			}

			/* Check if the client has been to this url before. */
			$res = $this->db->query("SELECT id FROM urls
			               WHERE id='$id' AND url='$url' LIMIT 1;");
			if ($res->num_rows === 0) {
				/* First time this client contacts this specific url,
				make a new entry. */
				$this->db->query("INSERT INTO urls (id, url, visits, time)
				                       VALUES ('$id', '$url', 1, '$now');");
			} else {
				/* Increment the visits column. */
				$this->db->query("UPDATE urls SET visits=visits+1
				                              WHERE id='$id' AND url='$url';");
			}
		}
	}

}
