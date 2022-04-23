# PHP-CLI

PHP-CLI is a simple library that helps with creating nice looking command line scripts.
It takes care of

- **option parsing**
- **help page generation**
- **automatic width adjustment**
- **colored output**
- **prompt**
- **PSR-4 compatibility**

Use composer:
```php composer.phar require traineratwot/php-cli```

## Usage and Examples

Minimal example:

```php
namespace index;
require __DIR__ . '/vendor/autoload.php';
use Traineratwot\PhpCli\CLI;
use Traineratwot\PhpCli\Cmd;
use Traineratwot\PhpCli\Console;
use Traineratwot\PhpCli\types\TypeFloat;
use Traineratwot\PhpCli\types\TypeInt;

class Test extends Cmd
{

	public function help()
	{
		return "Description for auto help";
	}

	public function setup()
	{
		$this->registerParameter('param1', 1, TypeInt::class, "Description for param1");// value after action
		$this->registerOption('option1', 'o', 0, TypeFloat::class, "Description for option1");//--option1=value,--option1 value,-o=value,-o value
	}

	public function run()
	{
		var_dump($this->getArgs());
		var_dump($this->getArg('param1'));
		var_dump($this->getArg('option1'));
	}
}
(new CLI())
	->registerDefaultCmd(function () { //Default action run when other command is not available
		Console::success('ok');
	})
	->registerCmd('test', $t) //Create command from Class
	->registerCmd('test2', function ($options, $params) {
		var_dump($options);
		var_dump($params);
		Console::success("ok")
	})//Create command from Class callback, not support Auto Help
	->run()
```