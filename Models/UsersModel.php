<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class UsersModel extends Model
{

	protected $db;
	protected $table = 'USERS';
	protected $primaryKey = 'id';
	protected $allowedFields = ['login_cd', 'is_super', 'name', 'hp', 'position', 'work_form', 'work_time', 'pay_form', 'pay_money',
															'status', 'note', 'ss_token', 'insert_datetime', 'update_datetime'];


	// 관리자 추가😶
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

		$this->update($id, ['status' => 99]);
	}

	// 로그인
	public function login($login_cd)
	{
		$sql = " SELECT * FROM USERS WHERE login_cd = '".$login_cd."' AND status <> 99 ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}

	public function getRecentlyCD() {
		$sql = " SELECT login_cd FROM USERS WHERE login_cd <> 'SS001' ORDER BY login_cd DESC, id DESC LIMIT 1 ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['login_cd'];
		}
	}

	public function checkUser($id, $ss_token) {
		$sql = " SELECT name FROM USERS WHERE id = ".$id." AND ss_token = '".$ss_token."' AND status=1 ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return (!empty($rs)) ? $rs['name'] : false;
		}
	}

	public function getUserList() {
		$sql = " SELECT id, login_cd, name, position, hp, is_super, note FROM USERS WHERE status = 1 ORDER BY id ASC";
		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	public function getIsSuper($id) {
		$sql = " SELECT is_super FROM USERS WHERE id = ".$id." AND status = 1 ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['is_super'];
		}
	}

	public function getUserInfo($id) {
		$sql = " SELECT id, login_cd, name, position, hp, is_super, note, work_form, work_time, pay_form AS pay_form_cd, pay_money,
										CASE pay_form WHEN 1 THEN '시급' WHEN 2 THEN '일급' WHEN 3 THEN '주급' WHEN 4 THEN '월급' WHEN 99 THEN '기타' ELSE NULL END pay_form
										FROM USERS WHERE id = ".$id." AND status = 1 ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}

	public function getUserName($id) {
		$sql = " SELECT name FROM USERS WHERE id=".$id." AND status=1 ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['name'];
		}
	}
}
