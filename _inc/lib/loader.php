<?php
/*
| -----------------------------------------------------
| PRODUCT NAME: 	MODERN POS
| -----------------------------------------------------
| AUTHOR:			ITSOLUTION24.COM
| -----------------------------------------------------
| EMAIL:			info@itsolution24.com
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY ITSOLUTION24.COM
| -----------------------------------------------------
| WEBSITE:			http://itsolution24.com
| -----------------------------------------------------
*/
final class Loader 
{
	private $registry;

	public function __construct($registry) 
	{
		$this->registry = $registry;
	}

	public function model($model) 
	{
		$file = DIR_MODEL . $model . '.php';
		$class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);

		if (file_exists($file)) {
			include_once($file);

			$model_name = 'model_' . str_replace('/', '_', $model);

			$this->registry->set($model_name, new $class($this->registry));
			return $this->registry->get($model_name);
		} else {
			trigger_error('Error: Could not load model ' . $file . '!');
			exit();
		}
	}

	public function library($library) 
	{
		$file = DIR_LIBRARY . $library . '.php';

		if (file_exists($file)) {
			print_r($file);die;
			include_once($file);
		} else {
			trigger_error('Error: Could not load library ' . $file . '!');
			exit();
		}
	}

	public function helper($helper) 
	{
		$file = DIR_HELPER . $helper . '.php';

		if (file_exists($file)) {
			include_once($file);
		} else {
			trigger_error('Error: Could not load helper ' . $file . '!');
			exit();
		}
	}

	public function config($config) 
	{
		$this->registry->get('config')->load($config);
	}

	public function language($language) 
	{
		return $this->registry->get('language')->load($language);
	}
}