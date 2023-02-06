<?php

namespace Alejodevop\Gfox;

use PHPUnit\Framework\TestCase;
use Alejodevop\Gfox\Core\Sys;

final class WebAppTest extends TestCase {
    public function testAppCommons() {        
        $app = Sys::createApp();
        $currentAppRoot = getcwd();
        $this->assertSame($currentAppRoot, $app->getAppRoot());
    }
}