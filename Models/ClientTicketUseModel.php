<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class ClientTicketUseModel extends Model
{

	protected $db;
	protected $table = 'CLIENT_TICKET_USE';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'client_ticket_id', 'schedule_id', 'visit_date', 'note', 'remain_number', 'user_id', 'insert_datetime'];

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
	public function getTicketUseList($client_ticket_id) {
		$sql =" SELECT id, DATE_FORMAT(insert_datetime,'%Y.%m.%d') AS use_date, note, remain_number
							FROM CLIENT_TICKET_USE WHERE client_ticket_id = ".$client_ticket_id." ORDER BY id DESC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}
}
