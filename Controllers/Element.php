<?php
namespace App\Controllers;

class Element extends BaseController
{
	public function index()
	{
		$data	= $this->get_data();

		return view('/element', ['_RES' => $data]);
	}

	public function error()
	{
		$data	= $this->get_data();

		return view('/error', ['_RES' => $data]);
	}
}
