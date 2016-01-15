<?php
namespace Scotty\mailing;

use \Scotty\project\ProjectConfiguration;

interface IMailSender
{
    public function sendMail($to, $subject, $text);
}