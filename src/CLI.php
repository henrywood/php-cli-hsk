<?php

	namespace Traineratwot\PhpCli;

	use RuntimeException;
	use Traineratwot\PhpCli\commands\Help;

	class CLI
	{
		/**
		 * @var Cmd[]
		 */
		private $commands;

		public function __construct()
		{
			$this->commands = [];
		}

		public function getCommands()
		{
			return $this->commands;
		}

		/**
		 * @throws TypeException
		 */
		public function run()
		{
			global $argv;
			$command = $argv[1];
			$this->_setup();
			if (array_key_exists($command, $this->commands)) {
				if ($this->commands[$command] instanceof Cmd) {
					$this->commands[$command]->_setup();
					$this->commands[$command]->run();
				} elseif (is_callable($this->commands[$command])) {
					$Options = Console::getOpt($Parameters);
					$this->commands[$command]($Options, $Parameters);
				} else {
					throw new RuntimeException(Console::getColoredString('Unknown command "' . $command . '" ', 'light_red'));
				}
			} else {
				throw new RuntimeException(Console::getColoredString('Unknown command "' . $command . '" ', 'light_red'));
			}
			return $this;
		}

		private function _setup()
		{
			$h = new Help();
			$this->registerCmd('help', $h);
			$this->registerCmd('?', $h);
			$h->setScope($this);
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
			$this->commands[$command] = $cmd;
			return $this;
		}

		/**
		 * run after register command
		 */
		public function setup()
		{
		}

	}