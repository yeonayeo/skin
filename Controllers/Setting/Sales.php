<?php

namespace App\Controllers\Setting;
use App\Controllers\BaseController;

class Sales extends BaseController
{
	public function index()
	{
		$data				= $this->get_data();

		$salesModel	= new \App\Models\CalculatorSalesModel();
		$adminModel	= new \App\Models\CalculatorAdminModel();
		helper('calculator');

		$date = CURR_DATE;

		// 판매내역 정산
		$sales_list = $salesModel->getSalesList($date, $date);

		// 관리내역 정산
		$admin_list = $adminModel->getAdminList($date, $date);

		// 판매 합계
		$sum_list = getSumSales($date, $date);

		$res = [
			'sales_list'					=> $sales_list,
			'money_list'					=> $sum_list['money'],
			'card_list'						=> $sum_list['card'],
			'sum_list'						=> $sum_list['sum'],
			'total_sales_amount'	=> $salesModel->getSumAmount($date, $date),
			'admin_list'					=> $admin_list,
			'ticket_use_cnt'			=> number_format($adminModel->getSumType(1, $date, $date)),
			'direct_cnt'					=> number_format($adminModel->getSumType(2, $date, $date)),
			'add_admin_cnt'				=> number_format($adminModel->getSumType(3, $date, $date)),
			'total_admin_amount'	=> $adminModel->getSumAmount($date, $date),
			'start_date'					=> date("Y.m.d", CURR_TIME),
			'end_date'						=> date("Y.m.d", CURR_TIME)
		];

		$data = array_merge($data, $res);

		return view('/setting/sales/index', ['_RES' => $data]);
	}

	public function get_sales()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$start_date	= getParam("start_date", $this->request);
		$end_date		= getParam("end_date", $this->request);

		$salesModel	= new \App\Models\CalculatorSalesModel();
		helper('calculator');

		$start_date = ($start_date) ? dateFormat($start_date) : null;
		$end_date = ($end_date) ? dateFormat($end_date) : null;

		$sales_list = $salesModel->getSalesList($start_date, $end_date);

		// 합계
		$sum_list = getSumSales($start_date, $end_date);

		$result['data'] = [
			'sales_list'					=> $sales_list,
			'money_list'					=> $sum_list['money'],
			'card_list'						=> $sum_list['card'],
			'sum_list'						=> $sum_list['sum'],
			'total_sales_amount'	=> $salesModel->getSumAmount($start_date, $end_date)
		];

		responseOut($result);
	}

	public function get_admin()
	{
		$result			= $this->result;
		$resultErr	= $this->resultErr;
		$start_date	= getParam("start_date", $this->request);
		$end_date		= getParam("end_date", $this->request);

		$adminModel	= new \App\Models\CalculatorAdminModel();

		$start_date = ($start_date) ? dateFormat($start_date) : null;
		$end_date = ($end_date) ? dateFormat($end_date) : null;

		// 관리내역 정산
		$admin_list = $adminModel->getAdminList($start_date, $end_date);

		$result['data'] = [
			'admin_list'					=> $admin_list,
			'ticket_use_cnt'			=> number_format($adminModel->getSumType(1, $start_date, $end_date)),
			'direct_cnt'					=> number_format($adminModel->getSumType(2, $start_date, $end_date)),
			'add_admin_cnt'				=> number_format($adminModel->getSumType(3, $start_date, $end_date)),
			'total_admin_amount'	=> $adminModel->getSumAmount($start_date, $end_date)
		];

		responseOut($result);
	}

	public function excel_download_sales()
	{
		$data					= $this->get_data();
		$start_date		= getParam("start_date", $this->request);
		$end_date			= getParam("end_date", $this->request);

		$salesModel	= new \App\Models\CalculatorSalesModel();
		helper('calculator');
		helper('excel');


		$start_dt = ($start_date && $start_date!='null') ? dateFormat($start_date) : '';
		$end_dt = ($end_date && $end_date!='null') ? dateFormat($end_date) : '';

		$list = $salesModel->excelSalesList($start_dt, $end_dt);
		$sum = getSumSales($start_dt, $end_dt);
		$total_sum = $salesModel->getSumAmount($start_dt, $end_dt);

		downloadSales($list, $sum, $total_sum, $start_date, $end_date);
	}

	public function excel_download_admin()
	{
		$data					= $this->get_data();
		$start_date		= getParam("start_date", $this->request);
		$end_date			= getParam("end_date", $this->request);

		$adminModel	= new \App\Models\CalculatorAdminModel();
		helper('calculator');
		helper('excel');

		$start_dt = ($start_date && $start_date!='null') ? dateFormat($start_date) : '';
		$end_dt = ($end_date && $end_date!='null') ? dateFormat($end_date) : '';

		$count = [];
		$list = $adminModel->excelAdminList($start_dt, $end_dt);

		$count[0] = number_format($adminModel->getSumType(1, $start_dt, $end_dt));
		$count[1] = number_format($adminModel->getSumType(2, $start_dt, $end_dt));
		$count[2] = number_format($adminModel->getSumType(3, $start_dt, $end_dt));
		$total_amount = $adminModel->getSumAmount($start_dt, $end_dt);

		downloadAdmin($list, $count, $total_amount, $start_date, $end_date);
	}
}
