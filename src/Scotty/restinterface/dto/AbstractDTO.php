<?php
namespace Scotty\restinterface\dto;

class AbstractDTO
{

    protected $logger;

    /**
     *
     * @var Field[]
     */
    protected $fields;

    private $fieldsToIgnore;

    private $requestParams;

    public function __construct($requestParams)
    {
        $this->logger = \Logger::getLogger("main");
        $this->requestParams = $requestParams;
        $this->fieldsToIgnore = $this->getFieldsToIgnore();
        $this->mapParamsToFields();
    }

    private function mapParamsToFields()
    {
        foreach ($this->requestParams as $key => $value) {
            $field = $this->findFieldByKey($key);
            if (in_array($key, $this->fieldsToIgnore)) {
                $this->logger->debug("Ignoring field '" . $key . "' because it is in ignore list.");
            } elseif ($field) {
                $this->logger->debug("Settings field '" . $field->getName() . "' to '" . $value . "'");
                $field->setValue($value);
            } else {
                $ex = new DTOException("Mapping of param '" . $key . "' failed!");
                $ex->setFieldName($key);
                throw $ex;
            }
        }
    }

    /**
     * Returns the list of fields to be ignored during mapping.
     * This method can be overridden in order to add additional fields.
     *
     * @return multitype:string
     */
    protected function getFieldsToIgnore()
    {
        $fieldsToIgnore = array(
            // We never want the id. It's either not allowed (in case of creating a new record)
            // or it is not needed (in case of updating an existing record) because it's determined via the url.
            "id",
            "created_at",
            "updated_at"
        );
        return $fieldsToIgnore;
    }

    private function findFieldByKey($key)
    {
        foreach ($this->fields as $aField) {
            if ($aField->getName() == $key) {
                return $aField;
            }
        }
        return null;
    }

    public function getAllFieldNames()
    {
        $fieldNames = array();
        foreach ($this->fields as $aField) {
            $fieldNames[] = $aField->getName();
        }
        return $fieldNames;
    }

    public function getAllFieldNamesInParams()
    {
        $fieldNames = array();
        foreach ($this->fields as $aField) {
            if (array_key_exists($aField->getName(), $this->requestParams)) {
                $fieldNames[] = $aField->getName();
            }
        }
        return $fieldNames;
    }

    /**
     * Returns all DTO fields with their value.
     *
     * @return multitype:NULL
     */
    public function getAllKeyValuePairs()
    {
        $keyValues = array();
        foreach ($this->fields as $aField) {
            $keyValues[$aField->getName()] = $aField->getValue();
        }
        return $keyValues;
    }

    /**
     * Returns all DTO fields with their value which were sent as parameter.
     *
     * @return multitype:NULL
     */
    public function getAllKeyValuePairsInParams()
    {
        $keyValues = array();
        foreach ($this->fields as $aField) {
            if (array_key_exists($aField->getName(), $this->requestParams)) {
                $keyValues[$aField->getName()] = $aField->getValue();
            }
        }
        return $keyValues;
    }

    protected function notNullOrEmpty()
    {
        return function ($input)
        {
            if (! isset($input) || trim($input) === '') {
                throw new DTOException("Das Feld darf nicht leer sein.");
            }
        };
    }
}
