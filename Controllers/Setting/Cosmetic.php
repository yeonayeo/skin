<?php

namespace App\Controllers\Setting;
use App\Controllers\BaseController;

class Cosmetic extends BaseController
{
	public function index()
	{
		$data				= $this->get_data();
		$page				= getParam('page', $this->request);
		$name				= getParam('name', $this->request);

		$cosmeticModel	= new \App\Models\CosmeticModel();

		// Page Count
		$page				= (!$page) ? 1 : $page;
		$inParam		= [
			'page'		=> $page,
			'row'			=> PAGE_ROW,
			'record'	=> ($page - 1) * PAGE_ROW,
			'name'		=> $name
		];

		$list = $cosmeticModel->getCosmeticList($inParam);
		$cnt = $cosmeticModel->getCosmeticCnt($inParam);
		$totPage = ceil($cnt / PAGE_ROW);
		$totalPage = ($totPage > 0) ? $totPage : 1;

		if(!empty($list)) {
			foreach($list AS $key => &$cosmetic) {
				$cosmetic['remain_quantity'] = ($cosmetic['remain_quantity']) ? number_format($cosmetic['remain_quantity']) : 0;
				$cosmetic['sales_price'] = ($cosmetic['sales_price']) ? number_format($cosmetic['sales_price']) : 0;
				$cosmetic['fee'] = ($cosmetic['fee']) ? number_format($cosmetic['fee']) : 0;
				$cosmetic['is_use'] = ynToBool($cosmetic['is_use']);
				$cosmetic['class'] = (!$cosmetic['is_use']) ? 'disabled' : '';
			}
		}

		// 페이지 정보
		$pagination = [
			'page'				=> $page,
			'total_page'	=> $totalPage,
			'prev_page'		=> prevPage($page),
			'next_page'		=> nextPage($page, $totalPage),
			'page_range'	=> rangePage($page, $totalPage)
		];

		$res = [
			'list'				=> $list,
			'name'				=> $name,
			'page'				=> $page,
			'total_page'	=> $totalPage,
			'total_cnt'		=> $cnt,
			'pagination'	=> $pagination
		];

		$data = array_merge($data, $res);

		return view('/setting/cosmetic/index', ['_RES' => $data]);
	}

	public function detail()
	{
		$data					= $this->get_data();
		$id						= getParam('id', $this->request);
		$start_date		= getParam('start_date', $this->request);
		$end_date			= getParam('end_date', $this->request);
		$page					= getParam('page', $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '제품을 선택해주세요.';

			return view('/error', ['_RES' => $resultErr]);
		}

		$cosmeticModel	= new \App\Models\CosmeticModel();
		$stockModel			= new \App\Models\CosmeticStockModel();

		// 화장품 정보
		$cosmetic = $cosmeticModel->getCosmeticInfo($id);
		$cosmetic['sales_price'] = ($cosmetic['sales_price']) ? number_format($cosmetic['sales_price']) : 0;
		$cosmetic['purchase_price'] = ($cosmetic['purchase_price']) ? number_format($cosmetic['purchase_price']) : 0;
		$cosmetic['fee'] = ($cosmetic['fee']) ? number_format($cosmetic['fee']) : 0;
		$cosmetic['memo'] = ($cosmetic['memo']) ? $cosmetic['memo'] : '-';

		// 재고 내역
		$page				= ($page) ? $page : 1;
		$inParam		= [
			'page'				=> $page,
			'row'					=> PAGE_ROW,
			'record'			=> ($page - 1) * PAGE_ROW,
			'start_date'	=> dateFormat($start_date),
			'end_date'		=> dateFormat($end_date)
		];

		$list = $stockModel->getCosmeticStockList($id, $inParam);
		$cnt = $stockModel->getCosmeticStockCnt($id, $inParam);
		$totPage = ceil($cnt / PAGE_ROW);
		$totalPage = ($totPage > 0) ? $totPage : 1;


		if(!empty($list)) {
			foreach($list AS $key => &$stock) {
				$stock['quantity'] = ($stock['quantity']) ? number_format($stock['quantity']) : 0;
				$stock['remain_quantity'] = ($stock['remain_quantity']) ? number_format($stock['remain_quantity']) : 0;
				$stock['note'] = ($stock['note']) ? $stock['note'] : '-';
				$stock['manager_name'] = ($stock['manager_name']) ? $stock['manager_name'] : '-';
			}
		}

		// 페이지 정보
		$pagination = [
			'page'				=> $page,
			'total_page'	=> $totalPage,
			'prev_page'		=> prevPage($page),
			'next_page'		=> nextPage($page, $totalPage),
			'page_range'	=> rangePage($page, $totalPage)
		];

		$data['pagination'] = $pagination;
		$data['stock_list'] = $list;
		$data['start_date'] = $start_date;
		$data['end_date']		= $end_date;

		$data = array_merge($data, $cosmetic);

		return view('/setting/cosmetic/detail', ['_RES' => $data]);
	}

