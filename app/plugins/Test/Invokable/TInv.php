<?php

namespace Test\Invokable;

class TInv extends \InvokableBase {
	
	public function sayHello($n1 = 'Default1', $n2 = 'Default2') {
		return "Hello to ya too: ".$n1.", ".$n2."<br>";
	}
}