<?php

	namespace Traineratwot\PhpCli\options;

	use Traineratwot\PhpCli\Type;
	use Traineratwot\PhpCli\TypeException;

	abstract class Value
	{

		/**
		 * @var bool|null
		 */
		protected $require;
		/**
		 * @template T of Type
		 * @var  class-string<T>|null
		 */
		protected $type;
		/**
		 * @var string|null
		 */
		protected $description;
		/**
		 * @var Type|null
		 */
		public $value;

		/**
		 * @return void
		 * @throws TypeException
		 */
		abstract public function initValue();

		abstract public function set($value);

		/**
		 * @return mixed
		 */
		abstract public function get();

		/**
		 * @return string
		 */
		public function getDescription()
		{
			return $this->description;
		}

		/**
		 * @return string
		 */
		public function getType()
		{
			return $this->type;
		}

		/**
		 * @return bool
		 */
		public function getRequire()
		{
			return (bool)$this->require;
		}
	}