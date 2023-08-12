<?php
require_once 'inc/db.php';

class Comments extends DatabaseInterface {

	public function getComments($entry_id, $after=0) {
		$after = $after ? "AND id > $after" : "";
		return $this->db_getAll(
		 "SELECT * FROM comments 
			WHERE entry_id=$entry_id
			$after
			ORDER BY timestamp ASC");
	}

	public function getComment($id) {
		return $this->db_getRow(
		 "SELECT * FROM comments 
			WHERE id=$id");
	}

	public function addComment($data) {
		$entry = $this->db_getRow("SELECT COUNT(*) as cnt FROM entries where id = ".$data['entry_id']);
		if (!$entry || $entry['cnt']==0)
			exitWithError('404', "Записи №".$data['entry_id']." не существует");
		return $this->db_insertRow('comments', $data);
	}

	public function countComments($entry_id) {
		$cnt = $this->db_getRow("SELECT COUNT(*) as cnt FROM comments WHERE entry_id=$entry_id");
		return $cnt['cnt']; 
	}

}