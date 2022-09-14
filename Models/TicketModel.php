<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class TicketModel extends Model
{

	protected $db;
	protected $table = 'TICKET';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'name', 'kind', 'note', 'is_use', 'insert_datetime', 'update_datetime'];


	public function _save($data) {
		$this->set('insert_datetime', 'now()', false);
		$this->save($data);
	}

	// 업데이트
	public function _update($id, $data) {
		$this->set('update_datetime', 'now()', false);

		$this->update($id, $data);
	}

	// 삭제
	public function _delete($id) {
		$this->delete($id);
	}

	// Ticket list
	public function getTicketList($inParam) {
		$whereClause = $limit = "";
		if($inParam['name']) {
			$whereClause = " WHERE name LIKE '%".$inParam['name']."%' ";
			if(isset($inParam['is_use']) && $inParam['is_use']) {
				$whereClause .= " AND is_use = '".$inParam['is_use']."' ";
			}
		}
		if(isset($inParam['row']) && $inParam['row']) {
			$limit = " LIMIT ".$inParam['record'].", ".$inParam['row']." ";
		}

		$sql =" SELECT * FROM TICKET ".$whereClause." ORDER BY id DESC ".$limit." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getTicketCnt($inParam) {
		$whereClause = "";
		if($inParam['name']) {
			$whereClause = " WHERE name LIKE '%".$inParam['name']."%' ";
		}

		$sql =" SELECT count(*) AS cnt FROM TICKET ".$whereClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['cnt'];
		}
	}

	public function getTicketInfo($id) {
		$sql =" SELECT * FROM TICKET WHERE id =".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}

	// 엑셀 다운로드
	public function excelTicketList() {
		$sql =" SELECT name, kind, note, is_use FROM TICKET ORDER BY id DESC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

}
