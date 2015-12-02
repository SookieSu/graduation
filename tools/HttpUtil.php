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
        //curl_close($ch); 
        //echo $data; 
        return $data;  
	}

	public static function doPost($url,$body)
	{

	}

}
?>