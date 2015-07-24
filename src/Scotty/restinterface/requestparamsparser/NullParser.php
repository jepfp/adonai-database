<?php
namespace Scotty\restinterface\requestparamsparser;

class NullParser implements RequestParamsParser
{

    public function parseParams()
    {
        return null;
    }
}

