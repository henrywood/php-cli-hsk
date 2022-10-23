<?php

	namespace Traineratwot\PhpCli\types;

	use Traineratwot\PhpCli\Type;
	use Traineratwot\PhpCli\TypeException;

	class TBool extends Type
	{
		public static $shotName = 'Bool';
		public static $no       = ['n', 'no', 'false', '0', ''];
		public static $yes      = ['y', 'yes', 'true', '1'];

		/**
		 * @inheritDoc
		 */
		public function validate($value)
		{
			$value = mb_strtolower((string)$value);
			if (in_array($value, array_merge(self::$no, self::$yes), TRUE)) {
				return TRUE;
			}
			return 'Invalid boolean value';
		}

		public function set($value)
		{
			$r = $this->validate($value);
			if ($r === TRUE) {
				if (in_array(mb_strtolower((string)$value), self::$yes, TRUE)) {
					$this->value = TRUE;
				} else {
					$this->value = FALSE;
				}
			} else {
				throw new TypeException($r, 2);
			}

		}
	}