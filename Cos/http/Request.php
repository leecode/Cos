<?php
namespace Cos\http;

class Request {
	const METHOD_HEAD = 'HEAD';
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_PATCH = 'PATCH';
	const METHOD_DELETE = 'DELETE';
	const METHOD_OPTIONS = 'OPTIONS';
	const METHOD_OVERRIDE = '_METHOD';

	/**
	 * Application environment.
	 * @var array
	 */
	protected $env;
	
	public function __construct(\Cos\Context $env) {
		$this->env = $env->getProperites();
	}
	
	/**
	 * Get HTTP method
	 * @return string
	 */
	public function getMethod() {
		return $this->env['REQUEST_METHOD'];
	}
	
	public function getRequestURI() {
		return $this->env['PATH_INFO'];
	}
	
	/**
	 * Is this a GET request?
	 * @return bool
	 */
	public function isGet() {
		return $this->getMethod() === self::METHOD_GET;
	}
	
	/**
	 * Is this a POST request?
	 * @return bool
	 */
	public function isPost() {
		return $this->getMethod() === self::METHOD_POST;
	}
	
	/**
	 * Is this a PUT request?
	 * @return bool
	 */
	public function isPut()	{
		return $this->getMethod() === self::METHOD_PUT;
	}
	
	/**
	 * Is this a PATCH request?
	 * @return bool
	 */
	public function isPatch() {
		return $this->getMethod() === self::METHOD_PATCH;
	}
	
	/**
	 * Is this a DELETE request?
	 * @return bool
	 */
	public function isDelete() {
		return $this->getMethod() === self::METHOD_DELETE;
	}
	
	/**
	 * Is this a HEAD request?
	 * @return bool
	 */
	public function isHead() {
		return $this->getMethod() === self::METHOD_HEAD;
	}
	
	/**
	 * Is this a OPTIONS request?
	 * @return bool
	 */
	public function isOptions() {
		return $this->getMethod() === self::METHOD_OPTIONS;
	}
	
	/**
	 * Is this a AJAX request?
	 * @return bool
	 */
	public function isAjax() {
		if(isset($this->env['X_REQUESTED_WITH']) && $this->env['X_REQUESTED_WITH'] === 'XMLHttpRequest') {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Is this a XMLHttpRequest ?
	 * An alia of Slim_Http_Request::isAjax().
	 * @return bool
	 */
	public function isXhr() {
		return $this->isAjax();
	}
	
	public function params($key = null, $default = null) {
		$union = array_merge($this->get(), $this->post());
		if ($key) {
			return isset($union[$key]) ? $union[$key] : $default;
		}
		
		return $union;
	}
	
	/**
	 * Fetch GET data
	 *
	 * This method returns a key-value array of data sent in the HTTP request query string, or
	 * the value of the array key if requested; if the array key does not exist, NULL is returned.
	 *
	 * @param  string           $key
	 * @param  mixed            $default Default return value when key does not exist
	 * @return array|mixed|null
	 */
	public function get($key = null, $default = null) {
		if(!isset($this->env['Cos.request.query_params'])) {
			parse_str($this->env['QUERY_STRING'], $params);
			$this->env['Cos.request.query_params'] = $params;
		}
		
		if($key) {
			if(isset($this->env['Cos.request.query_params'][$key])) {
				return $this->env['Cos.request.query_params'][$key];
			} else {
				return $default;
			}
		} else {
			return $this->env['Cos.request.query_params'];
		}
	}
	
	/**
	 * Fetch POST data
	 *
	 * This method returns a key-value array of data sent in the HTTP request body, or
	 * the value of a hash key if requested; if the array key does not exist, NULL is returned.
	 *
	 * @param  string           $key
	 * @param  mixed            $default Default return value when key does not exist
	 * @return array|mixed|null
	 * @throws \RuntimeException If environment input is not available
	 */
	public function post($key = null, $default = null) {
		if(!isset($this->env['Cos.request.form_params'])) {
			parse_str($this->env['Cos.input'], $params);
			$this->env['Cos.request.form_params'] = $params;
		}
		
		if($key) {
			if(isset($this->env['Cos.request.form_params'][$key])) {
				return $this->env['Cos.request.form_params'][$key];
			} else {
				return $default;
			}
		} else {
			return $this->env['Cos.request.form_params'];
		}
	}
	
}