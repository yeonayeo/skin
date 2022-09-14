<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class NoticeModel extends Model
{

	protected $db;
	protected $table = 'NOTICE';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'contents', 'notice_date', 'insert_datetime', 'update_datetime'];


	public function _save($data) {
		$this->set('insert_datetime', 'now()', false);
		$this->save($data);
	}

	// 업데이트
	public function _update($id, $data) {
		$this->set('update_datetime', 'now()', false);

		$this->update($id, $data);
	}

	public function getNoticeInfo($date=CURR_DATE) {
		$sql =" SELECT * FROM NOTICE WHERE notice_date = '".$date."' ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}
}
