<?php

namespace Alejodevop\Gfox\Cli;

use Alejodevop\Gfox\Core\Sys;

class CommandCreateModel extends Command {
    private $migrationsDir;
    public function beforeRun() {
        $this->migrationsDir = Sys::cliApp()->getAppDir() . DS . "migrations";
        Sys::cliApp()->getMigrationManager()->initMigrations();
    }
    public function afterRun() {}
    public function run() {
        $tableName = str_replace(' ', '_', $this->input("Enter the table name: "));
        $className = $this->dashesToCamelCase($tableName, true);
        $fields = Sys::cliApp()->db()->getFromSchema($tableName);
        $fieldsContent = [];
        foreach($fields as $field=>$fieldConfig) {
            $fieldsContent[] = " * @property mixed \$" . $this->dashesToCamelCase($field);
        }
        $templateFile = Sys::resolvePath('core.templates.model', true, 'template');
        $content = file_get_contents($templateFile);
        $content = str_replace('%ATTRIBUTES%', implode("\n", $fieldsContent), $content);
        $content = str_replace('%MODEL_NAME%', $className, $content);
        $content = str_replace('%TABLE_NAME%', $tableName, $content);

        $dest = Sys::resolvePath("app.model.$className");
        // echo $dest . "\n";
        // exit();
        $file = fopen($dest, 'w');
        fwrite($file, $content);
        fclose($file);
        $this->log("Model generated at: " . $dest);
    }
}