<?php

	namespace Traineratwot\PhpCli;

	abstract class Type
	{
		protected $value;

		/**
		 * @throws TypeException
		 */
		public function __construct($value)
		{
			$this->set($value);
		}

		/**
		 * @return mixed
		 */
		public function get() { return $this->value; }

		/**
		 * @throws TypeException
		 */
		public function set($value)
		{
			$r = $this->validate($value);
			if ($r === TRUE) {
				$this->value = $value;
			} else {
				throw new TypeException($r, 2);
			}
		}

		/**
		 * @return string|TRUE
		 */
		abstract public function validate($value);
	}