<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// 판매내역 합산내역
if (!function_exists('getSumSales')) {
	function getSumSales($start_date, $end_date) {
		$salesModel	= new \App\Models\CalculatorSalesModel();

		// type : 1.이용권, 2.화장품, 3.직접입력, 4.추가관리
		$money = $card = $sum = [];
		// 현금결제
		for($type=1; $type<5; $type++) {
			$amount = $salesModel->getSumSales($start_date, $end_date, $type, 'money');
			$money[] = number_format($amount);
		}
		// 카드결제
		for($type=1; $type<5; $type++) {
			$amount = $salesModel->getSumSales($start_date, $end_date, $type, 'card');
			$card[] = number_format($amount);
		}
		// 합계 (현금 + 카드)
		for($type=1; $type<5; $type++) {
			$amount = $salesModel->getSumSales($start_date, $end_date, $type);
			$sum[] = number_format($amount);
		}

		$sumList = ['money' => $money, 'card' => $card, 'sum' => $sum];

		return $sumList;
	}
}
