<?php

	namespace Traineratwot\PhpCli\options;

	use Exception;
	use Traineratwot\PhpCli\Console;
	use Traineratwot\PhpCli\Type;
	use Traineratwot\PhpCli\TypeException;

	class Parameter implements Value
	{
		/**
		 * @var string
		 */
		private $key;
		/**
		 * @var bool
		 */
		private $require;
		/**
		 * @template T of Type
		 * @var  class-string<T>
		 */
		private $type;
		/**
		 * @var string
		 */
		private $description;
		/**
		 * @var int
		 */
		private $position;
		/**
		 * @var Type|null
		 */
		private $value;

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
			$this->require     = $require;
			$this->type        = $type;
			$this->description = $description;
			$this->position    = $position;
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
			if ($argc >= $this->position && !empty($argv[$this->position])) {
				$this->set($argv[$this->position]);
			} else {
				if ($this->require) {
					throw new TypeException('"' . $this->key . '" is require Parameter', 1);
				}
				$this->set(NULL);
			}
		}

		public function getDescription()
		{
			return $this->description;
		}
	}