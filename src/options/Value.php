<?php

	namespace Traineratwot\PhpCli\options;

	use Traineratwot\PhpCli\TypeException;

	interface Value
	{
		/**
		 * @return void
		 * @throws TypeException
		 */
		public function initValue();

		public function set($value);

		/**
		 * @return mixed
		 */
		public function get();

		/**
		 * @return string
		 */
		public function getDescription();
	}