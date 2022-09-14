<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 로그인코드 생성
if (!function_exists('createLoginCD')) {
	function createLoginCD($recently_cd=null) {
		// 마지막 로그인코드 가져오기
		$usersModel		= new \App\Models\UsersModel();
		if(!$recently_cd) {
			$recently_cd	= $usersModel->getRecentlyCD();
		}

		// 로그인코드 생성
		$alphabet	= substr($recently_cd, 0, 1);
		$number		= substr($recently_cd, 1, 4);
		if($number<9999) {
			$cd_num = sprintf('%04d', $number+1);
		} else {
			$alphabet++;
			$cd_num = '0001';
		}
		$login_cd = $alphabet.$cd_num;

		return $login_cd;
	}
}

if ( ! function_exists('getUserID')) {
	function getUserID() {
		$session = \Config\Services::session();
		$userID = $session->get('userID');

		if($userID) {
			return getDecrypt($userID);
		}

		return false;
	}
}

if ( ! function_exists('getToken')) {
	function getToken() {
		$session = \Config\Services::session();

		return $session->get('ss_token');
	}
}

// 로그인 유무 체크
if ( ! function_exists('checkLogin')) {
	function checkLogin() {
		$userID		= getUserID();
		$ssToken	= getToken();

		if($userID && $ssToken) {
			$usersModel = new \App\Models\UsersModel();
			$chkUser = $usersModel->checkUser($userID, $ssToken);

			return $chkUser;
		} else {
			return false;
		}
	}
}

// 수퍼 관리자 유무 가져오기
if ( ! function_exists('getIsSuper')) {
	function getIsSuper($userID) {
		$isSuper = false;

		$usersModel = new \App\Models\UsersModel();
		$super = $usersModel->getIsSuper($userID);
		if($super=='Y') $isSuper = true;

		return $isSuper;
	}
}
