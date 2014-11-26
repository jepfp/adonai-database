<?php
namespace Scotty\restinterface\querybuilder\read;

class RefrainReadQueryBuilder extends AbstractReadQueryBuilder {
    
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

