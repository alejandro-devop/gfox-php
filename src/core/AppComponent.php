<?php

namespace Alejodevop\Gfox\Core;

use Alejodevop\Gfox\Http\Request;

/**
 * Base class to include common behavior and attributes for all application
 * components
 * @author Alejandro Quiroz <alejandro.devop@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 */
abstract class AppComponent {
    /**
     * Every app component should have a unique ID 
     * Note: Probably helpfull when I include some kind of dependency
     * injection
     */
    protected $ID;
    /**
     * Every component should be able to access a request component
     */
    private ?Request $request;

    public function __construct(string $ID, ?Request $request = null){
        $this->ID = $ID;
        $this->request = $request;
    }

    /**
     * Function which can be used by every app component to initialize staff.
     */
    public abstract function init();
}