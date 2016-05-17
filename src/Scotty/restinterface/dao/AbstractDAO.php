<?php
namespace Scotty\restinterface\dao;

use \Scotty\restinterface\Request;
use \Scotty\restinterface\Response;
use \Scotty\database\DatabaseConnector;
use \Scotty\database\DatabaseException;
use \Scotty\security\SecurityException;
use \Scotty\security\SecInfoProvider;
use Scotty\restinterface\querybuilder\AssocBinder;
use Scotty\database\DbHelper;
use Scotty\restinterface\dto\DTOException;
use Scotty\restinterface\DAOFactory;
use Scotty\exception\DomainException;

abstract class AbstractDAO
{

    const DEFAULT_USER_ERROR_MESSAGE = "Allgemeiner Fehler beim Zugriff auf die Daten. Falls das Problem weiterhin auftritt, melde dich bei lieder@adoray.ch.";

    protected $request;

    protected $logger;

    protected $queryBuilder;

    protected $db;

    public function __construct()
    {
        $this->logger = \Logger::getLogger("main");
        $this->db = DatabaseConnector::db();
    }

    /**
     * Dispatch request to appropriate controller-action by convention according to the HTTP method.
     *
     * @param Request $request            
     */
    public function dispatch($request)
    {
        $this->request = $request;
        
        $res = new Response();
        
        try {
            $this->checkIfUserIsLoggedIn();
            switch ($this->request->method) {
                case 'GET':
                    $this->read($res);
                    break;
                case 'POST':
                    $this->create($res);
                    break;
                case 'PUT':
                    $this->update($res);
                    break;
                case 'DELETE':
                    $this->delete($res);
                    break;
            }
        } catch (SecurityException $ex) {
            $res = $this->handleException($ex, $ex->getMessage());
        } catch (DTOException $ex) {
            $res = $this->handleDtoException($ex);
        } catch (DomainException $ex) {
            $res = $this->handleDomainException($ex);
        } catch (\RuntimeException $ex) {
            $res = $this->handleException($ex);
        }
        
        return $res;
    }

    private function checkIfUserIsLoggedIn()
    {
        SecInfoProvider::throwErrorIfNotLoggedIn();
    }

    private function read(&$res)
    {
        $CRUDOperation = "read";
        $fullyQualifiedQueryBuilderName = $this->determineQueryBuilderName($CRUDOperation);
        $this->instantiateQueryBuilder($fullyQualifiedQueryBuilderName, $CRUDOperation);
        $this->onBeforeExecuteRead();
        $this->executeStatement($this->queryBuilder, $res);
    }

    private function create(&$res)
    {
        $CRUDOperation = "create";
        $fullyQualifiedQueryBuilderName = $this->determineQueryBuilderName($CRUDOperation);
        $this->onBeforeBuildCreateQuery();
        $this->instantiateQueryBuilder($fullyQualifiedQueryBuilderName, $CRUDOperation);
        $this->validateSave($this->queryBuilder->getDto(), $CRUDOperation);
        $db = $this->executeStatement($this->queryBuilder, $res);
        $insertedId = $db->insert_id;
        $this->logger->debug("New record with id " . $insertedId . " inserted.");
        $this->onAfterCreate($insertedId);
        $this->redirectToGETWithId($res, $insertedId);
    }

    protected function onBeforeBuildCreateQuery()
    {
        // override if needed
    }

    protected function onAfterCreate($id)
    {
        // override if needed
    }
    
    // TODO: https://trello.com/c/Qjgy1bQz In case of an update: Validate save should work with the whole values and not just the changed ones
    protected function validateSave($dto, $operation)
    {
        // override if needed
    }

    protected function redirectToGETWithId(&$res, $id)
    {
        $controller = $this->request->controller;
        $action = $this->request->action;
        $this->logger->trace("Perform redirect to GET (controller: $controller, action: $action, id: $id).");
        $request = Request::create("GET", $controller, $action, $id);
        $dao = DAOFactory::createDAO($controller);
        $res = $dao->dispatch($request);
    }

    private function update(&$res)
    {
        $CRUDOperation = "update";
        $fullyQualifiedQueryBuilderName = $this->determineQueryBuilderName($CRUDOperation);
        $this->instantiateQueryBuilder($fullyQualifiedQueryBuilderName, $CRUDOperation);
        $this->validateSave($this->queryBuilder->getDto(), $CRUDOperation);
        $db = $this->executeStatement($this->queryBuilder, $res);
        $updatedId = $this->request->id;
        $this->logger->debug("Record with id " . $updatedId . " updated.");
        $this->onAfterUpdate($updatedId);
        $this->redirectToGETWithId($res, $updatedId);
    }

    protected function onAfterUpdate($id)
    {
        // override if needed
    }

