<?php
namespace App\helpers\vendor;

class Wechat
{
	public $appid = '';
	public $appsecret = '';
	public $mch_id = '';
	public $mch_key = '';

	// private $error =
	public function __construct()
	{
		$this->appid = config('wechat.appid');
		$this->appsecret = config('wechat.appsecret');
		$this->mch_id = config('wechat.mch_id');
		$this->mch_key = config('wechat.mch_key');
	}

	/**
	 * [login description]
	 * @param  [type] $jscode [description]
	 * @return [type]         [$result['data']['openid']]
	 */
	public function login($jscode)
	{
		$apiUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$this->appid.'&secret='.$this->appsecret.'&js_code='.$jscode.'&grant_type=authorization_code';

		$result = $this->http_request($apiUrl);

		return $this->handle($result);
	}
	public function notify()
	{
		$wx = new Wechat();

		echo $row_data = file_get_contents('php://input');

		file_put_contents('notify.txt', $row_data);

		echo json_encode(['status' => 'ok']);
	}
	/**
	 * [pay 统一下单]
	 * 接口链接 URL地址：https://api.mch.weixin.qq.com/pay/unifiedorder
	 * @param  [type] $body             [商家名称-销售商品类目 如:腾讯-游戏]
	 * @param  [type] $attach           [附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用。]
	 * @param  [type] $out_trade_no     [商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|*@ ，且在同一个商户号下唯一。]
	 * @param  [type] $total_fee        [订单总金额，单位为分]
	 * @param  [type] $spbill_create_ip [提交用户端ip]
	 * @param  [type] $openid           [trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。]
	 * @return [type] [description]
	 */
	public function pay($body, $attach, $out_trade_no, $total_fee, $spbill_create_ip, $openid)
	{
		$apiUrl = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		$notify_url = 'https://wechat.sfwl-web.com/anhuilongruidianzi/order/notify';

		$post_data = array(
			'appid' => $this->appid,
			'mch_id' => $this->mch_id,
			'device_info' => 'WEB',//否必须
			'nonce_str' => $this->nonce_str(),
			'sign_type' => 'MD5',//默认值
			'body' => $body,
			'attach' => $attach,
			'out_trade_no' => $out_trade_no,
			'fee_type' => 'CNY',//默认值
			'total_fee' => $total_fee*100,//单位为分
			'spbill_create_ip' => $spbill_create_ip,
			'notify_url' => $notify_url,
			'trade_type' => 'JSAPI',
			'openid' => $openid,
		);

		$post_data['sign'] = $this->paysign($post_data);

		$post_xml = $this->post_xml($post_data);
		
		

		$xml = $this->http_request($apiUrl, 'POST', $post_xml);
		$this->log($post_xml);
		$this->log($xml);
		$array = $this->xml($xml);

		$data = [];
		if($array['return_code'] == 'SUCCESS' && $array['return_msg'] == 'OK'){
		        $time = time();
		        $tmp='';//临时数组用于签名
		        $tmp['appId'] = $post_data['appid'];
		        $tmp['nonceStr'] = $post_data['nonce_str'];
		        $tmp['package'] = 'prepay_id='.$array['prepay_id'];
		        $tmp['signType'] = 'MD5';
		        $tmp['timeStamp'] = "$time";

		        $data['state'] = 1;
		        $data['timeStamp'] = "$time";//时间戳
		        $data['nonceStr'] = $post_data['nonce_str'];//随机字符串
		        $data['signType'] = 'MD5';//签名算法，暂支持 MD5
		        $data['package'] = 'prepay_id='.$array['prepay_id'];//统一下单接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
		        $data['paySign'] = $this->paysign($tmp);//签名,具体签名方案参见微信公众号支付帮助文档;
		        $data['out_trade_no'] = $post_data['out_trade_no'];

	    }else{
	        $data['state'] = 0;
	        $data['text'] = "错误";
	        $data['RETURN_CODE'] = $array['return_code'];
	        $data['RETURN_MSG'] = $array['return_msg'];
	    }

	    return $data;

	}
	





