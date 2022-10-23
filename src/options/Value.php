<?php

	namespace Traineratwot\PhpCli\options;

	use ReflectionEnum;
	use Traineratwot\PhpCli\Type;
	use Traineratwot\PhpCli\TypeException;

	abstract class Value
	{

		/**
		 * @var Type|null
		 */
		public $value;
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
		 * @return void
		 * @throws TypeException
		 */
		abstract public function initValue();

		/**
		 * @throws TypeException
		 */
		public function set($value)
		{
			if (is_array($this->type)) {
				$this->type = array_unique($this->type);
			} else {
				$this->type = [$this->type];
			}
			$errors = [];
			foreach ($this->type as $type) {
				try {
					$this->validate($type, $value);
				} catch (TypeException $e) {
					$errors[] = $e;
				}
			}
			if (count($errors) === count($this->type)) {
				$msg  = [];
				$code = 0;
				foreach ($errors as $e) {
					$msg[] = $e->getMessage();
					$code  |= $e->getCode();
				}
				throw new TypeException(implode(', ', $msg), $code);
			}
		}

		/**
		 * @throws TypeException
		 */
		public function validate(string $type, mixed $value)
		{
			try {
				if (enum_exists($type)) {
					$enum = new ReflectionEnum($type);
					if ($enum->isBacked()) {
						$valueType = $enum->getBackingType();
						if ($valueType && method_exists($valueType, 'getName')) {
							$valueType = $valueType->getName();
						} else {
							throw new TypeException("Incorrect Enum type", __LINE__);
						}
						switch ($valueType) {
							case 'int':
							case 'integer':
								$valueType = 'integer';
								$value     = (int)$value;
								break;
							case 'double':
							case 'float':
								$valueType = 'double';
								$value     = (float)$value;
								break;
							case 'boolean':
							case 'bool':
								$valueType = 'boolean';
								$value     = (bool)$value;
								break;
							case'string':
								$value = (string)$value;
								break;
							case 'array':
								$value = json_decode($value, 1);
								break;
							case 'object':
							case 'resource':
							case 'NULL':
							case 'unknown type':
								throw new TypeException("Incorrect Enum type", __LINE__);
								break;
						}
						if (!$enum->hasCase($value) && ($valueType !== gettype($value) || !$type::tryFrom($value))) {
							$enums = [];
							foreach ($enum->getCases() as $case) {
								$enums[] = $case->getValue()->value.'['.$case->getName().']';
							}
							$msg = "Incorrect enum value: (" . implode(', ', $enums) . ")";
							throw new TypeException($msg, 92);
						}
						$this->value = $value;
						return;
					}
					if (!$enum->hasCase($value)) {
						$enums = [];
						foreach ($enum->getCases() as $case) {
							$enums[] = $case->getName();
						}
						$msg = "Incorrect enum value: (" . implode(', ', $enums) . ")";
						throw new TypeException($msg, __LINE__);
					}
					$this->value = $value;
					return;
				}
				if (class_exists($type)) {
					$this->value = new $type($value);
				}
			} catch (TypeException $e) {
				throw $e;
			}
		}

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