<?php

namespace Alejodevop\Gfox\Core;
use Alejodevop\Gfox\Cli\Command;
use Alejodevop\Gfox\Cli\CommandOptions;
use Alejodevop\Gfox\Cli\MigrationManager;
use Alejodevop\YowlOrm\DBManager;

class CliApp {
    private $appDir = "";
    private $arguments;
    private CommandOptions $options;

    private Command $command;

    private MigrationManager $migrationManager;


    private function __construct($appDir = "", $args = []) {
        $this->appDir = $appDir;
        $this->arguments = $args;
        $this->options = new CommandOptions($args);
        $this->migrationManager = new MigrationManager();
        $this->options->init();
        $this->initializeDB();
    }

    private function initializeDB() {
        Sys::console("Mounting database", 2, __CLASS__);
        $cacheDir = APP_DIR . DS . 'cache';
        DBManager::getInstance()->loadDriver('MySql', [
            'host' => 'localhost',
            'user' => 'root',
            'database' => 'iobenkyo_draft_db',
            'password' => 'JKrules',
            'port' => '3306',
            'cache_dir' => $cacheDir,
        ])->initCache(false);
    }

    public function db(): DBManager {
        return DBManager::getInstance();
    }

    public function getMigrationManager() {
        return $this->migrationManager;
    }

    public function getAppDir(): string {
        return $this->appDir;
    }

    public static function getInstance(string $appDir = "", $args = ""): CliApp {
        static $instance = null;
        if (!$instance) {
            $instance = new CliApp($appDir, $args);
        }
        return $instance;
    }

    public function getOptions(): CommandOptions {
        return $this->options;
    }

    public function start() {
        $commandName = $this->options->getCommand();
        $this->command = new $commandName();
        $this->command->beforeRun();
        $this->command->run();
    }

    
}