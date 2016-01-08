<?php
namespace Scotty\mailing;

require_once __DIR__ . '/../../lib/swiftmailer/lib/swift_required.php';

use \Scotty\project\ProjectConfiguration;

class MailSender
{

    private $mailingConfig;

    public function __construct()
    {
        $this->mailingConfig = ProjectConfiguration::getInstance()->getMailingConfiguration();
    }

    public function sendMail($to, $subject, $text)
    {
        $subject = $this->buildSubjectWithPrefix($subject);
        $mailer = $this->setupMailer();
        $message = $this->buildMessage($this->mailingConfig['senderMail'], $this->mailingConfig['senderName'], $to, $subject, $text);
        $numSent = $mailer->send($message);
        \Logger::getLogger("dbLogger")->info("E-Mail to $to with subject $subject sent to $numSent receipent(s).");
    }

    private function setupMailer()
    {
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl");
        $transport->setUsername($this->mailingConfig['username']);
        $transport->setPassword($this->mailingConfig['password']);
        
        $mailer = \Swift_Mailer::newInstance($transport);
        return $mailer;
    }

    private function buildMessage($senderMail, $senderName, $to, $subject, $text)
    {
        $message = \Swift_Message::newInstance($subject);
        $message->setFrom(array(
            $senderMail => $senderName
        ));
        $message->setTo(array(
            $to
        ));
        $message->setBody($text);
        return $message;
    }

    private function buildSubjectWithPrefix($subject)
    {
        if ($this->mailingConfig['subjectPrefix'] != '') {
            return $this->mailingConfig['subjectPrefix'] . ": " . $subject;
        } else {
            return $subject;
        }
    }
}