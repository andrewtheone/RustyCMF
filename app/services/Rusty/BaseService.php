<?php

namespace Rusty;

class BaseService {
	protected $__di;

	public function __construct($di) {
		$this->__di = $di;
	}

	public function getDI() {
		return $this->__di;
	}
}