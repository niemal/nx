<?php

class NX_simple {
	private $db;
	private $config;

	public function NX_simple($db, $config) {
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


		$res = $this->db->query("SELECT id FROM simple WHERE uri='$uri' AND date='$date' AND ua='$ua' AND url='$url' LIMIT 1;");

		if ($res->num_rows === 0) {
			/* First time this case happens, treat it appropriately. */
			$this->db->query("INSERT INTO simple (ua, url, uri, ref, visits, date, ts)
			                       VALUES ('$ua', '$url', '$uri', '$ref', 1, $date, $ts);");
			$id =& $this->db->insert_id;
			$this->db->query("INSERT INTO refs (id, ref, times)
                                   VALUES ('$id', '$ref', 1);");
		} else {
			/* It has happened before, note it down. */
			$res = $res->fetch_assoc();
			$id  =& $res['id'];
			$this->db->query("UPDATE simple SET visits=visits+1 WHERE id='$id';");

			/* Check if this referer exists on the client's history. */
			$res = $this->db->query("SELECT id FROM refs WHERE id='$id' AND ref='$ref' LIMIT 1;");

			if ($res->num_rows === 0) {
				/* First time this client contacts with this referer, note it down. */
				$this->db->query("INSERT INTO refs (id, ref, times)
				                       VALUES ('$id', '$ref', 1);");
			} else {
				/* Increment the times column. */
				$this->db->query("UPDATE refs SET times=times+1 WHERE id='$id';");
			}
		}
	}

}