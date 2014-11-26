<?php
namespace Scotty\auth;

class UserDTO
{

    public $success = true;

    public $id;

    public $firstname;

    public $lastname;

    public $email;

    public function putToSession()
    {
        $_SESSION["id"] = $this->id;
        $_SESSION["firstname"] = $this->firstname;
        $_SESSION["lastname"] = $this->lastname;
        $_SESSION["email"] = $this->email;
    }

    public static function createFromSession()
    {
        if (! isset($_SESSION["id"])) {
            return null;
        }
        $dto = new UserDTO();
        $dto->id = $_SESSION["id"];
        $dto->firstname = $_SESSION["firstname"];
        $dto->lastname = $_SESSION["lastname"];
        $dto->email = $_SESSION["email"];
        return $dto;
    }
}