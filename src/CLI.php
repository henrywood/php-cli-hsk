<?php

	namespace Henrywood\PhpCli;

	use Exception;
	use RuntimeException;
	use Traineratwot\PhpCli\commands\Help;

	class CLI
	{
		/**
		 * @var Cmd[]
		 */
		private $commands;
		/**
		 * @var Help|null
		 */
		private $h;
		/**
		 * @var callable|Cmd|null
		 */
		private $defaultCmd;
		private $aliases;

		public function __construct()
		{
			$this->commands = [];
		}

		public function getCommands(&$aliases = [])
		{
			$aliases = $this->aliases;
			return array_merge(['Default' => $this->defaultCmd], $this->commands);
		}

		/**
		 * @throws TypeException
		 */
		public function run()
		{
			global $argv;
			$this->_setup();
			if (empty($this->defaultCmd)) {
				$this->defaultCmd = $this->h;
			}
			if (count($argv) <= 1) {
				$this->runDefault();
				return NULL;
			}
			$command = mb_strtolower((string)$argv[1]);
			if (array_key_exists($command, $this->commands)) {
				if ($this->commands[$command] instanceof Cmd) {
					$this->commands[$command]->_run();
				} elseif (is_callable($this->aliases[$command])) {
					$Options = Console::getOpt($Parameters);
					$this->commands[$command]($Options, $Parameters);
				} else {
					throw new RuntimeException(Console::getColoredString('Wrong command "' . $command . '" ', 'light_red'));
				}
			} else {
				try {
					$this->runDefault();
				} catch (TypeException $e) {
					throw new TypeException($e->getMessage(), $e->getCode());
				} catch (Exception $e) {
					throw new RuntimeException(Console::getColoredString('Unknown command "' . $command . '" ', 'light_red'));
				}
			}
			return NULL;
		}

		private function _setup()
		{
			$this->h = new Help();
			$this->registerCmd('help', $this->h);
			$this->registerCmd('?', $this->h);
			$this->h->setScope($this);
			$this->setup();
		}

		/**
		 * @param Cmd|Callback $cmd
		 * @return $this
		 */
		public function registerCmd($command, $cmd)
		{
			if (array_key_exists($command, $this->commands)) {
				throw new RuntimeException(Console::getColoredString('Command "' . $command . '" already exists', 'light_red'));
			}
			$alias                  = mb_strtolower((string)$command);
			$this->aliases[$alias]  = $command;
			$this->commands[$alias] = $cmd;
			if ($this->commands[$alias] instanceof Cmd) {
				$this->commands[$alias]->setScope($this)->setup();
			}
			return $this;
		}

		/**
		 * run after register command
		 */
		public function setup()
		{
		}

		/**
		 * @throws TypeException
		 */
		private function runDefault()
		{
			if ($this->defaultCmd instanceof Cmd) {
				$this->defaultCmd->_run();
			} elseif (is_callable($this->defaultCmd)) {
				$Options    = Console::getOpt($Parameters);
				$defaultCmd = $this->defaultCmd;
				$defaultCmd($Options, $Parameters);
			} else {
				throw new RuntimeException(Console::getColoredString('Unknown usage, use "help" ', 'light_red'));
			}
		}

		/**
		 * @param Cmd|Callback $cmd
		 * @return $this
		 */
		public function registerDefaultCmd($cmd)
		{
			$this->defaultCmd = $cmd;
			if ($this->defaultCmd instanceof Cmd) {
				$this->defaultCmd->setArgPosition(1);
				$this->defaultCmd->setScope($this)->setup();
			}
			return $this;
		}

	}
