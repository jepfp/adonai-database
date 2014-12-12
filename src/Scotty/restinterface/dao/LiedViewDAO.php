<?php
namespace Scotty\restinterface\dao;

use Scotty\session\SessionInfoProvider;

class LiedViewDAO extends AbstractDAO
{

    protected function onBeforeDispatch()
    {
        // We will never receive a POST request directly.
        if ($this->request->method == "PUT") {
            $this->addLiedIdAndLiederbuchId();
            $this->convertToCreateRequestIfNecessary();
        }
    }

    private function addLiedIdAndLiederbuchId()
    {
        $request = $this->request;
        $request->params->lied_id = $request->id;
        $request->params->liederbuch_id = $this->determineCurrentLiederbuchId();
    }

    private function convertToCreateRequestIfNecessary()
    {
        $request = $this->request;
        if (! $this->isfkLiederbuchLiedEntryExistingFor($request->params->liederbuch_id, $request->params->lied_id)) {
            $this->request->method = "POST";
        }
    }

    private function isfkLiederbuchLiedEntryExistingFor($liederbuchId, $liedId)
    {
        $db = $this->db;
        $statement = $db->prepare("SELECT id FROM fkLiederbuchLied WHERE liederbuch_id = ? AND lied_id = ?");
        $statement->bind_param("ss", $liederbuchId, $liedId);
        $statement->execute();
        $statement->bind_result($id);
        $exists = false;
        if ($statement->fetch() === true) {
            $exists = true;
        }
        $statement->close();
        return $exists;
    }
    
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

    protected function redirectToGETWithId($id)
    {
        // In case of a LiedView creation only the fkLiederbuchLied entry is created but the id is the id from Lied.
        // Therefore we ned to redirect to the id of the Lied and not of the inserted fkLiederbuchLied id.
        return parent::redirectToGETWithId($this->request->params->lied_id);
    }
}

