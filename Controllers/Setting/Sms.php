<?php

namespace App\Controllers\Setting;
use App\Controllers\BaseController;
use App\Libraries\Sms_gabia;

class Sms extends BaseController
{
	public function index()
	{
		$data				= $this->get_data();
		$result			= $this->result;
		$resultErr	= $this->resultErr;

		$templateModel = new \App\Models\SmsTemplateModel();
		$list = $templateModel->getTemplatelist();
		/* 220729 예약시 문자 즉시발송 */
		if(!empty($list)) {
			foreach($list AS $key => &$item) {
				$item['is_bottom'] = true;
				if($key==0) $item['is_bottom'] = false;
			}
		}

		$data['list'] = $list;
		return view('/setting/sms/index', ['_RES' => $data]);
	}

	public function action_update()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$id					= getParam("id", $this->request);
		$type				= getParam("type", $this->request);
		$title			= getParam("title", $this->request);
		$msg				= getParam("msg", $this->request);

		if(!$id || !$type) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수 인자가 없습니다.';

			responseOutExit($resultErr);
		}

		$templateModel = new \App\Models\SmsTemplateModel();

		if($type=='title') {
			$templateModel->_update($id, ['title' => $title]);
		} else {
			$templateModel->_update($id, ['msg' => $msg]);
		}

		responseOut($result);
	}

	// [popup] 문자 예약 발송
	public function popup_booking_send()
	{
		$data				= $this->get_data();
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$id					= getParam("id", $this->request);

		$templateModel = new \App\Models\SmsTemplateModel();
		$info = $templateModel->getTemplateInfo($id);
		$info['is_reserved'] = ynToBool($info['is_reserved']);

		$data = array_merge($data, $info);

		return view('/setting/sms/popup_booking_send', ['_RES' => $data]);
	}

	// 문자 예약 발송 action
	public function action_booking_send()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$id						= getParam("id", $this->request);
		$is_reserved	= getParam("is_reserved", $this->request);
		$send_type_cd	= getParam("send_type_cd", $this->request);
		$send_time		= getParam("send_time", $this->request);

		if(!$id || !$send_type_cd || !$send_time) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수 인자가 없습니다.';

			responseOutExit($resultErr);
		}

		$is_reserved = stringToBool($is_reserved);
		$templateModel = new \App\Models\SmsTemplateModel();
		$templateModel->_update($id, ['is_reserved' => ($is_reserved) ? 'Y' : 'N', 'send_type' => $send_type_cd, 'send_time' => $send_time]);

		responseOut($result);
	}

	// [popup] 문자 즉시 발송
	public function popup_now_send()
	{
		$data				= $this->get_data();
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$id					= getParam("id", $this->request);

		$templateModel = new \App\Models\SmsTemplateModel();
		$info = $templateModel->getTemplateInfo($id);
		$info['is_all'] = ynToBool($info['is_all']);

		$data = array_merge($data, $info);

		return view('/setting/sms/popup_now_send', ['_RES' => $data]);
	}

	// 문자 즉시 발송 action
	public function action_now_send()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$id					= getParam("id", $this->request);
		$is_all			= getParam("is_all", $this->request);
		$client_id	= getParam("client_id", $this->request);

		if(!$id || (!$is_all && !$client_id)) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수 인자가 없습니다.';

			responseOutExit($resultErr);
		}

		$clientModel		= new \App\Models\ClientModel();
		$smsModel				= new \App\Models\SmsLogModel();
		$templateModel	= new \App\Models\SmsTemplateModel();

		$templateInfo = $templateModel->getTemplateInfo($id);
		$msg = str_replace("\n", "\r\n", $templateInfo['msg']);

		if(stringToBool($is_all)) {
			$smsModel->setAllNowSmsData($id, $msg);
		} else {
			$client_hp = $clientModel->getClientHp($client_id);
			$smsModel->_save([	'template_id'				=> $id,
													'msg'								=> $msg,
													'client_id'					=> $client_id,
													'hp'								=> $client_hp
			]);
			$smsID = $smsModel->insertID();
			if($smsID) {
				$smsModel->_update($smsID, [], 'reserve');
			}
		}

		responseOut($result);
	}

}
