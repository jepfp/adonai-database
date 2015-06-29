<?php
namespace Scotty\restinterface\dao;

use Scotty\changebacktrack\ChangeBacktrack;

class FileMetadataDAO extends AbstractDAO
{

    protected function onBeforeDelete($id)
    {
        $this->updateUpdatedAtOnLied($id);
    }

    private function updateUpdatedAtOnLied($id)
    {
        ChangeBacktrack::updateBacktrackOnLiedByChildTable($this->determineTableName(), $id);
    }
}

