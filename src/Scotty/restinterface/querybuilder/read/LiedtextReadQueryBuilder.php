<?php
namespace Scotty\restinterface\querybuilder\read;

class LiedtextReadQueryBuilder extends AbstractReadQueryBuilder {
    
    protected function addFiltersAndParams()
    {
        parent::addFiltersAndParams();
        $this->addDefaultOrder();
    }
    
    
    private function addDefaultOrder()
    {
        $this->addOrderBy("Reihenfolge", "ASC");
    }
}

