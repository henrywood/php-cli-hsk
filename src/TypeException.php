<?php

	namespace Traineratwot\PhpCli;

	use Exception;

	class TypeException extends Exception
	{
		public function __construct($message = "", $code = 0, $previous = NULL)
		{
			$message = Console::getColoredString($message, 'light_red');
			parent::__construct($message, $code, $previous);
		}
	}