<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class CosmeticStockModel extends Model
{

	protected $db;
	protected $table = 'COSMETIC_STOCK';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'cosmetic_id', 'type', 'quantity', 'remain_quantity', 'goods_date',
															'manager_id', 'note', 'user_id', 'insert_datetime', 'update_datetime'];


	public function _save($data) {
		$this->set('insert_datetime', 'now()', false);
		$this->set('update_datetime', 'now()', false);

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

	// list
	public function getCosmeticStockList($cosmetic_id, $inParam) {
		$andClause = $limit = "";
		if(isset($inParam['start_date']) && $inParam['start_date']) {
			$andClause .= " AND stock.goods_date >= '".$inParam['start_date']."' ";
		}
		if(isset($inParam['end_date']) && $inParam['end_date']) {
			$andClause .= " AND stock.goods_date <= '".$inParam['end_date']."' ";
		}
		if(isset($inParam['row']) && $inParam['row']) {
			$limit = " LIMIT ".$inParam['record'].", ".$inParam['row']." ";
		}

		$sql =" SELECT	stock.id, DATE_FORMAT(stock.goods_date,'%Y.%m.%d') AS goods_date, stock.type AS type_cd,
										stock.quantity, stock.manager_id, user.name AS manager_name,
										stock.remain_quantity, stock.note,
										CASE stock.type WHEN 1 THEN '판매' WHEN '2' THEN '입고' WHEN 3 THEN '소진' WHEN 4 THEN '판매취소' ELSE NULL END type
						FROM COSMETIC_STOCK AS stock
						LEFT JOIN USERS AS user ON user.id = stock.manager_id
						WHERE cosmetic_id = ".$cosmetic_id."
						".$andClause."
						ORDER BY goods_date DESC, stock.update_datetime DESC, stock.id DESC
						".$limit." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getCosmeticStockCnt($cosmetic_id, $inParam) {
		$andClause = "";
		if(isset($inParam['start_date']) && $inParam['start_date']) {
			$andClause .= " AND goods_date >= '".$inParam['start_date']."' ";
		}
		if(isset($inParam['end_date']) && $inParam['end_date']) {
			$andClause .= " AND goods_date <= '".$inParam['end_date']."' ";
		}
		$sql =" SELECT count(*) AS cnt FROM COSMETIC_STOCK WHERE cosmetic_id = ".$cosmetic_id." ".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['cnt'];
		}
	}

	public function getCosmeticStockInfo($id) {
		$sql =" SELECT * FROM COSMETIC_STOCK WHERE id =".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}

	public function excelCosmeticStockList($cosmetic_id, $inParam) {
		$andClause = "";
		if(isset($inParam['start_date']) && $inParam['start_date']) {
			$andClause .= " AND stock.goods_date >= '".$inParam['start_date']."' ";
		}
		if(isset($inParam['end_date']) && $inParam['end_date']) {
			$andClause .= " AND stock.goods_date <= '".$inParam['end_date']."' ";
		}

		$sql =" SELECT	DATE_FORMAT(stock.goods_date,'%Y.%m.%d') AS goods_date, stock.type AS type_cd,
										CONCAT(IFNULL(FORMAT(stock.quantity, 0),0),'개') AS quantity, stock.manager_id, user.name AS manager_name,
										CONCAT(IFNULL(FORMAT(stock.remain_quantity, 0),0),'개') AS remain_quantity, stock.note,
										CASE stock.type WHEN 1 THEN '판매' WHEN '2' THEN '입고' WHEN 3 THEN '소진' WHEN 4 THEN '판매취소' ELSE NULL END type
						FROM COSMETIC_STOCK AS stock
						LEFT JOIN USERS AS user ON user.id = stock.manager_id
						WHERE cosmetic_id = ".$cosmetic_id."
						".$andClause."
						ORDER BY goods_date DESC, stock.update_datetime DESC, stock.id DESC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}
}
