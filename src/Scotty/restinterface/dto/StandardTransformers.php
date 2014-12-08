<?php
namespace Scotty\restinterface\dto;

use Scotty\session\SessionInfoProvider;

class StandardTransformers
{

    public static function determineCurrentUserId()
    {
        return function ($input)
        {
            $sessionInfoProvider = new SessionInfoProvider();
            $userDto = $sessionInfoProvider->getCurrentUserDTO();
            return $userDto->id;
        };
    }

    /**
     * For not mandatory fk fields where ext js submits 0 which means "no reference".
     * For details see int test LiedtextDAOTest#create_noRefrainSelectedWhichMeansRefrainId0_rowCreated
     */
    public static function number0ToNull()
    {
        return function ($input)
        {
            if ($input == 0) {
                return null;
            } else {
                return $input;
            }
        };
    }

    public static function timeInSecondsSinceFirstOfYear1970()
    {
        return function ($input)
        {
            return time();
        };
    }

    public static function emptyToNull()
    {
        return function ($input)
        {
            if ($input === "") {
                return null;
            } else {
                return $input;
            }
        };
    }
}

?>