<?php

namespace Alejodevop\Gfox\Cli;

use Alejodevop\Gfox\Core\Sys;

class CommandDoMigration extends Command {
    private $migrationsDir;
    public function beforeRun() {
        $this->migrationsDir = Sys::cliApp()->getAppDir() . DS . "migrations";
        Sys::cliApp()->getMigrationManager()->initMigrations();
    }
    public function afterRun() {}
    public function run() {
        $migrationNumber = time();
        $migrationName = $this->getMigrationName();
        $fileName = "{$migrationNumber}_{$migrationName}";
        $className = $this->dashesToCamelCase($migrationName, true);
        if (!Sys::fileExists('core.templates.migration', 'template')) {
            throw new \Exception('Migration template does not exist');
        } 
        $templateFile = Sys::resolvePath('core.templates.migration', true, 'template');
        $templateContent = file_get_contents($templateFile);
        $output = str_replace('%MIGRATION_NAME%', $className, $templateContent);
        $destDir = $this->migrationsDir . DS . $fileName . ".php";
        $file = fopen($destDir, 'w');
        fwrite($file, $output);
        fclose($file);
        $this->log("Migration saved at: " . $destDir);
    }

    private function getMigrationName() {
        $name = Sys::cliApp()->getOptions()->getFlag('name');
        return strtolower(str_replace(' ', '_', $name));
    }

    private function getMigrationsCount() {
        $migrations = scandir($this->migrationsDir);
        unset($migrations[0], $migrations[1]);
        return count($migrations);
    }
}