<?php

namespace Test;

use
	\PluginBase;

class HelloPlugin extends PluginBase {

	public function World($n1 = 'Default1', $n2 = 'Default2') {
		return "Hello to ya too: ".$n1.", ".$n2."<br>";
	}
}