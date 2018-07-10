<?php
namespace Vendor;

class SmsApi {

	var $yunxin_config=array();

	function __construct(){

		//云信接口URL, 请求地址请参考云信互联云通讯自助通平台查看或者询问您的商务负责人获取
		$this->yunxin_config['api_send_url'] = 'http://47.100.188.160:7862/sms';

		//云信账号 替换成你自己的账号
		 $this->yunxin_config['api_account']	= '10690136';

		//云信密码 替换成你自己的密码
		 $this->yunxin_config['api_password']	= '3c4Ytm';
	}
	/**
	 * 发送短信
	 *
	 * @param string $mobile 		手机号码
	 * @param string $msg 			短信内容
	 * @param string $needstatus 	是否需要状态报告
	 */
	public function sendSMS( $mobile, $msg, $extno='',$rt='json' ) {
		//云信接口参数
		$postArr = array (
			'action' =>'send',//短信发送发送
			'account'  =>  $this->yunxin_config['api_account'],
			'password' => $this->yunxin_config['api_password'],
			'mobile' => $mobile,
			'content' => $msg,
			'extno'	 => $extno,
			'rt' => $rt
        );
		$result = $this->curlPost( $this->yunxin_config['api_send_url'] , $postArr);
		return $result;
	}




	/**
	 * 查询额度
	 *
	 *  查询地址
	 */
	public function queryBalance($method) {

		//查询参数
		$postArr = array (
			'method' => $method,//查看用户账号信息
		    'username' => $this->yunxin_config['api_account'],
		    'password' => $this->yunxin_config['api_password']
		);

		$result = $this->curlPost($this->yunxin_config['api_send_url'], $postArr);
		return $result;
	}

	/**
	 * 通过CURL发送HTTP请求
	 * @param string $url  //请求URL
	 * @param array $postFields //请求参数
	 * @return mixed
	 */
	private function curlPost($url,$postFields){

		$postFields = http_build_query($postFields);

		$ch = curl_init ();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,60);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
		$ret = curl_exec ( $ch );

        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
		curl_close ( $ch );
		return $result;
	}

}