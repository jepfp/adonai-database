<?php
namespace Scotty\restinterface\dao;

use Scotty\changebacktrack\ChangeBacktrack;

class LiedtextDAO extends AbstractDAO
{
    protected function onAfterUpdate($id)
    {
        $this->updateUpdatedAtOnLied($id);
    }

    protected function onAfterCreate($id)
    {
        $this->updateUpdatedAtOnLied($id);
    }

    protected function onBeforeDelete($id)
    {
        $this->updateUpdatedAtOnLied($id);
    }
    
    private function updateUpdatedAtOnLied($id){
        ChangeBacktrack::updateBacktrackOnLiedByChildTable($this->determineTableName(), $id);
    }
}

