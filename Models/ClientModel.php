<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class ClientModel extends Model
{

	protected $db;
	protected $table = 'CLIENT';
	protected $primaryKey = 'id';
	protected $allowedFields = ['id', 'name', 'hp', 'birth', 'gender', 'first_visit_date', 'recently_visit_date', 'special_note',
															'address', 'memo', 'user_id', 'is_delete', 'insert_datetime', 'update_datetime'];


	public function _save($data) {
		$this->set('insert_datetime', 'now()', false);
		$this->save($data);
	}

	// 업데이트
	public function _update($id, $data, $type='send') {
		$this->set('update_datetime', 'now()', false);

		$this->update($id, $data);
	}

	// 삭제
	public function _delete($id) {
		$this->set('update_datetime', 'now()', false);

		$this->update($id, ['is_delete' => 'Y']);
	}

	// 고객 list
	public function getClientList($inParam) {
		$andClause = $limit = "";
		if(isset($inParam['name']) && $inParam['name']) {
			$andClause .= " AND name LIKE '%".$inParam['name']."%' ";
		}
		if(isset($inParam['row']) && $inParam['row']) {
			$limit = " LIMIT ".$inParam['record'].", ".$inParam['row']." ";
		}

		$sql =" SELECT id, name, hp, birth, gender AS gender_cd, CASE gender WHEN 'male' THEN '남성' WHEN 'female' THEN '여성' ELSE NULL END gender,
										DATE_FORMAT(recently_visit_date,'%Y.%m.%d') AS recently_visit_date
							FROM CLIENT
							WHERE is_delete = 'N'
							".$andClause."
							ORDER BY id DESC ".$limit." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

	// 고객 수
	public function getClientCnt($inParam) {
		$andClause = "";
		if($inParam['name']) {
			$andClause .= " AND name LIKE '%".$inParam['name']."%' ";
		}

		$sql =" SELECT count(*) AS cnt FROM CLIENT WHERE is_delete='N' ".$andClause." ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['cnt'];
		}
	}

	// 고객 정보
	public function getClientInfo($id) {
		$sql =" SELECT id, name, hp, birth, address, gender, memo, special_note,
		 								DATE_FORMAT(first_visit_date, '%Y.%m.%d') AS first_visit_date,
										DATE_FORMAT(recently_visit_date, '%Y.%m.%d') AS recently_visit_date
						FROM CLIENT WHERE id =".$id." AND is_delete='N' ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
	}

	public function getClientHp($id) {
		$sql =" SELECT hp FROM CLIENT WHERE id =".$id." AND is_delete='N' ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs['hp'];
		}
	}
}
