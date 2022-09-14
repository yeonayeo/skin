<?php
namespace App\Controllers;
use App\Libraries\Sms_gabia;

class Home extends BaseController
{

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
		$res  = $this->get_res();
		$data = $this->get_data();
	}

}
