<?php
namespace App\Controllers;

class Schedule extends BaseController
{

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
		$data		= $this->get_data();

		$scheduleModel	= new \App\Models\ScheduleModel();
		$noticeModel		= new \App\Models\NoticeModel();

		// 공지사항
		$notice = $noticeModel->getNoticeInfo();
		if(empty($notice)) {
			$notice = [
				'contents'		=> '',
				'notice_date'	=> CURR_DATE
			];
		}

		// 예약 현황 가져오기
		$list = [];
		for($room_cd=1; $room_cd<10; $room_cd++) {
			$schedule_list = $scheduleModel->getScheduleList($room_cd, CURR_DATE);
			if(!empty($schedule_list)) {
				foreach($schedule_list AS $key => &$schedule) {
					$is_visited = ($schedule['status_cd']==3) ? ' visited' : '';
					$schedule['class'] = 'booked '.$schedule['interval_style'].$is_visited;
					$schedule['style'] = 'top: calc(60px * '.$schedule['start_style'].');';
				}
			}
			$list[] = $schedule_list;
		}

		$res = [
			'list'					=> $list,
			'calendar_info'	=> getCalendar(),
			'ymd'						=> CURR_DATE_STRING,
			'notice'				=> $notice,
			'month'					=> date("n", CURR_TIME),
			'day'						=> date("j", CURR_TIME),
			'week_txt'			=> weekText(CURR_WEEK_NO),
			'prev_ymd'			=> date('Ymd',strtotime(CURR_DATE_STRING."-1 day")),
			'next_ymd'			=> date('Ymd',strtotime(CURR_DATE_STRING."+1 day"))
		];
		$data = array_merge($data, $res);

