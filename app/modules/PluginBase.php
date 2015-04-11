<?php

class PluginBase {

	protected $__di;

	public function __construct($di) {
		$this->__di = $di;
	}

	public function getDI() {
		return $this->__di;
	}

	public function __call($name, $args) {
		$n = get_class($this);
		return $n."::".$name." is not implemented!";
	}
}