<?php

namespace Alejodevop\Gfox\Cli;

use Alejodevop\Gfox\Core\Sys;
use Jfcherng\Utility\CliColor;
use LucidFrame\Console\ConsoleTable;


class CommandCheckMigrations extends Command {
    private $migrationsDir;
    public function beforeRun() {
        Sys::cliApp()->getMigrationManager()->initMigrations();
    }
    public function afterRun() {
        
    }

    private function getMigrated($migrations) {
        return array_map(fn ($item) => $item['migration'] ?? '', $migrations);
    }

    private function getBatchFormigration($migration, $list) {
        $found = 0;
        foreach ($list as $item) {
            if ($item['migration'] === $migration) {
                $found = $item['exec_batch'] ?? 0;
                break;
            }
        }
        return $found;
    }

    public function run() {
        $files = Sys::cliApp()->getMigrationManager()->getMigrationsInDir();
        $inDB = Sys::cliApp()->getMigrationManager()->getMigrationsInDB();
        $migrated = $this->getMigrated($inDB);
        
        $table = new ConsoleTable();
        $table->addHeader('File');
        $table->addHeader('Migrated');
        $table->addHeader('Batch');
        foreach($files as $file) {
            $migrationName = str_replace('.php', '', $file);
            $isMigrated = in_array($migrationName, $migrated);
            $batch = $this->getBatchFormigration($migrationName, $inDB);
            $table->addRow()
                ->addColumn($file)
                ->addColumn($isMigrated ? CliColor::color('Yes', ['b_green']) : CliColor::color('No', ['b_yellow']))
                ->addColumn($batch);
        }
        $table->display();
    }
}