		return view('/schedule/index', ['_RES' => $data]);
	}

	public function get_schedule_info() {
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$ymd				= getParam("ymd", $this->request);

		if(!$ymd) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다. 입력 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$scheduleModel	= new \App\Models\ScheduleModel();

		// 예약 현황 가져오기
		$list = [];
		for($room_cd=1; $room_cd<10; $room_cd++) {
			$schedule_list = $scheduleModel->getScheduleList($room_cd, $ymd);
			if(!empty($schedule_list)) {
				foreach($schedule_list AS $key => &$schedule) {
					$is_visited = ($schedule['status_cd']==3) ? ' visited' : ' ';
					$schedule['class'] = 'booked '.$schedule['interval_style'].$is_visited;
					$schedule['style'] = 'top: calc(60px * '.$schedule['start_style'].');';
				}
			}
			$list[] = $schedule_list;
		}

		$data = [
			'list'			=> $list,
			'ymd'				=> $ymd,
			'month'			=> date("n", strtotime($ymd)),
			'day'				=> date("j", strtotime($ymd)),
			'week_txt'	=> weekText(date("w", strtotime($ymd))),
			'prev_ymd'	=> date('Ymd',strtotime($ymd."-1 day")),
			'next_ymd'	=> date('Ymd',strtotime($ymd."+1 day")),
			'calendar_info'	=> getCalendar(date("Y", strtotime($ymd)), date("m", strtotime($ymd)))
		];

		$result['data'] = $data;

		responseOut($result);
	}

	public function search()
	{
		$data		= $this->get_data();
		$name		= getParam("name", $this->request);
		$page		= getParam("page", $this->request);

		if(!$name) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '고객명을 검색해주세요.';

			return view('/error', ['_RES' => $resultErr]);
		}

		$scheduleModel	= new \App\Models\ScheduleModel();
		$name = str_replace("'", "", $name); // DB오류남

		$page			= (!$page) ? 1 : $page;
		$inParam	= [
			'page'				=> $page,
			'row'					=> PAGE_ROW,
			'record'			=> ($page - 1) * PAGE_ROW,
			'client_name'	=> $name
		];

		$cnt = $scheduleModel->getSearchScheduleCnt($inParam);
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

		$list = $scheduleModel->getSearchScheduleList($inParam);
		if(!empty($list)) {
			foreach($list AS $key => &$schedule) {
				// 연락처
				$hp = phoneNumberToHyphenFormat(getDecrypt($schedule['client_hp']));
				$schedule['client_hp'] = phoneNumberToPassword($hp);

				// 관리시간
				$schedule['booking_time'] = $scheduleModel->getTimeText($schedule['start_time_id']);
				$schedule['booking_time'] .= ' ~ '.$scheduleModel->getTimeText($schedule['end_time_id']);

				// 관리실
				if($schedule['booking_room_cd']<=8) {
					$schedule['booking_room'] = 'Room '.$schedule['booking_room_cd'];
				} else if($schedule['booking_room_cd']==9) {
					$schedule['booking_room'] = 'VIP';
				}
			}
		}

		$res = [
			'name'				=> $name,
			'list'				=> $list,
			'total_cnt'		=> $cnt,
			'pagination'	=> $pagination
		];
		$data = array_merge($data, $res);

		return view('/schedule/search', ['_RES' => $data]);
	}

	public function popup_book()
	{
		$data	= $this->get_data();
		$id		= getParam("id", $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '예약정보가 없습니다. 선택 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$scheduleModel	= new \App\Models\ScheduleModel();

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

		// 추가매출
		$info['add_sales_amount']		= ($info['add_sales_amount']) ? number_format($info['add_sales_amount']) : 0;
		$info['add_admin_memo']			= ($info['add_admin_memo']) ? $info['add_admin_memo'] : '-';

		$info['ticket_name'] = ($info['ticket_name']) ? $info['ticket_name'] : '-';
		$info['memo'] = ($info['memo']) ? $info['memo'] : '-';

		$data = array_merge($data, $info);

		return view('/schedule/popup_book', ['_RES' => $data]);
	}

	public function popup_update_book()
	{
		$data	= $this->get_data();
		$id		= getParam("id", $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '예약정보가 없습니다. 선택 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$scheduleModel	= new \App\Models\ScheduleModel();
		$ticketModel		= new \App\Models\ClientTicketModel();
		helper('schedule');

		$info												= $scheduleModel->getScheduleInfo($id);
		$info['client_hp']					= phoneNumberToHyphenFormat(getDecrypt($info['client_hp']));
		$info['insert_week_txt']		= weekText2(date("w", strtotime($info['insert_dt'])));
		$info['subtraction_number']	= floatval($info['subtraction_number']);
		$data = array_merge($data, $info);

		// 관리시간 list
		$start_list = getStartTimeList();
		$end_list = getEndTimeList();
		$booking_list = $scheduleModel->getScheduleTimeList($info['booking_room_cd'], $info['booking_date']);
		if(!empty($booking_list)) {
			$start_disabled = [];
			$end_disabled = [];
			foreach($booking_list AS $bKey => &$booking) {
				// 시작시간 disabled 추가
				for($st=$booking['start_time_id']; $st<$booking['end_time_id']; $st++) {
					if($booking['id']!=$id) {
						$start_disabled[] = $st;
					}
				}
				// 종료시간 disabled 추가
				$start_time = $booking['start_time_id'] + 1;
				for($et=$start_time; $et<=$booking['end_time_id']; $et++) {
					if($booking['id']!=$id) {
						$end_disabled[] = $et;
					}
				}
			}
			// disabled 유무 변경
			foreach($start_list AS $sKey => &$start) {
				if(in_array($start['id'], $start_disabled)) {
					$start['is_disabled'] = true;
				}
			}
			foreach($end_list AS $eKey => &$end) {
				if(in_array($end['id'], $end_disabled)) {
					$end['is_disabled'] = true;
				}
			}
		}

		$ticket_list = [];
		if($info['client_id']) {
			$ticket_list = $ticketModel->getTicketList($info['client_id'], 'N');
			if(!empty($ticket_list)) {
				foreach($ticket_list AS $key => &$ticket) {
					$ticket['name_txt'] = $ticket['name'].' (잔여 '.number_format(floatval($ticket['remain_number'])).'회)';
				}
			}
		}

		$data['start_time_list'] = $start_list;
		$data['end_time_list'] = $end_list;
		$data['ticket_list'] = $ticket_list;

		return view('/schedule/popup_update_book', ['_RES' => $data]);
	}

	public function popup_update_visit()
	{
		$data	= $this->get_data();
		$id		= getParam("id", $this->request);
		$prev	= getParam("prev", $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '방문정보가 없습니다. 선택 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$scheduleModel	= new \App\Models\ScheduleModel();
		$visitModel			= new \App\Models\ClientVisitModel();

		$info												= $scheduleModel->getScheduleInfo($id);
		$info['client_hp']					= phoneNumberToHyphenFormat(getDecrypt($info['client_hp']));

		// 관리시간
		$info['booking_time'] = $scheduleModel->getTimeText($info['start_time_id']);
		$info['booking_time'] .= ' ~ '.$scheduleModel->getTimeText($info['end_time_id']);

		// 방문 회차
		$info['is_visit'] = false;
		if($info['client_id'] && $info['client_ticket_id']) {
			$info['visit_cnt'] = $scheduleModel->getVisitCnt($info['client_ticket_id'], $info['booking_date'], $info['start_time_id']);
			$info['is_visit'] = true;
			$info['subtraction_number'] = number_format(floatval($info['subtraction_number'])).'회 차감';
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
		$info['add_sales_amount']		= ($info['add_sales_amount']) ? number_format($info['add_sales_amount']) : 0;

		$data = array_merge($data, $info);

		// 방문정보 id
		$visitID = $visitModel->getVisitID($id);
		$data['visit_id'] = $visitID;

		// 특이사항
		$special_note = $visitModel->getSpecialNote($id);
		$data['special_note'] = ($special_note) ? $special_note : null;
		$data['prev'] = ($prev) ? $prev : 'schedule';

		return view('/schedule/popup_update_visit', ['_RES' => $data]);
	}

	public function popup_cancel_book()
	{
		$data	= $this->get_data();
		$id	= getParam("id", $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '예약정보가 없습니다. 선택 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$scheduleModel	= new \App\Models\ScheduleModel();
		$ticketModel		= new \App\Models\ClientTicketModel();

		$info = $scheduleModel->getScheduleInfo($id);

		// 이용권 list
		$ticket_list = [];
		$ticket_id = $ticket_type = null;
		$remain_number = 0;
		if($info['client_ticket_id']) {
			$ticket_list = $ticketModel->getTicketList($info['client_id'], 'N', $info['client_ticket_id']);
			if(!empty($ticket_list)) {
				foreach($ticket_list AS $key => &$ticket) {
					$ticket_id = $ticket['id'];
					$ticket_type = $ticket['type'];
					$ticket['name_txt'] = $ticket['name'].' (잔여 '.number_format(floatval($ticket['remain_number'])).'회)';
					$remain_number = floatval($ticket['remain_number']);
				}
			}
		}

		$data['id']								= $id;
		$data['manage_name']			= $info['manage_name'];
		$data['booking_date']			= $info['booking_dt'];
		$data['client_id']				= $info['client_id'];
		$data['ticket_id']				= $info['client_ticket_id'];
		$data['ticket_list']			= $ticket_list;
		$data['ticket_type_cd']		= $ticket_type;
		$data['remain_number']		= $remain_number;

		return view('/schedule/popup_cancel_book', ['_RES' => $data]);
	}

	public function popup_confirm_book()
	{
		$data	= $this->get_data();
		$id	= getParam("id", $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '예약정보가 없습니다. 선택 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$data['id'] = $id;

		return view('/schedule/popup_confirm_book', ['_RES' => $data]);
	}

	public function popup_visit()
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

		// 추가매출
		$info['add_sales_amount']		= ($info['add_sales_amount']) ? number_format($info['add_sales_amount']) : 0;
		// $info['add_admin_memo']			= ($info['add_admin_memo']) ? $info['add_admin_memo'] : '-';

		$data = array_merge($data, $info);

		return view('/schedule/popup_visit', ['_RES' => $data]);
	}

	public function popup_regist()
	{
		$data					= $this->get_data();
		$booking_date	= getParam("booking_date", $this->request);

		if(!$booking_date) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '일정을 선택해주세요.';

			responseOutExit($resultErr);
		}

		helper('schedule');

		$data['booking_date'] =  date("Y.m.d", strtotime($booking_date));
		$data['start_time_list'] = getStartTimeList();
		$data['end_time_list'] = getEndTimeList();

		return view('/schedule/popup_regist', ['_RES' => $data]);
	}

	// 일정 등록
	public function action_regist()
	{
		$result							= $this->result;
		$resultErr					= $this->resultErr;
		$client_type_cd			= getParam("client_type_cd", $this->request);
		$client_id					= getParam("client_id", $this->request);
		$client_name				= getParam("client_name", $this->request);
		$client_hp					= getParam("client_hp", $this->request);
		$booking_type_cd		= getParam("booking_type_cd", $this->request);
		$manage_name				= getParam("manage_name", $this->request);
		$booking_date				= getParam("booking_date", $this->request);
		$booking_room_cd		= getParam("booking_room_cd", $this->request);
		$start_time_id			= getParam("start_time_id", $this->request);
		$end_time_id				= getParam("end_time_id", $this->request);
		$client_ticket_id		= getParam("ticket_id", $this->request);
		$ticket_number			= getParam("ticket_number", $this->request);
		$subtraction_number	= getParam("subtraction_number", $this->request);
		$direct_type				= getParam("direct_type_cd", $this->request); // 직접입력금액 결제 타입
		$memo								= getParam("memo", $this->request);
		$userID							= getUserID();

		/* 추가 매출 */
		$add_sales_type			= getParam("add_sales_type_cd", $this->request);
		$add_sales_amount		= getParam("add_sales_amount", $this->request);
		$add_admin_memo			= getParam("add_admin_memo", $this->request);

		// 공통 필수인자
		if(!$manage_name || !$booking_date || !$booking_room_cd || !$start_time_id || !$end_time_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 없습니다1. 확인 후 다시 시도해주세요.';
			responseOutExit($resultErr);
		} else {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 없습니다2. 확인 후 다시 시도해주세요.';
			// 고객이 등록되어있는 경우 고객의 id 체크
			if($client_type_cd==1 && !$client_id) responseOutExit($resultErr);
			// 일회성 고객일경우 연락처 체크
			if($client_type_cd==2 && (!$client_hp || !$client_name)) responseOutExit($resultErr);
			// 이용권이 없을경우 관리 금액 체크
			if(!$client_ticket_id && !$subtraction_number) responseOutExit($resultErr);
			// 이용권이 있을경우 차감 금액 및 횟수 체크
			if($client_ticket_id && !$ticket_number) responseOutExit($resultErr);
		}

		$scheduleModel	= new \App\Models\ScheduleModel();
		$ticketModel		= new \App\Models\ClientTicketModel();
		$templateModel	= new \App\Models\SmsTemplateModel();
		$smsModel				= new \App\Models\SmsLogModel();

		$ticket_name = '';
		$number = $subtraction_number;

		// 중복예약 체크
		$booking_list = $scheduleModel->getScheduleTimeList($booking_room_cd, $booking_date);
		if(!empty($booking_list)) {
			foreach($booking_list AS $bKey => $booking) {
				if( ($start_time_id<$booking['start_time_id']) && ($end_time_id > $booking['start_time_id'])) {
					$resultErr['err_msg']	= '중복 예약은 불가능합니다.';
					responseOutExit($resultErr);
				}
			}
		}

		// 이용권이 있을경우 남은 금액&횟수 체크
		if($client_ticket_id) {
			$ticket_info = $ticketModel->getTicketInfo($client_ticket_id);
			$remain_number = floatval($ticket_info['remain_number']);
			// 예약중인 금액 및 횟수
			$booking_number = $scheduleModel->getBookingNumber($client_ticket_id);
			$booking_number =  floatval($booking_number);
			$remain_number = $remain_number - $booking_number;

			if($ticket_number > $remain_number) {
				$resultErr['res_cd']	= '003';
				// $resultErr['err_msg']	= '이용권 잔여 횟수는 '.number_format($remain_number).'회 입니다.';
				$resultErr['err_msg'] = '이미 예약된 횟수가 잔여횟수 이상입니다.';
				responseOutExit($resultErr);
			}
			$ticket_name = $ticket_info['name'];
			$number = $ticket_number;
		} else {
			$ticket_name = '이용권 미사용 '.number_format($subtraction_number).'원 차감';
		}

		$start_style = $scheduleModel->getTimeStyle($start_time_id);
		$end_style = $scheduleModel->getTimeStyle($end_time_id);
		// 간격 => 종료시간 - 시작시간
		$interval = (float)$end_style - (float)$start_style;
		$interval_style = 'hr'.str_replace('.5', 'f', $interval);

		// 등록
		$scheduleModel->_save([	'client_id'						=> ($client_id) ? $client_id : null,
														'client_name'					=> $client_name,
														'client_hp'						=> ($client_hp) ? getEncrypt($client_hp) : null,
														'booking_type'				=> ($booking_type_cd) ? $booking_type_cd : null,
														'booking_date'				=> dateFormat($booking_date),
														'booking_room'				=> $booking_room_cd,
														'start_time_id'				=> $start_time_id,
														'end_time_id'					=> $end_time_id,
														'interval_style'			=> $interval_style,
														'start_style'					=> $start_style,
														'manage_name'					=> $manage_name,
														'client_ticket_id'		=> ($client_ticket_id) ? $client_ticket_id : null,
														'subtraction_number'	=> $number,
														'ticket_name'					=> ($ticket_name) ? $ticket_name : null,
														'direct_type'					=> (!$client_ticket_id) ? $direct_type : null,
														'memo'								=> ($memo) ? avoidCrack($memo) : null,
														'add_sales_type'			=> ($add_sales_amount) ? $add_sales_type : null,
														'add_sales_amount'		=> ($add_sales_amount) ? $add_sales_amount : null,
														'add_admin_memo'			=> ($add_admin_memo) ? avoidCrack($add_admin_memo) : null,
														'status'							=> 1,
														'user_id'							=> $userID
		]);
		$scheduleID = $scheduleModel->insertID();

		/* 220729 예약시 문자 즉시발송 */
		$template = $templateModel->getTemplateInfo(1);
		$sms_hp = ($client_hp) ? getEncrypt($client_hp) : null;
		if($client_id>0) {
			$clientModel	= new \App\Models\ClientModel();
			$sms_hp = $clientModel->getClientHp($client_id);
		}
		$smsModel->_save([	'template_id'				=> $template['id'],
												'schedule_id'				=> $scheduleID,
												'msg'								=> $template['msg'],
												'client_id'					=> ($client_id>0) ? $client_id : null,
												'hp'								=> $sms_hp
		]);

		responseOut($result);
	}

	// 일정 수정
	public function action_update()
	{
		$result							= $this->result;
		$resultErr					= $this->resultErr;
		$id									= getParam("id", $this->request);
		$booking_type_cd		= getParam("booking_type_cd", $this->request);
		$manage_name				= getParam("manage_name", $this->request);
		$booking_date				= getParam("booking_date", $this->request);
		$booking_room_cd		= getParam("booking_room_cd", $this->request);
		$start_time_id			= getParam("start_time_id", $this->request);
		$end_time_id				= getParam("end_time_id", $this->request);
		$client_ticket_id		= getParam("ticket_id", $this->request);
		$ticket_number			= getParam("ticket_number", $this->request);
		$direct_type				= getParam("direct_type_cd", $this->request); // 직접입력금액 결제 타입
		$subtraction_number	= getParam("subtraction_number", $this->request);
		$memo								= getParam("memo", $this->request);
		/* 추가 매출 */
		$add_sales_type			= getParam("add_sales_type_cd", $this->request);
		$add_sales_amount		= getParam("add_sales_amount", $this->request);
		$add_admin_memo			= getParam("add_admin_memo", $this->request);

		// 공통 필수인자
		if(!$id || !$manage_name || !$booking_date || !$booking_room_cd || !$start_time_id || !$end_time_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 없습니다1. 확인 후 다시 시도해주세요.';
			responseOutExit($resultErr);
		} else {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 없습니다2. 확인 후 다시 시도해주세요.';
			// 이용권이 없을경우 관리 금액 체크
			if(!$client_ticket_id && !$subtraction_number) responseOutExit($resultErr);
			// 이용권이 있을경우 차감 금액 및 횟수 체크
			if($client_ticket_id && !$ticket_number) responseOutExit($resultErr);
		}

		$scheduleModel	= new \App\Models\ScheduleModel();
		$ticketModel		= new \App\Models\ClientTicketModel();

		// 중복예약 체크
		$booking_list = $scheduleModel->getScheduleTimeList($booking_room_cd, $booking_date);
		if(!empty($booking_list)) {
			foreach($booking_list AS $bKey => $booking) {
				if( ($id != $booking['id']) && ($start_time_id<$booking['start_time_id'])  && ($end_time_id > $booking['start_time_id'])) {
					$resultErr['err_msg']	= '중복 예약은 불가능합니다.';
					responseOutExit($resultErr);
				}
			}
		}

		$ticket_name = '';
		$number = $subtraction_number;
		// 이용권이 있을경우 남은 금액&횟수 체크
		if($client_ticket_id) {
			$ticket_info = $ticketModel->getTicketInfo($client_ticket_id);
			$remain_number = floatval($ticket_info['remain_number']);
			// 예약중인 금액 및 횟수
			$booking_number = $scheduleModel->getBookingNumber($client_ticket_id, $id);
			$booking_number =  floatval($booking_number);
			$remain_number = $remain_number - $booking_number;

			if($ticket_number > $remain_number) {
				$resultErr['res_cd']	= '003';
				// $resultErr['err_msg']	= '이용권 잔여 횟수는'.number_format($remain_number).'회 입니다.';
				$resultErr['err_msg'] = '이미 예약된 횟수가 잔여횟수 이상입니다.';
				responseOutExit($resultErr);
			}
			$ticket_name = $ticket_info['name'];
			$number = $ticket_number;
		} else {
			$ticket_name = '이용권 미사용 '.number_format($subtraction_number).'원 차감';
		}

		$start_style = $scheduleModel->getTimeStyle($start_time_id);
		$end_style = $scheduleModel->getTimeStyle($end_time_id);
		// 간격 => 종료시간 - 시작시간
		$interval = (float)$end_style - (float)$start_style;
		$interval_style = 'hr'.str_replace('.5', 'f', $interval);
		// 수정 전 데이터
		$oldInfo = $scheduleModel->getScheduleInfo($id);
		// 수정
		$scheduleModel->_update($id, ['booking_type'				=> ($booking_type_cd) ? $booking_type_cd : null,
																	'booking_date'				=> dateFormat($booking_date),
																	'booking_room'				=> $booking_room_cd,
																	'start_time_id'				=> $start_time_id,
																	'end_time_id'					=> $end_time_id,
																	'interval_style'			=> $interval_style,
																	'start_style'					=> $start_style,
																	'manage_name'					=> $manage_name,
																	'client_ticket_id'		=> ($client_ticket_id) ? $client_ticket_id : null,
																	'subtraction_number'	=> $number,
																	'ticket_name'					=> ($ticket_name) ? $ticket_name : null,
																	'direct_type'					=> (!$client_ticket_id) ? $direct_type : null,
																	'memo'								=> ($memo) ? avoidCrack($memo) : null,
																	'add_sales_type'			=> ($add_sales_amount) ? $add_sales_type : null,
																	'add_sales_amount'		=> ($add_sales_amount) ? $add_sales_amount : null,
																	'add_admin_memo'			=> ($add_admin_memo) ? avoidCrack($add_admin_memo) : null
		]);

		/* 220729 예약시 문자 즉시발송 */
		if(($oldInfo['booking_date']!=$booking_date) || ($oldInfo['start_time_id']!=$start_time_id)) {
			$templateModel	= new \App\Models\SmsTemplateModel();
			$smsModel				= new \App\Models\SmsLogModel();
			$clientModel		= new \App\Models\ClientModel();
			$template = $templateModel->getTemplateInfo(1);
			$sms_hp = ($oldInfo['client_hp']) ? getEncrypt($oldInfo['client_hp']) : null;
			if($oldInfo['client_id']>0) {
				$sms_hp = $clientModel->getClientHp($oldInfo['client_id']);
			}
			$smsModel->_save([	'template_id'				=> $template['id'],
													'schedule_id'				=> $id,
													'msg'								=> $template['msg'],
													'client_id'					=> ($oldInfo['client_id']>0) ? $oldInfo['client_id'] : null,
													'hp'								=> $sms_hp
			]);
		}

		responseOut($result);
	}

	public function action_confirm()
	{
		$result							= $this->result;
		$resultErr					= $this->resultErr;
		$id									= getParam("id", $this->request);
		$special_note				= getParam("special_note", $this->request);
		$userID							= getUserID();

		// 공통 필수인자
		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 없습니다. 확인 후 다시 시도해주세요.';
			responseOutExit($resultErr);
		}

		$scheduleModel		= new \App\Models\ScheduleModel();
		$clientModel			= new \App\Models\ClientModel();
		$ticketModel			= new \App\Models\ClientTicketModel();
		$useModel					= new \App\Models\ClientTicketUseModel();
		$visitModel				= new \App\Models\ClientVisitModel();
		$calculatorModel	= new \App\Models\CalculatorAdminModel();
		$salesModel				= new \App\Models\CalculatorSalesModel();

		// 예약 정보 가져오기
		$info = $scheduleModel->getScheduleInfo($id);

		$ticket_id = null;
		$amount = $info['subtraction_number'];
		if($info['client_ticket_id']) {
			// 고객 이용권 남은 금액&횟수 수정 --> 금액&횟수가 0일 경우 자동으로 사용완료 처리
			$client_ticket_info = $ticketModel->getTicketInfo($info['client_ticket_id']);
			$remain_number = $client_ticket_info['remain_number'] - $info['subtraction_number'];
			$ticket_id = $client_ticket_info['ticket_id'];
			$is_complete = 'N';
			if(floatval($remain_number) <= 0) {
				// 사용 완료
				$is_complete = 'Y';
			}
			$ticketModel->_update($info['client_ticket_id'], ['remain_number' => $remain_number, 'is_complete' => $is_complete]);

			// 고객 이용권 사용내역 추가
			$useModel->_save(['client_ticket_id'	=> $info['client_ticket_id'],
												'schedule_id'				=> $id,
												'visit_date'				=> $info['booking_dt'],
												'remain_number'			=> $remain_number,
												'note'							=> '방문 완료',
												'user_id'						=> $userID ]);

			// 횟수권 매출금액 변동
			$kind = $ticketModel->getKindInfo($info['client_ticket_id']);
			$count_amount = ($kind['sales_amount'] / floatval($kind['number']));
			$amount = $info['subtraction_number'] * $count_amount;
		}

		$is_direct = (!$info['client_ticket_id']) ? true : false;

		// 매출현황(관리내역) 추가
		$calculatorModel->_save([
			'client_id'				=> $info['client_id'],
			'ticket_id'				=> $ticket_id,
			'type'						=> (!$is_direct) ? 1 : 2,  // 1. 이용권, 2. 직접입력금액, 3. 추가관리
			'admin_date'			=> $info['booking_dt'],
			'name'						=> $info['manage_name'],
			'amount'					=> $amount,
			'payment_method'	=> ($is_direct) ? $info['direct_type_cd'] : null,
			'schedule_id'			=> $id
		]);

		// 직접입력금액일 경우 판매내역에도 추가
		if($is_direct) {
			$salesModel->_save([
				'client_id'				=> $info['client_id'],
				'ticket_id'				=> $ticket_id,
				'type'						=> 3,  // 1. 이용권, 2. 화장품, 3. 직접입력금액, 4. 추가관리
				'sales_date'			=> $info['booking_dt'],
				'name'						=> $info['manage_name'],
				'amount'					=> $amount,
				'payment_method'	=> $info['direct_type_cd'],
				'schedule_id'			=> $id
			]);
		} else {
			// 추가금액이 있을경우 판매내역 추가 --> 추가금액은 이용권 이용할때만 존재한다.
			if($info['add_sales_amount']) {
				$salesModel->_save([
					'client_id'				=> $info['client_id'],
					'ticket_id'				=> $ticket_id,
					'type'						=> 4,  // 1. 이용권, 2. 화장품, 3. 직접입력금액, 4. 추가관리
					'sales_date'			=> $info['booking_dt'],
					'name'						=> $info['manage_name'],
					'amount'					=> $info['add_sales_amount'],
					'payment_method'	=> $info['add_sales_type_cd'],
					'schedule_id'			=> $id
				]);

				$calculatorModel->_save([
					'client_id'				=> $info['client_id'],
					'ticket_id'				=> $ticket_id,
					'type'						=> 3,  // 1. 이용권, 2. 직접입력금액, 3. 추가관리
					'admin_date'			=> $info['booking_dt'],
					'name'						=> $info['manage_name'],
					'amount'					=> $info['add_sales_amount'],
					'payment_method'	=> $info['add_sales_type_cd'],
					'schedule_id'			=> $id
				]);
			}
		}


		if($info['client_id']) {
			// 고객 첫 방문일, 최근 방문일 update
			$updateData = ['recently_visit_date' => $info['booking_dt']];
			$is_first_visit = $visitModel->getIsFirstVisitDate($info['client_id']);
			if(!$is_first_visit) {
				$updateData['first_visit_date'] = $info['booking_dt'];
			}
			$clientModel->_update($info['client_id'], $updateData);

			// 고객 방문정보 추가
			$visitModel->_save(['client_id'						=> $info['client_id'],
													'schedule_id'					=> $id,
													'status'							=> 1,
													'manage_name'					=> $info['manage_name'],
													'subtraction_number'	=> $info['subtraction_number'],
													'special_note'				=> ($special_note) ? avoidCrack($special_note) : null,
													'booking_date'				=> $info['booking_dt'],
													'user_id'							=> $userID
			]);
		}


		// 예약 상태 변경
		$scheduleModel->_update($id, ['status' => 3]);

		responseOut($result);
	}

	public function action_cancel()
	{
		$result							= $this->result;
		$resultErr					= $this->resultErr;
		$id									= getParam("id", $this->request);
		$cancel_type_cd			= getParam("cancel_type_cd", $this->request);
		$manage_name				= getParam("manage_name", $this->request);
		$client_id					= getParam("client_id", $this->request);
		$client_ticket_id		= getParam("ticket_id", $this->request);
		$ticket_number			= getParam("ticket_number", $this->request);
		$remain_number			= getParam("remain_number", $this->request);
		$cancel_reason			= getParam("cancel_reason", $this->request);
		$booking_date				= getParam("booking_date", $this->request);
		$userID							= getUserID();

		// 공통 필수인자
		if(!$id || !$cancel_type_cd || !$manage_name || !$booking_date) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 없습니다. 확인 후 다시 시도해주세요.';
			responseOutExit($resultErr);
		}

		$scheduleModel		= new \App\Models\ScheduleModel();
		$ticketModel			= new \App\Models\ClientTicketModel();
		$useModel					= new \App\Models\ClientTicketUseModel();
		$visitModel				= new \App\Models\ClientVisitModel();
		$calculatorModel	= new \App\Models\CalculatorAdminModel();

		if($client_id && $client_ticket_id && $ticket_number) {
			if($client_ticket_id && $ticket_number) {
				// 고객 이용권 남은 금액&횟수 수정 --> 금액&횟수가 0일 경우 자동으로 사용완료 처리
				$client_ticket_info = $ticketModel->getTicketInfo($client_ticket_id);
				$remain_number = $remain_number - $ticket_number;
				$ticket_id = $client_ticket_info['ticket_id'];
				$is_complete = 'N';
				if(floatval($remain_number) <= 0) {
					// 사용 완료
					$is_complete = 'Y';
				}
				$ticketModel->_update($client_ticket_id, ['remain_number' => $remain_number, 'is_complete' => $is_complete]);

				// 고객 이용권 사용내역 추가
				$useModel->_save(['client_ticket_id'	=> $client_ticket_id,
													'schedule_id'				=> $id,
													'visit_date'				=> $booking_date,
													'remain_number'			=> $remain_number,
													'note'							=> '당일 취소 (패널티 부여)',
													'user_id'						=> $userID ]);

				// 매출현황(관리내역) 추가
				$calculatorModel->_save([
					'client_id'		=> $client_id,
					'ticket_id'		=> $ticket_id,
					'type'				=> 1,  // 1. 이용권, 2. 직접입력금액
					'admin_date'	=> $booking_date,
					'name'				=> $manage_name,
					'amount'			=> $remain_number,
					'schedule_id'	=> $id
				]);
			}

			// 고객 방문정보 추가
			if($client_id) {
				$visitModel->_save(['client_id'						=> $client_id,
														'schedule_id'					=> $id,
														'status'							=> 2,
														'manage_name'					=> $manage_name,
														'subtraction_number'	=> ($client_ticket_id && $ticket_number) ? $remain_number : null,
														'cancel_type'					=> $cancel_type_cd,
														'cancel_reason'				=> ($cancel_reason) ? avoidCrack($cancel_reason) : null,
														'booking_date'				=> $booking_date,
														'user_id'							=> $userID
				]);
			}
		}


		// 예약 상태 변경
		$scheduleModel->_update($id, ['status' => 2]);

		responseOut($result);
	}

	// 관리정보 수정
	public function action_update_visit()
	{
		$result							= $this->result;
		$resultErr					= $this->resultErr;
		$id									= getParam("id", $this->request);
		$visit_id						= getParam("visit_id", $this->request);
		$booking_type_cd		= getParam("booking_type_cd", $this->request);
		$manage_name				= getParam("manage_name", $this->request);
		$memo								= getParam("memo", $this->request);
		$special_note				= getParam("special_note", $this->request);
		$add_admin_memo			= getParam("add_admin_memo", $this->request);

		// 필수인자
		if(!$id || !$visit_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 없습니다. 확인 후 다시 시도해주세요.';
			responseOutExit($resultErr);
		}

		$scheduleModel	= new \App\Models\ScheduleModel();
		$visitModel		= new \App\Models\ClientVisitModel();

		// 수정
		$scheduleModel->_update($id, ['booking_type'				=> ($booking_type_cd) ? $booking_type_cd : null,
																	'manage_name'					=> $manage_name,
																	'memo'								=> ($memo) ? avoidCrack($memo) : null,
																	'add_admin_memo'			=> ($add_admin_memo) ? avoidCrack($add_admin_memo) : null
		]);
		$visitModel->_update($visit_id, ['special_note'			=> ($special_note) ? avoidCrack($special_note) : null]);

		responseOut($result);
	}
}
