<?php
namespace App\Controllers;

class Auth extends BaseController
{

	public function index() { }

	// 로그인
	public function login() {
		$data	= $this->get_data();

		return view('/auth/login', ['_RES' => $data]);
	}

	public function action_login() {
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$login_cd		= getParam("login_cd", $this->request);

		if(!$login_cd) {
			$resultErr['res_cd']	= '002';
			$resultErr['err_msg']	= '로그인코드를 입력해주세요.';

			responseOutExit($resultErr);
		}

		$usersModel = new \App\Models\UsersModel();

		// 로그인
		$userInfo = $usersModel->login($login_cd);
		if(empty($userInfo)) {
			$resultErr['res_cd']	= '004';
			$resultErr['err_msg']	= '유효하지 않은 코드입니다.';

			responseOutExit($resultErr);
		}

		// 로그인 성공
		$ssToken = md5(getSerialCode());

		$usersModel->_update($userInfo['id'], ["ss_token" => $ssToken]);

		// 로그인 id 및 토큰 저장
		setSession('userID', getEncrypt($userInfo['id']));
		setSession('ss_token', $ssToken);
		expireSession('userID', 31536000);
		expireSession('ss_token', 31536000);

		responseOut($result);
	}

	/**
	*	로그아웃 action
	*/
	public function action_logout()
	{
		$result			= $this->result;

		removeSession('userID');
		removeSession('ss_token');

		responseOut($result);
	}
}
