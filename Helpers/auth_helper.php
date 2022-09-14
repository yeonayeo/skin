<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// 로그인 유무 체크

/*
/ 로그인 해야 하는곳인지 아닌지 검사
*/
if(! function_exists('check_login_location')){
	function check_login_location($uri){
		$page = array("auth","home","privacy","terms_of_use","api","pds/download","msgcenter");
		foreach($page as $key => $value) {
			if(strpos($uri,DOMAIN."/$value") > -1 ){
				return false;
			}
		}
		return true;
	}
}

if (!function_exists('auto_login')) {
	function auto_login($id){
		if($id!=""){
			$email    = "";
			$bEmail   = false;
			$img ="/assets/images/profile_default.svg";
			$userInfo = findInforBy("simple",["user_id"=>$id]);

			if(isset($userInfo) && count($userInfo)>0){

				foreach ($userInfo as $ky => $v) {
					$$ky = $v;
				}

				$orign_id = $id;

				$hp_link=true;
				 if($user_hp==""){
					  $hp_link=false;
				 }





				if($app_token=="" && isset($_COOKIE["uid"])&& isset($_COOKIE["mac_type"])){
					update_app_token($user_id,$_COOKIE["uid"],$_COOKIE["mac_type"]);
				}
				if(isset($email) && $email!="")$email = getDecrypt($email);

				if($email==""){
					$bEmail = true;
				}

				if(isset($profile_img) && $profile_img!=""){
					$img =  $profile_img;
				}

				$member_info=[

					"user_id"=>$user_id,
					"id"=>$orign_id,
					"name"=>$user_name,
					"profile_img"=>$img,
					"email"=>$email,
					"bEmail"=>$bEmail,
				];


				setSession('userID', $user_id);
				setSession('member_info', $member_info);
			}
		}
	}
}




if (!function_exists('update_user_pwd')) {
	function update_user_pwd($param){
		$msg	= new \App\Models\UserModel();
		$result = $msg->update_user_pwd($param);
		return $result;
	}
}

if (!function_exists('check_slient_otp')) {
	function check_slient_otp($param){
		$msg	= new \App\Models\AuthModel();
		$result = $msg->check_slient_otp($param);
		return $result;

	}
}


if (!function_exists('check_otp')) {
	function check_otp($param){
		$msg	= new \App\Models\AuthModel();
		$result = $msg->check_otp($param);
		return $result;
	}
}

if(!function_exists('set_login')){
	function set_login($param){
	}
}



if (!function_exists('mount_nbh_user')) {
	function mount_nbh_user($param){
		$msg	= new \App\Models\NbhUserModel();
		$result = $msg->mount_nbh_user($param);
		return $result;
	}
}



if (!function_exists('can_send_otp')) {
	function can_send_otp($param){
		$msg	= new \App\Models\AuthModel();
		$result = $msg->can_send_otp($param);
		return $result;
	}
}



if (!function_exists('insert_otp')) {
	function insert_otp($param){
		$msg	= new \App\Models\AuthModel();
		$result = $msg->insert_otp($param);
	}
}





if(! function_exists('isLogin')){
	function isLogin(){

		$userID = getUserID();

		if($userID!="") {
			$userModel	= new \App\Models\UserModel();
			$status			= $userModel->checkStatus($userID);

			if($status=="0"){
				setSession('userID',"");
			}

			return $status;
		}

		return 0;
	}
}


if(! function_exists('Login')){
	function Login($param){
		// $db = db_connect('allround');
		// $param["db"] =$db;
		$userInfo = "";
		$userModel = new \App\Models\UserModel();
		$userInfo = $userModel->login($param);
		return $userInfo;

	}
}
?>
