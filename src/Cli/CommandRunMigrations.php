<?php

namespace Alejodevop\Gfox\Cli;

use Alejodevop\Gfox\Core\Sys;
use Alejodevop\Gfox\Database\Blueprint;
use Alejodevop\Gfox\Database\Migration;
use Jfcherng\Utility\CliColor;


class CommandRunMigrations extends Command {
    public function beforeRun() {
        Sys::cliApp()->getMigrationManager()->initMigrations();
    }
    public function afterRun() {
        
    }


    public function run() {
        $migrations = Sys::cliApp()->getMigrationManager()->checkPendingMigrations();
        if (count($migrations) === 0) {
            $this->log("No migrations to run");
        }
        foreach ($migrations as $file) {
            $this->log("Running $file...");
            $migration = $this->loadMigration($file);
            $bluePrint = $migration->mount(new Blueprint());
            if ($this->buildFromBluePrint($bluePrint)) {
                $this->log(CliColor::color("[OK]", ['b_green']));
                Sys::cliApp()->getMigrationManager()->registerMigration($file);
            }
        }
        Sys::cliApp()->db()->updateCache();
    }

    private function buildFromBluePrint(BluePrint $bluePrint) {
        $result = Sys::cliApp()->db()->createTable($bluePrint->getTableCreator());
        return $result;
    }

    private function loadMigration($file): Migration {
        $path = Sys::cliApp()->getMigrationManager()->getMigrationsDir() . DS . $file;
        if (!file_exists($path)) {
            throw new \Exception("The migration file could not be found");
        }
        require_once($path);
        $migrationName = $this->dashesToCamelCase(str_replace('.php', '', substr($file, strpos($file, '_') + 1)), true);
        $className = "App\Migrations\\" . $migrationName;
        $migration = new $className();        
        if (!$migration instanceof Migration) {
            throw new \Exception("Invalid migration");
        }
        return $migration;
    }
}