<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class CalculatorAdminModel extends Model
{

	protected $db;
	protected $table = 'CALCULATOR_ADMIN';
	protected $primaryKey = 'id';
	protected $allowedFields = ['schedule_id', 'client_id', 'type', 'ticket_id', 'admin_date', 'name', 'amount', 'payment_method', 'insert_datetime', 'update_datetime'];

	public function _save($data) {
		$this->set('insert_datetime', 'now()', false);

		$this->save($data);
	}

	public function _update($id, $data) {
		$this->set('update_datetime', 'now()', false);

		$this->update($id, $data);
	}

	public function _delete($id) {

		$this->delete($id);
	}

	public function getAdminList($start_date='', $end_date='') {
		$andClause = " WHERE 1 ";
		if($start_date) {
			$andClause .= " AND admin_date >= '".$start_date."' ";
		}
		if($end_date) {
			$andClause .= " AND admin_date <= '".$end_date."' ";
		}

		$sql = " SELECT id, DATE_FORMAT(admin_date,'%Y.%m.%d') AS admin_date, name, FORMAT(amount,0) AS amount,
										CASE type WHEN 1 THEN '이용권' WHEN 2 THEN '직접 입력 금액' WHEN 3 THEN '추가 관리' ELSE '-' END type,
										CASE payment_method WHEN 'card' THEN '카드' WHEN 'money' THEN '현금' ELSE '-' END payment_method
							FROM CALCULATOR_ADMIN
							".$andClause."
							ORDER BY admin_date DESC, id DESC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	// 이용권/직접입력금액 판매건수 합계
	public function getSumType($type, $start_date='', $end_date='') {
		$andClause = "";
		if($start_date) {
			$andClause .= " AND admin_date >= '".$start_date."' ";
		}
		if($end_date) {
			$andClause .= " AND admin_date <= '".$end_date."' ";
		}

		$sql = " SELECT count(*) AS cnt FROM CALCULATOR_ADMIN WHERE type = ".$type." ".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['cnt'];
		}
	}

	// 판매 합계액
	public function getSumAmount($start_date='', $end_date='') {
		$andClause = " WHERE 1 ";
		if($start_date) {
			$andClause .= " AND admin_date >= '".$start_date."' ";
		}
		if($end_date) {
			$andClause .= " AND admin_date <= '".$end_date."' ";
		}

		$sql = " SELECT IFNULL(FORMAT(SUM(amount),0),0) AS total_admin_amount FROM CALCULATOR_ADMIN ".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['total_admin_amount'];
		}
	}

	public function excelAdminList($start_date='', $end_date='') {
		$andClause = " WHERE 1 ";
		if($start_date) {
			$andClause .= " AND admin_date >= '".$start_date."' ";
		}
		if($end_date) {
			$andClause .= " AND admin_date <= '".$end_date."' ";
		}

		$sql = " SELECT id, DATE_FORMAT(admin_date,'%Y.%m.%d') AS admin_date, name, FORMAT(amount,0) AS amount,
										CASE type WHEN 1 THEN '이용권' WHEN 2 THEN '직접 입력 금액' WHEN 3 THEN '추가 관리' ELSE '-' END type,
										CASE payment_method WHEN 'card' THEN '카드' WHEN 'money' THEN '현금' ELSE '-' END payment_method
							FROM CALCULATOR_ADMIN
							".$andClause."
							ORDER BY admin_date DESC, id DESC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}
}
