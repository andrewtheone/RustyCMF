<?php
$start_time = microtime(TRUE);

error_reporting(E_ALL);
ini_set('display_errors', 1);
try {

	/**
	 * Read the configuration
	 */
	$config = require __DIR__ . "/../app/config/config.php";

	/**
	 * Include loader
	 */
	require __DIR__ . '/../app/config/loader.php';

	/**
	 * Include services
	 */
	require __DIR__ . '/../app/config/services.php';

	/**
	 * Handle the request
	 */
	$application = new \Phalcon\Mvc\Application();
	$application->setDI($di);
	echo $application->handle()->getContent();
	$end_time = microtime(TRUE);

	header("Rusty-Benchmark: ".($end_time - $start_time));
} catch (Phalcon\Exception $e) {
	echo $e->getMessage();
} catch (PDOException $e){
	echo $e->getMessage();
}