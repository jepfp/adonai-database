<?php
namespace Scotty\security;

class SecInfoProvider
{

    public static function isLoggedIn()
    {
        return isset($_SESSION["id"]);
    }

    public static function throwErrorIfNotLoggedIn()
    {
        if (! SecInfoProvider::isLoggedIn()) {
            throw new SecurityException("Sie sind nicht angemeldet.");
        }
    }
}