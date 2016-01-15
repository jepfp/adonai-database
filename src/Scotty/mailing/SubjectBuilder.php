<?php
namespace Scotty\mailing;

use \Scotty\project\ProjectConfiguration;

/**
 * Builds a mail subject considering the maybe configured subject prefix.
 */
class SubjectBuilder
{
    public static function buildSubjectWithPrefix($subject)
    {
        $mailingConfig = ProjectConfiguration::getInstance()->getMailingConfiguration();
        if ($mailingConfig['subjectPrefix'] != '') {
            return $mailingConfig['subjectPrefix'] . ": " . $subject;
        } else {
            return $subject;
        }
    }
}