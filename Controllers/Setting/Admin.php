<?php

namespace App\Controllers\Setting;
use App\Controllers\BaseController;

class Admin extends BaseController
{
	public function index()
	{
		$data				= $this->get_data();

		$usersModel = new \App\Models\UsersModel();
		$noticeModel = new \App\Models\NoticeModel();

		$list = $usersModel->getUserList();
		if(!empty($list)) {
			foreach($list AS $key => &$user) {
				$user['no'] = $key+1;
				$user['hp'] = phoneNumberToHyphenFormat(getDecrypt($user['hp']));
				$user['is_super'] = ynToBool($user['is_super']);
				$user['note'] = ($user['note']) ? $user['note'] : '-';
			}
		}

		// 공지사항
		$notice = $noticeModel->getNoticeInfo();
		if(empty($notice)) {
			$notice = [
				'contents'		=> '',
				'notice_date'	=> CURR_DATE
			];
		}

		$data['admin_list'] = $list;
		$data['calendar_info'] = getCalendar();
		$data['ymd'] = CURR_DATE_STRING;
		$data['notice'] = $notice;

		return view('/setting/admin/index', ['_RES' => $data]);
	}

	// [popup] 관리자 추가
	public function popup_regist()
	{
		$data	= $this->get_data();
		$data['login_cd'] = createLoginCD();

		return view('/setting/admin/popup_regist', ['_RES' => $data]);
	}

	// 관리자 추가 action
	public function action_regist() {
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$name					= getParam("name", $this->request);
		$position			= getParam("position", $this->request);
		$hp						= getParam("hp", $this->request);
		$login_cd			= getParam("login_cd", $this->request);
		$note					= getParam("note", $this->request);
		$work_form		= getParam("work_form", $this->request);
		$work_time		= getParam("work_time", $this->request);
		$pay_form_cd	= getParam("pay_form_cd", $this->request);
		$pay_money		= getParam("pay_money", $this->request);

		if(!$name || !$position || !$hp || !$login_cd) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다. 입력 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$usersModel = new \App\Models\UsersModel();
		$usersModel->_save([
			'login_cd'		=> $login_cd,
			'name'				=> $name,
			'position'		=> $position,
			'hp'					=> getEncrypt($hp),
			'note'				=> ($note) ? avoidCrack($note) : null,
			'status'			=> 1,
			'work_form'		=> ($work_form) ? $work_form : null,
			'work_time'		=> ($work_time) ? $work_time : null,
			'pay_form'		=> ($pay_form_cd) ? $pay_form_cd : null,
			'pay_money'		=> ($pay_money) ? $pay_money : null
		]);

		responseOut($result);
	}

	// [popup] 관리자 상세
	public function popup_detail()
	{
		$data				= $this->get_data();
		$resultErr	= $this->resultErr;
		$id					= getParam("id", $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '관리자를 선택해주세요.';

			return view('/error', ['_RES' => $resultErr]);
		}

		$usersModel = new \App\Models\UsersModel();
		$info = $usersModel->getUserInfo($id);
		$info['hp'] = phoneNumberToHyphenFormat(getDecrypt($info['hp']));
		$info['note'] = ($info['note']) ? $info['note'] : '-';
		$info['work_form'] = ($info['work_form']) ? $info['work_form'] : '-';
		$info['work_time'] = ($info['work_time']) ? $info['work_time'] : '-';
		$info['pay_form'] = ($info['pay_form']) ? $info['pay_form'] : '-';
		$info['pay_money'] = ($info['pay_money']) ? number_format($info['pay_money']).'원' : '';
		$info['is_super'] = ynToBool($info['is_super']);


		$data = array_merge($data, $info);

		return view('/setting/admin/popup_detail', ['_RES' => $data]);
	}

