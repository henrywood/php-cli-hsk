<?php

	namespace Traineratwot\PhpCli\types;

	use Traineratwot\PhpCli\Type;

	abstract class TEnum extends Type
	{
		public static $shotName = 'Enum';

		/**
		 * @return array
		 */
		public function enums()
		{
			return [];
		}

		/**
		 * @inheritDoc
		 */
		public function validate($value)
		{
			foreach ($this->enums() as $e) {
				if (mb_strtolower((string)$e) === mb_strtolower((string)$value)) {
					return TRUE;
				}
			}
			return "Incorrect enum value: (" . implode(', ', $this->enums()) . ")";
		}

		/**
		 * @inheritDoc
		 */
		public function get()
		{
			foreach ($this->enums() as $e) {
				if (mb_strtolower((string)$e) === mb_strtolower((string)$this->value)) {
					return $e;
				}
			}
		}
	}