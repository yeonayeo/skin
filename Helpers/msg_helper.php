<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use App\Libraries\Sms_gabia;

if (!function_exists('sendSMS')) {
	function sendSMS($param) {
		$result						= ['result_code'=>'OK', 'data'=>[], 'redirect' => ''];
		$result_error			= ['result_code'=>'ERROR', 'error_code' => '', 'error_msg' => '', 'data'=>'', 'redirect' => ''];
		$date							= "_RESERVE_DATE_"; //예약 날짜 없을 때
		if(!isset($param["hp"]) || $param["hp"]=="") {
			return 0;
		}
		if(isset($param["date"]) && $param["date"]) {
			$date = date("Y-m-d H:i:s", strtotime($param["date"]));
		}

		$smsGabia = new Sms_gabia();
		$param["id"]			= SMS_ID;
		$param["key"]			= SMS_KEY;
		$param["sender"]	= SMS_SENDER;
		$smsGabia->set_cfg($param);

		// ID, KEY에 대한 인증
		if(!$smsGabia->auth()) {
			$result_error['error_msg'] = $smsGabia->get_auth_com_result();
			$result_error['error_code'] = "Failure_Auth";
			return 99; //가비아 인증키 오류
		}

		$hp = $param["hp"];
		$title = "";
		$message = strip_tags($param["msg"]);
		$message = str_replace("newline", "\r\n", strip_tags($param["msg"]));
		$msgLenth = strlen($message);

		// 80자이하 SMS, 81자 이상 2000자 미만 LMS 2000자 이상 MMS
		if($msgLenth <= 80) {
			// SMS
			if(!$smsGabia->send_sms($hp, $message, $date)) {
				return 0;  //발송 실패
			}
		} else if(($msgLenth >= 81) && $msgLenth < 2000) {
			// LMS
			if(!$smsGabia->send_lms($hp, $title, $message, $date)) {
				return 0;  //발송 실패
			}
		} else {
			// MMS
			$images0 = $images1 = $images2 = "";
			$imageCnt = 0;
			if(isset($file[0]) && $file[0]) {
				$images0 = SMS_UPLOAD.$file[0];
				$imageCnt++;
			}
			if(isset($file[1]) && $file[1]) {
				$images1 = SMS_UPLOAD.$file[1];
				$imageCnt++;
			}
			if(isset($file[2]) && $file[2]) {
				$images2 = SMS_UPLOAD.$file[2];
				$imageCnt++;
			}

			if(!$smsGabia->send_mms($hp, $title, $message, $imageCnt, $images0, $images1, $images2, $date)) {
				return 0;  //발송 실패
			}
		}

		$result = $smsGabia->get_send_com_result(); // 발송 결과 성공시 1
		return $result;
	}
}
?>
