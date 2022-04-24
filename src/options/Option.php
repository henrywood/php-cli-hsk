<?php

	namespace Traineratwot\PhpCli\options;

	use RuntimeException;
	use Traineratwot\PhpCli\Console;
	use Traineratwot\PhpCli\TypeException;

	class Option extends Value
	{
		protected $long;
		protected $position;
		protected $short;

		public function __construct($long, $short, $require, $type, $description, $position)
		{
			$this->long = $long;
			if ($short && strlen($short) > 1) {
				throw new RuntimeException(Console::getColoredString("Short options should be exactly one ASCII character", 'light_red'));
			}
			$this->short       = $short;
			$this->require     = $require;
			$this->type        = $type;
			$this->description = $description;
			$this->position    = $position;
		}

		public function get()
		{
			if ($this->value) {
				return $this->value->get();
			}
			return NULL;
		}

		public function initValue()
		{
			$option = Console::getOpt();
			if (array_key_exists($this->long, $option) || array_key_exists($this->short, $option)) {
				$v = $option[$this->short] ?: $option[$this->long];
				$this->set($v);
			} else {
				if ($this->require) {
					if ($this->short) {
						throw new TypeException('"-' . $this->short . '" is require Option', 1);
					}
					throw new TypeException('"--' . $this->long . '" is require Option', 1);
				}
			}
		}

		/**
		 * @param $value
		 * @return void
		 * @throws TypeException
		 */
		public function set($value)
		{
			try {
				$cls         = $this->type;
				$this->value = new $cls($value);
			} catch (TypeException $e) {
				throw new TypeException($e->getMessage(), $e->getCode());
			}
		}

		public function getLong()
		{
			return $this->long;
		}

		public function getShort()
		{
			return $this->short;
		}
	}