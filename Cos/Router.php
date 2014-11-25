<?php
namespace Cos;

class Router {
	/**
	 * Routes
	 * @var array
	 */
	private $routes;
	/**
	 * @var array Array of route object that mathced the request http method and resource URI (lazy-loaded).
	 */
	private $matchedRoutes;
	
	public function __construct() {
		$this->routes = array();
	}

	/**
	 * Map route.
	 * @param \Cos\Route $route
	 */
	public function map(\Cos\Route $route) {
		$this->routes[] = $route;
	}

	/**
	 * Return the route objects that matched the give HTTP method and request URI.
	 * @param string 			$httpMethod 	The HTTP method to match against.
	 * @param string 			$requestURI 	The request URI.
	 * @return array[\Cos\Route]
	 */
	public function getMatchedRoutes($httpMethod, $requestURI) {
		$this->matchedRoutes = array();
		
		foreach ($this->routes as $route) {
			if(!$route->supportHTTPMethod($httpMethod)) {
				continue;
			}

			if($route->matches($requestURI)) {
				$this->matchedRoutes[] = $route;
			}
		}
		
		return $this->matchedRoutes;
	}
}