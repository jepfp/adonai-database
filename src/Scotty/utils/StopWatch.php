<?php
namespace Scotty\utils;

class StopWatch
{

    private $startTime;

    private function __construct()
    {
        $this->startTime = time();
    }

    public static function start()
    {
        return new StopWatch();
    }

    public function measure()
    {
        return time() - $this->startTime;
    }
}