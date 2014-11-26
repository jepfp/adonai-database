<?php
namespace Scotty\user;

use \Scotty\database\DatabaseConnector;

class ManageUser
{
	private $projectConfiguration;

	public function register($formPacket)
	{
		$response = array();
		
		$verificationResult = $this->verifyForm($formPacket);
		if($verificationResult !== true){
			return array(
					"response" => false,
					"message" => "Es wurden nicht alle Felder ausgefühlt (nicht ausgefüllt: " . implode(", ", $verificationResult) . ")."
			);
		}
		
		$email = $formPacket['email'];
		$firstname = $formPacket['firstname'];
		$lastname = $formPacket['lastname'];
		$adoray = $formPacket['adoray'];
		$password = $formPacket['password'];
		$passwordRepeat = $formPacket['passwordRepeat'];
		
		if($this->checkIfUserExists($email)){
			return array(
					"response" => false,
					"message" => "Ein Benutzer mit dieser E-Mail Adresse existiert bereits."
			);
		}
		if($password != $passwordRepeat){
			return array(
					"response" => false,
					"message" => "Das Passwort wurde nicht korrekt wiederholt."
			);
		}

		if($this->insertUserIntoDatabase($email, $password, $firstname, $lastname, $adoray)){
			$this->sendMail($email, $firstname, $lastname, $adoray);
			return array("success" => true);
		}

		return array(
				"response" => false,
				"message" => "Ein allgemeiner Fehler ist aufgetreten. Bitte melde dich bei lieder@adoray.ch."
		);
	}

	private function verifyForm($form){

		$mustParams = array("email", "firstname", "lastname", "adoray", "password", "passwordRepeat");
		$missingParams = array();

		foreach ($mustParams as $param) {
			if(!isset($form[$param])){
				$missingParams[] = $param;
			}
		}
		
		if(empty($missingParams)){
			return true;
		}else{
			return $missingParams;
		}
	}

	private function insertUserIntoDatabase($email, $password, $firstname, $lastname, $additionalInfos){
		$db = DatabaseConnector::db();
		$statement = $db->prepare("INSERT INTO user (email, hash, firstname, lastname, additionalInfos) VALUES (?, SHA1(?), ?, ?, ?)");
		$statement->bind_param("sssss", $email, $password, $firstname, $lastname, $additionalInfos);
		$statement->execute();
		$result = false;
		if($statement->affected_rows > 0){
			$result = true;
		}
		$statement->close();
		return $result;
	}

	private function checkIfUserExists($email){
		$db = DatabaseConnector::db();
		$statement = $db->prepare("SELECT count(*) as amount FROM user WHERE email = ?");
		$statement->bind_param("s", $email);
		$statement->bind_result($amount);
		$statement->execute();
		$statement->fetch();
		return ($amount > 0);
	}

	private function sendMail($email, $firstname, $lastname, $adoray){
		try
		{
			// Die Nachricht
			$nachricht = "Neue Registration auf der Liederdatenbank. ";
			$nachricht .= $firstname . " " . $lastname . " (" . $email . ")";

			// Falls eine Zeile der Nachricht mehr als 70 Zeichen enthälten könnte,
			// sollte wordwrap() benutzt werden
			$nachricht = wordwrap($nachricht, 70);

			// Send
			mail('philipp@jenni-pfaffen.ch', 'Neue Registration', $nachricht);
		}
		catch (Exception $e)
		{
			logMessage('Failed to send email.');
		}
	}

	private function logMessage($message){
		$dbLogger = \Logger::getLogger("dbLogger");
		$dbLogger->error($message);
	}
}