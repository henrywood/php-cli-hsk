<?php

	namespace Henrywood\PhpCli\types;

	use Henrywood\PhpCli\Type;

	class TString extends Type
	{
		public static $shotName = 'string';

		public function validate($value)
		{
			return is_string($value) ? TRUE : 'Invalid string "' . $value . '"';
		}
	}
