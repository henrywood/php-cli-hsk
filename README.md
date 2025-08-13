# PHP-CLI

PHP-CLI is a simple library that helps with creating nice looking command line scripts.
It takes care of

- **option parsing**
- **help page generation**
- **automatic width adjustment**
- **colored output**
- **prompt**
- **PSR-4 compatibility**
- **support php versions >= 8.1**
- **support Enum**

Use composer:
```php composer.phar require henrywood/php-cli-hsk```

## Usage and Examples

Minimal example:

```php
<?php
	namespace index;
	require dirname(__DIR__) . '/vendor/autoload.php';

	use Henrywood\PhpCli\CLI;
	use Henrywood\PhpCli\Cmd;
	use Henrywood\PhpCli\commands\Help;
	use Henrywood\PhpCli\Console;
	use Henrywood\PhpCli\types\TEnum;
	use Henrywood\PhpCli\types\TFloat;
	use Henrywood\PhpCli\types\TInt;

	enum TestEnum
	{
		case test;
		case test1;
		case test2;
		case test3;
		case test4;
	}

	class Test2Enum extends TEnum
	{

		public function enums()
		{
			return scandir(__DIR__);
		}
	}

	class Test extends Cmd
	{

		public function help()
		{
			return "Description for auto help";
		}

		public function setup()
		{
			$this->registerParameter('param1', 1, TInt::class, "Description for param1");                            // value after action
			$this->registerOption('option1', 'o', 0, TFloat::class, "Description for option1");                      //--option1=value,--option1 value,-o=value,-o value
			$this->registerOption('option2', 'f', 0, [TestEnum::class, Test2Enum::class], "Description for option1");//--option1=value,--option1 value,-o=value,-o value
		}

		public function run()
		{
			var_dump($this->getArgs());
			var_dump($this->getArg('param1'));
			var_dump($this->getArg('option1'));
			var_dump($this->getArg('option2'));
		}
	}

	(new CLI())
		->registerDefaultCmd(new Help()) 
		->registerCmd('test', new Test()) //Create command from Class
		->registerCmd('test2', function ($options, $params) {
			var_dump($options);
			var_dump($params);
			Console::success("ok");
		})                        //Create command from Class callback, not support Auto Help
		->run()
	;
```
