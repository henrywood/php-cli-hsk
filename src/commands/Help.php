<?php

	namespace Traineratwot\PhpCli\commands;

	use LucidFrame\Console\ConsoleTable;
	use RuntimeException;
	use Traineratwot\PhpCli\CLI;
	use Traineratwot\PhpCli\Cmd;
	use Traineratwot\PhpCli\Console;
	use Traineratwot\PhpCli\options\Option;
	use Traineratwot\PhpCli\options\Parameter;
	use Traineratwot\PhpCli\types\TEnum;
	use Traineratwot\PhpCli\types\TString;

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
			$this->registerParameter('cmd', 0, TString::class, 'Enter cmd name for more information');
		}

		public function run()
		{
			$command = $this->getArg('cmd');
			if (!$command) {
				$table = new ConsoleTable();
				$table
					->hideBorder()
					->addHeader('command')
					->addHeader('arguments')
					->addHeader('description')
					->addHeader('synonyms')
				;
				$helps    = [];
				$commands = $this->CIL->getCommands($aliases);
				foreach ($commands as $command => $cls) {
					if ($cls instanceof Cmd) {
						$c                      = isset($aliases[$command]) ? $aliases[$command] : $command;
						$helps[get_class($cls)] = [
							'cls'      => $cls,
							'commands' => array_merge(isset($helps[get_class($cls)]) ? $helps[get_class($cls)]['commands'] : [], [$c]),
						];
					}
				}
				foreach ($helps as $info) {
					$description = $info['cls']->help();
					if ($description === FALSE) {
						continue;
					}
					$mainCommand = array_shift($info['commands']);
					$arguments   = $info['cls']->getArgumentsList();
					/**
					 * @var Option|Parameter $arguments
					 */
					$args = $this->extracted($arguments);
					$table->addRow()
						  ->addColumn("\> $mainCommand")
						  ->addColumn($args)
						  ->addColumn((string)$description)
						  ->addColumn(implode(", \n", $info['commands']))
						  ->addBorderLine()
					;
				}
				$table->display();
			} else {
				$commands = $this->CIL->getCommands($aliases);
				$c        = mb_strtolower($command);
				if (!array_key_exists($c, $commands)) {
					throw new RuntimeException(Console::getColoredString('Unknown command "' . $command . '" ', 'light_red'));
				}
				$command = $aliases[$c];
				$cls     = $commands[$c];
				if ($cls instanceof Cmd) {
					$description = $cls->help();
					if ($description === FALSE) {
						throw new RuntimeException(Console::getColoredString('Unknown command "' . $command . '" ', 'light_red'));
					}
					$helps = [
						'cls'      => $cls,
						'commands' => array_merge(isset($helps[get_class($cls)]) ? $helps[get_class($cls)]['commands'] : [], [$command]),
					];
					if (empty($description)) {
						Console::info($command);
					} else {
						Console::info($command . " - " . $description);
					}
					$arguments = $cls->getArgumentsList();
					$this->extracted($arguments, $args, $match);
					if (count($args) > 0) {
						$table2 = new ConsoleTable();
						$table2
							->hideBorder()
							->addHeader('argument')
							->addHeader('type')
							->addHeader('require')
							->addHeader('description')
						;
						foreach ($match as $arg) {
							$table2->addRow()
								   ->addColumn((string)$arg['key'])
							;
							$cls = $arg['typeCls'];
							$cls = new $cls(FALSE);
							if ($cls instanceof TEnum) {
								$table2->addColumn($arg['type'] . '[' . implode(", \n", $cls->enums()) . ']');
							} else {
								$table2->addColumn((string)$arg['type']);
							}
							$table2->addColumn((string)$arg['require'])
								   ->addColumn((string)$arg['description'])
								   ->addBorderLine()
							;
						}
						$table2->setIndent(4);
						$table2->display();
					}
				}
			}
		}

		/**
		 * @inheritDoc
		 */
		public function help()
		{
			return "Show help information about command";
		}

		/**
		 * @param       $arguments
		 * @param array $args
		 * @param array $match
		 * @return string
		 */
		public function extracted($arguments, &$args = [], &$match = [])
		{
			foreach ($arguments as $key => $argument) {
				$type    = '';
				$require = $argument->getRequire();
				$t       = $argument->getType();
				if ($t && class_exists($t)) {
					$type = $t::$shotName;
				} else {
					$t = NULL;
				}
				$description  = $argument->getDescription();
				$key_extended = $key;
				if ($argument instanceof Option) {
					$key_extended = '--' . $argument->getLong();
					if ($s = $argument->getShort()) {
						$key_extended .= ', -' . $s;
					}
				} elseif ($argument instanceof Parameter) {
					$key_extended .= ' (pos:' . $argument->getPos() . ')';
				}
				$match[$key] = [
					"type"        => $type,
					"typeCls"     => $t,
					"require"     => (int)$require,
					"key"         => $key_extended,
					"description" => $description,
				];
			}
			foreach ($match as $key => $argument) {
				$arg = '';
				if (!$argument['require']) {
					$arg .= "?";
				}
				$arg .= $key;
				if ($argument['type']) {
					$arg .= "<{$argument['type']}>";
				}
				if ($arg) {
					$args[] = $arg;
				}
			}

			return implode(", ", $args);
		}
	}