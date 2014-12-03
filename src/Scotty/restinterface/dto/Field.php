<?php
namespace Scotty\restinterface\dto;

class Field
{

    private $name;

    private $validators;

    private $transformers;

    private $originalValue;

    private $transformedValue;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public static function create($name, $transformers, $validators)
    {
        $field = new Field($name);
        $field->transformers = $transformers;
        $field->validators = $validators;
        return $field;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValue($value)
    {
        $this->originalValue = $value;
    }

    private function validateAndTransformValue()
    {
        $this->performTransformations();
        $this->performValidations();
    }

    private function performTransformations()
    {
        $this->transformedValue = $this->originalValue;
        foreach ($this->transformers as $t) {
            $this->transformedValue = $t($this->transformedValue);
        }
    }

    private function performValidations()
    {
        if (! $this->validators) {
            return;
        }
        
        try {
            foreach ($this->validators as $v) {
                $v($this->transformedValue);
            }
        } catch (DTOException $ex) {
            $ex->setFieldName($this->getName());
            throw $ex;
        }
    }

    public function getValue()
    {
        $this->validateAndTransformValue();
        return $this->transformedValue;
    }
}

?>