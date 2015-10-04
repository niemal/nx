<?php

class NX_simple {
	private $db;
	private $config;

	public function NX_simple($db, $config) {
		$this->db = &$db;
		$this->config = $config;
	}

	public function log()
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
			$res = $res->fetch_assoc();
			$id  =& $res['id'];
			$this->db->query("UPDATE simple SET visits=visits+1
			                                WHERE id='$id';");
		}
	}

}