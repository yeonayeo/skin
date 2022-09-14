<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class PasswordModel extends Model
{

	protected $db;
	protected $table = 'PASSWORD';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'pw', 'is_use', 'insert_datetime'];


	public function _save($data) {
		$this->set('insert_datetime', 'now()', false);
		$this->save($data);
	}

	// 업데이트
	public function _update($id, $data, $type='send') {
		$this->set('update_datetime', 'now()', false);

		$this->update($id, $data);
	}

	public function checkPassword($pw) {
		$sql =" SELECT * FROM PASSWORD WHERE is_use = 'Y' AND pw = '".$pw."' ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return (!empty($rs)) ? true : false;
		}
	}

	// 비밀번호 변경에 따라 이전 비밀번호 사용여부 N
	public function changePassword() {
		$sql =" UPDATE PASSWORD SET is_use = 'N' ";

		$this->db->query($sql);
		return true;
	}
}
