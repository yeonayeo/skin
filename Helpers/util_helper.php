<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	// get post 받기
	if ( ! function_exists('getParam')) {
		function getParam($key, $request) {
			return $request->getPostGet($key);
		}
	}

	if ( ! function_exists('responseOut')) {
		function responseOut($result) {
			echo json_encode($result);
			exit;
		}
	}

	if ( ! function_exists('responseOutExit')) {
		function responseOutExit($result) {
			if(isset($result['err_act']) && $result['err_act']=='alert') { // alert 띄우기
				echo "<script>alert('".$result['err_msg']."');</script>";
				exit;
			} else if(isset($result['err_act']) && $result['err_act']=='redirect') { // 페이지이동
				echo "<script>location.href='".$result['err_url']."';</script>";
				exit;
			} else if(isset($result['err_act']) && $result['err_act']=='alert_redirect') {
				echo "<script>alert('".$result['err_msg']."'); location.href='".$result['err_url']."';</script>";
				exit;
			} else if(isset($result['err_act']) && $result['err_act']=='popup_close_redirect') {
				if($result['err_msg']) {
					echo "<script>alert('".$result['err_msg']."'); opener.location.href='".$result['err_url']."'; window.close();</script>";
				} else {
					echo "<script>opener.location.href='".$result['err_url']."'; window.close();</script>";
				}
				exit;
			} else {
				echo json_encode($result);
				exit;
			}
		}
	}

	if ( ! function_exists('getSerialCode')) {
		function getSerialCode() {
			$serial = CURR_DATETIME_STRING . "" . random_int(10000, 99999);
			return $serial;
		}
	}

	if ( ! function_exists('userIp')) {
		function userIp() {
			$ipaddress = '';
			if($_SERVER['HTTP_X_FORWARDED_FOR']) {
				$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else if($_SERVER['HTTP_X_FORWARDED']) {
				$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
			} else if($_SERVER['HTTP_FORWARDED_FOR']) {
				$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
			} else if($_SERVER['HTTP_FORWARDED']) {
				$ipaddress = $_SERVER['HTTP_FORWARDED'];
			} else if($_SERVER['REMOTE_ADDR']) {
				$ipaddress = $_SERVER['REMOTE_ADDR'];
			} else {
				$ipaddress = 'UNKNOWN';
			}
			return $ipaddress;
		}
	}

	// 암호화
	if ( ! function_exists('getEncrypt')) {
		function getEncrypt($item) {
			$encrypter	= \Config\Services::encrypter();

			return $encrypter->encrypt($item);
		}
	}

	// 복호화
	if ( ! function_exists('getDecrypt')) {
		function getDecrypt($item) {
			$encrypter	= \Config\Services::encrypter();

			return $encrypter->decrypt($item);
		}
	}

	// 세션 값 저장
	if ( ! function_exists('setSession')) {
		function setSession($key, $value) {
			$session = \Config\Services::session();

			$session->set($key, $value);

			return true;
		}
	}

	// 세션 만료시간 변경
	if ( ! function_exists('expireSession')) {
		function expireSession($key, $expire=3600) {
			$session = \Config\Services::session();
			$session->markAsTempdata($key, $expire);

			return true;
		}
	}

	// 세션 값 가져오기
	if ( ! function_exists('getSession')) {
		function getSession($key) {
			$session = \Config\Services::session();

			return $session->get($key);
		}
	}


	// 세션삭제
	if ( ! function_exists('removeSession')) {
		function removeSession($key) {
			$session = \Config\Services::session();
			$session->remove($key);

			return true;
		}
	}

	// HTML 중에 공격 태그 삭제
	if ( ! function_exists('avoidCrack')) {
		function avoidCrack($str) {
			$str = str_replace("<script.*</script>", "", $str);
			$str = str_replace("<script.*>", "", $str);
			$str = str_replace("<iframe.*>", "", $str);
			$str = str_replace("<param.*>", "", $str);
			$str = str_replace("<plaintext.*>", "", $str);
			$str = str_replace("<xml.*>", "", $str);
			$str = str_replace("<base.*>", "", $str);
			$str = str_replace("<meta.*>", "", $str);
			$str = str_replace("<applet.*>", "", $str);
			$str = str_replace("c\|/con/con/", "", $str);

			return $str;
		}
	}

	// 페이지 범위
	if ( ! function_exists('rangePage')) {
		function rangePage($page, $totalPage) {
			$pageRange = [];
			$startPage = ( ( (int)( ($page - 1 ) / 5 ) ) * 5 ) + 1;
			$endPage = $startPage + 5 - 1;
			for($i=$startPage; $i<=$endPage; $i++) {
				if($i<=$totalPage) {
					$pageRange[] = $i;
				}
			}

			return $pageRange;
		}
	}

	// 이전 페이지
	if ( ! function_exists('prePage')) {
		function prevPage($page) {
			$startPage = ( ( (int)( ($page - 1 ) / 5 ) ) * 5 ) + 1;
			$prePage = ($startPage>5) ? ($startPage-1) : 1;

			return $prePage;
		}
	}

	// 다음 페이지
	if ( ! function_exists('nextPage')) {
		function nextPage($page, $totalPage) {
			$startPage = ( ( (int)( ($page - 1 ) / 5 ) ) * 5 ) + 1;
			$endPage = ( $startPage + 5 - 1 ) + 1;
			$endPage = ($endPage > $totalPage) ? $totalPage : $endPage;

			$nextPage = ($totalPage>5) ? $endPage : $totalPage;

			return $nextPage;
		}
	}

	// 캘린더
	if ( ! function_exists('getCalendar')) {
		function getCalendar($year=CURR_YEAR, $month=CURR_MONTH_NO) {
			$year				= ($year) ? $year : CURR_YEAR;
			$month			= ($month) ? $month : CURR_MONTH_NO;
			$firstDate	= mktime(0, 0, 0, $month, 1, $year); // 이번달 첫 날
			$lastDate		= mktime(0, 0, 0, $month, date('t', $firstDate), $year); // 이번달 마지막 날

			$prevM			= date('m', strtotime('-1 month', $firstDate));
			$prevY			= date('Y', strtotime('-1 month', $firstDate));
			$nextM			= date('m', strtotime('+1 month', $firstDate));
			$nextY			= date('Y', strtotime('+1 month', $firstDate));
			$prevLastDt	= date('t', strtotime('-1 month', $firstDate)); // 지난달 마지막 날
			$firstW			= date('w', $firstDate); // 시작 요일 : 0 ~ 6, 일 ~ 토
			$lastW			= date('w', $lastDate); // 마지막 요일
			$firstDt		= date('d', $firstDate);	// 이달의 시작일
			$lastDt			= date('d', $lastDate);	// 이달의 마지막일
			$is_today		= false;

			$calendarArr	= $weekList = $dateList = $scheduleList = [];
			$weekKey = 0;
			$weekCnt = 1;
			for ($i = -$firstW + 1; $i < $lastDt + (7 - $lastW); $i++) {
				$day = $i;
				$class = '';
				if ($i < 1) {
					// 지난달
					$day		= $prevLastDt + $i;
					$ymd		= $prevY . $prevM . ($prevLastDt + $i);
					$class	.= 'disabled';
					$scheduleDt = $prevY.'-'.$prevM.'-'.($prevLastDt + $i);
					array_push($calendarArr, $ymd);
				} else if ($i >= 1 && $i <= $lastDt) {
					// 이번달
					$day	= $i;
					$ymd	= $year . $month . str_pad($i, 2, '0', STR_PAD_LEFT);
					$scheduleDt = $year.'-'.$month.'-'.str_pad($i, 2, '0', STR_PAD_LEFT);
					array_push($calendarArr, $ymd);

					if(CURR_DATE == $scheduleDt) {
						$class	.= 'today';
						$is_today = true;
					}
				} else {
					// 다음달
					$day 		= $i - $lastDt;
					$ymd		= $nextY . $nextM . str_pad(($i - $lastDt), 2, '0', STR_PAD_LEFT);
					$class	.= 'disabled';
					$scheduleDt = $nextY.'-'.$nextM.'-'.str_pad($day, 2, '0', STR_PAD_LEFT);
					array_push($calendarArr, $ymd);
				}
				$date = [
					'date'			=> $day,
					'ymd'				=> $ymd,
					'class'			=> $class,
					'is_today'	=> $is_today
				];
				$dateList[]	= $date;
				if($weekKey == 6) {
					array_push($weekList, ['date_list'	=> $dateList]);
					$weekKey = 0;
					$dateList = [];
					$weekCnt++;
				} else {
					$weekKey++;
				}
			}

			$calnderFirstDt	= date('Y-m-d', strtotime($calendarArr[0]));
			$calnderLastDt	= date('Y-m-d', strtotime($calendarArr[count($calendarArr) - 1]));

			$data = [
				'year'							=> $year,
				'month'							=> $month,
				'month_txt'					=> ($month>=10) ? $month : str_replace('0', '', $month),
				'prev_y'						=> $prevY,
				'prev_m'						=> $prevM,
				'next_y'						=> $nextY,
				'next_m'						=> $nextM,
				'first_dt'					=> $firstDt,
				'last_dt'						=> $lastDt,
				'prev_last_dt'			=> $prevLastDt,
				'first_week'				=> $firstW,
				'last_week'					=> $lastW,
				'calnder_first_dt'	=> $calnderFirstDt,
				'calnder_last_dt'		=> $calnderLastDt,
				'calendar'					=> $weekList
			];

			return $data;
		}
	}
