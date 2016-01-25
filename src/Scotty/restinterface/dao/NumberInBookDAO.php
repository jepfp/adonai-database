<?php
namespace Scotty\restinterface\dao;

use Scotty\session\SessionInfoProvider;
use Scotty\changebacktrack\ChangeBacktrack;
use Scotty\database\DbHelper;
use Scotty\restinterface\dto\DTOException;

class NumberInBookDAO extends AbstractDAO
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

    private function updateUpdatedAtOnLied($id)
    {
        ChangeBacktrack::updateBacktrackOnLiedByChildTable("fkliederbuchlied", $id);
    }

    protected function validateSave($dto, $operation)
    {
        $requestId = $this->request->id; // will be null in case of a creation request
        $liederbuchId = $this->determineLiederbuchId($requestId, $operation, $dto);
        $liedNr = $dto->findFieldByKey("Liednr")->getValue();
        if ($this->doesLiedNrInLiederbuchAlreadyExistAndIsNotCurrentAssociation($liederbuchId, $liedNr, $requestId)) {
            $this->throwSongnumberAlreadyUsedException($liedNr);
        }
    }

    private function determineLiederbuchId($requestId, $operation, $dto)
    {
        if ($operation == "update") {
            $liederbuchId = $this->findLiederbuchIdByFkLiederbuchLiedId($requestId);
        } else {
            $liederbuchId = $dto->findFieldByKey("liederbuch_id")->getValue();
        }
        return $liederbuchId;
    }

    private function throwSongnumberAlreadyUsedException($liedNr)
    {
        $message = "Die Nummer '" . $liedNr . "' ist in diesem Liederbuch bereits vergeben.";
        $ex = new DTOException($message);
        $ex->setFieldName("Liednr");
        throw $ex;
    }

    private function doesLiedNrInLiederbuchAlreadyExistAndIsNotCurrentAssociation($liederbuchId, $liedNr, $currentFkLiederbuchLiedId)
    {
        if($currentFkLiederbuchLiedId === null){
            // set to 0 because the sql AND id != null doesn't work as expected
            $currentFkLiederbuchLiedId = 0;
        }
        $db = $this->db;
        $statement = $db->prepare("SELECT id FROM fkliederbuchlied WHERE liederbuch_id = ? AND Liednr = ? AND id != ?");
        DbHelper::throwExceptionOnStatementError($statement);
        $statement->bind_param("isi", $liederbuchId, $liedNr, $currentFkLiederbuchLiedId);
        $statement->execute();
        $statement->bind_result($id);
        $doesEntryExist = ($statement->fetch() === true);
        $statement->close();
        if ($doesEntryExist) {
            return true;
        } else {
            return false;
        }
    }

    private function findLiederbuchIdByFkLiederbuchLiedId($fkLiederbuchLiedId)
    {
        $db = $this->db;
        $statement = $db->prepare("SELECT liederbuch_id FROM fkliederbuchlied WHERE id = ?");
        DbHelper::throwExceptionOnStatementError($statement);
        $statement->bind_param("i", $fkLiederbuchLiedId);
        $statement->execute();
        $statement->bind_result($liederbuchId);
        $isSuccessful = ($statement->fetch() === true);
        $statement->close();
        if ($isSuccessful) {
            return $liederbuchId;
        } else {
            throw new \RuntimeException("No entry found in fkliederbuchlied for lied_id = $fkLiederbuchLiedId.");
        }
    }
}

