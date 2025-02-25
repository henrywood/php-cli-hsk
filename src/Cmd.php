<?php

	namespace Henrywood\PhpCli;

	use RuntimeException;
	use Henrywood\PhpCli\options\Option;
	use Henrywood\PhpCli\options\Parameter;
	use Henrywood\PhpCli\types\TString;
        use Henrywood\PhpCli\CLI;

	abstract class Cmd
	{
		public CLI $CIL;

		public function setScope($CLI)
		{
			$this->CIL = $CLI;
			return $this;
		}

		public function getScope()
		{
			return $this->CLI;
		}

		private $_argPosition = 2;
		/**
		 * @var Option[]|Parameter[]
		 */
		private $arguments = [];

		public function setArgPosition($v)
		{
			$this->_argPosition = $v;
		}

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
		public function _run()
		{
			foreach ($this->arguments as $v) {
				$v->initValue();
			}
			$this->run();
		}

		abstract public function run();

		abstract public function setup();

		/**
		 * @template T of Traineratwot\PhpCli\Type
		 * @param string             $key
		 * @param bool               $require
		 * @param class-string|array $type
		 * @param string             $description
		 * @return void
		 */
		public function registerParameter(string $key, bool $require, string|array $type = TString::class, string $description = '')
		{
			if (debug_backtrace()[1]['function'] !== 'setup') {
				throw new RuntimeException(Console::getColoredString(__FUNCTION__ . ' Must be called from method setup', 'light_red'));
			}
			if (!array_key_exists($key, $this->arguments)) {
				$this->arguments[$key] = new Parameter($key, $require, $type, $description, $this->_argPosition);
				$this->_argPosition++;
			}
		}

		/**
		 * Register an option for option parsing and help generation
		 * @param string       $long    multi character option (specified with --)
		 * @param string|null  $short   one character option (specified with -)
		 * @param bool|TString $require does this option require an argument? give it a name here
		 * @param string|array $type
		 * @param string       $description
		 * @return void
		 * @template T of Traineratwot\PhpCli\Type
		 */
		public function registerOption(string $long, string $short = NULL, bool $require = FALSE, string|array $type = TString::class, $description = '')
		{
			if (debug_backtrace()[1]['function'] !== 'setup') {
				throw new RuntimeException(__FUNCTION__ . ' Must be called from method setup');
			}
			if (!array_key_exists($long, $this->arguments)) {
				$this->arguments[$long] = new Option($long, $short, $require, $type, $description, $this->_argPosition);
				$this->_argPosition++;
			}
		}

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
