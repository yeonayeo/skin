<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class CalculatorSalesModel extends Model
{

	protected $db;
	protected $table = 'CALCULATOR_SALES';
	protected $primaryKey = 'id';
	protected $allowedFields = ['client_id', 'type', 'schedule_id', 'ticket_id', 'cosmetic_id', 'sales_date', 'name', 'amount', 'payment_method', 'insert_datetime', 'update_datetime'];

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

	public function getSalesList($start_date='', $end_date='') {
		$andClause = " WHERE 1 ";
		if($start_date) {
			$andClause .= " AND sales_date >= '".$start_date."' ";
		}
		if($end_date) {
			$andClause .= " AND sales_date <= '".$end_date."' ";
		}

		$sql = " SELECT id, DATE_FORMAT(sales_date,'%Y.%m.%d') AS sales_date, name, FORMAT(amount,0) AS amount,
										CASE type WHEN 1 THEN '이용권' WHEN 2 THEN '화장품' WHEN 3 THEN '직접 입력 금액' WHEN 4 THEN '추가 관리' ELSE NULL END type,
										CASE payment_method WHEN 'card' THEN '카드' WHEN 'money' THEN '현금' ELSE NULL END payment_method
							FROM CALCULATOR_SALES
							".$andClause."
							ORDER BY sales_date DESC, id DESC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	// 이용권/화장품 판매건수 합계
	public function getSumType($type, $start_date='', $end_date='') {
		$andClause = "";
		if($start_date) {
			$andClause .= " AND sales_date >= '".$start_date."' ";
		}
		if($end_date) {
			$andClause .= " AND sales_date <= '".$end_date."' ";
		}

		$sql = " SELECT count(*) AS cnt FROM CALCULATOR_SALES WHERE type = ".$type." ".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['cnt'];
		}
	}

	// 판매 합계액
	public function getSumAmount($start_date='', $end_date='') {
		$andClause = " WHERE 1 ";
		if($start_date) {
			$andClause .= " AND sales_date >= '".$start_date."' ";
		}
		if($end_date) {
			$andClause .= " AND sales_date <= '".$end_date."' ";
		}

		$sql = " SELECT IFNULL(FORMAT(SUM(amount),0),0) AS total_sales_amount FROM CALCULATOR_SALES ".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['total_sales_amount'];
		}
	}

	// 판매합계액 2022.07.23
	public function getSumSales($start_date='', $end_date='', $type, $payment_method='') {
		$andClause = "";
		if($start_date) {
			$andClause .= " AND sales_date >= '".$start_date."' ";
		}
		if($end_date) {
			$andClause .= " AND sales_date <= '".$end_date."' ";
		}
		if($payment_method) {
			$andClause .= " AND payment_method = '".$payment_method."' ";
		}

		$sql = " SELECT SUM(amount) AS amount
							FROM CALCULATOR_SALES
						WHERE sales_date >= '".$start_date."'
							AND sales_date <= '".$end_date."'
							AND type = ".$type."
							".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();

			return $rs['amount'];
		}
	}

	public function excelSalesList($start_date='', $end_date='') {
		$andClause = " WHERE 1 ";
		if($start_date) {
			$andClause .= " AND sales_date >= '".$start_date."' ";
		}
		if($end_date) {
			$andClause .= " AND sales_date <= '".$end_date."' ";
		}

		$sql = " SELECT id, DATE_FORMAT(sales_date,'%Y.%m.%d') AS sales_date, name, FORMAT(amount,0) AS amount,
										CASE type WHEN 1 THEN '이용권' WHEN 2 THEN '화장품' WHEN 3 THEN '직접 입력 금액' WHEN 4 THEN '추가 관리' ELSE NULL END type,
										CASE payment_method WHEN 'card' THEN '카드' WHEN 'money' THEN '현금' ELSE NULL END payment_method
							FROM CALCULATOR_SALES
							".$andClause."
							ORDER BY sales_date DESC, id DESC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}
}
