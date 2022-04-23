<?php

	namespace Traineratwot\PhpCli;

	use RuntimeException;
	use Traineratwot\PhpCli\options\Option;
	use Traineratwot\PhpCli\options\Parameter;
	use Traineratwot\PhpCli\types\TypeString;

	abstract class Cmd
	{
		private $_argPosition = 2;
		/**
		 * @var Option[]|Parameter[]
		 */
		private $arguments = [];

		/**
		 * @return Option[]|Parameter[]
		 */
		public function getArgumentsList()
		{
			return $this->arguments;
		}

		/**
		 * Returns command description
		 * if return FALSE disable HELP info
		 * @return string|false|void|null;
		 */
		abstract public function help();

		/**
		 * @throws TypeException
		 */
		public function _setup()
		{
			$this->setup();
			foreach ($this->arguments as $v) {
				$v->initValue();
			}
		}

		abstract public function setup();

		/**
		 * @template T of Traineratwot\PhpCli\Type
		 * @param                 $key
		 * @param                 $require
		 * @param class-string<T> $type
		 * @param string          $description
		 * @return void
		 * @throws RuntimeException
		 */
		public function registerParameter($key, $require, $type = TypeString::class, $description = '')
		{
			if (debug_backtrace()[1]['function'] !== 'setup') {
				throw new RuntimeException(Console::getColoredString(__FUNCTION__ . ' Must be called from method setup', 'light_red'));
			}
			$this->arguments[$key] = new Parameter($key, $require, $type, $description, $this->_argPosition);
			$this->_argPosition++;
		}

		/**
		 * Register an option for option parsing and help generation
		 * @param string          $long    multi character option (specified with --)
		 * @param string|null     $short   one character option (specified with -)
		 * @param bool|string     $require does this option require an argument? give it a name here
		 * @template T of Traineratwot\PhpCli\Type
		 * @param class-string<T> $type
		 * @param string          $description
		 * @return void
		 * @throws RuntimeException
		 */
		public function registerOption($long, $short = NULL, $require = FALSE, $type = TypeString::class, $description = '')
		{
			if (debug_backtrace()[1]['function'] !== 'setup') {
				throw new RuntimeException(__FUNCTION__ . ' Must be called from method setup');
			}
			$this->arguments[$long] = new Option($long, $short, $require, $type, $description, $this->_argPosition);
			$this->_argPosition++;
		}

		abstract public function Run();

		public function getArg($key)
		{
			if (array_key_exists($key, $this->arguments)) {
				return $this->arguments[$key]->get();
			}
			return NULL;
		}

		public function getArgs()
		{
			$r = [];
			foreach ($this->arguments as $key => $v) {
				$r[$key] = $v->get();
			}
			return $r;
		}
	}