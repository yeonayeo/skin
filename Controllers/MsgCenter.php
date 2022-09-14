<?php
defined('BASEPATH') OR exit('No direct script access allowed');

namespace App\Controllers;
use App\Libraries\Sms_gabia;

class MsgCenter extends BaseController {

	public function index() {
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$return_url		= getParam('return_url', $this->request);
		$param 				=	[];

		helper("msg");
		$smsModel = new \App\Models\SmsLogModel();

		$list = $smsModel->getSmslist();
		if(!empty($list)) {
			foreach($list AS $key => $val) {
				if($val['hp']) {
					$msg = $val['msg'];
					if($val['schedule_id']>0) {
						// 예약 발송 고객명, 방문시간 변경
						$msg = str_replace("고객명님", $val['client_name']."님", $msg);
						$msg = str_replace("N일", $val['booking_date'], $msg);
						$msg = str_replace("N시", $val['booking_time'], $msg);
					}

					$param['msg'] = $msg;
					$param['hp'] = phoneNumberToHyphenFormat(getDecrypt($val['hp']));

					$res = sendSMS($param);
					if($res) {
						$smsModel->_update($val['id'], ['is_send'	=> 'Y']);
					} else {
						echo "발송 실패";
					}
				}
			}
		}

		echo json_encode($result);
	}

	// 예약된 문자 발송
	public function send_reserved_sms() {
		$templateModel	= new \App\Models\SmsTemplateModel();
		$smsModel				= new \App\Models\SmsLogModel();
		$scheduleModel	= new \App\Models\ScheduleModel();

		$is_reserved		= 'Y';
		$template_list	= $templateModel->getTemplatelist($is_reserved);

		// 1. 방문 전일, 2. 방문 당일, 3. 방문 후일, 4. 방문 일주일 후, 5. 방문 한달 후
		if(!empty($template_list)) {
			foreach($template_list AS $tKey => $template) {
				$interval_day = null;
				$list = [];
				switch ($template['send_type']) {
					case '1':
						$interval_day = -1;
						break;
					case '2':
						$interval_day = 0;
						break;
					case '3':
						$interval_day = 1;
						break;
					case '4':
						$interval_day = 7;
						break;
					case '5':
						$interval_day = 30;
						break;
					default:
						// code...
						break;
				}

				if($template['send_type']>0) {
					$list = $scheduleModel->getSmsReserveList(CURR_DATE, $interval_day);
				}
				if(!empty($list)) {
					foreach($list AS $key => $schedule) {
						// 존재 유무 확인
						$is_sms = $smsModel->chkSmsLog($template['id'], $schedule['id'], CURR_DATE);
						if(!$is_sms) {
							$smsModel->_save([	'template_id'				=> $template['id'],
																	'schedule_id'				=> $schedule['id'],
																	'msg'								=> $template['msg'],
																	'client_id'					=> ($schedule['client_id']>0) ? $schedule['client_id'] : null,
																	'hp'								=> $schedule['client_hp'],
																	'reserve_datetime'	=> CURR_DATE.' '.$template['send_time']
							]);
						}
					}
				}
			}
		}
	}
}
