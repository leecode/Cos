<?php
namespace Cos\helper;
class Container implements \ArrayAccess, \Countable, \IteratorAggregate {
	protected $data = array();

	public function __construct($items = array()) {
		if(!is_null($items)) {
			$this->data = $items;
		}
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function get($key, $default = null) {
		if($this->has($key)) {
			/**
			 * Closure has a method named '__invoke'(magic method), Since PHP 5.3.0
			 * __invoke will be called when you try to call an *object* as function.
			 * We use this for singleton, it accept an object of Closure.
			 */
			$isInvokable = is_object($this->data[$key]) 
						&& method_exists($this->data[$key], '__invoke');

			return $isInvokable ? $this->data[$key]($this) : $this->data[$key];
		}

		return $default;
	}

	// Magic methods
	public function remove($key) {
		unset($this->data[$key]);
	}

	public function __isset($key) {
		return $this->has($key);
	}

	public function __unset($key) {
		unset($this->data[$key]);
	}

	public function __set($key, $value) {
		$this->set($key, $value);
	}

	public function __get($key) {
		return $this->get($key);
	}
	// End of magic methods.

	public function has($key) {
		return array_key_exists($key, $this->data);
	}

	/**
	 * ArrayAccess 
	 */
	public function offsetSet($offset, $value) {
		if(is_null($offset)) {
			$this->data[] = $value;
		} else {
			$this->data[$offset] = $value;	
		}
	}

	public function offsetExists($offset) {
		return $this->has($offset);
	}

	public function offsetUnset($offset) {
		$this->remove($offset);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

	/*
	 * Countable.
	 */
	public function count() {
		return count($this->data);
	}

	/**
	 * IteratorAggregate
	 */
	public function getIterator() {
		return new \ArrayIterator($this->data);
	}

	/**
	 * Make sure a value or object will be globally unique.
	 * This function map the handler, the object will be intialized in get(), 
	 * @param string $key 	A value or object name.
	 * @param Closure $value The Closure object that defines the object.
	 */
	public function singleton($key, $value) {
		$this->data[$key] = function($c) use ($value) {
			static $obj = null;

			if(null === $obj) {
				$obj = $value($c);
			}

			return $obj;
		};
	}
}