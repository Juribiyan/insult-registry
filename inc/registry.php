<?php
require_once 'inc/io.php';
require_once 'inc/db.php';

class Registry extends DatabaseInterface {

	public function totalEntryCount() {
		$c = $this->db_getRow("SELECT COUNT(1) as total FROM entries");
		return is_null($c) ? 0 : $c['total'];
	}

	public function getEntries($page=0, $ajax=false) {
		$filter = []; $where = "";
		foreach (['offender', 'victim', 'site'] as $f) {
			if (isset($_GET[$f]) && strlen($_GET[$f])) {
				$value = ($f=='offender' && $_GET[$f]=='_')
					? ''
					: $this->db->real_escape_string($_GET[$f]);
				$filter []= "e." . $f . " = '$value'";
			}
		}
		if (count($filter)) {
			$where = " WHERE " . implode(" AND ", $filter);
		}
		$total = $this->db_getRow("SELECT COUNT(1) AS cnt FROM entries e" . $where)['cnt'];
		if (!$total) {
			return [0, 0];
		}

		$pages = ceil($total / ENTRIES_PER_PAGE);
		$offset = $page * ENTRIES_PER_PAGE;
		if ($offset > $total) {
			if ($is_ajax)
				return [0, $pages];
			else
				redirectToPage($pages - 1);
		}

		$entries = $this->db_getAll(
		 "SELECT e.*, c.comment_count 
		 	FROM entries e
		 	LEFT JOIN (
		 		SELECT entry_id, count(*) as comment_count
		 		FROM comments
		 		GROUP BY entry_id
		 	) c
			ON c.entry_id = e.id
			$where
			ORDER BY e.timestamp DESC
			LIMIT " . ENTRIES_PER_PAGE."
			OFFSET $offset");

		return [$entries, $pages];
	}

	public function submitEntry($data) {
		if (@$$link) {
			$link = $this->db->real_escape_string($data['link']);
			$existing_id = $this->db_getRow("SELECT id FROM entries WHERE link='$link' LIMIT 1");
			if ($existing_id) {
				exitWithError(400, "<a href=\"/entry/".$existing_id['id']."\">Эта ссылка</a> уже присутствует в базе");
			}
		}
		return $this->db_insertRow('entries', $data);
	}

	public function getEntry($id) {
		$entry = $this->db_getRow("SELECT * FROM entries WHERE id = $id");
		if (is_null($entry)) {
			exitWithError(404, "Запись №$id не найдена");
		}
		return $entry;
	}

	public function getTopList($by) {
		return $this->db_getAll(
		 "SELECT $by as name, COUNT(*) AS total
			FROM entries
			WHERE $by != ''
			GROUP BY name
			ORDER BY total DESC, name ASC");
	}

	public function getNames() {
		return $this->db_getAll(
		 "SELECT DISTINCT name FROM 
			( (SELECT offender as name FROM entries where offender != '')
				UNION ALL
				(SELECT victim as name FROM entries)
			) names
			ORDER BY name ASC");
	}
}