<?php
namespace Smartworking\CustomOrderProcessing\Model\Api;
class RateExceededException extends \Magento\Framework\Exception\LocalizedException {}
class RateLimiter {
	private $prefix;
	public function __construct() {
        $token = $_SERVER["REMOTE_ADDR"];
		$prefix = "rate";
		$this->prefix = md5($prefix . $token);

		if( !isset($_SESSION["cache"]) ){
			$_SESSION["cache"] = array();
		}

		if( !isset($_SESSION["expiries"]) ){
			$_SESSION["expiries"] = array();
		}else{
			$this->expireSessionKeys();
		}
	}

	public function limitRequestsInMinutes($allowedRequests, $minutes) {
		$this->expireSessionKeys();
		$requests = 0;

		foreach ($this->getKeys($minutes) as $key) {
			$requestsInCurrentMinute = $this->getSessionKey($key);
			if (false !== $requestsInCurrentMinute) $requests += $requestsInCurrentMinute;
		}

		if (false === $requestsInCurrentMinute) {
			$this->setSessionKey( $key, 1, ($minutes * 60 + 1) );
		} else {
			$this->increment($key, 1);
		}
		
		if ($requests > $allowedRequests){
			throw new RateExceededException(__('Rate Limited Reached'));
		}
	}

	private function getKeys($minutes) {
		$keys = array();
		$now = time();
		for ($time = $now - $minutes * 60; $time <= $now; $time += 60) {
			$keys[] = $this->prefix . date("dHi", $time);
		}
		return $keys;
	}

	private function increment( $key, $inc){
		$cnt = 0;
		if( isset($_SESSION['cache'][$key]) ){
			$cnt = $_SESSION['cache'][$key];
		}
		$_SESSION['cache'][$key] = $cnt + $inc;
	}

	private function setSessionKey( $key, $val, $expiry ){
		$_SESSION["expiries"][$key] = time() + $expiry;
		$_SESSION['cache'][$key] = $val;
	}
	
	private function getSessionKey( $key ){
		return isset($_SESSION['cache'][$key]) ? $_SESSION['cache'][$key] : false;
	}

	private function expireSessionKeys() {
		if(isset($_SESSION["expiries"])){
			foreach ($_SESSION["expiries"] as $key => $value) {
				if (time() > $value) { 
					unset($_SESSION['cache'][$key]);
					unset($_SESSION["expiries"][$key]);
				}
			}
		}
	}
}