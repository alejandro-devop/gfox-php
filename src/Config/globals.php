<?php
/**
 * File containing all application constatns
 * @author Alejandro Quiroz <alejandro.devop@gmail.com>
 */
function defineConst($const, $value) {
    if (!defined($const)) {
        define($const, $value);
    }
}

defineConst('DS', DIRECTORY_SEPARATOR);
defineConst('GFOX_ROOT', realpath(__DIR__ . '/..'));