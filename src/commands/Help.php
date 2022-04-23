<?php

	namespace Traineratwot\PhpCli\commands;

	use LucidFrame\Console\ConsoleTable;
	use Traineratwot\PhpCli\CLI;
	use Traineratwot\PhpCli\Cmd;
	use Traineratwot\PhpCli\options\Option;
	use Traineratwot\PhpCli\options\Parameter;
	use Traineratwot\PhpCli\types\TypeString;

	class Help extends Cmd
	{
		/**
		 * @var CLI|null
		 */
		private $CIL;

		public function setScope($CLI)
		{
			$this->CIL = $CLI;
		}

		public function setup()
		{
			$this->registerParameter('cmd', 0, TypeString::class, 'Enter cmd name for more information');
		}

		public function Run()
		{
			if (!$this->getArg('cmd')) {
				$table = new ConsoleTable();
				$table
					->hideBorder()
					->addHeader('command')
					->addHeader('arguments')
					->addHeader('description')
					->addHeader('synonyms')
				;
				$helps    = [];
				$commands = $this->CIL->getCommands();
				foreach ($commands as $command => $cmd) {
					if ($cmd instanceof Cmd) {
						$helps[get_class($cmd)] = [
							'cls'      => $cmd,
							'commands' => array_merge(isset($helps[get_class($cmd)]) ? $helps[get_class($cmd)]['commands'] : [], [$command]),
						];
					}
				}
				foreach ($helps as $info) {
					$description = $info['cls']->help();
					if ($description === FALSE) {
						continue;
					}
					$mainCommand = array_shift($info['commands']);

					$arguments = $info['cls']->getArgumentsList();
					/**
					 * @var Option|Parameter $arguments
					 */
					$args = '';
					foreach ($arguments as $key => $argument) {
						$require = $argument->getRequire();
						if (!$require) {
							$args .= "?";
						}
						$args .= $key;
						$t    = $argument->getType();
						$type = $t::$shotName;
						if ($t) {
							$args .= "<$type>";
						}
					}
					$table->addRow()
						  ->addColumn("\> $mainCommand")
						  ->addColumn($args)
						  ->addColumn((string)$description)
						  ->addColumn(implode(", ", $info['commands']))
						  ->addBorderLine()
					;
				}

				$table->display();
			}
		}

		/**
		 * @inheritDoc
		 */
		public function help()
		{
			return "Show help information about command";
		}
	}