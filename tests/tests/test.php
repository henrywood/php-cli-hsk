<?php

	namespace tests;

	use Traineratwot\PhpCli\CLI;
	use Traineratwot\PhpCli\Cmd;
	use Traineratwot\PhpCli\Console;
	use Traineratwot\PhpCli\types\TypeFloat;
	use Traineratwot\PhpCli\types\TypeString;

	require dirname(__DIR__, 2) . '/vendor/autoload.php';

	class Test2 extends Cmd
	{

		public function help()
		{
			// TODO: Implement help() method.
		}

		public function setup()
		{
			$this->registerOption('test', 't', 1, TypeFloat::class);
		}

		public function Run()
		{
			var_dump($this->getArgs());
		}
	}

	class test extends CLI
	{
		public function setup()
		{
			$this->registerCmd('test', new Test2());
		}
	}

	(new test())->run();