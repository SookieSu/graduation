<?php

$dir = dirname(__FILE__);
require_once($dir.'/../tools/AccessToken.php');
require_once($dir.'/../api/mpApi.php');
require_once($dir.'/../DB/DBMocks.php');

$accessToken_instance = AccessTokenUtil::getInstance();

class AccessTokenUtil{
	/**
	 * 凭证的存储需要全局唯一
	 * <p>
	 * 单机部署情况下可以存在内存中 <br>
	 * 分布式情况需要存在集中缓存或DB中
	 */
	private static $token;
	//定义单例
	private static $_instance;

	public static function getInstance()
	{
		if(!(self::$_instance instanceof self))
		{
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	public function __construct()
	{
		self::init();
	}

	/**
	 * 获取凭证
	 */
	public static function getTokenStr() {
		//echo var_dump(self::queryAccessToken());
		return self::queryAccessToken()->getAccess_token();
	}

	//刷新并返回新凭证
	public static function refreshAndGetToken() {
		$tk = self::queryAccessToken();
		// 10秒之内只刷新一次，防止并发引起的多次刷新
		//echo "tk : ".var_dump($tk)."\n";
		//echo "\n".time() - $tk->getCreateTime()."\n";
		if ($tk == null || (time() - $tk->getCreateTime() > 10000)) {
			self::refreshToken();
		}
		return self::getTokenStr();
	}

	// 刷新凭证并更新全局凭证值
	private static function refreshToken() {
		//echo "refresh token...";
		$accessToken = MpApi::getAccessToken();
		//echo var_dump($accessToken);
		self::saveAccessToken($accessToken);
	}

	private static function init() {
		//echo "print in init .";
		self::$token = new AccessToken();
		if (self::queryAccessToken() == null) {
			self::refreshToken();
		}
		//initTimer(queryAccessToken());
	}

	/*
	 * 定时刷新token
	 
	private static function initTimer($tk) {
		// 刷新频率：有效期的三分之二
		$refreshTime = $tk->getExpires_in() * 2 / 3;
		// 延迟时间100秒内随机
		$delay = 100 * (new Random().nextDouble()));
		$timer.scheduleAtFixedRate(new Runnable() {
			@Override
			public void run() {
				AccessToken actk = queryAccessToken();
				// 200秒内只刷新一次，防止分布式集群定时任务同一段时间内重复刷新
				if (actk == null
						|| (System.currentTimeMillis() - actk.getCreateTime() > 200000)) {
					refreshToken();
				}
			}
		}, $delay, $refreshTime, $TimeUnit.SECONDS);
		Runtime.getRuntime().addShutdownHook(new Thread(new Runnable() {
			@Override
			public void run() {
				timer.shutdown();
			}
		}));
	}
*/
	
	/**
	 * 获取存储的token
	 */
	public static function queryAccessToken() {
		self::$token->setAccess_token(DBMocks::queryAccessToken());
		return self::$token;
	}
	
	/**
	 * 保存token
	 */
	private static function saveAccessToken($accessToken) {
		/*
		$rs = json_decode($accessToken,true);
		*/
		//echo "access_token: \n".$accessToken['access_token'];
		//echo "expires_in : \n".$accessToken['expires_in'];
		self::$token->setAccess_token($accessToken['access_token']);
		self::$token->setExpires_in($accessToken['expires_in']);
		self::$token->setCreateTime(time());
		//存入数据库
		DBMocks::updateAccessToken($accessToken['access_token']);
	}
}
?>