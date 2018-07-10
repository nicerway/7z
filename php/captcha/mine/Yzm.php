<?php

class Yzm
{

	private $debug = true;

	private static $var = 'yzm';

	private static $var_expire = 'yzm_expire';

	public $form = null;



	public function __construct()
	{

		// $this->debug = config('app_debug');

		/*#验证码未过期.不可再次发送

		if ( $this->expired() ) {

			returnJson(-100, config('tips.yzm_expired'));

		}*/

	}

	public function sanbox()
    {
        $this->debug = true;
    }



	public function demo()
	{

		$verify = [

			'telphone' => ['required', config('tips.phone')],
			'password' => ['password', config('tips.password')],

		];

		$form = new VerifyForm($verify, 'post');

		#表单信息不完整

		if ($form->result()) {

			returnJson(-100, $form->error, $form->field);

		}

		$telphone = $form->telphone;
		#验证码未过期.不可再次发送
		if ( $this->expired($telphone) ) {

			returnJson(-100, config('tips.yzm_expired'));

		}

		#邮件->发送验证

		$code = $this->setCode($telphone);

		if (Send::sms($form->phone, $code)) {

			returnJson(-100, config('tips.sms_success') . ($this->debug ? ',验证码为:' . $code:'') );

		} else {

			returnJson(-100, config('tips.sms_failed'));

		}

	}


	public function expired($id)
	{

		if ($this->debug) {

			return true;

		} else {

			if (isset($_SESSION[self::$var][$id])) {

				return time() - $_SESSION[self::$var][$id][self::$var_expire] < 0;

			} else {

				return false;

			}

		}

	}



	public function setCode($id, $time = 63)
	{

		if (!isset($_SESSION[self::$var]) || is_array($_SESSION[self::$var])) {

			$_SESSION[self::$var] = [];

		}

		$_SESSION[self::$var][$id][self::$var_expire] = time() + $time;

		$code = $_SESSION[self::$var][$id][self::$var] = mt_rand(100000,999999);

		return $code;

	}



	public function clear($id)
	{

		if (isset($_SESSION[self::$var][$id])) {

			$_SESSION[self::$var][$id][self::$var_expire] = time();

			$_SESSION[self::$var][$id][self::$var] = '';

		}

	}



	/**
	 * Prevents Cross-Site Scripting Forgery
	 * @return boolean
	 */

	public function verify_error($id, $captcha)
	{

		if ( isset($_SESSION[self::$var][$id]) ) {

			if( strtolower($captcha) == strtolower($_SESSION[self::$var][$id][self::$var]) ) {

				return false;

			}

		}

		return true;

	}



}
