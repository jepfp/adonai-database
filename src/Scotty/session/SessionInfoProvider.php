<?php
namespace Scotty\session;

use \Scotty\database\DatabaseConnector;
use Scotty\auth\UserDTO;

class SessionInfoProvider
{
    public function getCurrentLiederbuchId()
    {
        $liederbuchId = 1;
        if (isset($_SESSION["liederbuchId"])) {
            $liederbuchId = $_SESSION["liederbuchId"];
        }
        return $liederbuchId;
    }

    public function setCurrentLiederbuchId($liederbuchId)
    {
        if (is_numeric($liederbuchId)) {
            $_SESSION["liederbuchId"] = $liederbuchId;
            return true;
        } else {
            throw new \Exception("Invalid liederbuchId");
        }
    }
    
    /**
     * @return Ambigous <NULL, \Scotty\auth\UserDTO>
     */
    public function getCurrentUserDTO(){
        $dto = UserDTO::createFromSession();
        return $dto;
    }
}