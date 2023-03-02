<?php

namespace Alejodevop\Gfox\Cli;

use Alejodevop\Gfox\Core\Sys;

abstract class Command {
    public abstract function beforeRun();
    public abstract function afterRun();
    public abstract function run();

    public function log($log) {
        Sys::console($log, 2, __CLASS__);
    }

    protected function dashesToCamelCase($string, $capitalizeFirst = false) {
        $str = str_replace(' ', '', 
            ucwords(
                str_replace('_', ' ', $string)
            )
        );
        return $capitalizeFirst? ucfirst($str) : lcfirst($str);
    }

    function input(string $prompt = null): string {
        echo $prompt;
        $handle = fopen ("php://stdin","r");
        $output = fgets ($handle);
        return trim ($output);
    }
}