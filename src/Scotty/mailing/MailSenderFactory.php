<?php
namespace Scotty\mailing;

use \Scotty\project\ProjectConfiguration;

class MailSenderFactory
{

    public static function createMailSender()
    {
        $mailingConfig = ProjectConfiguration::getInstance()->getMailingConfiguration();
        if ($mailingConfig['enable'] === true) {
            return new MailSender();
        } else {
            return new LogMailSender();
        }
    }
}