<?php
namespace Scotty\restinterface\querybuilder\read;

class LiedViewReadQueryBuilder extends AbstractReadQueryBuilder {
    
    protected function addFiltersAndParams()
    {
        $this->addQuicksearchFilter();
        parent::addFiltersAndParams();
        $this->addDefaultOrder();
        $this->modifyOrdering();
    }
    
    private function addQuicksearchFilter()
    {
        $quicksearch = $this->request->getRequestParam("quicksearch");
        if ($quicksearch != null) {
            $this->logger->trace("Quicksearch filter: " . $quicksearch);
            $this->andWhere()
            ->openBrace()
            ->addLikeWhere("Titel", "%" . $quicksearch . "%")
            ->orWhere()
            ->addEqualsWhere("Liednr", $quicksearch)
            ->closeBrace();
        }
    }
    
    private function addDefaultOrder()
    {
        // if no order by param is set, we want to order by Liednr as a default
        // if a param is set we anyway want the last order to be by Liednr
        $this->addOrderBy("Liednr", "ASC");
    }
    
    private function modifyOrdering()
    {
        $liednrAsc = "";
        $liednrDesc = "";
        // null values at the and
        $liednrAsc .= "ISNULL(Liednr) ASC, ";
        $liednrDesc .= "ISNULL(Liednr) DESC, ";
        // parsable int values shall be sorted as numbers
        $liednrAsc .= "Liednr * 1 ASC, ";
        $liednrDesc .= "Liednr * 1 DESC, ";
        // the rest shall be ordered alphanumerically
        $liednrAsc .= "Liednr ASC";
        $liednrDesc .= "Liednr DESC";
    
        $this->modifyOrderByPart("Liednr ASC", $liednrAsc);
        $this->modifyOrderByPart("Liednr DESC", $liednrDesc);
    }
}

