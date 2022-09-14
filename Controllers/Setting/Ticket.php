<?php

namespace App\Controllers\Setting;
use App\Controllers\BaseController;

class Ticket extends BaseController
{
	public function index()
	{
		$data				= $this->get_data();
		$page				= getParam('page', $this->request);
		$name				= getParam('name', $this->request);

		$ticketModel = new \App\Models\TicketModel();

		// Page Count
		$page				= (!$page) ? 1 : $page;
		$inParam		= [
			'page'		=> $page,
			'row'			=> PAGE_ROW,
			'record'	=> ($page - 1) * PAGE_ROW,
			'name'		=> $name
		];

		$list = $ticketModel->getTicketList($inParam);
		$cnt = $ticketModel->getTicketCnt($inParam);
		$totPage = ceil($cnt / PAGE_ROW);
		$totalPage = ($totPage > 0) ? $totPage : 1;

		if(!empty($list)) {
			foreach($list AS $key => &$ticket) {
				$ticket['is_use'] = ynToBool($ticket['is_use']);
				$ticket['class'] = (!$ticket['is_use']) ? 'disabled' : '';
				$ticket['note'] = ($ticket['note']) ? $ticket['note'] : '-';
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
			'page'				=> $page,
			'total_page'	=> $totalPage,
			'total_cnt'		=> number_format($cnt),
			'pagination'	=> $pagination
		];
		$data = array_merge($data, $res);

		return view('/setting/ticket/index', ['_RES' => $data]);
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

		$ticketModel = new \App\Models\TicketModel();
		foreach($ids AS $id) {
			$ticketModel->_update($id, ['is_use' => $is_use]);
		}

		responseOut($result);
	}

	// [popup] 이용권 등록
	public function popup_regist()
	{
		$data	= $this->get_data();

		return view('/setting/ticket/popup_regist', ['_RES' => $data]);
	}

	public function action_regist() {
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$name					= getParam("name", $this->request);
		$note					= getParam("note", $this->request);
		$count_list		= getParam("count_list", $this->request);

		if(!$name || empty($count_list)) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다. 확인 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$ticketModel	= new \App\Models\TicketModel();
		$kindModel		= new \App\Models\TicketKindModel();

		$ticketModel->_save([	'name'			=> $name,
													'note'			=> ($note) ? avoidCrack($note) : null,
													'is_use'		=> 'Y'
		]);
		$ticketID = $ticketModel->insertID();
		if($ticketID) {
			$kind = '';
			$kind_cnt = 0;

			foreach($count_list AS $cKey => $count) {
				if($kind_cnt==0) $kind = number_format($count['number']).'회';
				$kindModel->_save([
					'ticket_id'			=> $ticketID,
					'type'					=> 2,
					'number'				=> $count['number'],
					'sales_amount'	=> $count['sales_amount']
				]);
				$kind_cnt++;
			}

			$kind .= ($kind_cnt>1) ? ' 외 '.($kind_cnt-1).'건' : '';
			$ticketModel->_update($ticketID, ['kind' => $kind]);
		} else {
			$resultErr['res_cd']	= '006';
			$resultErr['err_msg']	= '이용권 등록 중 오류가 발생하였습니다. 잠시 후 다시 시도해주세요';

			responseOutExit($resultErr);
		}

		responseOut($result);
	}

	// [popup] 이용권 상세 및 수정
	public function popup_detail()
	{
		$data				= $this->get_data();
		$resultErr	= $this->resultErr;
		$id					= getParam('id', $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '이용권을 선택해주세요.';

			return view('/error', ['_RES' => $resultErr]);
		}

		$ticketModel	= new \App\Models\TicketModel();
		$kindModel		= new \App\Models\TicketKindModel();

		$info = $ticketModel->getTicketInfo($id);

		// 이용권 종류 list
		$count_list = $kindModel->getTicketKindlist($id);
		if(!empty($count_list)) {
			$count_cnt = count($count_list);
			foreach($count_list AS $cKey => &$count) {
				$count['checkbox'] = ($cKey<=0) ? true : false;
				$count['class_type'] = ($cKey<=0) ? 'add_row' : 'add_row no_check'; // 프론트 요청사항
				$count['add_btn'] = ($cKey==($count_cnt-1)) ? true : false; // 프론트 요청사항
			}
		}

		$info['count_list'] = $count_list;

		$data = array_merge($data, $info);

		return view('/setting/ticket/popup_detail', ['_RES' => $data]);
	}

	public function action_update() {
		$result						= $this->result;
		$resultErr				= $this->resultErr;
		$id								= getParam("id", $this->request);
		$name							= getParam("name", $this->request);
		$note							= getParam("note", $this->request);
		$count_list				= getParam("count_list", $this->request);
		$delete_kind_list	= getParam("delete_kind_list", $this->request);

		if(!$id || !$name || empty($count_list)) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다. 확인 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$ticketModel	= new \App\Models\TicketModel();
		$kindModel		= new \App\Models\TicketKindModel();

		$ticketModel->_update($id, ['name'			=> $name,
																'note'			=> ($note) ? avoidCrack($note) : null ]);

		$kind = '';
		$kind_cnt = 0;
		foreach($count_list AS $cKey => $count) {
			if($kind_cnt==0) $kind = number_format($count['number']).'회';
			$kindData = [
				'ticket_id'			=> $id,
				'type'					=> 2,
				'number'				=> $count['number'],
				'sales_amount'	=> $count['sales_amount']
			];
			if(isset($count['id']) && $count['id']) {
				$kindModel->_update($count['id'], $kindData);
			} else {
				$kindModel->_save($kindData);
			}
			$kind_cnt++;
		}

		$kind .= ($kind_cnt>1) ? ' 외 '.($kind_cnt-1).'건' : '';
		$ticketModel->_update($id, ['kind' => $kind]);

		// 이용권 종류 삭제
		if(!empty($delete_kind_list)) {
			foreach($delete_kind_list AS $delete_id) {
				$kindModel->_delete($delete_id);
			}
		}

		responseOut($result);
	}

	public function excel_download()
	{
		$ticketModel = new \App\Models\TicketModel();
		helper('excel');

		$list = $ticketModel->excelTicketList();
		$count = number_format(count($list));

		downloadTicket($list, $count);
	}
}
