<?php
namespace Cos;

class Route {
	/**
	 * @var string The route pattern like ('/blogs/:id')
	 */
	private $pattern;
	/**
	 * @var array Key-value array of URL parameters. NOTE: not query paramters.
	 */
	private $params = array();
	/**
	 * @var array Parameters names.
	 */
	private $paramNames = array();
	/**
	 * @var mixed The route handler.
	 */
	private $handler;
	
	/**
	 * @var array Methods supportted.
	 */
	private $methods = array();

	/**
	 * Constructor.
	 * @param string $pattern The url pattern like (/blogs/:id).
	 * @param mixed $hanlder	Anything that return true for is_callable().
	 */
	public function __construct($pattern, $handler) {
		$this->setPattern($pattern);
		$this->setHandler($handler);
	}

	public function setPattern($pattern) {
		$this->pattern = $pattern;
	}
	
	public function getParams() {
		return $this->params;
	}
	
	public function getHandler() {
		return $this->handler;
	}
	
	/**
	 * Check if this route support the give HTTP method.
	 * @param string $method
	 * @return boolean
	 */
	public function supportHTTPMethod($method) {
		return in_array($method, $this->methods);
	}
	
	/**
	 * Append supported HTTP methods.
	 * @return \Cos\Route
	 */
	public function appendHTTPMethods() {
		$args = func_get_args();
		$this->methods = array_merge($this->methods, $args);
		
		return $this;
	}
	
	/**
     * Append supported HTTP methods. (alias for Route::appendHttpMethods)
	 * @return \Cos\Route
	 */
	public function via() {
		$args = func_get_args();
		$this->methods = array_merge($this->methods, $args);
		
		return $this;
	}

	/**
	 * Set route handler.
	 * Can be a closure function or a method of function like (BlogHandler::get).
	 * @param mixed $handler	Anything that returns TRUE for is_callable().
	 */
	public function setHandler($handler) {
		$matches = array();
		// Check if is "Class::method" pattern.
		if(is_string($handler) && preg_match('!^([^\:]+)\:([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!', $handler, $matches)) {
			$class = $matches[1];
			$method = $matches[2];
			$handler = function() use ($class, $method) {
				static $obj = null;
				if(null === $obj) {
					$obj = new $class;
				}
				return call_user_func_array(array($obj, $method), func_get_args());
			};
		}
		
		if (!is_callable($handler)) {
			throw new \InvalidArgumentException('Route callable must be callable!');
		}
		
		$this->handler = $handler;
	}
	
	/**
	 * If resource uri mathces the pattern ?
	 * @param string $resourceURI A request URI.
	 * @return bool
	 */
	public function matches($resourceURI) {
		$patternAsRegex = preg_replace_callback(
			'#:([\w]+)#', 
			array($this, 'convertURLParamsToRegex'), 
			$this->pattern);

		if (substr($this->pattern, -1) === '/') {
            $patternAsRegex .= '?';
        }

        $regex = '#^' . $patternAsRegex . '$#';

        if(!preg_match($regex, $resourceURI, $paramValues)) {
        	return false;
        }

        // Set parameter values.
        foreach ($this->paramNames as $name) {
        	if(isset($paramValues[$name])) {
        		$this->params[$name] = $paramValues[$name];
        	}
        }

        return true;
	}

    /**
     * Convert a URL parameter (e.g. ":id") into a regular expression
     * @param  array 		$matches URL parameters
     * @return string       Regular expression for URL parameter
     */
	public function convertURLParamsToRegex($matches) {
		// Store parameter names, and return replaced regular expression.
		$this->paramNames[] = $matches[1];
		return '(?P<' . $matches[1] . '>[^/]+)';
	}
	
	/**
	 * Dispatch the route.
	 * @return boolean
	 */
	public function dispatch() {
		$return = call_user_func_array($this->getHandler(), array_values($this->getParams()));
		
		return false === $return ? false : true;	// Why use this ? when function call doesn't have return value, the $return will be empty, but that is not the "FALSE".
	}
}