<?php

namespace Alejodevop\Gfox\Http;
use Alejodevop\Gfox\Core\AppComponent;
use Alejodevop\Gfox\Core\Sys;

/**
 * Base class for all the application controllers
 * @author Alejandro Quiroz <alejandro.devop@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
abstract class Controller  extends AppComponent {
    protected $ID = 'Controller';

    public function init() {
        Sys::console("Initializing $this->ID", 2, __CLASS__);
    }

}