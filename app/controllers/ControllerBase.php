<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class ControllerBase extends \Phalcon\Mvc\Controller{

	public function renderTemplate($template_name, $vars = [], $layout = "layout", $return = false) {
        

        $volt_template = null;

        $page = Pages::findFirst("name = '".$template_name."'");
        $volt_template = $page->template;

        $engine = new \Phalcon\Mvc\View\Engine\Volt($this->getDI()->get('view'));
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

        	$code = "(new ".$expr[0]['expr']['value']."Plugin(\$GLOBALS['di']))->".$expr[1]['expr']['value']."(".$args.")";
        	return $code;
        	//return "\"PHP Plugin <b>".print_r(."</b> called!\";";
        });
        $parsed = $compiler->compileString($volt_template);
        $this->escaper = new Phalcon\Escaper();

		ob_start();
		$data = $vars;
        eval('; ?>'. $parsed);
		$output = ob_get_contents();
		ob_end_clean();

        $this->view->setMainView('index');
        $this->view->setParamToView('pageVars', $vars);
        $this->view->setParamToView('template', $output);

        $this->view->pick("layouts/simple");
        return;
	}
}