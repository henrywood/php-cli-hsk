<?php

	namespace Traineratwot\PhpCli\commands;

	use LucidFrame\Console\ConsoleTable;
	use RuntimeException;
	use Symfony\Component\Console\Terminal;
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
				$terminal      = new Terminal();
				$terminalWidth = $terminal->getWidth();
				$limits        = [
					$terminalWidth * 0.2,
					$terminalWidth * 0.5,
					$terminalWidth * 0.2,
					$terminalWidth * 0.1,
				];
				$table         = new ConsoleTable();
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
						  ->addColumn(self::substr("\> $mainCommand", $limits[0]))
						  ->addColumn(self::substr($args, $limits[1]))
						  ->addColumn(self::substr((string)$description, $limits[2]))
						  ->addColumn(self::substr(implode(", ", $info['commands']), $limits[3]))
						  ->addBorderLine()
					;
				}
				$table->display();
			} else {
				$commands = $this->CIL->getCommands($aliases);
				$c        = mb_strtolower((string)$command);
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
							$types = $arg['typeCls'];
							if (!is_array($types)) {
								$types = [$types];
							}
							$type = [];
							foreach ($types as $t) {
								if ($t && enum_exists($t)) {
									$type[] =  $arg['type'];
								} elseif ($t && class_exists($t)) {
									$cls = $arg['typeCls'];
									$cls = new $cls(FALSE);
									if ($cls instanceof TEnum) {
										$type[] = '[' . implode(',', $cls->enums()) . ']';
									} else {
										$type[] = $arg['type'];
									}
								}
							}
							$type = implode(', ', $type);
							$table2->addColumn((string)$type);

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
				$type    = [];
				$require = $argument->getRequire();
				$types   = $argument->getType();
				if (!is_array($types)) {
					$types = [$types];
				}
				foreach ($types as $t) {
					if ($t && enum_exists($t)) {
						$type[] = 'enum:' . $t;
					} elseif ($t && class_exists($t)) {
						$type[] = $t::$shotName;
					}
				}
				$type         = implode(', ', $type);
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

		public static function substr($str, $limit = 0)
		{
			$limit = (int)abs(floor($limit));
			if (strlen($str) > $limit) {
				if ($limit > 3) {
					$str = mb_substr($str, 0, $limit - 3) . '...';
				} else {
					$str = mb_substr($str, 0, $limit);
				}
			}
			return $str;
		}
	}