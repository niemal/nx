<?php

class NX
{
	public $db;
	public $config;
	public $mode;
	public $error;

	function __construct() {
		if (!defined('NX-ANALYTICS')) define('NX-ANALYTICS', true);
		$this->config = $this->getConfig();
		$this->db = $this->getDb();
		$this->mode = $this->config['nx-mode'];
	}

	public function init() {
		if ($this->mode === 'simple') {
			require_once('NX_simple.php');
			$nx = new NX_simple($this->db, $this->config);
			$nx->log();
		} else {
			require_once('NX_advanced.php');
			$nx = new NX_advanced($this->db, $this->config);
			$nx->log();
		}
	}

	public function getConfig() {
		// install script makes sure everything is correctly set
		$filename = dirname(__FILE__) . '/config.php';
		if ( file_exists($filename) ) {
			require($filename);
			if( isset($nx_config) ){
				return json_decode($nx_config, true);
			} else {
				$this->error('critical - configuration damaged', true);
			}
		} else {
			$this->error('configuration file doesnt exist', true);
		}
	}

	private function getDb() {
		$db = new mysqli(
			$this->config['db-host'],
			$this->config['db-user'],
			$this->config['db-pass'],
			$this->config['nx-db'],
			$this->config['db-port']
		);

		if ($db->connect_errno) {
			$this->error('Connection failed to establish: [' . $db->connect_errno . '] ' . $db->connect_error);
		}

		return $db;
	}

	private function error($err, $die=false) {
		$errs = $this->config['nx-errors'];
		if ($errs === 'show') {
			echo '[NX] Error: ' . htmlspecialchars($err) . PHP_EOL;//because this echo will force output to HTML during database outages
		} else {
			error_log("[NX] $err");
		}
		if ($die === true) die(-1);
	}
}
