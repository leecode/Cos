<?php
namespace Cos;
class Cos2 {
	public static function autoload($className) {
		$thisClass = str_replace(__NAMESPACE__.'\\', '', __CLASS__);
		
		\ChromePhp::log('+++++++++ thisClass: ' . $thisClass . ', __CLASS__ : ' . __CLASS__);
	
		$baseDir = __DIR__;
		\ChromePhp::log('+++++++ before baseDir: ' . $baseDir);
	
		if (substr($baseDir, -strlen($thisClass)) === $thisClass) {
			$baseDir = substr($baseDir, 0, -strlen($thisClass));
		}
	
		\ChromePhp::log('+++++++ baseDir: ' . $baseDir);
		$className = ltrim($className, '\\');
		$fileName  = $baseDir;
		$namespace = '';
		if ($lastNsPos = strripos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	
		\ChromePhp::log('+++++++++++ WHAT WE GOT: ' . $fileName);
		if (file_exists($fileName)) {
			\ChromePhp::log('++++++++++++++++++++ requiring ' . $fileName);
			///require $fileName;
		}
	}
}