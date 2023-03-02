<?php

namespace Alejodevop\Gfox\Cli;

use Alejodevop\Gfox\Core\Sys;
use Alejodevop\Gfox\Cli\CommandDoMigration;

class CommandOptions {
    private $args = [];
    private $command = null;
    private $flags = [];
    private $commands = [
        'do:migration' => CommandDoMigration::class,
        'check:migrations' => CommandCheckMigrations::class,
        'run:migrations' => CommandRunMigrations::class,
        'create:model' => CommandCreateModel::class
    ];

    public function __construct($args){
        $this->args = $args;
    }

    public function init() {
        Sys::console("Checking arguments", 1,  __CLASS__);
        $command = $this->args[1]?? null;
        $firstFlag = $this->args[2]?? null;

        if (!key_exists($command, $this->commands)) {
            throw new \Exception("The command $command does not exists");
        }
        if (isset($firstFlag)) {
            [$flag, $flagValue] = explode('=', $firstFlag);
            $flagName = str_replace('--', '', $flag);
            $this->flags[$flagName] = $flagValue;
        }
        $this->command = $this->commands[$command];
    }

    public function getCommand() {
        return $this->command;
    }

    public function getFlags() {
        $this->getFlags();
    }

    public function getFlag(string $flagName, mixed $defaultValue = ""): mixed {
        return $this->flags[$flagName]?? $defaultValue;
    }

}