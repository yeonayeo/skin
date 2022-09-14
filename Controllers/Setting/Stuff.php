<?php

namespace App\Controllers\Setting;
use App\Controllers\BaseController;

class Stuff extends BaseController
{
	public function index()
	{
		$data				= $this->get_data();
		$page				= getParam('page', $this->request);
		$name				= getParam('name', $this->request);

		$stuffModel	= new \App\Models\StuffModel();

		// Page Count
		$page				= (!$page) ? 1 : $page;
		$inParam		= [
			'page'		=> $page,
			'row'			=> PAGE_ROW,
			'record'	=> ($page - 1) * PAGE_ROW,
			'name'		=> $name
		];

		$list = $stuffModel->getStuffList($inParam);
		$cnt = $stuffModel->getStuffCnt($inParam);
		$totPage = ceil($cnt / PAGE_ROW);
		$totalPage = ($totPage > 0) ? $totPage : 1;

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
			'total_cnt'		=> number_format($cnt),
			'pagination'	=> $pagination
		];

		$data = array_merge($data, $res);

		return view('/setting/stuff/index', ['_RES' => $data]);
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
			$resultErr['err_msg']	= '비품을 선택해주세요.';

			return view('/error', ['_RES' => $resultErr]);
		}

		$stuffModel	= new \App\Models\StuffModel();
		$stockModel	= new \App\Models\StuffStockModel();

		// 비품 정보
		$stuff = $stuffModel->getStuffInfo($id);
		$stuff['memo'] = ($stuff['memo']) ? $stuff['memo'] : '-';
		$stuff['purchase_price'] = number_format($stuff['purchase_price']);

		// 재고 내역
		$page				= ($page) ? $page : 1;
		$inParam		= [
			'page'				=> $page,
			'row'					=> PAGE_ROW,
			'record'			=> ($page - 1) * PAGE_ROW,
			'start_date'	=> dateFormat($start_date),
			'end_date'		=> dateFormat($end_date)
		];

		$list = $stockModel->getStuffStockList($id, $inParam);
		$cnt = $stockModel->getStuffStockCnt($id, $inParam);
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

		$data = array_merge($data, $stuff);

		return view('/setting/stuff/detail', ['_RES' => $data]);
	}

	// [popup] 제품 등록
	public function popup_regist()
	{
		$data	= $this->get_data();

		return view('/setting/stuff/popup_regist', ['_RES' => $data]);
	}

	// 비품 등록 action
	public function action_regist()
	{
		$result					= $this->result;
		$resultErr			= $this->resultErr;
		$name						= getParam("name", $this->request);
		$type						= getParam("type", $this->request);
		$memo						= getParam("memo", $this->request);
		$purchase_price	= getParam("purchase_price", $this->request);

		if(!$name || !$type) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다. 확인 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$stuffModel	= new \App\Models\StuffModel();
		$stuffModel->_save([	'name'						=> $name,
													'type'						=> $type,
													'memo'						=> ($memo) ? avoidCrack($memo) : null,
													'purchase_price'	=> ($purchase_price) ? $purchase_price : 0
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
			$resultErr['err_msg']	= '비품을 선택해주세요.';

			return view('/error', ['_RES' => $resultErr]);
		}

		$stuffModel	= new \App\Models\StuffModel();

		// 화장품 정보
		$info = $stuffModel->getStuffInfo($id);
		$data = array_merge($data, $info);

		return view('/setting/stuff/popup_update', ['_RES' => $data]);
	}

	// 비품 수정 action
	public function action_update()
	{
		$result					= $this->result;
		$resultErr			= $this->resultErr;
		$id							= getParam("id", $this->request);
		$name						= getParam("name", $this->request);
		$type						= getParam("type", $this->request);
		$memo						= getParam("memo", $this->request);
		$purchase_price	= getParam("purchase_price", $this->request);

		if(!$id || !$name || !$type) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다. 확인 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$stuffModel	= new \App\Models\StuffModel();
		$stuffModel->_update($id, [	'name'	=> $name,
																'type'	=> $type,
																'memo'	=> ($memo) ? avoidCrack($memo) : null,
																'purchase_price'	=> ($purchase_price) ? $purchase_price : 0
		]);

		responseOut($result);
	}

	// 변경 내역 추가 [popup]
	public function popup_change_regist()
	{
		$data				= $this->get_data();
		$resultErr	= $this->resultErr;
		$stuff_id		= getParam("id", $this->request);

		if(!$stuff_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '비품을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$stuffModel		= new \App\Models\StuffModel();
		$usersModel		= new \App\Models\UsersModel();

		$userList					= $usersModel->getUserList();
		$remain_quantity	= $stuffModel->getRemainQuantity($stuff_id);

		$res = [
			'manager_list'		=> $userList,
			'goods_date'			=> date("Y.m.d", time()),
			'stuff_id'				=> $stuff_id,
			'remain_quantity'	=> $remain_quantity
		];
		$data = array_merge($data, $res);

		return view('/setting/stuff/popup_change_regist', ['_RES' => $data]);
	}

	public function action_regist_stock()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$stuff_id			= getParam("stuff_id", $this->request);
		$goods_date		= getParam("goods_date", $this->request);
		$type_cd			= getParam("type_cd", $this->request);
		$quantity			= getParam("quantity", $this->request);
		$manager_id		= getParam("manager_id", $this->request);
		$note					= getParam("note", $this->request);
		$userID				= getUserID();

		if(!$stuff_id || !$goods_date || !$type_cd || !$quantity) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다.';

			responseOutExit($resultErr);
		}

		$stuffModel		= new \App\Models\StuffModel();
		$stockModel		= new \App\Models\StuffStockModel();

		// 현재 재고 가져오기
		$curr_quantity = $stuffModel->getRemainQuantity($stuff_id);
		$remain_quantity = 0;

		// 입고 : 재고 + 수량
		if($type_cd==1) {
			$remain_quantity = $curr_quantity + $quantity;
		} else { // 소진 : 재고 - 수량
			$remain_quantity = $curr_quantity - $quantity;
		}

		$goods_date = dateFormat($goods_date);

		// 재고관리 추가
		$stockModel->_save(['stuff_id'				=> $stuff_id,
												'type'						=> $type_cd,
												'quantity'				=> $quantity,
												'remain_quantity'	=> $remain_quantity,
												'goods_date'			=> $goods_date, // 날짜 format 변경
												'manager_id'			=> ($manager_id) ? $manager_id : null,
												'note'						=> ($note) ? avoidCrack($note) : null,
												'user_id'					=> $userID

		]);
		// 재고 수정
		$stuffModel->_update($stuff_id, ['remain_quantity' => $remain_quantity]);

		responseOut($result);
	}

	public function action_delete()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$ids					= getParam("id", $this->request);

		if(empty($ids)) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '비품을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$stuffModel	= new \App\Models\StuffModel();

		foreach($ids AS $id) {
			$stuffModel->_delete($id);
		}

		responseOut($result);
	}

	public function excel_download()
	{
		$stuffModel = new \App\Models\StuffModel();
		helper('excel');

		$list = $stuffModel->excelStuffList();
		$count = number_format(count($list));

		downloadStuff($list, $count);
	}

	public function excel_download_stock()
	{
		$data					= $this->get_data();
		$stuff_id			= getParam("stuff_id", $this->request);
		$start_date		= getParam("start_date", $this->request);
		$end_date			= getParam("end_date", $this->request);

		if(!$stuff_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '비품을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$stuffModel		= new \App\Models\StuffModel();
		$stockModel		= new \App\Models\StuffStockModel();
		helper('excel');

		$inParam		= [
			'start_date'	=> ($start_date && $start_date!='null') ? dateFormat($start_date) : '',
			'end_date'		=> ($end_date && $end_date!='null') ? dateFormat($end_date) : ''
		];

		$list = $stockModel->excelStuffStockList($stuff_id, $inParam);
		$count = number_format(count($list));
		$info = $stuffModel->getStuffInfo($stuff_id);

		downloadStuffStock($list, $count, $info, $data['is_super'], $start_date, $end_date);
	}
}
