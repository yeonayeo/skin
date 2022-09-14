<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('getStartTimeList')) {
	function getStartTimeList() {
		$scheduleModel = new \App\Models\ScheduleModel();
		$list = $scheduleModel->getStartTimeList();
		foreach($list AS $key => &$time) {
			$time['is_disabled'] = false;
		}

		return $list;
	}
}

if (!function_exists('getEndTimeList')) {
	function getEndTimeList($start_time='') {
		$scheduleModel = new \App\Models\ScheduleModel();
		$list = $scheduleModel->getEndTimeList();
		foreach($list AS $key => &$time) {
			$time['is_disabled'] = false;
		}

		 return $list;
	}
}
