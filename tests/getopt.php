<?php

	namespace index;
	require dirname(__DIR__, 1) . '/vendor/autoload.php';

	use Traineratwot\PhpCli\Cmd;

	function t($callback)
	{
		var_dump($callback instanceof Cmd);
	}

	t(function (){

	});