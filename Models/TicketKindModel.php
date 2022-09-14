<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class TicketKindModel extends Model
{

	protected $db;
	protected $table = 'TICKET_KIND';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'ticket_id', 'number', 'sales_amount', 'note', 'is_delete', 'insert_datetime', 'update_datetime'];


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

	// 티켓 구분 통채로 삭제
	public function deleteTicketKind($ticket_id) {
		$sql = " UPDATE TICKET_KIND SET is_delete='Y' AND update_datetime=now() WHERE ticket_id = ".$ticket_id." AND is_delete='N' ";

		$this->db->query($sql);
		return true;
	}

	// Ticket Kind list
	public function getTicketKindlist($ticket_id) {
		$sql =" SELECT id, ticket_id, TRUNCATE(number,0) AS number, note, sales_amount
							FROM TICKET_KIND
						WHERE ticket_id = ".$ticket_id."
						AND is_delete = 'N'
						ORDER BY id ASC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getKindNumber($id) {
		$sql = " SELECT number FROM TICKET_KIND WHERE id = ".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['number'];
		}
	}

	public function getKindSalesAmount($id) {
		$sql = " SELECT IFNULL(sales_amount,0) AS sales_amount FROM TICKET_KIND WHERE id = ".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['sales_amount'];
		}
	}
}
