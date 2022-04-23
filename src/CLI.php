<?php

	namespace Traineratwot\PhpCli;

	use RuntimeException;

	abstract class CLI
	{
		/**
		 * @var Cmd[]
		 */
		private $commands;

		public function __construct()
		{
			$this->commands = [];
		}

		abstract public function setup();

		/**
		 * @param Cmd $cmd
		 * @return void
		 */
		final public function registerCmd($command, $cmd)
		{
			if (debug_backtrace()[1]['function'] !== 'setup') {
				throw new RuntimeException(Console::getColoredString('Method: "' . __FUNCTION__ . '" Must be called from method setup', 'light_red'));
			}
			if (array_key_exists($command, $this->commands)) {
				throw new RuntimeException(Console::getColoredString('Command "' . $command . '" already exists', 'light_red'));
			}
			$this->commands[$command] = $cmd;
		}


		/**
		 * @throws TypeException
		 */
		final public function run()
		{
			global $argv;
			$command= $argv[1];
			$this->setup();
			if (array_key_exists($command, $this->commands)) {
				$this->commands[$command]->_setup();
				$this->commands[$command]->run();
			}else{
				throw new RuntimeException(Console::getColoredString('Unknown command "' . $command . '" ', 'light_red'));
			}

		}
	}