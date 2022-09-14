<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class SmsTemplateModel extends Model
{

	protected $db;
	protected $table = 'SMS_TEMPLATE';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'title', 'msg', 'target', 'send_type', 'send_time', 'schedule_status', 'is_reserved', 'is_all', 'insert_datetime', 'update_datetime'];


	public function _save($data) {
		$this->set('insert_datetime', 'now()', false);
		$this->save($data);
	}

	// 업데이트
	public function _update($id, $data) {
		$this->update($id, $data);
	}

	// 템플릿 list
	public function getTemplatelist($is_reserved='') {
		$whereClause = "";
		if($is_reserved) {
			$whereClause = " WHERE is_reserved = '".$is_reserved."' ";
		}
		$sql =" SELECT * FROM SMS_TEMPLATE ".$whereClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

  // 템플릿 정보
  public function getTemplateInfo($id) {
    $sql = " SELECT id, target, send_type AS send_type_cd, send_time, is_reserved, is_all, msg FROM SMS_TEMPLATE WHERE id = ".$id." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
  }
}