	// [popup] 제품 등록
	public function popup_regist()
	{
		$data	= $this->get_data();

		return view('/setting/cosmetic/popup_regist', ['_RES' => $data]);
	}

	// 제품 등록 action
	public function action_regist()
	{
		$result						= $this->result;
		$resultErr				= $this->resultErr;
		$name							= getParam("name", $this->request);
		$purchase_price		= getParam("purchase_price", $this->request);
		$sales_price			= getParam("sales_price", $this->request);
		$fee							= getParam("fee", $this->request);
		$memo							= getParam("memo", $this->request);

		if(!$name || !$purchase_price || !$sales_price ) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다. 확인 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$cosmeticModel	= new \App\Models\CosmeticModel();
		$cosmeticModel->_save([ 'name'						=> $name,
														'purchase_price'	=> $purchase_price,
														'sales_price'			=> $sales_price,
														'fee'							=> ($fee) ? $fee : 0,
														'memo'						=> ($memo) ? avoidCrack($memo) : null
		]);

		responseOut($result);
	}

	// [popup] 제품 수정
	public function popup_update()
	{
		$data				= $this->get_data();
		$resultErr	= $this->resultErr;
		$id					= getParam("id", $this->request);
		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '제품을 선택해주세요.';

			return view('/error', ['_RES' => $resultErr]);
		}

		$cosmeticModel	= new \App\Models\CosmeticModel();

		// 화장품 정보
		$info = $cosmeticModel->getCosmeticInfo($id);
		$data = array_merge($data, $info);

