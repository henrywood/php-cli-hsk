<?php

	namespace tests;

	use Traineratwot\PhpCli\CLI;
	use Traineratwot\PhpCli\Cmd;
	use Traineratwot\PhpCli\types\TypeFloat;

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


		(new CLI())
			->registerCmd('test', new Test2())
			->registerCmd('test2', function($options,$params) {
				var_dump($options);
				var_dump($params);
			})
			->run()
		;
