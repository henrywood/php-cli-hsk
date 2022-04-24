<?php

	namespace Traineratwot\PhpCli\options;

	use Traineratwot\PhpCli\Console;
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
			if ($this->value) {
				return $this->value->get();
			}
			return NULL;
		}

		public function initValue()
		{
			Console::getOpt($argv);
			$argc = count($argv);
			if ($argc >= $this->position && isset($argv[$this->position])) {
				$this->set($argv[$this->position]);
			} else
				if ($this->require) {
				throw new TypeException('"' . $this->key . '" is require Parameter', 1);
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

		public function getPos()
		{
			return $this->position;
		}
	}