    private function delete(&$res)
    {
        $CRUDOperation = "delete";
        $fullyQualifiedQueryBuilderName = $this->determineQueryBuilderName($CRUDOperation);
        $this->instantiateQueryBuilder($fullyQualifiedQueryBuilderName, $CRUDOperation);
        $this->onBeforeDelete($this->request->id);
        $this->executeStatement($this->queryBuilder, $res);
        if ($res->totalCount != 1) {
            throw new \RuntimeException("Deleting of " . $this->request->id . " failed.");
        }
    }

    protected function onBeforeDelete($id)
    {
        // override if needed
    }

    private function determineQueryBuilderName($CRUDOperation)
    {
        $CRUDOperation = strtolower($CRUDOperation);
        $suffix = ucfirst($CRUDOperation) . "QueryBuilder";
        $daoClassWithNamespace = get_class($this);
        $classNameReplaced = str_replace("DAO", $suffix, $daoClassWithNamespace);
        $fullyQualifiedQueryBuilderName = str_replace("dao\\", "querybuilder\\" . $CRUDOperation . "\\", $classNameReplaced);
        $this->logger->trace("Query Builder to instantiate: " . $fullyQualifiedQueryBuilderName);
        return $fullyQualifiedQueryBuilderName;
    }

    private function instantiateQueryBuilder($fullyQualifiedQueryBuilderName, $CRUDOperation)
    {
        if (class_exists($fullyQualifiedQueryBuilderName)) {
            $this->queryBuilder = new $fullyQualifiedQueryBuilderName($this->determineTableName(), $this->request);
        } else {
            $message = "Controller '" . $this->request->controller . "' does not support the operation '" . $CRUDOperation . "'.";
            $this->logger->error($message);
            throw new \RuntimeException($message);
        }
    }

    private function executeStatement($queryBuilder, $res)
    {
        $db = $this->db;
        $statement = $queryBuilder->build($db);
        if ($statement === false) {
            // false if an error occurred
            $builderClassName = get_class($queryBuilder);
            $message = "The statement from the builder " . $builderClassName . " is invalid.\n";
            $message .= "Note to developer: The builder itself should catch the error so that this error never appears. Go and fix it in the builder!";
            throw new DatabaseException($message);
        }
        $statement->execute();
        // store_result: http://php.net/manual/de/mysqli-result.num-rows.php#105289
        // Without store_result, Unaxus server returned 0 for num_rows inside determineTotalCountAndClose. On the
        // local testing environment it worked always. Buffered queries should actually already be the default setting.
        $statement->store_result();
        DbHelper::throwExceptionOnStatementError($statement);
        $res->data = $this->fetchAndTransformResult($statement);
        $res->totalCount = $queryBuilder->determineTotalCountAndClose($statement);
        return $db;
    }

    protected function onBeforeExecuteRead()
    {
        // override if needed
    }

    private function fetchAndTransformResult($statement)
    {
        $row = array();
        AssocBinder::bind($statement, $row);
        $transformedResult = array();
        while ($statement->fetch()) {
            foreach ($row as $key => $value) {
                $row_copy[$key] = $value;
            }
            $transformedResult[] = $this->transformResult($row_copy);
        }
        return $transformedResult;
    }

    protected function handleException(\Exception $ex, $userMesssage = AbstractDAO::DEFAULT_USER_ERROR_MESSAGE)
    {
        $res = new Response();
        $res->success = false;
        $res->type = "exception";
        $res->message = $userMesssage;
        $this->logger->error($userMesssage, $ex);
        return $res;
    }

    protected function handleDtoException(DTOException $ex)
    {
        $res = new Response();
        $res->success = false;
        $res->type = "exception";
        $res->message = "Fehler im Feld " . $ex->getFieldName() . ": " . $ex->getMessage();
        $this->logger->error($res->message, $ex);
        return $res;
    }

    protected function handleDomainException(DomainException $ex)
    {
        $res = new Response();
        $res->success = false;
        $res->type = "exception";
        $res->message = $ex->getMessage();
        $this->logger->error($res->message, $ex);
        return $res;
    }

    protected function determineTableName()
    {
        $fullyQualifiedClassName = get_class($this);
        $splited = explode('\\', $fullyQualifiedClassName);
        $simpleClassName = end($splited);
        $tableName = strtolower(str_replace("DAO", "", $simpleClassName));
        return $tableName;
    }

    protected function transformResult($row)
    {
        if (isset($row['updated_at'])) {
            $time = new \DateTime($row['updated_at']);
            $row['updated_at'] = $time->format("d.m.Y H:i:s");
        }
        if (isset($row['created_at'])) {
            $time = new \DateTime($row['created_at']);
            $row['created_at'] = $time->format("d.m.Y H:i:s");
        }
        return $row;
    }
}

