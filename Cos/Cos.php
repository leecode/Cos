<?php
namespace Cos;

class Cos {
	/**
	 * @var \Cos\helper\Container
	 */
	public $container;
	
	public function __construct() {
		$this->container = new \Cos\helper\Container();
		
		$this->container->singleton('context', function() {
			return Context::getInstance();
		});
		
		$this->container->singleton('request', function($c) {
			return new \Cos\http\Request($c['context']);
		});

		$this->container->singleton('router', function($c) {
			return new \Cos\Router();
		});
	}

	function mapRoutes($args) {
		// First parameter is pattern, and the last one is arguments.
		$pattern = array_shift($args);
		$callable = array_pop($args);
		$route = new \Cos\Route($pattern, $callable);
		$this->router->map($route);

		return $route;
	}

	function get() {
		$args = func_get_args();

		return $this->mapRoutes($args)->via(\Cos\http\Request::METHOD_GET);
	}

	function post() {
		$args = func_get_args();

		return $this->mapRoutes($args)->via(\Cos\http\Request::METHOD_POST);
	}

	function put() {
		$args = func_get_args();

		return $this->mapRoutes($args)->via(\Cos\http\Request::METHOD_PUT);
	}

	function delete() {
		$args = func_get_args();

		return $this->mapRoutes($args)->via(\Cos\http\Request::METHOD_DELETE);
	}

	function getRouter() {
		return $this->router;
	}
	
	public static function registerAutoloader() {
		spl_autoload_register(__NAMESPACE__ . "\\Cos::autoload");
	}
	
	public static function autoload($className) {
		$thisClass = str_replace(__NAMESPACE__.'\\', '', __CLASS__);
	
		$baseDir = __DIR__;
	
		if (substr($baseDir, -strlen($thisClass)) === $thisClass) {
			$baseDir = substr($baseDir, 0, -strlen($thisClass));
		}
	
		$className = ltrim($className, '\\');
		$fileName  = $baseDir;
		$namespace = '';
		if ($lastNsPos = strripos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	
		if (file_exists($fileName)) {
			require $fileName;
		}
	}
	
	public function run() {
		$matchedRoutes = $this->router->getMatchedRoutes($this->request->getMethod(), $this->request->getRequestURI());
		
		foreach($matchedRoutes as $route) {
			$route->dispatch();
		}
	}
	
	
	// Magic methods
	public function __get($name) {
		return $this->container[$name];
	}
	
	public function __set($name, $value) {
		$this->container[$name] = $value;
	}
	
	public function __isset($name) {
		return isset($this->container[$name]);
	}
	
	public function __unset($name) {
		unset($this->container[$name]);
	}
}