<?php

	namespace Traineratwot\PhpCli;

	abstract class Type
	{
		/**
		 * Shot name for help
		 * if empty use class name
		 * @var string
		 */
		public static $shotName = '';
		protected     $value;

		/**
		 * Class for validation input parameters
		 *
		 * @throws TypeException
		 */
		public function __construct($value)
		{
			if ($value !== FALSE) {
				$this->set($value);
			}
		}

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
		 * @return \string|TRUE
		 */
		abstract public function validate($value);

		/**
		 * @return mixed
		 */
		public function get() { return $this->value; }
	}