	// [popup] 관리자 수정
	public function popup_update()
	{
		$data				= $this->get_data();
		$resultErr	= $this->resultErr;
		$id					= getParam("id", $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '관리자를 선택해주세요.';

			return view('/error', ['_RES' => $resultErr]);
		}

		$usersModel = new \App\Models\UsersModel();
		$info = $usersModel->getUserInfo($id);
		$info['hp'] = getDecrypt($info['hp']);
		$info['is_super'] = ynToBool($info['is_super']);

		$data = array_merge($data, $info);

		return view('/setting/admin/popup_update', ['_RES' => $data]);
	}

	// 관리자 수정 action
	public function action_update() {
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$id						= getParam("id", $this->request);
		$name					= getParam("name", $this->request);
		$position			= getParam("position", $this->request);
		$hp						= getParam("hp", $this->request);
		$note					= getParam("note", $this->request);
		$work_form		= getParam("work_form", $this->request);
		$work_time		= getParam("work_time", $this->request);
		$pay_form_cd	= getParam("pay_form_cd", $this->request);
		$pay_money		= getParam("pay_money", $this->request);


		if(!$id || !$name || !$position || !$hp) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다. 입력 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$usersModel = new \App\Models\UsersModel();
		$usersModel->_update($id, [
			'name'				=> $name,
			'position'		=> $position,
			'hp'					=> getEncrypt($hp),
			'note'				=> ($note) ? avoidCrack($note) : null,
			'work_form'		=> ($work_form) ? $work_form : null,
			'work_time'		=> ($work_time) ? $work_time : null,
			'pay_form'		=> ($pay_form_cd) ? $pay_form_cd : null,
			'pay_money'		=> ($pay_money) ? $pay_money : null
		]);

		responseOut($result);
	}

	// 관리자 수정 action
	public function action_delete() {
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$id					= getParam("id", $this->request);

		if(!$id) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다. 입력 후 다시 시도해주세요.';

			responseOutExit($resultErr);
		}

		$usersModel = new \App\Models\UsersModel();
		$usersModel->_delete($id);

		responseOut($result);
	}

	// [popup] 비밀번호 변경
	public function popup_update_password()
	{
		$data	= $this->get_data();

		return view('/setting/admin/popup_update_password', ['_RES' => $data]);
	}

	// 비밀번호 변경 action
	public function action_update_password()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$pw					= getParam("pw", $this->request);
		$new_pw			= getParam("new_pw", $this->request);

		if(!$pw || !$new_pw) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '필수인자가 누락되었습니다. 입력 후 다시 시도해주세요.';

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

		// 비밀번호 변경
		$pwModel->changePassword(); // 기존 비밀번호 사용X 처리
		$pwModel->_save(['pw' => md5($new_pw), 'is_use'	=> 'Y']);

		responseOut($result);
	}

	public function action_notice_update()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$id						= getParam("id", $this->request);
		$contents			= getParam("contents", $this->request);
		$notice_date	= getParam("notice_date", $this->request);

		if(!$notice_date) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '공지일을 선택해 주세요.';

			responseOutExit($resultErr);
		}

		$noticeModel = new \App\Models\NoticeModel();
		$notice_date = date("Y-m-d", strtotime($notice_date));
		$contents = ($contents) ? avoidCrack($contents) : null;

		if($id) {
			$noticeModel->_update($id, ['contents' => $contents]);
		} else {
			$noticeModel->_save(['contents' => $contents, 'notice_date'	=> $notice_date]);
		}

		responseOut($result);
	}

	public function get_notice_info()
	{
		$result				= $this->result;
		$resultErr		= $this->resultErr;
		$notice_date	= getParam("notice_date", $this->request);

		if(!$notice_date) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '공지일을 선택해 주세요.';

			responseOutExit($resultErr);
		}

		$noticeModel = new \App\Models\NoticeModel();
		$notice_date = date("Y-m-d", strtotime($notice_date));

		// 공지사항
		$notice = $noticeModel->getNoticeInfo($notice_date);
		if(empty($notice)) {
			$notice = [
				'contents'		=> '',
				'notice_date'	=> $notice_date
			];
		}

		$result['data'] = $notice;
		responseOut($result);
	}
}
