<?php
/**
 * File containing all application aliases, an alias is a shortcut
 * to access important files on your project using the Sys::import function
 * @author Alejandro Quiroz <alejandro.devop@gmail.com>
 * 
 * |_ app: Points to the project main directory (my_app/app/)
 * |_ controller: Points to the project controllers (my_app/app/controller)
 * |_ core: Points to the framework main classes (my_app/app/core)
 * |_ model: Points to the Models folder in the project (my_app/app/model)
 * |_ serializer: Points the the serializers folder (my_app/app/serializer)
 * |_ view: Points the the views folder (my_app/app/view)
 */
return [
    'app' => constant('APP_DIR'),
    'controller' => constant('APP_DIR') . DS . 'controller',
    'core' => GFOX_ROOT,
    'model' => constant('APP_DIR') . DS . 'model',
    'serializer' => constant('APP_DIR') . DS . 'serializer',
    'view' => constant('APP_DIR') . DS . 'view',
];