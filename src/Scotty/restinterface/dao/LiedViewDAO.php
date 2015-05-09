<?php
namespace Scotty\restinterface\dao;

use Scotty\session\SessionInfoProvider;
use Scotty\database\DbHelper;
use Scotty\changebacktrack\ChangeBacktrack;
use \Scotty\restinterface\Request;
use Scotty\restinterface\DAOFactory;

class LiedViewDAO extends AbstractDAO
{

    public function dispatch($request)
    {
        $this->request = $request;
        
        if ($this->request->method == "PUT") {
            
            $this->addLiedIdAndLiederbuchId();
            
            $request = $this->request;
            $liedId = $request->params->lied_id;
            $liederbuchId = $request->params->liederbuch_id;
            $fkLiederbuchLiedId = $this->loadfkLiederbuchLiedEntryFor($liederbuchId, $liedId);
            
            // does relationship already exist?
            if ($fkLiederbuchLiedId > 0) {
                // yes --> delete or edit (put)
                $action = (! $request->params->Liednr) ? "DELETE" : "PUT";
                $this->redirectToNumberInBook($action, $fkLiederbuchLiedId);
                return $this->redirectToGETWithId($liedId);
            } else {
                // post (new entry)
                return $this->redirectToNumberInBook("POST", $fkLiederbuchLiedId);
            }
        }
        return parent::dispatch($request);
    }

    private function redirectToNumberInBook($action, $id)
    {
        $originalRequest = $this->request;
        $controller = "numberInBook";
        $this->logger->trace("Perform redirect from LiedView to (controller: $controller, action: $action, id: $id).");
        $request = Request::create($action, $controller, $action, $id);
        $request->params = $originalRequest->params;
        $dao = DAOFactory::createDAO($controller);
        return $dao->dispatch($request);
    }

    private function addLiedIdAndLiederbuchId()
    {
        $request = $this->request;
        $request->params->lied_id = $request->id;
        $request->params->liederbuch_id = $this->determineCurrentLiederbuchId();
    }

    private function loadfkLiederbuchLiedEntryFor($liederbuchId, $liedId)
    {
        $db = $this->db;
        $statement = $db->prepare("SELECT id FROM fkliederbuchlied WHERE liederbuch_id = ? AND lied_id = ?");
        DbHelper::throwExceptionOnStatementError($statement);
        $statement->bind_param("ss", $liederbuchId, $liedId);
        $statement->execute();
        $statement->bind_result($id);
        $fkLiederbuchLiedId = null;
        if ($statement->fetch() === true) {
            $fkLiederbuchLiedId = $id;
        }
        $statement->close();
        return $fkLiederbuchLiedId;
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

    protected function onAfterUpdate($id)
    {
        $this->updateUpdatedAtOnLied($id);
    }

    protected function onAfterCreate($id)
    {
        $this->updateUpdatedAtOnLied($id);
    }

    private function updateUpdatedAtOnLied($id)
    {
        ChangeBacktrack::updateBacktrackOnLiedByChildTable("fkliederbuchlied", $id);
    }
}

