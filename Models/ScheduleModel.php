<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class ScheduleModel extends Model
{

	protected $db;
	protected $table = 'SCHEDULE';
	protected $primaryKey = 'id';

	protected $allowedFields = ['client_id', 'client_name', 'client_hp', 'booking_type', 'booking_date', 'booking_room',
															'start_time_id', 'end_time_id', 'interval_style', 'start_style', 'manage_name', 'client_ticket_id',
															'subtraction_number', 'ticket_name', 'direct_type', 'memo', 'status', 'add_sales_type', 'add_sales_amount',
															'add_admin_memo', 'user_id', 'is_delete', 'insert_datetime', 'update_datetime'];

	public function _save($data) {
		$this->set('insert_datetime', 'now()', false);
		$this->save($data);
	}

	public function _update($id, $data) {
		$this->set('update_datetime', 'now()', false);

		$this->update($id, $data);
	}

	public function _delete($id) {
		$this->set('update_datetime', 'now()', false);

		$this->update($id, ['is_delete'	=> 'Y']);
	}

	public function getScheduleList($booking_room, $booking_date) {
		$sql =" SELECT id, client_name, interval_style, start_style, status AS status_cd, manage_name
							FROM SCHEDULE
							WHERE booking_room = ".$booking_room." AND booking_date = '".$booking_date."' AND is_delete='N' AND status IN (1,3)
							ORDER BY start_time_id ASC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getScheduleTimeList($booking_room, $booking_date='') {
		$andClause = "";
		if($booking_date) {
			$andClause .= " AND booking_date = '".$booking_date."' ";
		}
		$sql =" SELECT id, start_time_id, end_time_id
							FROM SCHEDULE
							WHERE booking_room = ".$booking_room." AND status IN (1,3)
							".$andClause."
							ORDER BY start_time_id ASC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getScheduleInfo($id) {
		$sql =" SELECT schedule.id, schedule.client_id, schedule.client_name, IF(schedule.client_id>0, client.hp, schedule.client_hp) AS client_hp,
									schedule.status AS status_cd, schedule.booking_type, schedule.manage_name, schedule.booking_date AS booking_dt,
									DATE_FORMAT(schedule.booking_date, '%Y.%m.%d') AS booking_date, schedule.booking_room AS booking_room_cd,
									schedule.start_time_id, schedule.end_time_id, schedule.client_ticket_id, schedule.ticket_name, schedule.memo,
									DATE(schedule.insert_datetime) AS insert_dt,  DATE_FORMAT(schedule.insert_datetime, '%Y.%m.%d') AS insert_date,
									schedule.booking_type AS booking_type_cd, schedule.subtraction_number,
									CASE booking_type WHEN 1 THEN '(전화예약)' WHEN '2' THEN '(방문예약)' WHEN 3 THEN '(기타)' ELSE NULL END booking_type,
									schedule.add_sales_amount, schedule.add_admin_memo, schedule.direct_type AS direct_type_cd, schedule.add_sales_type AS add_sales_type_cd,
									CASE add_sales_type WHEN 'card' THEN '카드' WHEN 'money' THEN '현금' ELSE NULL END add_sales_type,
									CASE direct_type WHEN 'card' THEN '카드' WHEN 'money' THEN '현금' ELSE NULL END direct_type
						FROM SCHEDULE AS schedule
						LEFT JOIN CLIENT AS client ON client.id = schedule.client_id
						LEFT JOIN CLIENT_TICKET AS ct ON ct.id = schedule.client_ticket_id
						WHERE schedule.id = ".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}

	public function getSearchScheduleCnt($inParam) {
		$andClause = $limit = "";
		if(isset($inParam['client_name']) && $inParam['client_name']) {
			$andClause .= " AND client_name LIKE '%".$inParam['client_name']."%' ";
		}

		$sql = " SELECT count(*) AS cnt FROM SCHEDULE WHERE status IN (1,3) AND is_delete='N' ".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['cnt'];
		}
	}

	public function getSearchScheduleList($inParam) {
		$andClause = $limit = "";
		if(isset($inParam['client_name']) && $inParam['client_name']) {
			$andClause .= " AND client_name LIKE '%".$inParam['client_name']."%' ";
		}
		if(isset($inParam['row']) && $inParam['row']) {
			$limit = " LIMIT ".$inParam['record'].", ".$inParam['row']." ";
		}

		$sql =" SELECT schedule.id, schedule.client_id, schedule.client_name, IF(schedule.client_id>0, client.hp, schedule.client_hp) AS client_hp,
									schedule.status AS status_cd, schedule.booking_room AS booking_room_cd, schedule.start_time_id, schedule.end_time_id,
									CASE schedule.status WHEN 1 THEN '방문예약' WHEN '2' THEN '예약취소' WHEN 3 THEN '방문완료' ELSE NULL END status,
									schedule.manage_name, DATE_FORMAT(schedule.booking_date, '%Y.%m.%d') AS booking_date
						FROM SCHEDULE AS schedule
						LEFT JOIN CLIENT AS client ON client.id = schedule.client_id
						WHERE schedule.status IN (1,3)
						AND schedule.is_delete='N'
						".$andClause."
						ORDER BY booking_date DESC, id DESC
						".$limit." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getBookingNumber($client_ticket_id, $id='') {
		$andClause = "";
		if($id) {
			$andClause .= " AND id NOT IN (".$id.")";
		}
		$sql =" SELECT SUM(subtraction_number) AS subtraction_number
							FROM SCHEDULE WHERE client_ticket_id = ".$client_ticket_id." AND status=1 AND is_delete='N' ".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['subtraction_number'];
		}
	}


	public function getVisitCnt($client_ticket_id, $date, $start_time_id) {
		$sql =" SELECT count(*) AS cnt
							FROM SCHEDULE
							WHERE client_ticket_id = ".$client_ticket_id."
							AND booking_date <= '".$date."'
							AND IF(booking_date = '".$date."', start_time_id <= ".$start_time_id.", true)
							AND status <> 2 AND is_delete='N' ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['cnt'];
		}
	}

	public function getStartTimeList() {
		$sql =" SELECT id, CONCAT(type, ' ', DATE_FORMAT(time, '%l:%i')) AS name FROM DEFINE_TIME WHERE id <= 23 ORDER BY id ASC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getEndTimeList() {
		$sql =" SELECT id, CONCAT(type, ' ', DATE_FORMAT(time, '%l:%i')) AS name FROM DEFINE_TIME WHERE id >= 3 AND id<=25 ORDER BY id ASC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getTimeStyle($time_id) {
		$sql =" SELECT time_style FROM DEFINE_TIME WHERE id = ".$time_id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['time_style'];
		}
	}

	public function getTimeText($time_id) {
		$sql =" SELECT CONCAT(type, ' ', DATE_FORMAT(time, '%l:%i')) AS name FROM DEFINE_TIME WHERE id = ".$time_id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['name'];
		}
	}

	// 문자 예약 발송
	public function getSmsReserveList($date, $interval_day, $status='') {
		$andClause = "";
		if($status) {
			$andClause = " AND schedule.status IN (".$status.") ";
		}
		$sql = " SELECT schedule.id, client_id, IF(schedule.client_id>0, client.hp, schedule.client_hp) AS client_hp
							FROM SCHEDULE AS schedule
				LEFT JOIN CLIENT AS client ON client.id = schedule.client_id
						WHERE DATE_ADD(booking_date, INTERVAL ".$interval_day." DAY) = '".$date."'
						AND schedule.is_delete = 'N'
						".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function checkClientTicket($client_ticket_id) {
		$sql = " SELECT count(*) AS cnt FROM SCHEDULE WHERE client_ticket_id = ".$client_ticket_id." AND status = 1 ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['cnt'];
		}
	}
}
