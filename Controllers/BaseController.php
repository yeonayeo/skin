<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\Seed;
use App\Libraries\Crypto;

/**
* Class BaseController
*
* BaseController provides a convenient place for loading components
* and performing functions that are needed by all your controllers.
* Extend this class in any new controllers:
*     class Home extends BaseController
*
* For security be sure to declare any new methods as protected or private.
*/

class BaseController extends Controller
{
	/**
	* An array of helpers to be loaded automatically upon
	* class instantiation. These helpers will be available
	* to all other controllers that extend BaseController.
	*
	* @var array
	*/
	protected $is_mobile;
	protected $pnx;
	protected $seed;
	protected $crypto;
	protected $helpers = ['util','user','format'];
	protected $agent;
	private $data;
	private $res;
	/**
	* Constructor.
	*
	* @param RequestInterface  $request
	* @param ResponseInterface $response
	* @param LoggerInterface   $logger
	*/

	public function initCrypto(){

	}

	public function get_data(){
		return $this->data;
	}

	public function get_res(){
		return $this->res;
	}

	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		$agent			=  $this->request->getUserAgent();
		$data				= [];
		// 결과
		$result			=	['res_cd' => 'OK', 'res_msg' => '', 'res_url' => ''];
		$resultErr	= ['res_cd' => '', 'err_cd' => '', 'err_msg' => '', 'err_url' => '', 'err_act' => 'none'];

		// url
		$url			= mb_strtolower($this->request->uri);
		$prev_url	= (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : false;

		$chkLogin	= checkLogin();
		$isSuper	= false;

		if($chkLogin) {
			$data['user_name'] = $chkLogin;
			$isSuper = getIsSuper(getUserID());
			// 로그인 유저, 로그인페이지 접근 금지
			if(strpos($url, 'auth')!== false && strpos($url, 'action_logout')=== false ) {
				header('Location: '.SITEURL);
			}
			// 슈퍼관리자 이외에 관리자메뉴 접근 금지
			if(strpos($url, 'setting')!== false && !$isSuper) {
				// header('Location: '.SITEURL);
			}
		} else {
			if(strpos($url, 'popup')!==false) {
				echo "<script>location.href='/auth/login';</script>";
			} else {
				// 로그인 페이지만 접근 가능
				if(strpos($url, 'auth')===false && strpos($url, 'msgcenter')===false) {
					header('Location: '.SITEURL.'auth/login');
				}
			}
		}

		$data['is_super'] = $isSuper;

		// 서브 메뉴 선택유무 --> 이용권 설정, 화장품 관리, 문자 발송, 관리자 메뉴, 매출 현황
		$data['sub_selected'] = [false, false, false, false, false];
		if(strpos($url, 'setting/ticket')!== false)		$data['sub_selected'][0] = true;
		if(strpos($url, 'setting/cosmetic')!== false)	$data['sub_selected'][1] = true;
		if(strpos($url, 'setting/stuff')!== false)		$data['sub_selected'][2] = true;
		if(strpos($url, 'setting/sms')!== false)			$data['sub_selected'][3] = true;
		if(strpos($url, 'setting/admin')!== false)		$data['sub_selected'][4] = true;
		if(strpos($url, 'setting/sales')!== false)		$data['sub_selected'][5] = true;

		$this->data				= $data;
		$this->result			= $result;
		$this->resultErr	= $resultErr;
	}

	public function __construct()
	{
		helper($this->helpers);
	}

}
