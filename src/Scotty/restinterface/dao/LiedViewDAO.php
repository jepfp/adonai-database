<?php
namespace Scotty\restinterface\dao;

use Scotty\session\SessionInfoProvider;

class LiedViewDAO extends AbstractDAO
{
    
    // TODO: At the moment this is at the same place as it is needed only for read operations. But for update and create operations it will be helpful as well.
    protected function onBeforeExecuteRead()
    {
        $this->queryBuilder->addEqualsWhere("id_liederbuch", $this->determineCurrentLiederbuchId());
    }

    private function determineCurrentLiederbuchId()
    {
        $sessionInfoProvider = new SessionInfoProvider();
        $id = $sessionInfoProvider->getCurrentLiederbuchId();
        $this->logger->trace("Current liederbuchId: " . $id);
        return $id;
    }
}

