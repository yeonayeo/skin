<?php
namespace App\Controllers;

class Client extends BaseController
{
	public function index()
	{
		$data	= $this->get_data();
		$page	= getParam("page", $this->request);
		$name	= getParam("name", $this->request);

		$permission = getSession('is_access');
		if(!$permission) {
			return view('/empty', ['_RES' => $data]);
		}

		$clientModel = new \App\Models\ClientModel();
		$ticketModel = new \App\Models\ClientTicketModel();
		$page				= (!$page) ? 1 : $page;
		$inParam		= [
			'page'		=> $page,
			'row'			=> PAGE_ROW,
			'record'	=> ($page - 1) * PAGE_ROW,
			'name'		=> $name
		];

		$cnt = $clientModel->getClientCnt($inParam);
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


		$list = $clientModel->getClientList($inParam);

		if(!empty($list)) {
			foreach($list AS $key => &$client) {
				$client['hp'] = phoneNumberToHyphenFormat(getDecrypt($client['hp']));
				$client['birth'] = ($client['birth']) ? birthToHyphenToFormat($client['birth']) : '-';
				$client['recently_visit_date'] = ($client['recently_visit_date']) ? $client['recently_visit_date'] : '-';
				$client['use_ticket'] = '-';
				// 사용중인 이용권 가져오기
				$ticket = $ticketModel->getClientTicket($client['id']);
				if($ticket['cnt'] > 0) {
					$ticket_txt = ($ticket['cnt']>1) ? ' 외 '.($ticket['cnt']-1).'건' : '';
					$client['use_ticket'] = $ticket['name'].$ticket_txt;
				}
			}
		}
		$res = [
			'pagination'	=> $pagination,
			'list'				=> $list,
			'total_cnt'		=> number_format($cnt),
			'name'				=> $name
		];

		$data = array_merge($data, $res);

		return view('/client/index', ['_RES' => $data]);
	}

	public function popup_password()
	{
		$data				= $this->get_data();
		$client_id	= getParam("client_id", $this->request);

		$data['client_id'] = $client_id;

		return view('/client/popup_password', ['_RES' => $data]);
	}

	// [popup] 고객 등록
	public function popup_regist()
	{
		$data	= $this->get_data();

		return view('/client/popup_regist', ['_RES' => $data]);
	}

	// 고객등록 action
	public function action_regist()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$name					= getParam("name", $this->request);
		$hp						= getParam("hp", $this->request);
		$birth				= getParam("birth", $this->request);
		$gender_cd		= getParam("gender_cd", $this->request);
		$address			= getParam("address", $this->request);
		$memo					= getParam("memo", $this->request);
		$special_note	= getParam("special_note", $this->request);

		if(!$name || !$hp || !$gender_cd) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다.';

