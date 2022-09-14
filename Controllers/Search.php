<?php
namespace App\Controllers;

class Search extends BaseController
{
	public function index()
	{
	}

	// 달력
	public function calendar()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$year				= getParam("year", $this->request);
		$month			= getParam("month", $this->request);

		$result['data'] = getCalendar($year, $month);

		responseOut($result);
	}

	// 이용권
	public function ticket()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$name				= getParam("name", $this->request);

		$ticketModel	= new \App\Models\TicketModel();

		$list = $ticketModel->getTicketList(['name' => $name, 'is_use' => 'Y']);
		if(!empty($list)) {
			foreach($list AS $key => &$ticket) {
				$ticket['name_txt'] = str_replace($name, '<span class="keyword">'.$name.'</span>', $ticket['name']);
			}
		}
		$result['data'] = $list;

		responseOut($result);
	}

	public function ticket_kind()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$ticket_id	= getParam("ticket_id", $this->request);

		if(!$ticket_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '이용권을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$kindModel		= new \App\Models\TicketKindModel();

		$amount_list	= $kindModel->getTicketKindlist($ticket_id, 1);
		if(!empty($amount_list)) {
			foreach($amount_list AS $aKey => &$amount) {
				$amount['number'] = number_format($amount['number']);
			}
		}
		$count_list		= $kindModel->getTicketKindlist($ticket_id, 2);
		if(!empty($count_list)) {
			foreach($count_list AS $cKey => &$count) {
				$count['number'] = number_format($count['number']);
			}
		}

		$result['data'] = [
			'amount_list'	=> $amount_list,
			'count_list'	=> $count_list
		];

		responseOut($result);
	}

	// 고객
	public function client()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$name					= getParam("name", $this->request);

		$clientModel	= new \App\Models\ClientModel();

		$list = $clientModel->getClientList(['name' => $name]);
		if(!empty($list)) {
			foreach($list AS $key => &$client) {
				$client['name_txt'] = str_replace($name, '<span class="keyword">'.$name.'</span>', $client['name']);
				// 연락처
				$hp = phoneNumberToHyphenFormat(getDecrypt($client['hp']));
				$client['hp'] = phoneNumberToPassword($hp);
			}
		}
		$result['data'] = $list;

		responseOut($result);
	}

	// 화장품
	public function cosmetic()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$name				= getParam("name", $this->request);

		$cosmeticModel	= new \App\Models\CosmeticModel();

		$list = $cosmeticModel->getCosmeticList(['name' => $name, 'is_use' => 'Y', 'remain_quantity' => true]);
		if(!empty($list)) {
			foreach($list AS $key => &$cosmetic) {
				$cosmetic['name_txt'] = str_replace($name, '<span class="keyword">'.$name.'</span>', $cosmetic['name']);
			}
		}
		$result['data'] = $list;

		responseOut($result);
	}

	public function cosmetic_info()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$cosmetic_id	= getParam("cosmetic_id", $this->request);
		if(!$cosmetic_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '제품을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$cosmeticModel	= new \App\Models\CosmeticModel();

		$info = $cosmeticModel->getCosmeticInfo($cosmetic_id);

		$result['data'] = $info;

		responseOut($result);
	}

	public function client_ticket()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$client_id		= getParam("client_id", $this->request);
		$is_complete	= getParam("is_complete", $this->request);

		if(!$client_id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '고객을 선택해주세요.';

			responseOutExit($resultErr);
		}

		$ticketModel	= new \App\Models\ClientTicketModel();

		$is_complete	= ($is_complete) ? $is_complete : false;
		$list = $ticketModel->getTicketList($client_id, boolToYN($is_complete));
		if(!empty($list)) {
			foreach($list AS $key => &$ticket) {
				$ticket['name_txt'] = $ticket['name'].' (잔여 '.number_format(floatval($ticket['remain_number'])).'회'.')';
			}
		}

		$result['data'] = $list;

		responseOut($result);
	}

	public function schedule_time()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$booking_date	= getParam("booking_date", $this->request);
		$booking_room	= getParam("booking_room_cd", $this->request);
		$schedule_id	= getParam("schedule_id", $this->request);

		if(!$booking_date || !$booking_room) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '관리일과 관리실 모두 선택해주세요.';

			responseOutExit($resultErr);
		}

		$scheduleModel = new \App\Models\ScheduleModel();
		helper('schedule');

		// 예약 리스트 가져오고
		$booking_date = dateFormat($booking_date);
		$start_list = getStartTimeList();
		$end_list = getEndTimeList();
		$booking_list = $scheduleModel->getScheduleTimeList($booking_room, $booking_date);
		if(!empty($booking_list)) {
			$start_disabled = [];
			$end_disabled = [];
			foreach($booking_list AS $bKey => &$booking) {
				// 시작시간 disabled 추가
				for($st=$booking['start_time_id']; $st<$booking['end_time_id']; $st++) {
					if($schedule_id) {
						if($booking['id'] == $schedule_id) $start_disabled[] = $st;
					} else {
						$start_disabled[] = $st;
					}
				}
				// 종료시간 disabled 추가
				$start_time = $booking['start_time_id'] + 1;
				for($et=$start_time; $et<=$booking['end_time_id']; $et++) {
					if($schedule_id) {
						if($booking['id'] == $schedule_id) $end_disabled[] = $et;
					} else {
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

		$result['data'] = [
			'start_time_list'	=> $start_list,
			'end_time_list'		=> $end_list
		];

		responseOut($result);
	}
}
