<?php

class AccessTokenUtil{
	/**
	 * 获取凭证
	 */
	public static function getTokenStr() {
		return queryAccessToken().getAccess_token();
	}

	/*
	  刷新并返回新凭证
	public static synchronized String refreshAndGetToken() {
		AccessToken tk = queryAccessToken();
		// 10秒之内只刷新一次，防止并发引起的多次刷新
		if (tk == null
				|| (System.currentTimeMillis() - tk.getCreateTime() > 10000)) {
			refreshToken();
		}
		return getTokenStr();
	}
	*/
	// 刷新凭证并更新全局凭证值
	private static function refreshToken() {
		echo "refresh token...";
		$accessToken = MpApi::getAccessToken();
		saveAccessToken($accessToken);
		
	}

	private static function init() {
		if (queryAccessToken() == null) {
			refreshToken();
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
	 * 凭证的存储需要全局唯一
	 * <p>
	 * 单机部署情况下可以存在内存中 <br>
	 * 分布式情况需要存在集中缓存或DB中
	 */
	private static $token;

	/**
	 * 获取存储的token
	 */
	public static function queryAccessToken() {
		return self::$token;
	}

	/**
	 * 保存token
	 */
	private static function saveAccessToken($accessToken) {
		self::$token = $accessToken;
	}
}
?>
