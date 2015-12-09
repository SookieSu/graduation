<?php
/*
 * @className : HttpUtil
 * @classDescription : 
 * 用于向微信发送http get/post请求
*/

class HttpUtil
{
	public static function doGet($url)
	{

	}
	public static function executeGet($url,$timeout=30)
	{
		if (!function_exists('curl_init')) {  
            throw new Exception('server not install curl');  
        }  
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        curl_setopt($ch, CURLOPT_HEADER, false);  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        /////
     
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	
        /* 
        if (!empty($header)) {  
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  
        }  
        */
        $data = curl_exec($ch);  
        //list($header, $data) = explode("\r\n\r\n", $data);  
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        /* 
        if ($http_code == 301 || $http_code == 302) {  
            $matches = array();  
            preg_match('/Location:(.*?)\n/', $header, $matches);  
            $url = trim(array_pop($matches));  
            curl_setopt($ch, CURLOPT_URL, $url);  
            curl_setopt($ch, CURLOPT_HEADER, false);  
            $data = curl_exec($ch);  
        }  
        */

        if ($data == false) {  
            curl_close($ch);  
        }  
        //var_dump($data);
        /////
        return $data;  
	}

	public static function doPost($url,$body)
	{

	}

	public static function executePost($url,$body)
	{ 
		// 模拟提交数据函数
    	$curl = curl_init(); // 启动一个CURL会话
    	curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    	curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
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
    	curl_close($curl); // 关闭CURL会话
    	return $tmpInfo; // 返回数据
	}

}
?>