<?php

	namespace index;

	use Traineratwot\PhpCli\CLI;
	use Traineratwot\PhpCli\Cmd;
	use Traineratwot\PhpCli\Console;
	use Traineratwot\PhpCli\types\TypeFloat;
	use Traineratwot\PhpCli\types\TypeInt;

	require dirname(__DIR__) . '/vendor/autoload.php';

	class Test extends Cmd
	{

		public function help()
		{

			return "Описание Комманды test";
		}

		public function setup()
		{
			$this->registerParameter('aaa', 1, TypeInt::class, "описание для aaa");
			$this->registerParameter('bbb', 0, TypeFloat::class);
			$this->registerOption('ccc', 'c', 1, TypeFloat::class, "описание для ccc");
			$this->registerOption('ddd', 'd', 0);
		}

		public function run()
		{
			var_dump($this->getArgs());
		}
	}
	class DefaultCmd extends Cmd
	{
		public function help()
		{
			return "Описание Комманды Default2";
		}

		public function setup()
		{
			$this->registerParameter('test', 0, TypeInt::class, "описание для test");
		}

		public function run()
		{
			var_dump($this->getArgs());
		}
	}
	$t = new Test();
	(new CLI())
		->registerDefaultCmd(new DefaultCmd)
		->registerCmd('test', $t)
		->registerCmd('abc', $t)
		->registerCmd('oli', $t)
		->registerCmd('test2', function ($options, $params) {
			var_dump($options);
			var_dump($params);
		})
		->run()
	;
