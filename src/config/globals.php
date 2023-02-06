<?php

function defineConst($const, $value) {
    if (!defined($const)) {
        define($const, $value);
    }
}

defineConst('APP_DIR', realpath(getcwd() . '/app'));
defineConst('DS', DIRECTORY_SEPARATOR);
defineConst('GFOX_ROOT', realpath(__DIR__ . '/..'));