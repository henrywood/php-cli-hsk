<?php

	namespace index;

	use Traineratwot\PhpCli\CLI;
	use Traineratwot\PhpCli\Cmd;
	use Traineratwot\PhpCli\types\TBool;
	use Traineratwot\PhpCli\types\TEnum;
	use Traineratwot\PhpCli\types\TInt;

	require dirname(__DIR__) . '/vendor/autoload.php';

	class myTEnum extends TEnum
	{
		public function enums()
		{
			return ['val1', 'val2', 'VaL3'];
		}
	}

	class Test extends Cmd
	{

		public function help()
		{

			return "Описание Комманды test";
		}

		public function setup()
		{
			$this->registerOption('cmd', 'c', 0, [TBool::class, TInt::class], "описание для aaa");
		}

		public function run()
		{
			var_dump($this->getArgs());
		}
	}

	enum test32
	{
		case test1;
		case test2;
		case test3;
		case test4;
		case test5;
		case test6;
		case test7;
	}
	enum test23
	{
		case test1;
		case test2;
		case test3;
		case test4;
		case test5;
		case test6;
		case test7;
	}


	class test3 extends Cmd
	{

		public function help()
		{

			return "Описание Комманды test";
		}

		public function setup()
		{
			$this->registerOption('cmd', 'c',1, [test32::class,test23::class], "описание для bbb");
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
			$this->registerParameter('Test', 0, TInt::class, "описание для test");
		}

		public function run()
		{
			var_dump($this->getArgs());
		}
	}


	(new CLI())
		->registerCmd('TesT', new Test())
		->registerCmd('test2', function ($options, $params) {
			var_dump($options);
			var_dump($params);
		})
		->registerCmd('test3', new Test3())
		->run()
	;
