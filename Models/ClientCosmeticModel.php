<?php

namespace App\Models;
use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class ClientCosmeticModel extends Model
{

	protected $db;
	protected $table = 'CLIENT_COSMETIC';
	protected $primaryKey = 'id';
	protected $allowedFields = ['client_id', 'cosmetic_id', 'calculator_sales_id', 'cosmetic_stock_id',
															'purchase_date', 'quantity', 'discount_rate', 'amount', 'payment_method',
															'manager_id', 'manager_name', 'memo',
															'user_id', 'is_delete', 'insert_datetime', 'update_datetime'];

	public function _save($data) {
		$this->set('insert_datetime', 'now()', false);

		$this->save($data);
	}

	public function _update($id, $data) {
		$this->set('update_datetime', 'now()', false);

		$this->update($id, $data);
	}

	public function getCosmeticList($client_id) {
		// (cosmetic.sales_price * client.quantity) AS sales_price,
		$sql =" SELECT client.id, client.client_id, client.cosmetic_id, cosmetic.name, cosmetic.sales_price,
									DATE_FORMAT(client.purchase_date,'%Y.%m.%d') AS purchase_date, client.manager_name, client.quantity, IFNULL(client.memo, '-') AS memo,
									discount_rate, amount, payment_method AS payment_method_cd,
									CASE payment_method WHEN 'card' THEN '카드' WHEN 'money' THEN '현금' ELSE NULL END payment_method
							FROM CLIENT_COSMETIC AS client
							JOIN COSMETIC AS cosmetic ON cosmetic.id = client.cosmetic_id
							WHERE client_id = ".$client_id."
							  AND client.is_delete = 'N'
							ORDER BY id DESC ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getResultArray();
			return $rs;
		}
	}

  public function getCosmeticInfo($id) {
    $sql =" SELECT client.id, client.client_id, client.cosmetic_id, cosmetic.name, cosmetic.remain_quantity, client.quantity, client.memo,
									DATE_FORMAT(client.purchase_date,'%Y.%m.%d') AS purchase_date, client.manager_id, client.calculator_sales_id, client.cosmetic_stock_id,
									discount_rate, amount, payment_method AS payment_method_cd,
									CASE payment_method WHEN 'card' THEN '카드' WHEN 'money' THEN '현금' ELSE NULL END payment_method
							FROM CLIENT_COSMETIC AS client
							JOIN COSMETIC AS cosmetic ON cosmetic.id = client.cosmetic_id
							WHERE client.id = ".$id."
							 AND client.is_delete = 'N' ";

		if($query = $this->db->query($sql)) {
			$rs = $query->getRowArray();
			return $rs;
		}
  }
}
