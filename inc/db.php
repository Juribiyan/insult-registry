<?php
require_once 'config.php';
require_once 'inc/io.php';

class DatabaseInterface {
	public function __construct() {
		$this->db = new mysqli(DB_HOST, DB_USER, DB_PASSWD, DB_DATABASE);
		$this->db->set_charset(DB_CHARSET);
		$this->db->query("SET NAMES '".DB_CHARSET."'"); 
		$this->db->query("SET CHARACTER SET '".DB_CHARSET."'");
		$this->db->query("SET SESSION collation_connection = '".DB_COLLATION."'");
	}

	public function db_getRow($query) {
		try {
			$qr = $this->db->query($query);
		}
		catch(Exception $e) {
			exitWithError(500, "Ошибка при запросе в базу данных (исключение)");
		}
		if (!$qr) {
			exitWithError(500, "Ошибка при запросе в базу данных");
		}
		$r = $qr->fetch_assoc();
		if (!$r && !is_null($r)) {
			exitWithError(500, "Ошибка при обработке запроса в базу данных");
		}
		return $r;
	}

	public function db_getAll($query) {
		try {
			$qr = $this->db->query($query);
		}
		catch(Exception $e) {
			echo $query; die();
			exitWithError(500, "Ошибка при запросе в базу данных (исключение)");
		}
		if (!$qr) {
			exitWithError(500, "Ошибка при запросе в базу данных");
		}
		if ($qr->num_rows == 0) {
			return null;
		}
		return $qr->fetch_all(MYSQLI_ASSOC);
	}

	public function db_insertRow($table, $data_assoc) {
		$keys = array_keys($data_assoc);
		$cols = implode(',', $keys);
		$vals = array_values($data_assoc);
		$qmarks = implode(',', array_fill(0, count($keys), '?'));
		$hiss = implode('', array_fill(0, count($keys), 's'));
		$q = $this->db->prepare("INSERT INTO $table ($cols) VALUES ($qmarks)");
		$q->bind_param($hiss, ...$vals);
		$q->execute();
		if (!$q) {
			exitWithError(500, "Ошибка при записи в базу данных");
		}
		return $this->db->insert_id;
	}
}