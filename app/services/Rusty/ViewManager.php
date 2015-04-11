<?php

namespace Rusty;

/**
 * undocumented class
 *
 * @package default
 * @author 
 **/
class ViewManager extends \Phalcon\Mvc\View
{

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function compile($key)
	{

        $volt_template = null;

        $page = \Pages::findFirst("name = '".$key."'");
        $volt_template = $page->template;

        $engine = new \Phalcon\Mvc\View\Engine\Volt($this);
        $compiler = $engine->getCompiler();
        $compiler->addFunction('plugin', function($params, $expr) use(&$compiler) {

        	$args = [];
        	$e_a = array_slice($expr, 2);

        	if(count($e_a) > 0) {
        		foreach($e_a as $a) {
        			$args[] = $compiler->expression($a['expr']);
        		}
        	}

        	$args = implode(", ", $args);

        	$code = "(new ".$expr[0]['expr']['value']."(\$GLOBALS['di']))->".$expr[1]['expr']['value']."(".$args.")";
        	return $code;
        	//return "\"PHP Plugin <b>".print_r(."</b> called!\";";
        });
        $parsed = $compiler->compileString($volt_template);
        $this->escaper = new \Phalcon\Escaper();

        file_put_contents(__DIR__ . '/../../../app/views/'.$key.'.phtml', $parsed);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function show($key, $data = [], $pageVars = [], $layout = 'simple')
	{
		if(!file_exists(__DIR__ . '/../../../app/views/'.$key.'.phtml')) {
			$this->compile($key);
		}

        $this->setMainView("layouts/".$layout);
        $this->setParamToView('pageVars', $pageVars);
        $this->setParamToView('data', $data);

        $this->pick($key);
	}

} // END class ViewManager extends \BaseService