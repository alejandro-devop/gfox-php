<?php

namespace Alejodevop\Gfox\Database;
use Alejodevop\Gfox\Core\Sys;
use Alejodevop\YowlOrm\Core\DBTableCreator;

class Blueprint {
    
    private DBTableCreator $tableCreator;

    private $activeField;

    public function __construct() {
        $this->tableCreator = Sys::cliApp()->db()->getTableCreator();
    }
    public function name(string $name): Blueprint {
        $this->tableCreator->tableName($name);
        return $this;
    }

    /**
     * [required, ]
     *
     * @param [type] $name
     * @param array $options
     * @return Blueprint
     */
    public function addField($name, $type,  $options = []): Blueprint {
        $this->activeField = $name;
        $this->tableCreator->addColumn($name, array_merge([
            'type' => $type
        ], $options));
        return $this;
    }

    public function required(): Blueprint {
        $this->tableCreator->modifyField($this->activeField, [
            'required' => true,
        ]);
        return $this;
    }

    public function size($size): Blueprint {
        $this->tableCreator->modifyField($this->activeField, [
            'size' => $size,
        ]);
        return $this;
    }

    public function text($name): Blueprint {
        return $this->addField($name, DBTableCreator::STRING_FIELD, ['size' => 255]);
    }

    public function int($name): Blueprint {
        return $this->addField($name, DBTableCreator::INT_FIELD);
    }

    public function bigInt($name): Blueprint {
        return $this->addField($name, DBTableCreator::BIG_INT);
    }

    public function date($name): Blueprint {
        return $this->addField($name, DBTableCreator::DATE_FIELD);
    }

    public function boolean($name): Blueprint {
        return $this->addField($name, DBTableCreator::BOOL_FIELD);
    }

    public function longText($name): Blueprint {
        return $this->addField($name, DBTableCreator::TEXT_FIELD, ['size' => 500]);
    }

    public function dateTime($name): Blueprint {
        return $this->addField($name, DBTableCreator::TEXT_FIELD, []);
    }

    public function unique(): Blueprint {
        $this->tableCreator->modifyField($this->activeField, ['unique' => true]);
        return $this;
    }


    public function bigIntId($name) {
        $this->activeField = $name;
        $this->tableCreator->pkBigInt($name);
        return $this;
    }

    public function rel($targetTable, $targetTablePK = 'id'): Blueprint {
        return $this;
    }

    public function timestamp($name) {
        return $this->addField($name, DBTableCreator::TIMESTAMP_FIELD, []);
    }

    public function id($name = 'id', bool $uuid = false) {
        $this->activeField = $name;
        $this->tableCreator->pkCol($name, $uuid);
        return $this;
    }

    public function getTableCreator(): DBTableCreator {
        return $this->tableCreator;
    }
}