			responseOutExit($resultErr);
		}

		$clientModel = new \App\Models\ClientModel();
		$clientModel->_save([	'name'					=> $name,
													'hp'						=> getEncrypt($hp),
													'birth'					=> ($birth) ? $birth : null,
													'gender'				=> $gender_cd,
													'address'				=> ($address) ? getEncrypt($address) : null,
													'memo'					=> ($memo) ? avoidCrack($memo) : null,
													'special_note'	=> ($special_note) ? avoidCrack($special_note) : null,
												 	'user_id'				=> getUserID()]);

		responseOut($result);
	}

	// 고객수정 action
	public function action_update()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$id						= getParam("id", $this->request);
		$name					= getParam("name", $this->request);
		$hp						= getParam("hp", $this->request);
		$birth				= getParam("birth", $this->request);
		$gender_cd		= getParam("gender_cd", $this->request);
		$address			= getParam("address", $this->request);
		$memo					= getParam("memo", $this->request);
		$special_note	= getParam("special_note", $this->request);

		if(!$id || !$name || !$hp || !$gender_cd) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다.';

			responseOutExit($resultErr);
		}

		$clientModel = new \App\Models\ClientModel();
		$clientModel->_update($id, ['name'					=> $name,
																'hp'						=> getEncrypt($hp),
																'birth'					=> ($birth) ? $birth : null,
																'gender'				=> $gender_cd,
																'address'				=> ($address) ? getEncrypt($address) : null,
																'memo'					=> ($memo) ? avoidCrack($memo) : null,
																'special_note'	=> ($special_note) ? avoidCrack($special_note) : null ]);

		responseOut($result);
	}

	// 고객 정보 열람 action
	public function action_password()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$pw					= getParam("pw", $this->request);

		if(!$pw) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '비밀번호 입력 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$pwModel = new \App\Models\PasswordModel();

		// 기존 비밀번호 체크
		$md5pw = md5($pw);
		if(!$pwModel->checkPassword($md5pw)) {
			$resultErr['res_cd']	= '001';
			$resultErr['err_msg']	= '비밀번호가 일치하지 않습니다.';

			responseOutExit($resultErr);
		}

		setSession('is_access', true);
		expireSession('is_access', 3600);

		responseOut($result);
	}

	// 고객 삭제 action
	public function action_delete()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$ids				= getParam("ids", $this->request);

		if(empty($ids)) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '삭제할 고객을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$clientModel = new \App\Models\ClientModel();
		foreach($ids AS $id) {
			$clientModel->_delete($id);
		}

		responseOut($result);
	}

	public function detail()
	{
		$data				= $this->get_data();
		$id					= getParam('id', $this->request);

		$permission = getSession('is_access');
		if(!$permission) {
			return view('/empty', ['_RES' => $data]);
		}

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '고객을 선택해주세요.';

			return view('/error', ['_RES' => $resultErr]);
		}

		$clientModel		= new \App\Models\ClientModel();
		$visitModel			= new \App\Models\ClientVisitModel();
		$ticketModel		= new \App\Models\ClientTicketModel();
		$useModel				= new \App\Models\ClientTicketUseModel();
		$cosmeticModel	= new \App\Models\ClientCosmeticModel();

		$info = $clientModel->getClientInfo($id);
		if(empty($info)) {
			$resultErr['res_cd']	= '004';
			$resultErr['err_msg']	= '삭제되거나 존재하지않은 고객입니다.';

			return view('/error', ['_RES' => $resultErr]);
		}

		$basicInfo = [
			'name'					=> $info['name'],
			'hp'						=> phoneNumberToHyphenFormat(getDecrypt($info['hp'])),
			'birth'					=> ($info['birth']) ? birthToHyphenToFormat($info['birth']) : '-',
			'gender'				=> ($info['gender']=='female') ? '여성' : '남성',
			'address'				=> ($info['address']) ? getDecrypt($info['address']) : '-',
			'memo'					=> ($info['memo']) ? $info['memo'] : '-',
			'special_note'	=> ($info['special_note']) ? $info['special_note'] : '-'
		];

		// 방문 정보
		$page				= 1;
		$visit_cnt = $visitModel->getClientVisitCnt($id);
		$totPage = ceil($visit_cnt / PAGE_ROW);
		$totalPage = ($totPage > 0) ? $totPage : 1;
		// 페이지 정보
		$pagination = [
			'page'				=> $page,
			'total_page'	=> $totalPage,
			'prev_page'		=> prevPage($page),
			'next_page'		=> nextPage($page, $totalPage),
			'page_range'	=> rangePage($page, $totalPage)
		];

		$visitList = $visitModel->getClientVisitList(['page'			=> $page,
																									'row'				=> PAGE_ROW,
																									'record'		=> ($page - 1) * PAGE_ROW,
																									'client_id'	=> $id]);

		$visitInfo = [
			'first_visit_date'	=> $info['first_visit_date'],
			'last_visit_date'		=> $info['recently_visit_date'],
			'list'							=> $visitList
		];

		// 이용권 list
		$ticketList = [];
		$ticketList = $ticketModel->getTicketList($id);
		if(!empty($ticketList)) {
			foreach($ticketList AS $tKey => &$ticket) {
				$ticket['use_list'] = $useModel->getTicketUseList($ticket['id']);
				$ticket['is_complete'] = ynToBool($ticket['is_complete']);
				$remain = number_format(floatval($ticket['remain_number']));
				$ticket['remain'] = $remain.'회';
				$ticket['memo'] = ($ticket['memo']) ? $ticket['memo'] : '-';
				$ticket['amount'] = number_format($ticket['amount']);
				$ticket['is_toggle'.$tKey] = false; // 프론트 요청
				$ticket['is_memo_update'] = false; // 프론트 요청
				$ticket['is_memo_update'] = false; // 프론트 요청
				foreach($ticket['use_list'] AS $uKey => &$use) {
					$remain = number_format(floatval($use['remain_number']));
					$use['remain'] = $remain.'회';
				}
			}
		}

		// 제품 list (화장품)
		$cosmeticList = [];
		$cosmeticList = $cosmeticModel->getCosmeticList($id);
		if(!empty($cosmeticList)) {
			foreach($cosmeticList AS $cKey => &$cosmetic) {
				$cosmetic['price'] = number_format($cosmetic['sales_price']);
				$cosmetic['quantity'] = number_format($cosmetic['quantity']);
				$cosmetic['amount'] = number_format($cosmetic['amount']);
				$cosmetic['manager_name'] = ($cosmetic['manager_name']) ? $cosmetic['manager_name'] : '-';
				$cosmetic['is_toggle'.$cKey] = false; // 프론트 요청
			}
		}

		$res = [
			'id'								=> $id,
			'basic_info'				=> $basicInfo,
			'is_visit'					=> (!empty($visitList)) ? true : false,
			'visit_info'				=> $visitInfo,
			'visit_pagination'	=> $pagination,
			'ticket_list'				=> $ticketList,
			'is_ticket'					=> (!empty($ticketList)) ? true : false,
			'cosmetic_list'			=> $cosmeticList,
			'is_cosmetic'				=> (!empty($cosmeticList)) ? true : false
		];

		$data = array_merge($data, $res);

		return view('/client/detail', ['_RES' => $data]);
	}

	public function get_visit_list()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$id					= getParam("id", $this->request);
		$page				= getParam("page", $this->request);

		$visitModel	= new \App\Models\ClientVisitModel();

		$page				= ($page) ? $page : 1;
		$visit_cnt	= $visitModel->getClientVisitCnt($id);
		$totPage		= ceil($visit_cnt / PAGE_ROW);
		$totalPage	= ($totPage > 0) ? $totPage : 1;

		// 페이지 정보
		$pagination = [
			'page'				=> $page,
			'total_page'	=> $totalPage,
			'prev_page'		=> prevPage($page),
			'next_page'		=> nextPage($page, $totalPage),
			'page_range'	=> rangePage($page, $totalPage)
		];

		$list = $visitModel->getClientVisitList(['page'			=> $page,
																						'row'				=> PAGE_ROW,
																						'record'		=> ($page - 1) * PAGE_ROW,
																						'client_id'	=> $id]);

		$result['data'] = [
			'pagination'	=> $pagination,
			'list'				=> $list
		];

		responseOut($result);
	}

	public function popup_update_basic()
	{
		$data	= $this->get_data();
		$id		= getParam("id", $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '수정할 고객을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$clientModel = new \App\Models\ClientModel();
		$info = $clientModel->getClientInfo($id);
		if(empty($info)) {
			$resultErr['res_cd']	= '004';
			$resultErr['err_msg']	= '삭제되거나 존재하지않은 고객입니다.';

			return view('/error', ['_RES' => $resultErr]);
		}

		$info['hp'] = getDecrypt($info['hp']);
		$info['address'] = ($info['address']) ? getDecrypt($info['address']) : '';

		$data = array_merge($data, $info);

		return view('/client/popup_update_basic', ['_RES' => $data]);
	}

	public function popup_regist_ticket()
	{
		$data				= $this->get_data();
		$client_id	= getParam("id", $this->request);

		if(!$client_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '고객을 선택해주세요.';

			return view('/error', ['_RES' => $resultErr]);
		}

		return view('/client/popup_regist_ticket', ['_RES' => $data]);
	}

	public function action_regist_ticket()
	{
		$result					= $this->result;
		$resultErr			= $this->resultErr;
		$client_id			= getParam("client_id", $this->request);
		$ticket_id			= getParam("ticket_id", $this->request);
		$ticket_name		= getParam("ticket_name", $this->request);
		$kind_id				= getParam("kind_id", $this->request);
		$purchase_date	= getParam("purchase_date", $this->request);
		$memo						= getParam("memo", $this->request);
		$userID					= getUserID();
		/* 수량, 할인율, 결제 방식 추가 */
		$quantity				= getParam("quantity", $this->request);
		$discount_rate	= getParam("discount_rate", $this->request);
		$payment_method	= getParam("payment_method_cd", $this->request);

		if(!$client_id || !$ticket_id || !$ticket_name || !$kind_id || !$purchase_date || !$quantity || !$payment_method) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다.';

			responseOutExit($resultErr);
		}

		$ticketModel	= new \App\Models\ClientTicketModel();
		$useModel			= new \App\Models\ClientTicketUseModel();
		$kindModel		= new \App\Models\TicketKindModel();
		$salesModel		= new \App\Models\CalculatorSalesModel();

		$purchase_date = dateFormat($purchase_date); // 결제일 format 변경

		$num = $kindModel->getKindNumber($kind_id);
		$number = floatval($num) * $quantity;
		$note = number_format($number).'회';
		$name = $ticket_name.' '.$note.'권';

		// 구매가
		$amount = $kindModel->getKindSalesAmount($kind_id);
		$amount = $amount * $quantity;
		if($discount_rate) {
			// 할인율이 존재할때
			$amount = $amount - ($amount * ( $discount_rate / 100));
		}
		$ticketModel->_save([
			'client_id'				=> $client_id,
			'ticket_id'				=> $ticket_id,
			'purchase_date'		=> $purchase_date,
			'name'						=> $name,
			'remain_number'		=> $number,
			'ticket_kind_id'	=> $kind_id,
			'memo'						=> ($memo) ? avoidCrack($memo) : null,
			'quantity'				=> $quantity,
			'discount_rate'		=> ($discount_rate) ? $discount_rate : null,
			'payment_method'	=> $payment_method,
			'amount'					=> $amount,
			'user_id'					=> $userID
		]);
		$ticketID = $ticketModel->insertID();
		if($ticketID) {
			$useModel->_save([
				'client_ticket_id'	=> $ticketID,
				'visit_date'				=> $purchase_date,
				'note'							=> '등록 ('.$note.')',
				'remain_number'			=> $number,
				'user_id'						=> $userID
			]);
		}

		// 매출 판매내역 추가
		$name = number_format(floatval($num)).'회';
		$sales_name = ($quantity<=1) ? $name : $name.' '.$quantity.'개';
		$salesModel->_save(['type'						=> 1,
												'client_id'				=> $client_id,
												'ticket_id'				=> $ticket_id,
												'sales_date'			=> dateFormat($purchase_date),
												'name'						=> $sales_name,
												'amount'					=> $amount,
												'payment_method'	=> $payment_method ]);
		$salesID = $salesModel->insertID();

		responseOut($result);
	}

	public function action_update_ticket_memo()
	{
		$result					= $this->result;
		$resultErr			= $this->resultErr;
		$ticket_id			= getParam("ticket_id", $this->request);
		$memo						= getParam("memo", $this->request);
		$userID					= getUserID();

		if(!$ticket_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다.';

			responseOutExit($resultErr);
		}

		$ticketModel = new \App\Models\ClientTicketModel();
		$ticketModel->_update($ticket_id, ['memo'	=> ($memo) ? avoidCrack($memo) : null]);

		$result['data']['memo'] = ($memo) ? $memo : '-';

		responseOut($result);
	}

	// 이용권 사용 완료처리
	public function action_ticket_confirm()
	{
		$result					= $this->result;
		$resultErr			= $this->resultErr;
		$ticket_id			= getParam("ticket_id", $this->request);
		$userID					= getUserID();

		if(!$ticket_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다.';

			responseOutExit($resultErr);
		}

		$scheduleModel	= new \App\Models\ScheduleModel();
		$ticketModel		= new \App\Models\ClientTicketModel();
		$useModel				= new \App\Models\ClientTicketUseModel();

		// 예약여부 체크
		$cnt = $scheduleModel->checkClientTicket($ticket_id);
		if($cnt) {
			$resultErr['res_cd']	= '004';
			$resultErr['err_msg']	= "이 이용권은 현재 예약된 상태입니다.\n예약관리에서 확인 후 이용해주세요.";

			responseOutExit($resultErr);
		}

		$useModel->_save([
			'client_ticket_id'	=> $ticket_id,
			'visit_date'				=> CURR_DATE,
			'note'							=> '사용완료처리(수동)',
			'remain_number'			=> 0,
			'user_id'						=> $userID
		]);
		$ticketModel->_update($ticket_id, ['is_complete'	=> 'Y', 'remain_number' => 0]);

		responseOut($result);
	}

	public function popup_detail_visit()
	{
		$data	= $this->get_data();
		$id		= getParam("id", $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '방문정보가 없습니다. 선택 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$scheduleModel	= new \App\Models\ScheduleModel();
		$visitModel			= new \App\Models\ClientVisitModel();

		$info = $scheduleModel->getScheduleInfo($id);
		$info['client_hp'] = phoneNumberToHyphenFormat(getDecrypt($info['client_hp']));

		// 관리시간
		$info['booking_time'] = $scheduleModel->getTimeText($info['start_time_id']);
		$info['booking_time'] .= ' ~ '.$scheduleModel->getTimeText($info['end_time_id']);

		// 방문 회차
		$info['is_visit'] = false;
		if($info['client_id'] && $info['client_ticket_id']) {
			$info['visit_cnt'] = $scheduleModel->getVisitCnt($info['client_ticket_id'], $info['booking_date'], $info['start_time_id']);
			$info['is_visit'] = true;
			$info['subtraction_number'] = number_format(floatval($info['subtraction_number'])).'회 차감';
		} else {
			$info['subtraction_number'] = '';
		}

		// 요일
		$info['insert_week_txt'] = weekText2(date("w", strtotime($info['insert_dt'])));
		$info['booking_week_txt'] = weekText2(date("w", strtotime($info['booking_dt'])));

		// 관리실
		if($info['booking_room_cd']<=8) {
			$info['booking_room'] = 'Room '.$info['booking_room_cd'];
		} else if($info['booking_room_cd']==9) {
			$info['booking_room'] = 'VIP';
		}

		$info['ticket_name'] = ($info['ticket_name']) ? $info['ticket_name'] : '-';
		$info['memo'] = ($info['memo']) ? $info['memo'] : '-';

		// 특이사항
		$special_note = $visitModel->getSpecialNote($id);
		$data['special_note'] = ($special_note) ? $special_note : '-';

		// 취소사유
		$info['is_cancel'] = ($info['status_cd']==2) ? true : false;
		$cancel_info = $visitModel->getCancelInfo($id);

		$info['add_sales_amount'] = ($info['add_sales_amount']) ? number_format($info['add_sales_amount']) : 0;

		$info = array_merge($info, $cancel_info);

		$data = array_merge($data, $info);

		return view('/client/popup_detail_visit', ['_RES' => $data]);
	}

	// [popup] 화장품 구매내역 등록
	public function popup_cosmetic_regist()
	{
		$data					= $this->get_data();
		$resultErr		= $this->resultErr;
		$client_id		= getParam("id", $this->request);

		$usersModel	= new \App\Models\UsersModel();
		$userList		= $usersModel->getUserList();

		if(!$client_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '고객을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$res = [
			'client_id'				=> $client_id,
			'manager_list'		=> $userList,
			'purchase_date'		=> date("Y.m.d", time())
		];
		$data = array_merge($data, $res);

		return view('/client/popup_cosmetic_regist', ['_RES' => $data]);
	}

	// 화장품 구매내역 action
	public function action_regist_cosmetic()
	{
		$result						= $this->result;
		$resultErr				= $this->resultErr;
		$client_id				= getParam("client_id", $this->request);
		$cosmetic_id			= getParam("cosmetic_id", $this->request);
		$cosmetic_name		= getParam("cosmetic_name", $this->request);
		$quantity					= getParam("quantity", $this->request);
		$purchase_date		= getParam("purchase_date", $this->request);
		$manager_id				= getParam("manager_id", $this->request);
		$remain_quantity	= getParam("remain_quantity", $this->request);
		$memo							= getParam("memo", $this->request);
		$userID						= getUserID();
		/* 할인율, 결제 방식 추가 */
		$discount_rate	= getParam("discount_rate", $this->request);
		$payment_method	= getParam("payment_method_cd", $this->request);

		if(!$client_id || !$cosmetic_id || !$quantity || !$purchase_date || !$manager_id || !$remain_quantity || !$payment_method) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다.';

			responseOutExit($resultErr);
		}

		$clientModel		= new \App\Models\ClientCosmeticModel();
		$cosmeticModel	= new \App\Models\CosmeticModel();
		$stockModel			= new \App\Models\CosmeticStockModel();
		$usersModel			= new \App\Models\UsersModel();
		$salesModel			= new \App\Models\CalculatorSalesModel();

		// 판매자명 가져오기
		$manager_name = $usersModel->getUserName($manager_id);
		// 판매가 가져오기
		$price = $cosmeticModel->getSalesPrice($cosmetic_id);
		$amount = $price * $quantity;
		if($discount_rate) {
			// 할인율이 존재할때
			$amount = $amount - ($amount * ( $discount_rate / 100));
		}

		// 정산 등록
		$salesModel->_save(['type'						=> 2,
												'cosmetic_id'			=> $cosmetic_id,
												'sales_date'			=> dateFormat($purchase_date),
												'name'						=> ($quantity<=1) ? $cosmetic_name : $cosmetic_name.' '.$quantity.'개',
												'payment_method'	=> $payment_method,
												'amount'					=> $amount ]);
		$salesID = $salesModel->insertID();
		if($salesID) {
			// 화장품 재고 수정
			$update_quantity = $remain_quantity - $quantity;
			// 재고관리 추가
			$stockModel->_save(['cosmetic_id'			=> $cosmetic_id,
													'type'						=> 1,
													'quantity'				=> $quantity,
													'remain_quantity'	=> $update_quantity,
													'goods_date'			=> dateFormat($purchase_date),
													'manager_id'			=> ($manager_id) ? $manager_id : null,
													'user_id'					=> $userID

			]);
			$stockID = $stockModel->insertID();
			$cosmeticModel->_update($cosmetic_id, ['remain_quantity' => $update_quantity]);

			// 구매내역 등록
			$clientModel->_save([
				'client_id'						=> $client_id,
				'cosmetic_id'					=> $cosmetic_id,
				'calculator_sales_id'	=> $salesID,
				'cosmetic_stock_id'		=> $stockID,
				'purchase_date'				=> dateFormat($purchase_date),
				'quantity'						=> $quantity,
				'memo'								=> ($memo) ? avoidCrack($memo) : null,
				'manager_id'					=> $manager_id,
				'manager_name'				=> $manager_name,
				'payment_method'			=> $payment_method,
				'amount'							=> $amount,
				'discount_rate'				=> ($discount_rate) ? $discount_rate : null,
				'user_id'							=> $userID
			]);
		}

		responseOut($result);
	}

	// [popup] 화장품 구매내역 수정
	public function popup_cosmetic_update()
	{
		$data	= $this->get_data();
		$id		= getParam("id", $this->request);

		$clientModel	= new \App\Models\ClientCosmeticModel();
		$usersModel		= new \App\Models\UsersModel();
		$userList			= $usersModel->getUserList();

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '구매 내역을 선택해주세요.';

			responseOutExit($resultErr);
		}

		// 구매내역
		$info = $clientModel->getCosmeticInfo($id);

		$res = [
			'id'							=> $id,
			'manager_list'		=> $userList
		];
		$data = array_merge($data, $res);
		$data = array_merge($data, $info);

		return view('/client/popup_cosmetic_update', ['_RES' => $data]);
	}

	// 화장품 구매내역 수정 action
	public function action_update_cosmetic()
	{
		$result								= $this->result;
		$resultErr						= $this->resultErr;
		$id										= getParam("id", $this->request);
		$client_id						= getParam("client_id", $this->request);
		$cosmetic_id					= getParam("cosmetic_id", $this->request);
		$cosmetic_name				= getParam("cosmetic_name", $this->request);
		$quantity							= getParam("quantity", $this->request);
		$memo									= getParam("memo", $this->request);
		$remain_quantity			= getParam("remain_quantity", $this->request);
		$purchase_date				= getParam("purchase_date", $this->request);
		$manager_id						= getParam("manager_id", $this->request);
		$calculator_sales_id	= getParam("calculator_sales_id", $this->request);
		$cosmetic_stock_id		= getParam("cosmetic_stock_id", $this->request);
		$original_quantity		= getParam("original_quantity", $this->request);
		$original_cosmetic_id	= getParam("original_cosmetic_id", $this->request);
		$userID								= getUserID();
		/* 할인율, 결제 방식 추가 */
		$discount_rate	= getParam("discount_rate", $this->request);
		$payment_method	= getParam("payment_method_cd", $this->request);

		if(!$id || !$client_id || !$cosmetic_id || !$quantity || !$purchase_date || !$manager_id ||
			!$original_cosmetic_id || !$original_quantity || !$calculator_sales_id || !$cosmetic_stock_id || !$payment_method) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다.';

			responseOutExit($resultErr);
		}

		$clientModel		= new \App\Models\ClientCosmeticModel();
		$cosmeticModel	= new \App\Models\CosmeticModel();
		$stockModel			= new \App\Models\CosmeticStockModel();
		$usersModel			= new \App\Models\UsersModel();
		$salesModel			= new \App\Models\CalculatorSalesModel();

		// 판매자명 가져오기
		$manager_name = $usersModel->getUserName($manager_id);
		// 판매가 가져오기
		$price = $cosmeticModel->getSalesPrice($cosmetic_id);
		$amount = $price * $quantity;
		if($discount_rate) {
			// 할인율이 존재할때
			$amount = $amount - ($amount * ( $discount_rate / 100));
		}


		// 정산 수정
		$salesModel->_update($calculator_sales_id, ['type'						=> 2,
																								'cosmetic_id'			=> $cosmetic_id,
																								'sales_date'			=> dateFormat($purchase_date),
																								'name'						=> ($quantity<=1) ? $cosmetic_name : $cosmetic_name.' '.$quantity.'개',
																								'amount'					=> $amount,
																								'payment_method'	=> $payment_method]);

		// 구매내역 수정
		$clientModel->_update($id, ['client_id'				=> $client_id,
																'cosmetic_id'			=> $cosmetic_id,
																'purchase_date'		=> dateFormat($purchase_date),
																'quantity'				=> $quantity,
																'memo'						=> ($memo) ? avoidCrack($memo) : null,
																'manager_id'			=> $manager_id,
																'manager_name'		=> $manager_name,
																'payment_method'	=> $payment_method,
																'discount_rate'		=> ($discount_rate) ? $discount_rate : null,
																'amount'					=> $amount
		]);

		// 화장품 재고 수정
		$update_quantity	= $remain_quantity - $quantity;
		$stock_data				= [	'cosmetic_id'		=> $cosmetic_id,
													'type'						=> 1,
													'quantity'				=> $quantity,
													'remain_quantity'	=> $update_quantity,
													'goods_date'			=> dateFormat($purchase_date),
													'manager_id'			=> ($manager_id) ? $manager_id : null ];

		if($original_cosmetic_id==$cosmetic_id) {
			$update_quantity = ($remain_quantity + $original_quantity) - $quantity;
			$stock_data['remain_quantity'] = $update_quantity;
			$stockModel->_update($cosmetic_stock_id, $stock_data);
		} else {
			// 기존 제품 재고 수 원상복귀 및 재고관리 판매취소처리, 새 제품 재고관리 save
			$remain_quantity = $cosmeticModel->getRemainQuantity($original_cosmetic_id);
			$stockModel->_save(['cosmetic_id'			=> $original_cosmetic_id,
													'type'						=> 4,
													'quantity'				=> $original_quantity,
													'remain_quantity'	=> $remain_quantity + $original_quantity,
													'goods_date'			=> dateFormat($purchase_date),
													'user_id'					=> $userID ]);
			$cosmeticModel->_update($original_cosmetic_id, ['remain_quantity' => $remain_quantity + $original_quantity]);

			// 변경된 제품의 재고내역 추가
			$stock_data['user_id'] = $userID;
			$stockModel->_save($stock_data);
			$cosmetic_stock_id = $stockModel->insertID();
		}

		$cosmeticModel->_update($cosmetic_id, ['remain_quantity' => $update_quantity, 'cosmetic_stock_id' => $cosmetic_stock_id]);

		responseOut($result);
	}
}
