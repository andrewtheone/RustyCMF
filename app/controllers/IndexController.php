<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class IndexController extends ControllerBase
{

    public function indexAction()
    {
    	$this->renderTemplate("hello_world", [
    		'name' => 'Daniel'
    	]);
    }
}

