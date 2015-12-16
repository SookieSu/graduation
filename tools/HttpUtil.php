<?php
/*
 * @className : HttpUtil
 * @classDescription : 
 * 用于向微信发送http get/post请求
*/
$dir = dirname(__FILE__);
require_once($dir.'/../tools/AccessTokenUtil.php');
//test
//HttpUtil::executeGet("https://api.weixin.qq.com/cgi-bin/media/get?access_token=vnu5sQAFWkZXgoDxZcTKg2MJqAUb61rLQpVj2bF9ZCh3z2l38vyXkVXDpNJ6F7_czN5YdrmtTYOKgP1keJzrl96lEjEIX994T4qSmf5Z_jQSBPhAFAZIW&media_id=lXH4OQLILzhr_I54SguvfRfR6q6lr9M3CMJJJ_k58-EZKRYTSutmJh7MwYwnn2B4");
class HttpUtil
{
	public static function doGet($url)
	{
		$realUrl = str_replace("ACCESS_TOKEN",AccessTokenUtil::getTokenStr(),$url);
		//echo "\nrealUrl in doGet : \n".$realUrl;
		$rs = self::executeGet($realUrl);
		$json = json_decode($rs,true);
		if ($json != null && array_key_exists("errcode",$json) 
				&& ($json["errcode"] == 40001
						|| $json["errcode"] == 40014
						|| $json["errcode"] == 41001 
						|| $json["errcode"] == 42001)) {
			$realUrl = str_replace("ACCESS_TOKEN",
					AccessTokenUtil::refreshAndGetToken(),$url);
			$rs = self::executeGet($realUrl);
		}
		return $rs;
	}
	public static function executeGet($url,$timeout=30)
	{
		if (!function_exists('curl_init')) {  
            throw new Exception('server not install curl');  
        }  
        $ch = curl_init(); 
        if (stripos ( $url, "https://" ) !== FALSE) {
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
            curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        } 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        curl_setopt($ch, CURLOPT_HEADER, false);  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        $data = curl_exec($ch);   
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        if($http_code == '502')
        {
            echo "get 502 ! \n";
            return false;
        }
        curl_close($ch);     
        //var_dump($data);
        return $data;  
	}

	public static function doPost($url,$body)
	{
		$realUrl = str_replace("ACCESS_TOKEN",AccessTokenUtil::getTokenStr(),$url);
		//echo "realurl: ".$realUrl."\n";
		$rs = self::executePost($realUrl, $body);
		echo "rs : ".$rs."\n";
		$json = json_decode($rs,true);
		// 访问凭证失效时，重新进行一次s获取凭证并发起原来业务调用
		if ($json != null && array_key_exists("errcode",$json) 
				&& ($json["errcode"] == 40001
						|| $json["errcode"] == 40014
						|| $json["errcode"] == 41001 
						|| $json["errcode"] == 42001)) {
			$realUrl = str_replace("ACCESS_TOKEN",
					AccessTokenUtil::refreshAndGetToken(),$url);
			$rs = self::executePost($realUrl, $body);
		}
		return $rs;
	}

	public static function executePost($url,$body)
	{ 
		// 模拟提交数据函数
    	$curl = curl_init(); // 启动一个CURL会话
        if (stripos ( $url, "https://" ) !== FALSE) {
            curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
            curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
        } 
    	curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    	//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
    	//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
    	//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    	//curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    	curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $body); // Post提交的数据包
    	curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    	curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    	$tmpInfo = curl_exec($curl); // 执行操作
    	if(curl_errno($curl)) 
    	{
   	    	echo 'Errno'.curl_error($curl);//捕抓异常
    	}
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
        if($http_code == '502')
        {
            echo "post 502 ! \n";
            return false;
        }
    	curl_close($curl); // 关闭CURL会话
    	return $tmpInfo; // 返回数据
	}

}
?>