<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class StuffModel extends Model
{

	protected $db;
	protected $table = 'STUFF';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'name', 'remain_quantity', 'purchase_price', 'type', 'memo', 'is_delete', 'insert_datetime', 'update_datetime'];


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
		$this->set('update_datetime', 'now()', false);

		$this->update($id, ['is_delete' => 'Y']);
	}

	// list
	public function getStuffList($inParam) {
		$andClause = $limit = "";
		if($inParam['name']) {
			$andClause = " AND name LIKE '%".$inParam['name']."%' ";
		}
		if(isset($inParam['row']) && $inParam['row']) {
			$limit = " LIMIT ".$inParam['record'].", ".$inParam['row']." ";
		}

		$sql =" SELECT id, name, IFNULL(FORMAT(remain_quantity, 0),0) AS remain_quantity, memo, type
							FROM STUFF
						WHERE is_delete='N'
						".$andClause."
						ORDER BY id DESC ".$limit." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getStuffCnt($inParam) {
		$andClause = "";
		if($inParam['name']) {
			$andClause .= " AND name LIKE '%".$inParam['name']."%' ";
		}

		$sql =" SELECT count(*) AS cnt FROM STUFF WHERE is_delete='N' ".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['cnt'];
		}
	}

	public function getStuffInfo($id) {
		$sql =" SELECT * FROM STUFF WHERE id = ".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}

	public function getRemainQuantity($id) {
		$sql =" SELECT IFNULL(remain_quantity, 0) AS remain_quantity FROM STUFF WHERE id = ".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['remain_quantity'];
		}
	}

	public function excelStuffList() {
		$sql =" SELECT name, CONCAT(IFNULL(FORMAT(remain_quantity, 0),0),'개') AS remain_quantity, memo, type
							FROM STUFF
						WHERE is_delete='N'
						ORDER BY id DESC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}
}
