<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('phoneNumberToHyphenFormat')) {
	// 핸드폰번호 하이픈 포맷으로 변경 : ex) 01012345678 => 010-1234-5678
	function phoneNumberToHyphenFormat($tel) {
		$tel = str_replace("-", "", $tel);
		$tel = preg_replace("/[^0-9]/", "", $tel); // 숫자 이외 제거

		if(substr($tel,0,2) == '02') {
			return preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
		} else if (strlen($tel)=='8' && (substr($tel,0,2)=='15' || substr($tel,0,2)=='16' || substr($tel,0,2)=='18')) {
			// 지능망 번호이면
			return preg_replace("/([0-9]{4})([0-9]{4})$/", "\\1-\\2", $tel);
		}	else {
			return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
		}
	}
}

// 휴대폰 가운데번호 암호화
if (!function_exists('phoneNumberToPassword')) {
	// 핸드폰번호 하이픈 포맷으로 변경 : ex) 01012345678 => 010-****-5678
	function phoneNumberToPassword($tel) {
		$arr = explode("-", $tel);
		$arr[1] = "****";

		return implode($arr, "-");
	}
}

// 사업자번호 포맷
if (!function_exists('businessLicenseToHyphenFormat')) {
	function businessLicenseToHyphenFormat($businessLicense) {
		$result = substr($businessLicense, 0, 3).'-'.substr($businessLicense, 3, 2).'-'.substr($businessLicense, 5, 5);

		return $result;
	}
}

// 법인번호 포맷
if (!function_exists('corporateNumberToHyphenFormat')) {
	function corporateNumberToHyphenFormat($corporateNumber) {
		$result = substr($corporateNumber, 0, 6).'-'.substr($corporateNumber, 6, 7);

		return $result;
	}
}

// 생일 포맷
if (!function_exists('birthToHyphenToFormat')) {
	function birthToHyphenToFormat($birth) {
		$result = substr($birth, 0, 4).'.'.substr($birth, 4, 2).'.'.substr($birth, 6, 2);

		return $result;
	}
}

// Y/N -> true /false
if ( ! function_exists('ynToBool')) {
	function ynToBool($item) {
		return ($item == 'Y') ? true : false;
	}
}

// String true/false -> true /false
if ( ! function_exists('stringToBool')) {
	function stringToBool($item) {
		return ($item == 'true') ? true : false;
	}
}

// true / false --> Y/N
if ( ! function_exists('boolToYN')) {
	function boolToYN($item) {
		if($item && (strtoupper($item) == "true" || strtoupper($item) == "Y") ) {
			$item = "Y";
		} else {
			$item = "N";
		}

		return $item;
	}
}

// YYYY.MM.DD -> YYYY-MM-DD
if ( ! function_exists('dateFormat')) {
	function dateFormat($date) {
		$date_format = '';
		if($date) {
			$dt = str_replace('.', '', $date);
			$date_format = date("Y-m-d", strtotime($dt));
		}

		return $date_format;
	}
}

// 요일
if ( ! function_exists('weekText')) {
	function weekText($week) {
		$week_text = '';
		switch ($week) {
			case 0:
				$week_text = '일요일';
				break;
			case 1:
				$week_text = '월요일';
				break;
			case 2:
				$week_text = '화요일';
				break;
			case 3:
				$week_text = '수요일';
				break;
			case 4:
				$week_text = '목요일';
				break;
			case 5:
				$week_text = '금요일';
				break;
			case 6:
				$week_text = '토요일';
				break;
			default:
				// code...
				break;
		}

		return $week_text;
	}
}

// 요일2
if ( ! function_exists('weekText2')) {
	function weekText2($week) {
		$week_text = '';
		switch ($week) {
			case 0:
				$week_text = '일';
				break;
			case 1:
				$week_text = '월';
				break;
			case 2:
				$week_text = '화';
				break;
			case 3:
				$week_text = '수';
				break;
			case 4:
				$week_text = '목';
				break;
			case 5:
				$week_text = '금';
				break;
			case 6:
				$week_text = '토';
				break;
			default:
				// code...
				break;
		}

		return $week_text;
	}
}
