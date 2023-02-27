<?php

namespace Alejodevop\Gfox\Cli;

use Alejodevop\Gfox\Core\Sys;
use Alejodevop\YowlOrm\DBManager;

final class MigrationManager {

    private $migrationsDir;
    public function checkMigrationsDB() {
        $tableExists = Sys::cliApp()->db()->tableExists("gfox_migrations");
        if (!$tableExists) {
            $tableCreator = Sys::cliApp()->db()->getTableCreator();
            $tableCreator->tableName('gfox_migrations')
                ->pkCol('id')
                ->stringCol('migration')->requried()
                ->numberCol('exec_batch')
                ->dateCol('date')
                ;
            Sys::cliApp()->db()->createTable($tableCreator);
        }
    }

    public function checkPendingMigrations() {
        $appDir = Sys::cliApp()->getAppDir();
        $migrationsDir = $appDir . DS . "migrations";
        if (!file_exists($migrationsDir)) {
            mkdir($migrationsDir);
        }
        $migrationsInDir = $this->getMigrationsInDir();
        $migrationsInDB = array_map(fn ($item) => $item['migration'] ?? "", $this->getMigrationsInDB());
        if (count($migrationsInDir) === 0) {
            $this->log("No migrations pending");
            return [];
        } else {
            return array_filter($migrationsInDir, fn ($file) => !in_array(str_replace('.php', '', $file), $migrationsInDB));
        }
    }

    public function getMigrationsInDir() {
        $files = scandir($this->migrationsDir);
        unset($files[0], $files[1]);
        return $files;
    }

    public function getMigrationsInDB() {
        $query = Sys::cliApp()->db()->createQuery();
        $query->setTable('gfox_migrations')
            ->setColumnNames(['id', 'migration', 'exec_batch']);
        Sys::cliApp()->db()->setQuery($query);
        $result = Sys::cliApp()->db()->select();
        return $result;
    }
    public function checkMigrations() {
        $this->checkMigrationsDB();
        $this->checkPendingMigrations();
    }
    public function initMigrations() {
        $this->migrationsDir = Sys::cliApp()->getAppDir() . DS . "migrations";
        $this->checkMigrations();
        $this->log("Initializing migrations");
    }

    public function getMigrationsDir() {
        return $this->migrationsDir;
    }

    public function getNextBatch() {
        $query = Sys::cliApp()->db()->createQuery();
        $query->setTable('gfox_migrations');
        $query->max('exec_batch', 'batch');
        Sys::cliApp()->db()->setQuery($query);
        $result = Sys::cliApp()->db()->select();
        $item = $result[0] ?? [];
        return ($item['batch']?? 0) + 1;
    }

    public function registerMigration ($migrationFile) {
        $batch = $this->getNextBatch();
        $query = (Sys::cliApp()->db()->createQuery())
            ->setTable("gfox_migrations")
            ->setColumnNames(['migration', 'exec_batch', 'date'])
            ->setValues([str_replace('.php', '', $migrationFile), $batch, date("Y-m-d")]);
        Sys::cliApp()->db()->setQuery($query);
        Sys::cliApp()->db()->insert();
    }

    public function log($log) {
        Sys::console($log, 3, __CLASS__);
    }
}