<?php

	namespace Traineratwot\PhpCli\options;

	use Traineratwot\PhpCli\Console;
	use Traineratwot\PhpCli\Type;
	use Traineratwot\PhpCli\TypeException;

	class Parameter extends Value
	{
		/**
		 * @var string
		 */
		protected $key;
		/**
		 * @var int
		 */
		protected $position;

		/**
		 * @template T of Traineratwot\PhpCli\Type
		 * @param string          $long
		 * @param bool            $require
		 * @param class-string<T> $type
		 * @param string          $description
		 * @param int             $position
		 */
		public function __construct($long, $require, $type, $description, $position)
		{
			$this->key         = $long;
			$this->require     = (bool)$require;
			$this->type        = $type;
			$this->description = $description;
			$this->position    = $position;
		}

		public function get()
		{
			if ($this->value instanceof Type) {
				return $this->value->get();
			}
			return $this->value;
		}

		public function initValue()
		{
			Console::getOpt($argv);
			$argc = count($argv);
			if ($argc >= $this->position && isset($argv[$this->position])) {
				$this->set($argv[$this->position]);
			} elseif ($this->require) {
				throw new TypeException('"' . $this->key . '" is require Parameter', 1);
			}
		}

		public function getPos()
		{
			return $this->position;
		}
	}