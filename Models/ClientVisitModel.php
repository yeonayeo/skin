<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class ClientVisitModel extends Model
{

	protected $db;
	protected $table = 'CLIENT_VISIT';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'client_id', 'schedule_id', 'status', 'booking_date', 'manage_name', 'subtraction_number', 'cancel_reason', 'special_note', 'user_id', 'insert_datetime', 'update_datetime'];

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

	// 방문 정보
	public function getClientVisitCnt($client_id) {
		$sql = " SELECT count(*) AS cnt FROM CLIENT_VISIT WHERE client_id = ".$client_id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['cnt'];
		}
	}

	// 방문 정보
	public function getClientVisitList($inParam) {
		$limit = "";
		if(isset($inParam['row']) && $inParam['row']) {
			$limit = " LIMIT ".$inParam['record'].", ".$inParam['row']." ";
		}

		$sql = " SELECT id, schedule_id, DATE_FORMAT(booking_date, '%Y.%m.%d') AS booking_date, status AS status_cd,
										CASE status WHEN 1 THEN '방문 완료' WHEN '2' THEN '예약 취소' ELSE NULL END status,
										manage_name, IFNULL(special_note, '-') AS special_note
							FROM CLIENT_VISIT
							WHERE client_id = ".$inParam['client_id']."
							ORDER BY booking_date DESC, id DESC
							".$limit." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	// 첫 방문일
	public function getFirstVisitDate($client_id) {
		$sql =" SELECT DATE_FORMAT(booking_date, '%Y.%m.%d') AS booking_date FROM CLIENT_VISIT WHERE client_id = ".$client_id." AND status = 1 ORDER BY booking_date ASC LIMIT 1";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['booking_date'];
		}
	}

	// 최근 방문일
	public function getLastVisitDate($client_id) {
		$sql =" SELECT DATE_FORMAT(booking_date, '%Y.%m.%d') AS booking_date FROM CLIENT_VISIT WHERE client_id = ".$client_id." AND status = 1 ORDER BY booking_date DESC LIMIT 1";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['booking_date'];
		}
	}

	// 첫 방문일 유무
	public function getIsFirstVisitDate($client_id) {
		$sql =" SELECT booking_date FROM CLIENT_VISIT WHERE client_id = ".$client_id." AND status = 1 ORDER BY booking_date ASC LIMIT 1";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return ($rs['booking_date']) ? true : false;
		}
	}

	// 최근 방문일
	public function getLastVisitDt($client_id) {
		$sql =" SELECT booking_date FROM CLIENT_VISIT WHERE client_id = ".$client_id." AND status = 1 ORDER BY booking_date DESC LIMIT 1";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['booking_date'];
		}
	}

	public function getVisitID($schedule_id) {
		$sql =" SELECT id FROM CLIENT_VISIT WHERE schedule_id = ".$schedule_id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['id'];
		}
	}

	public function getSpecialNote($schedule_id) {
		$sql =" SELECT special_note FROM CLIENT_VISIT WHERE schedule_id = ".$schedule_id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['special_note'];
		}
	}

	public function getCancelInfo($schedule_id) {
		$sql =" SELECT cancel_reason, CASE cancel_type WHEN 1 THEN '일반 취소' WHEN '2' THEN '당일 취소' ELSE NULL END cancel_type FROM CLIENT_VISIT WHERE schedule_id = ".$schedule_id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}
}
