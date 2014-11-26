<?php
namespace Scotty\auth;

use \Scotty\database\DatabaseConnector;

class Authentication
{
	public function login($formPacket)
	{
		$response = array();
		$email = $formPacket['email'];
		$password = $formPacket['password'];
		$user = $this->authenticate($email, sha1($password));
		if($user){
		    $user->putToSession();
			$this->logLogin();
			$response = $user;
		}else{
			$response = false;
		}
		return $response;
	}

	private function authenticate($email, $hash){
		$db = DatabaseConnector::db();
		$statement = $db->prepare("SELECT id, email, firstname, lastname FROM user WHERE email = ? AND hash = ? AND active = 1");
		$statement->bind_param("ss", $email, $hash);
		$statement->execute();
		$statement->bind_result($id, $email, $firstname, $lastname);
		$userDto = new UserDTO();
		if($statement->fetch() === true){
			$userDto->id = $id;
			$userDto->email = $email;
			$userDto->firstname = $firstname;
			$userDto->lastname = $lastname;
		}else{
			$userDto = null;
		}
		$statement->close();
		return $userDto;
	}

	private function logLogin(){
		$dbLogger = \Logger::getLogger("dbLogger");
		$dbLogger->info("User logged in. (user=" . $_SESSION["email"] . ", id=" . $_SESSION["id"] . ")");
	}
}