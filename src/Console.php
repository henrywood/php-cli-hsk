<?php

	namespace Henrywood\PhpCli;

	use Exception;

	class Console
	{
		/**
		 * @var string[]
		 */
		public static $FOREGROUND_COLORS
			= [
				'black'        => '0;30',
				'dark_gray'    => '1;30',
				'blue'         => '0;34',
				'light_blue'   => '1;34',
				'green'        => '0;32',
				'light_green'  => '1;32',
				'cyan'         => '0;36',
				'light_cyan'   => '1;36',
				'red'          => '0;31',
				'light_red'    => '1;31',
				'purple'       => '0;35',
				'light_purple' => '1;35',
				'brown'        => '0;33',
				'yellow'       => '1;33',
				'light_gray'   => '0;37',
				'white'        => '1;37',
			];
		/**
		 * @var string[]
		 */
		public static  $BACKGROUND_COLORS
								 = [
				'black'      => '40',
				'red'        => '41',
				'green'      => '42',
				'yellow'     => '43',
				'blue'       => '44',
				'magenta'    => '45',
				'cyan'       => '46',
				'light_gray' => '47',
			];
		private static $times    = [];
		private static $progress = [];

		/**
		 * Returns all foreground color names
		 * @return array
		 */
		public static function getForegroundColors()
		{
			return array_keys(self::$FOREGROUND_COLORS);
		}

		/**
		 * Returns all background color names
		 * @return array
		 */
		public static function getBackgroundColors()
		{
			return array_keys(self::$BACKGROUND_COLORS);
		}

		/**
		 * Prompt Ask user, Return user prompt
		 * @param string $prompt message for user
		 * @param bool   $hidden hide text like password prompt
		 * @return string
		 */
		public static function prompt($prompt = "", $hidden = FALSE)
		{
			if (self::getSystem() !== 'nix') {
				$prompt   = strtr($prompt, [
					'"' => "'",
				]);
				$vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
				file_put_contents(
					$vbscript, 'wscript.echo(InputBox("'
							 . addslashes($prompt)
							 . '", "", ""))');
				$command  = "cscript //nologo " . escapeshellarg($vbscript);
				$password = rtrim(shell_exec($command));
				unlink($vbscript);
				return $password;
			}
			$prompt  = strtr($prompt, [
				"'" => '"',
			]);
			$hidden  = $hidden ? '-s' : '';
			$command = "/usr/bin/env bash -c 'echo OK'";
			if (rtrim(shell_exec($command)) !== 'OK') {
				trigger_error("Can't invoke bash");
				return '';
			}
			$command  = "/usr/bin/env bash -c $hidden 'read  -p \""
				. addslashes($prompt . ' ')
				. "\" answer && echo \$answer'";
			$password = rtrim(shell_exec($command));
			echo "\n";
			return $password;
		}

		/**
		 * Return operating system type
		 * @return string
		 */
		public static function getSystem()
		{
			$sys = mb_strtolower(php_uname('s'));
			if (strpos($sys, 'windows') !== FALSE) {
				return 'win';
			}
			return 'nix';
		}

		/**
		 * Echo Red text
		 * @param $t
		 * @return void
		 */
		public static function failure($t)
		{
			$t = ucfirst($t);
			echo self::getColoredString($t, 'red') . PHP_EOL;
		}

		/**
		 * @param $string
		 * @param $foreground_color
		 * @param $background_color
		 * @return mixed|string
		 */
		public static function getColoredString($string, $foreground_color = NULL, $background_color = NULL)
		{
			if (PHP_SAPI === 'cli') {
				$colored_string = "";
				// Check if given foreground color found
				if (isset(self::$FOREGROUND_COLORS[$foreground_color])) {
					$colored_string .= "\033[" . self::$FOREGROUND_COLORS[$foreground_color] . "m";
				}
				// Check if given background color found
				if (isset(self::$BACKGROUND_COLORS[$background_color])) {
					$colored_string .= "\033[" . self::$BACKGROUND_COLORS[$background_color] . "m";
				}
				// Add string and end coloring
				$colored_string .= $string . "\033[0m";
				return $colored_string;
			}

			return $string;
		}

		/**
		 * Echo light_red text
		 * @param $t
		 * @return void
		 */
		public static function error($t)
		{
			$t = ucfirst($t);
			echo self::getColoredString($t, 'light_red') . PHP_EOL;
		}

		/**
		 * Echo Yellow text
		 * @param $t
		 * @return void
		 */
		public static function warning($t)
		{
			$t = ucfirst($t);
			echo self::getColoredString($t, 'yellow') . PHP_EOL;
		}

		/**
		 * Echo blue text
		 * @param $t
		 * @return void
		 */
		public static function info($t)
		{
			$t = ucfirst($t);
			echo self::getColoredString($t, 'cyan') . PHP_EOL;
		}

		/**
		 * Echo Green text
		 * @param $t
		 * @return void
		 */
		public static function success($t)
		{
			$t = ucfirst($t);
			echo self::getColoredString($t, 'green') . PHP_EOL;
		}

		/**
		 * Parse CLI params
		 * @param $non_opts
		 * @return array
		 */
		public static function getOpt(&$non_opts = [])
		{
			global $argv, $argc;
			$options = [];
			for ($i = 0; $i < $argc; $i++) {
				$arg = $argv[$i];
				// The special element '--' means explicit end of options. Treat the rest of the arguments as non-options
				// and end the loop.
				if ($arg === '--') {
					$non_opts[$i] = $arg;
					continue;
				}
				// '-' is stdin - a normal argument
				if ($arg === '-') {
					$non_opts[$i] = $arg;
					continue;
				}

				// first non-option
				if ($arg[0] !== '-') {
					$non_opts[$i] = $arg;
					continue;
				}

				// long option
				if (strlen($arg) > 1 && strpos($arg, '--') === 0) {
					$arg           = explode('=', substr($arg, 2), 2);
					$opt           = array_shift($arg);
					$val           = array_shift($arg);
					$options[$opt] = $val;
					continue;
				}

				// short option
				$opt = substr($arg, 1);
				// argument required?
				if (strpos($arg, '-') === 0 && (strlen($arg) === 2 || substr($arg, 2, 1) === '=')) {
					$val = NULL;
					if ($i + 1 < $argc && !preg_match('/^--?\w/', $argv[$i + 1])) {
						$val = $argv[++$i];
					}
					if (empty($val)) {
						$arg = explode('=', substr($arg, 1), 2);
						if (count($arg) === 2) {
							$opt = array_shift($arg);
							$val = array_shift($arg);
						}
					}
					$options[$opt] = $val;
				}

			}
			return $options;
		}

		public static function time($name)
		{
			if (isset(self::$times[$name])) {
				self::timeEnd($name);
				return;
			}
			self::$times[$name] = microtime(1);
		}

		public static function timeGet($name)
		{
			$time = microtime(1) - self::$times[$name];
			self::info($name . ": " . $time . 's');
		}

		public static function timeEnd($name)
		{
			$time = microtime(1) - self::$times[$name];
			self::info($name . ": " . $time . 's');
			unset(self::$times[$name]);
		}

		public static function getColSize($min = 80)
		{
			$col = $min;
			try {
				if (PHP_OS !== 'Linux') {
					$a1  = shell_exec('mode con');
					$arr = explode("\n", $a1);
					$col = trim(explode(':', $arr[4])[1]);

				} else {
					$col = exec('tput cols');
				}
			} catch (Exception $ex) {
			}
			return (int)$col;
		}

		/**
		 * @return void
		 */
		public static function progress($name, $current, $total, $color = 'white')
		{
			$current = (int)$current;
			$total   = (int)$total;
			$shell   = self::getColSize();

			$o          = $current / $total;
			$fill       = (int)self::$progress[$name];
			$needLength = floor($shell * $o);
			$needAdd    = $needLength - $fill;
			if ($needAdd >= 1) {
				for ($i = 0; $i < $needAdd; $i++) {
					echo self::getColoredString("█", $color);
				}
			}
			self::$progress[$name] = $needLength;
		}
	}
