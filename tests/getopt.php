<?php
	namespace index;
	require dirname(__DIR__, 1) . '/vendor/autoload.php';

	use Traineratwot\PhpCli\Console;

	var_dump(Console::getOpt($non_opts));
	var_dump($non_opts);
	var_dump($argv);
