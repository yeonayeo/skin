<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class ClientTicketModel extends Model
{

	protected $db;
	protected $table = 'CLIENT_TICKET';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'client_id', 'ticket_id', 'ticket_kind_id', 'purchase_date', 'name', 'remain_number', 'amount',
															'memo', 'quantity', 'discount_rate', 'payment_method', 'is_complete', 'user_id', 'insert_datetime', 'update_datetime'];

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
	public function getTicketList($client_id, $is_complete='', $id='') {
		$andClause = "";
		if($is_complete) {
			$andClause .= " AND is_complete = '".$is_complete."' ";
		}
		if($id) {
			$andClause .= " AND id = '".$id."' ";
		}

		$sql =" SELECT id, client_id, ticket_id, name, remain_number, memo, is_complete, amount, payment_method AS payment_method_cd,
									DATE_FORMAT(purchase_date,'%Y.%m.%d') AS purchase_date, quantity, discount_rate,
									CASE payment_method WHEN 'card' THEN '카드' WHEN 'money' THEN '현금' ELSE NULL END payment_method
							FROM CLIENT_TICKET
							WHERE client_id = ".$client_id."
							".$andClause."
							ORDER BY id DESC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getTicketInfo($id) {
		$sql =" SELECT id, client_id, ticket_id, name, IFNULL(remain_number, 0) AS remain_number, memo, is_complete,
									DATE_FORMAT(purchase_date,'%Y.%m.%d') AS purchase_date, quantity, discount_rate, payment_method AS payment_method_cd,
									CASE payment_method WHEN 'card' THEN '카드' WHEN 'money' THEN '현금' ELSE NULL END payment_method
							FROM CLIENT_TICKET WHERE id = ".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}

	public function getKindInfo($id) {
		$sql =" SELECT kind.number, kind.sales_amount
							FROM CLIENT_TICKET AS client
							JOIN TICKET_KIND AS kind ON client.ticket_kind_id = kind.id
							WHERE client.id = ".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}

	public function getRemainNumber($id) {
		$sql =" SELECT IFNULL(remain_number, 0) AS remain_number FROM CLIENT_TICKET WHERE id = ".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['remain_number'];
		}
	}

	public function getClientTicket($client_id) {
		$sql = " SELECT name, count(*) AS cnt FROM CLIENT_TICKET WHERE client_id=".$client_id." AND is_complete='N' ORDER BY id DESC LIMIT 1";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}
}