	public function post_xml($data)
	{
		extract($data);

$XML = <<<XML
<xml>
   <appid>$appid</appid>
   <mch_id>$mch_id</mch_id>
   <device_info>$device_info</device_info>
   <nonce_str>$nonce_str</nonce_str>
   <sign_type>$sign_type</sign_type>
   <body>$body</body>
   <attach>$attach</attach>
   <out_trade_no>$out_trade_no</out_trade_no>
   <fee_type>$fee_type</fee_type>
   <total_fee>$total_fee</total_fee>
   <spbill_create_ip>$spbill_create_ip</spbill_create_ip>
   <notify_url>$notify_url</notify_url>
   <trade_type>$trade_type</trade_type>
   <openid>$openid</openid>
   <sign>$sign</sign>
</xml>
XML;
		return $XML;
	}



	//获取xml
	public function xml($xml, $array = true){
	    $xml =simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
	    if ($array) {
		    $xmljson= json_encode($xml);
		    $xml=json_decode($xmljson,true);
	    }
	    return $xml;
	}

	//签名
	public function sign($data){
	    ksort($data);
		$sign = '';

	    foreach ($data as $key=>$value){
            if(!$value) continue;
            if($sign) $sign .= "&$key=$value";
            else $sign = "$key=$value";
        }
		
		//return $sign;
		return strtoupper(md5($sign));
	}
	//支付签名
	public function paysign($data){
	    ksort($data);
		$sign = '';

	    foreach ($data as $key=>$value){
            if(!$value) continue;
            if($sign) $sign .= "&$key=$value";
            else $sign = "$key=$value";
        }
		$sign .= "&key=".config('wechat.mch_key');
		//return $sign;
		return strtoupper(md5($sign));
	}
	//随机32位字符串
	public function nonce_str(){
	    $result = '';
	    $str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
	    for ($i=0;$i<32;$i++){
	        $result .= $str[rand(0,48)];
	    }
	    return $result;
	}

	public function http_request($url, $method='get', $data=array()) {

	    is_array($data) && $data = http_build_query($data);

	    $ch = curl_init();//初始化
	    $headers = array('Accept-Charset: utf-8');
	    //设置URL和相应的选项
	    curl_setopt($ch, CURLOPT_URL, $url);//指定请求的URL
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));//提交方式
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//不验证SSL
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//不验证SSL
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);//设置HTTP头字段的数组

	    // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT 5.0)');//头的字符串

	    #curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies

	    #curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //使用上面获取的cookies

	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);//自动设置header中的Referer:信息
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//提交数值
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//是否输出到屏幕上,true不直接输出

	    $temp = curl_exec($ch);//执行并获取结果
	    curl_close($ch);
	    return $temp;//return 返回值
	}


	private function handle($result)
	{

		$responseJson = json_decode($result, true);

		if (isset($responseJson['errcode'])) {
			return array(
				'data' => $responseJson,
				'status' => 0,
			);
		} else {
			return array(
				'data' => $responseJson,
				'status' => 1,
			);
		}
	}


	public function log($log,$destination=''){

	    //是否开启日志写入功能

	    $now = date('c');



	    if(empty($destination)){

	        $destination = __DIR__.'/'.date('y_m_d').'.log';

	    }



	        // 自动创建日志目录

	    $log_dir = dirname($destination);

	    if (!is_dir($log_dir)) {

	        mkdir($log_dir, 0755, true);

	    }

	        //检测日志文件大小，超过配置大小则备份日志文件重新生成

	    if(is_file($destination) && floor(2097152) <= filesize($destination) ){

	        rename($destination,dirname($destination).'/'.time().'-'.basename($destination));

	    }

	        error_log("[{$now}] "./*$_SERVER['REMOTE_ADDR'].' '.$_SERVER['REQUEST_URI'].*/"\r\n{$log}\r\n", 3,$destination);

	    return $log;

	}



}

