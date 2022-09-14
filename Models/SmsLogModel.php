<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class SmsLogModel extends Model
{

	protected $db;
	protected $table = 'SMS_LOG';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'template_id', 'msg', 'schedule_id', 'client_id', 'hp', 'is_send', 'reserve_datetime', 'send_datetime', 'insert_datetime'];


	public function _save($data) {
		$this->set('insert_datetime', 'now()', false);
		$this->save($data);
	}

	// 업데이트
	public function _update($id, $data, $type='send') {
		if($type=='reserve') {
			$this->set('reserve_datetime', 'now()', false);
		} else if ($type=='send') {
			$this->set('send_datetime', 'now()', false);
		}
		$this->update($id, $data);
	}

	// 삭제
	public function _delete($id) {
		$this->delete($id);
	}

	// SMS 보낼 list
	public function getSmslist() {
		$sql =" SELECT sms.id, sms.hp, sms.msg, sms.schedule_id,
										schedule.client_name, schedule.start_time_id,
										DATE_FORMAT(schedule.booking_date, '%c월 %e일') AS booking_date,
										CONCAT(time.type, ' ', DATE_FORMAT(time.time, '%l:%i')) AS booking_time
						FROM SMS_LOG AS sms
						LEFT JOIN SCHEDULE AS schedule ON schedule.id = schedule_id
						LEFT JOIN DEFINE_TIME AS time ON time.id = schedule.start_time_id
						WHERE sms.is_send = 'N'
						AND sms.send_datetime IS NULL
						AND sms.reserve_datetime <= '".CURR_DATETIME."' ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	// log 에 존재 유무 확인
	public function chkSmsLog($template_id, $schedule_id, $date) {
		$sql =" SELECT *
							FROM SMS_LOG
							WHERE template_id = ".$template_id."
							AND schedule_id = ".$schedule_id."
							AND DATE(insert_datetime) = '".$date."' ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return (!empty($rs)) ? true : false;
		}
	}

	// 전체고객 즉시 발송
	public function setAllNowSmsData($template_id, $msg) {
		$sql = " INSERT INTO SMS_LOG (template_id, msg, client_id, hp,  reserve_datetime, insert_datetime)
						(SELECT ".$template_id." AS template_id, '".$msg."' AS msg, id, hp, now(), now() FROM CLIENT WHERE is_delete='N' ORDER BY recently_visit_date DESC, id ASC) ";

		$query = $this->db->query($sql);
		return true;
	}
}
