<?php
namespace Scotty\restinterface\querybuilder\read;

class EqualsWherePart implements IWherePart
{

    private $attribute;

    private $value;

    public function getPart()
    {
        return " $this->attribute = ? ";
    }

    public function getParamsToBind()
    {
        // TODO: Find out the type of the param somehow.
        return array(
            array(
                "s",
                $this->value
            )
        );
    }

    public function where($attribute, $value)
    {
        $this->attribute = $attribute;
        $this->value = $value;
    }
}

?>