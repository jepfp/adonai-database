<?php
namespace Scotty\user;

use \Scotty\database\DatabaseConnector;
use Scotty\mailing\Scotty\mailing;
use Scotty\mailing\MailSenderFactory;

class ManageUser
{

    public function register($formPacket)
    {
        $response = array();
        
        $verificationResult = $this->verifyForm($formPacket);
        if ($verificationResult !== true) {
            throw new \RuntimeException("Es wurden nicht alle Felder ausgefühlt (nicht ausgefüllt: " . implode(", ", $verificationResult) . ").");
        }
        
        $email = $formPacket['email'];
        $firstname = $formPacket['firstname'];
        $lastname = $formPacket['lastname'];
        $adoray = $formPacket['adoray'];
        $password = $formPacket['password'];
        $passwordRepeat = $formPacket['passwordRepeat'];
        
        if ($this->checkIfUserExists($email)) {
            throw new \RuntimeException("Ein Benutzer mit dieser E-Mail Adresse existiert bereits.");
        }
        if ($password != $passwordRepeat) {
            throw new \RuntimeException("Das Passwort wurde nicht korrekt wiederholt.");
        }
        
        if ($this->insertUserIntoDatabase($email, $password, $firstname, $lastname, $adoray)) {
            $this->sendMail($email, $firstname, $lastname, $adoray);
            return true;
        }
        
        throw new \RuntimeException("Ein allgemeiner Fehler ist aufgetreten. Bitte melde dich bei lieder@adoray.ch.");
    }

    private function verifyForm($form)
    {
        $mustParams = array(
            "email",
            "firstname",
            "lastname",
            "adoray",
            "password",
            "passwordRepeat"
        );
        $missingParams = array();
        
        foreach ($mustParams as $param) {
            if (! isset($form[$param])) {
                $missingParams[] = $param;
            }
        }
        
        if (empty($missingParams)) {
            return true;
        } else {
            return $missingParams;
        }
    }

    private function insertUserIntoDatabase($email, $password, $firstname, $lastname, $additionalInfos)
    {
        $db = DatabaseConnector::db();
        $statement = $db->prepare("INSERT INTO user (email, hash, firstname, lastname, additionalInfos) VALUES (?, SHA1(?), ?, ?, ?)");
        $statement->bind_param("sssss", $email, $password, $firstname, $lastname, $additionalInfos);
        $statement->execute();
        $result = false;
        if ($statement->affected_rows > 0) {
            $result = true;
        }
        $statement->close();
        return $result;
    }

    private function checkIfUserExists($email)
    {
        $db = DatabaseConnector::db();
        $statement = $db->prepare("SELECT count(*) as amount FROM user WHERE email = ?");
        $statement->bind_param("s", $email);
        $statement->bind_result($amount);
        $statement->execute();
        $statement->fetch();
        return ($amount > 0);
    }

    private function sendMail($email, $firstname, $lastname, $adoray)
    {
        $mailSender = MailSenderFactory::createMailSender();
        try {
            $nachricht = "Neue Registration auf der Liederdatenbank.\r\n";
            $nachricht .= $firstname . " " . $lastname . " (" . $email . ")";
            $mailSender->sendMail('philippjenni@bluemail.ch', 'Neue Registration', $nachricht);
        } catch (\Exception $e) {
            $this->logMessage('Failed to send email.', $e);
            throw new \RuntimeException("Die Registrierung konnte abgeschlossen werden. Beim Senden der automatischen Anforderung zur Freischaltung des Accounts ist aber ein Problem aufgetreten. Bitte informiere lieder@adoray.ch");
        }
    }

    private function logMessage($message, $e)
    {
        $dbLogger = \Logger::getLogger("main");
        $dbLogger->error($message, $e);
    }
}