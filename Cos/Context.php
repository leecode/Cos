<?php
namespace Cos;

/**
 * 
 * @author lixiaoliang
 *
 */
class Context {
	protected $properties;
	protected static $context;
	
	
	public static function getInstance($refresh = false) {
		if(is_null(self::$context) || $refresh) {
			self::$context = new self();
		}
		
		return self::$context;
	}
	
	private function __construct($settings = null) {
		if($settings) {
			$this->properties = $settings;
		} else {
			$env = array();
			
			$env['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
			
			// Server Params.
			$scriptName = $_SERVER['SCRIPT_NAME']; // "/foo/index.php".
			$requestURI = $_SERVER['REQUEST_URI']; // "/foo/bar?test=abc" or "/foo/index.php/bar?test=abc".
			$queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ""; // "test=abc" or "".
			
			if(strpos($requestURI, $scriptName) !== false) {
				$physicalPath = $scriptName; // Not from url rewriting.
			} else {
				$physicalPath = str_replace("\\", "", dirname($scriptName));
			}
			
			$env['SCRIPT_NAME'] = rtrim($physicalPath, '/'); // Remove trailing slashes.
			
			
			
			// Virtual path
			$env['PATH_INFO'] = substr_replace($requestURI, '', 0, strlen($physicalPath)); // <-- Remove physical path
			$env['PATH_INFO'] = str_replace('?' . $queryString, '', $env['PATH_INFO']); // <-- Remove query string
			$env['PATH_INFO'] = '/' . ltrim($env['PATH_INFO'], '/'); // <-- Ensure leading slash
			
			// Query string (without leading "?")
			$env['QUERY_STRING'] = $queryString;
			
			// Not sure what does Cos.input mean.
			//Input stream (readable one time only; not available for multipart/form-data requests)
			$rawInput = file_get_contents('php://input');
			if (!$rawInput) {
				$rawInput = '';
			}
			$env['Cos.input'] = $rawInput;
			
			$this->properties = $env;
		}
	}
	
	public function getProperites() {
		return $this->properties;
	}
}