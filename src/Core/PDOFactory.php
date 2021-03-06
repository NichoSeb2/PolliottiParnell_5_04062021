<?php
namespace App\Core;

use PDO;
use App\Exceptions\ConfigException;

class PDOFactory {
	private array $config;

	public function __construct() {
		$this->config = $this->getConfig();
	}

	/**
	 * Load database config
	 * 
	 * @return array
	 */
	private function getConfig(): array {
		$confDir = CONF_DIR. "/db-config.yml";
		$tempConfig = yaml_parse_file($confDir);

		if (!$tempConfig) {
			throw new ConfigException("Error loading ". $confDir);
		}

		return $tempConfig;
	}

	/**
	 * Return an instant of PDO connected to the specified database
	 * 
	 * @return PDO
	 */
	public function getMYSQLConnection(): PDO {
		$db = new PDO("mysql:host=". $this->config['db_host']. ";dbname=". $this->config['db_name'], $this->config['db_user'], $this->config['db_password']);

		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		return $db;
	}
}
