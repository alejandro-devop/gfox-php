<?php

namespace Alejodevop\Gfox\Database;
abstract class Migration {
    public abstract function mount(Blueprint $table): Blueprint;
    public abstract function unmount(Blueprint $table): Blueprint;
}