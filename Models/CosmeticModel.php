<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class CosmeticModel extends Model
{

	protected $db;
	protected $table = 'COSMETIC';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'name', 'remain_quantity', 'purchase_price', 'sales_price', 'fee',
															'memo', 'is_use', 'is_delete', 'insert_datetime', 'update_datetime'];


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
	public function getCosmeticList($inParam) {
		$andClause = $limit = "";
		if($inParam['name']) {
			$andClause = " AND name LIKE '%".$inParam['name']."%' ";
			if(isset($inParam['is_use']) && $inParam['is_use']) {
				$andClause .= " AND is_use = '".$inParam['is_use']."' ";
			}
		}
		if(isset($inParam['remain_quantity']) && $inParam['remain_quantity']) {
			$andClause .= " AND remain_quantity > 0 ";
		}
		if(isset($inParam['row']) && $inParam['row']) {
			$limit = " LIMIT ".$inParam['record'].", ".$inParam['row']." ";
		}

		$sql =" SELECT id, name, remain_quantity, sales_price, fee, is_use
						FROM COSMETIC
						WHERE is_delete='N'
						".$andClause."
						ORDER BY id DESC ".$limit." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getCosmeticCnt($inParam) {
		$andClause = "";
		if($inParam['name']) {
			$andClause .= " AND name LIKE '%".$inParam['name']."%' ";
		}

		$sql =" SELECT count(*) AS cnt FROM COSMETIC WHERE is_delete='N' ".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['cnt'];
		}
	}

	public function getCosmeticInfo($id) {
		$sql =" SELECT * FROM COSMETIC WHERE id =".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}

	public function getRemainQuantity($id) {
		$sql =" SELECT IFNULL(remain_quantity, 0) AS remain_quantity FROM COSMETIC WHERE id = ".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['remain_quantity'];
		}
	}

	public function getSalesPrice($id) {
		$sql =" SELECT IFNULL(sales_price, 0) AS sales_price FROM COSMETIC WHERE id = ".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['sales_price'];
		}
	}

	// 엑셀 다운로드
	public function excelCosmeticList() {
		$sql =" SELECT name, CONCAT(IFNULL(FORMAT(remain_quantity, 0),0),'개') AS remain_quantity, CONCAT(FORMAT(sales_price,0),'원') AS sales_price, memo, is_use
							FROM COSMETIC
							WHERE is_delete='N'
							ORDER BY id DESC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}
}
