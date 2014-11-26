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
}

?>