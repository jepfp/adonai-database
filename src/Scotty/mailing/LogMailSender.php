<?php
namespace Scotty\mailing;

use \Scotty\project\ProjectConfiguration;

class LogMailSender implements IMailSender
{

    public function sendMail($to, $subject, $text)
    {
        $subject = SubjectBuilder::buildSubjectWithPrefix($subject);
        \Logger::getLogger("main")->info("LogMailSender: E-Mail to $to with subject $subject logged AND NOT SENT.");
    }
}