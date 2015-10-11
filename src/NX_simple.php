<?php

class NX_simple
{
	private $db;
	private $config;

	public function NX_simple($db, $config)
	{
		$this->db = &$db;
		$this->config = &$config;
	}

	public function log()
	{
		$ts   = time();
		$date = intval(date('Ymd', $ts));
		$url  = $this->db->real_escape_string($_SERVER['REQUEST_URI']);

		if (isset($_SERVER['HTTP_USER_AGENT']))
			$ua = $this->db->real_escape_string($_SERVER['HTTP_USER_AGENT']);
		else
			$ua = 'N/A';

		if (isset($_SERVER['HTTP_REFERER']))
			$ref = $this->db->real_escape_string($_SERVER['HTTP_REFERER']);
		else
			$ref = 'N/A';

		if (isset($_SERVER['SERVER_NAME']))
			$uri = $this->db->real_escape_string($_SERVER['SERVER_NAME']);
		else
			$uri = 'N/A';

		$this->db->query("CALL nx_simple('$uri', '$url', '$ua', '$ref', '$date', '$ts');");
		if ($this->db->error) die('Fatal error: ' . $this->db->errno . ' | ' . $this->db->error . '\n');
	}
}