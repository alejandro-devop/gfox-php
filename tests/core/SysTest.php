<?php

namespace Alejodevop\Gfox;

use PHPUnit\Framework\TestCase;
use Alejodevop\Gfox\Core\Sys;

final class SysTest extends TestCase {

    public function testCreateAppMethod() {
        $app = Sys::createApp();
        $definedDS = defined('DS');
        $definedGFRoot = defined('GFOX_ROOT');

        $this->assertSame(get_class($app), "Alejodevop\Gfox\Core\WebApp");
        $this->assertTrue($definedDS);
        $this->assertTrue($definedGFRoot);
        
    }


}