		return view('/setting/cosmetic/popup_update', ['_RES' => $data]);
	}

	// 제품 수정 action
	public function action_update()
	{
		$result						= $this->result;
		$resultErr				= $this->resultErr;
		$id								= getParam("id", $this->request);
		$name							= getParam("name", $this->request);
		$purchase_price		= getParam("purchase_price", $this->request);
		$sales_price			= getParam("sales_price", $this->request);
		$fee							= getParam("fee", $this->request);
		$memo							= getParam("memo", $this->request);

		if(!$id || !$name || !$purchase_price || !$sales_price ) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다. 확인 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$cosmeticModel	= new \App\Models\CosmeticModel();
		$cosmeticModel->_update($id, [	'name'					=> $name,
																		'purchase_price'	=> $purchase_price,
																		'sales_price'			=> $sales_price,
																		'fee'							=> ($fee) ? $fee : 0,
																		'memo'						=> ($memo) ? avoidCrack($memo) : null
		]);

		responseOut($result);
	}

	// 활성화/비활성화 처리
	public function action_use() {
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$is_use			= getParam("is_use", $this->request);
		$ids				= getParam("ids", $this->request);

		if(empty($ids) || !$is_use) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '이용권을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$cosmeticModel	= new \App\Models\CosmeticModel();
		foreach($ids AS $id) {
			$cosmeticModel->_update($id, ['is_use' => $is_use]);
		}

		responseOut($result);
	}

	// 변경 내역 추가 [popup]
	public function popup_change_regist()
	{
		$data						= $this->get_data();
		$resultErr			= $this->resultErr;
		$cosmetic_id		= getParam("id", $this->request);

		if(!$cosmetic_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '제품을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$cosmeticModel		= new \App\Models\CosmeticModel();
		$usersModel				= new \App\Models\UsersModel();

		$userList					= $usersModel->getUserList();
		$remain_quantity	= $cosmeticModel->getRemainQuantity($cosmetic_id);

		$res = [
			'manager_list'		=> $userList,
			'goods_date'			=> date("Y.m.d", time()),
			'cosmetic_id'			=> $cosmetic_id,
			'remain_quantity'	=> $remain_quantity
		];
		$data = array_merge($data, $res);

		return view('/setting/cosmetic/popup_change_regist', ['_RES' => $data]);
	}

	public function action_regist_stock()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$cosmetic_id	= getParam("cosmetic_id", $this->request);
		$goods_date		= getParam("goods_date", $this->request);
		$type_cd			= getParam("type_cd", $this->request);
		$quantity			= getParam("quantity", $this->request);
		$manager_id		= getParam("manager_id", $this->request);
		$note					= getParam("note", $this->request);
		$userID				= getUserID();

		if(!$cosmetic_id || !$goods_date || !$type_cd || !$quantity) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다.';

			responseOutExit($resultErr);
		}

		$cosmeticModel	= new \App\Models\CosmeticModel();
		$stockModel			= new \App\Models\CosmeticStockModel();
		$salesModel			= new \App\Models\CalculatorSalesModel();

		// 현재 재고 가져오기
		$curr_quantity = $cosmeticModel->getRemainQuantity($cosmetic_id);
		$remain_quantity = 0;

		// 입고 : 재고 + 수량
		if($type_cd==2) {
			$remain_quantity = $curr_quantity + $quantity;
		} else { // 판매 or 소진 : 재고 - 수량
			$remain_quantity = $curr_quantity - $quantity;
		}

		$goods_date = dateFormat($goods_date);

		// 재고관리 추가
		$stockModel->_save(['cosmetic_id'			=> $cosmetic_id,
												'type'						=> $type_cd,
												'quantity'				=> $quantity,
												'remain_quantity'	=> $remain_quantity,
												'goods_date'			=> $goods_date, // 날짜 format 변경
												'manager_id'			=> ($manager_id) ? $manager_id : null,
												'note'						=> ($note) ? avoidCrack($note) : null,
												'user_id'					=> $userID

		]);
		// 재고 수정
		$cosmeticModel->_update($cosmetic_id, ['remain_quantity' => $remain_quantity]);

		// 판매일 경우 정산에 추가
		if($type_cd==1) {
			$cosmetic = $cosmeticModel->getCosmeticInfo($cosmetic_id);
			$amount = $cosmetic['sales_price'] * $quantity;
			$salesModel->_save(['type'				=> 2,
													'cosmetic_id'	=> $cosmetic_id,
													'sales_date'	=> $goods_date,
													'name'				=> $cosmetic['name'],
													'amount'			=> $amount]);
		}

		responseOut($result);
	}

	public function action_delete()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$id						= getParam("id", $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '제품을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$cosmeticModel	= new \App\Models\CosmeticModel();
		$cosmeticModel->_delete($id);

		responseOut($result);
	}

	public function excel_download()
	{
		$cosmeticModel = new \App\Models\CosmeticModel();
		helper('excel');

		$list = $cosmeticModel->excelCosmeticList();
		$count = number_format(count($list));

		downloadCosmetic($list, $count);
	}

	public function excel_download_stock()
	{
		$data					= $this->get_data();
		$cosmetic_id	= getParam("cosmetic_id", $this->request);
		$start_date		= getParam("start_date", $this->request);
		$end_date			= getParam("end_date", $this->request);

		if(!$cosmetic_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '제품을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$cosmeticModel = new \App\Models\CosmeticModel();
		$stockModel			= new \App\Models\CosmeticStockModel();
		helper('excel');

		$inParam		= [
			'start_date'	=> ($start_date && $start_date!='null') ? dateFormat($start_date) : '',
			'end_date'		=> ($end_date && $end_date!='null') ? dateFormat($end_date) : ''
		];

		$list = $stockModel->excelCosmeticStockList($cosmetic_id, $inParam);
		$count = number_format(count($list));
		$info = $cosmeticModel->getCosmeticInfo($cosmetic_id);

		downloadCosmeticStock($list, $count, $info, $data['is_super'], $start_date, $end_date);
	}
}
